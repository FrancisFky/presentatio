<?php

namespace App\Livewire;

use App\Livewire\Concerns\ProtectsPublicForms;
use App\Mail\NewSubmission;
use App\Models\Message;
use App\Support\Site;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Locked;
use Livewire\Component;

/** Formulaire de la page Contact : le message arrive dans l'admin et par e-mail */
class ContactForm extends Component
{
    use ProtectsPublicForms;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $subject = '';
    public string $body = '';

    #[Locked]
    public bool $sent = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+().\s-]{6,}$/'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    protected function messages(): array
    {
        return ['phone.regex' => __('site.forms.phone_invalid')];
    }

    protected function validationAttributes(): array
    {
        return collect(['name', 'email', 'phone', 'subject', 'body'])
            ->mapWithKeys(fn ($field) => [$field => mb_strtolower(__("site.contact.fields.{$field}"))])
            ->all();
    }

    public function updated(string $property): void
    {
        if (array_key_exists($property, $this->rules())) {
            $this->validateOnly($property);
        }
    }

    public function submit(): void
    {
        if ($this->tooManyAttempts()) {
            return;
        }

        if ($this->isSpam()) {
            $this->sent = true;

            return;
        }

        $data = $this->validate();
        $this->countAttempt();

        $message = Message::create([
            ...$data,
            'phone' => $data['phone'] ?: null,
            'status' => Message::STATUS_UNREAD,
        ]);

        if ($to = Site::notificationEmail()) {
            $this->sendSafely(fn () => Mail::to($to)->send(new NewSubmission($message)));
        }

        $this->resetExcept('locale');
        $this->sent = true;
    }

    public function startOver(): void
    {
        $this->reset('name', 'email', 'phone', 'subject', 'body', 'sent', 'website');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
