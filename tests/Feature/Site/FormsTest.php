<?php

use App\Livewire\AppointmentForm;
use App\Livewire\ContactForm;
use App\Mail\AppointmentReceived;
use App\Mail\NewSubmission;
use App\Models\Appointment;
use App\Models\Message;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\Feature\Site\Make;

beforeEach(function () {
    // Un lundi : les dates relatives des tests tombent toujours sur les mêmes jours
    $this->travelTo(Carbon::parse('2026-10-05 10:00'));
    RateLimiter::clear('site-form:' . AppointmentForm::class . ':127.0.0.1');
    RateLimiter::clear('site-form:' . ContactForm::class . ':127.0.0.1');
    Mail::fake();
});

function fillAppointment($component, array $overrides = [])
{
    $values = $overrides + [
        'name' => 'Jean Mabiala',
        'email' => 'jean@example.com',
        'phone' => '+254 700 000 000',
        'nationality' => 'Congolaise',
        'preferred_date' => '2026-10-07', // mercredi
        'preferred_time' => '10:00',
        'consent' => true,
    ];

    foreach ($values as $field => $value) {
        $component->set($field, $value);
    }

    return $component;
}

test('une demande de rendez-vous est enregistrée et confirmée par e-mail', function () {
    Setting::set('site.notification_email', 'consulat@ambassade.test');
    $service = Make::service();
    app()->setLocale('en');

    $component = fillAppointment(Livewire::test(AppointmentForm::class)->set('service_id', (string) $service->id))
        ->call('submit')
        ->assertHasNoErrors();

    $appointment = Appointment::sole();
    expect($appointment)
        ->service_id->toBe($service->id)
        ->locale->toBe('en')
        ->status->toBe(Appointment::STATUS_PENDING)
        ->reference->toStartWith('RDV-');

    $component->assertSet('reference', $appointment->reference)->assertSee($appointment->reference);

    Mail::assertSent(AppointmentReceived::class, fn ($mail) => $mail->hasTo('jean@example.com') && $mail->appointment->is($appointment));
    Mail::assertSent(NewSubmission::class, fn ($mail) => $mail->hasTo('consulat@ambassade.test'));
});

test('la page de rendez-vous propose tous les créneaux horaires', function () {
    $response = $this->get('/fr/rendez-vous')->assertOk();

    foreach (Appointment::TIME_SLOTS as $time) {
        $response->assertSee('<option value="' . $time . '">' . $time . '</option>', false);
    }
});

test('le service est pré-sélectionné depuis la fiche service', function () {
    $service = Make::service();

    Livewire::withQueryParams(['service' => $service->id])
        ->test(AppointmentForm::class, ['service' => $service->id])
        ->assertSet('service_id', (string) $service->id);

    $this->get("/fr/rendez-vous?service={$service->id}")->assertOk();
});

test('« Autre » demande de préciser la démarche', function () {
    fillAppointment(Livewire::test(AppointmentForm::class)->set('service_id', AppointmentForm::OTHER))
        ->call('submit')
        ->assertHasErrors(['service_label' => 'required_if']);

    fillAppointment(Livewire::test(AppointmentForm::class)->set('service_id', AppointmentForm::OTHER)->set('service_label', 'Laissez-passer'))
        ->call('submit')
        ->assertHasNoErrors();

    expect(Appointment::sole())->service_id->toBeNull()->service_label->toBe('Laissez-passer');
});

test('les dates passées, les week-ends et les jours fériés sont refusés', function (string $date, string $message) {
    Make::holiday('2026-10-08', ['name_fr' => 'Fête nationale']);
    $service = Make::service();

    fillAppointment(Livewire::test(AppointmentForm::class)->set('service_id', (string) $service->id), ['preferred_date' => $date])
        ->call('submit')
        ->assertHasErrors('preferred_date')
        ->assertSee($message, false);

    expect(Appointment::count())->toBe(0);
    Mail::assertNothingSent();
})->with([
    'passée' => ['2026-10-02', 'Choisissez une date à partir'],
    'samedi' => ['2026-10-10', 'ne reçoit pas le week-end'],
    'dimanche' => ['2026-10-11', 'ne reçoit pas le week-end'],
    'férié' => ['2026-10-08', 'fermée ce jour-là (Fête nationale)'],
]);

test('les erreurs de validation sont traduites', function () {
    app()->setLocale('en');

    Livewire::test(AppointmentForm::class)
        ->set('preferred_date', '2026-10-10')
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'service_id', 'preferred_time', 'consent'])
        ->assertSee('The Embassy is closed at weekends');

    app()->setLocale('fr');

    Livewire::test(AppointmentForm::class)
        ->call('submit')
        ->assertSee('Le champ nom complet est obligatoire.');
});

test('le piège à robots fait semblant de réussir sans rien enregistrer', function () {
    $service = Make::service();

    fillAppointment(Livewire::test(AppointmentForm::class)->set('service_id', (string) $service->id))
        ->set('website', 'http://spam.example')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertNotSet('reference', null);

    expect(Appointment::count())->toBe(0);
    Mail::assertNothingSent();
});

test('au-delà de cinq demandes en dix minutes, la même IP est bloquée', function () {
    $service = Make::service();

    foreach (range(1, 5) as $i) {
        fillAppointment(Livewire::test(AppointmentForm::class)->set('service_id', (string) $service->id))->call('submit')->assertHasNoErrors();
    }

    fillAppointment(Livewire::test(AppointmentForm::class)->set('service_id', (string) $service->id))
        ->call('submit')
        ->assertHasErrors('form');

    expect(Appointment::count())->toBe(5);
});

test("un e-mail qui échoue ne fait pas perdre la demande", function () {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP indisponible'));
    $service = Make::service();

    fillAppointment(Livewire::test(AppointmentForm::class)->set('service_id', (string) $service->id))
        ->call('submit')
        ->assertHasNoErrors();

    expect(Appointment::count())->toBe(1);
});

test('le formulaire de contact enregistre le message et prévient l’ambassade', function () {
    Livewire::test(ContactForm::class)
        ->set('name', 'Awa Nkounkou')
        ->set('email', 'awa@example.com')
        ->set('subject', 'Légalisation')
        ->set('body', 'Bonjour, quels documents faut-il pour une légalisation ?')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    expect(Message::sole())
        ->name->toBe('Awa Nkounkou')
        ->status->toBe(Message::STATUS_UNREAD)
        ->phone->toBeNull();

    Mail::assertSent(NewSubmission::class, fn ($mail) => $mail->hasTo(config('mail.from.address')) && $mail->submission->is(Message::sole()));
});

test('le formulaire de contact valide et ignore les robots', function () {
    Livewire::test(ContactForm::class)
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'subject', 'body']);

    Livewire::test(ContactForm::class)
        ->set('name', 'Robot')->set('email', 'bot@example.com')->set('subject', 'Spam')->set('body', 'Achetez maintenant, offre limitée !')
        ->set('website', 'x')
        ->call('submit')
        ->assertSet('sent', true);

    expect(Message::count())->toBe(0);
});
