<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Support\Translated;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Formulaires et publications à télécharger */
class DocumentController extends Controller
{
    use HandlesUploads;

    public function index(Request $request)
    {
        $documents = Document::query()
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q) => $q
                ->where('title_fr', 'like', '%' . $request->search . '%')
                ->orWhere('title_en', 'like', '%' . $request->search . '%')))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderBy('category')
            ->orderBy('title_fr')
            ->paginate(30)
            ->withQueryString();

        return view('admin.documents.index', [
            'current' => 'documents',
            'documents' => $documents,
            'categories' => $this->categories(),
        ]);
    }

    public function create()
    {
        return $this->form(new Document(['status' => Document::STATUS_PUBLISHED]));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, creating: true);
        $document = Document::create($data + $this->fileAttributes($request));

        return redirect()->route('documents.index')->with('success', "« {$document->title_fr} » ajouté.");
    }

    public function edit(Document $document)
    {
        return $this->form($document);
    }

    public function update(Request $request, Document $document)
    {
        $data = $this->validated($request, creating: false);

        if ($request->hasFile('file')) {
            $old = $document->file_path;
            $data += $this->fileAttributes($request);
            $this->deleteFile($old);
        }

        $document->update($data);

        return redirect()->route('documents.index')->with('success', 'Document mis à jour.');
    }

    public function destroy(Document $document)
    {
        $this->deleteFile($document->file_path);
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document supprimé.');
    }

    private function form(Document $document)
    {
        return view('admin.documents.form', [
            'current' => 'documents',
            'document' => $document,
            'categories' => $this->categories(),
        ]);
    }

    private function categories(): array
    {
        return Document::whereNotNull('category')->distinct()->orderBy('category')->pluck('category')->all();
    }

    private function fileAttributes(Request $request): array
    {
        $file = $request->file('file');

        return [
            'file_path' => $this->storeFile($file, 'documents'),
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ];
    }

    private function validated(Request $request, bool $creating): array
    {
        $data = $request->validate([
            ...Translated::rules('title', ['string', 'max:255'], required: true),
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(array_keys(Document::STATUSES))],
            'file' => [$creating ? 'required' : 'nullable', 'file', 'mimes:' . implode(',', Document::EXTENSIONS), 'max:20480'],
        ]);

        unset($data['file']);

        return $data;
    }
}
