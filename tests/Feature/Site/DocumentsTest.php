<?php

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Site\Make;

beforeEach(fn () => Storage::fake('public'));

test('les documents sont groupés par catégorie', function () {
    Make::document(['title_fr' => 'Formulaire de visa', 'category' => 'Visas']);
    Make::document(['title_fr' => 'Guide sans catégorie', 'category' => null]);
    Make::document(['title_fr' => 'Brouillon interne', 'status' => 'draft']);

    $this->get('/fr/documents')
        ->assertOk()
        ->assertSeeInOrder(['Visas', 'Formulaire de visa', 'Autres documents', 'Guide sans catégorie'])
        ->assertDontSee('Brouillon interne');
});

test('le téléchargement compte les téléchargements et renvoie le fichier', function () {
    Storage::disk('public')->put('documents/visa.pdf', '%PDF-1.4 test');
    $document = Make::document();

    $this->get("/fr/documents/{$document->id}/telecharger")
        ->assertOk()
        ->assertDownload('formulaire-visa.pdf');

    expect($document->fresh()->download_count)->toBe(1);
});

test('un document brouillon ou sans fichier ne se télécharge pas', function () {
    Storage::disk('public')->put('documents/visa.pdf', '%PDF');
    $draft = Make::document(['status' => Document::STATUS_DRAFT]);
    $missing = Make::document(['file_path' => 'documents/absent.pdf']);

    $this->get("/fr/documents/{$draft->id}/telecharger")->assertNotFound();
    $this->get("/fr/documents/{$missing->id}/telecharger")->assertNotFound();

    expect($draft->fresh()->download_count)->toBe(0);
});
