<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Services\Images;
use App\Support\Translated;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    use HandlesUploads;

    public function index(Request $request)
    {
        $announcements = Announcement::query()
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q) => $q
                ->where('title_fr', 'like', '%' . $request->search . '%')
                ->orWhere('title_en', 'like', '%' . $request->search . '%')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_on')
            ->paginate(20)
            ->withQueryString();

        return view('admin.announcements.index', ['current' => 'announcements', 'announcements' => $announcements]);
    }

    public function create()
    {
        return $this->form(new Announcement(['published_on' => today(), 'status' => Announcement::STATUS_DRAFT, 'priority' => 'normal']));
    }

    public function store(Request $request)
    {
        $announcement = Announcement::create($this->validated($request));
        $this->syncImage($request, $announcement, 'announcements');
        $this->syncFile($request, $announcement, 'announcements/attachments');

        return redirect()->route('announcements.index')->with('success', 'Communiqué créé.');
    }

    public function edit(Announcement $announcement)
    {
        return $this->form($announcement);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $announcement->update($this->validated($request));
        $this->syncImage($request, $announcement, 'announcements');
        $this->syncFile($request, $announcement, 'announcements/attachments');

        return redirect()->route('announcements.index')->with('success', 'Communiqué mis à jour.');
    }

    public function destroy(Announcement $announcement)
    {
        Images::delete($announcement->image_path);
        $this->deleteFile($announcement->attachment_path);
        $announcement->delete();

        return redirect()->route('announcements.index')->with('success', 'Communiqué supprimé.');
    }

    private function form(Announcement $announcement)
    {
        return view('admin.announcements.form', ['current' => 'announcements', 'announcement' => $announcement]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            ...Translated::rules('title', ['string', 'max:255'], required: true),
            ...Translated::rules('body', ['string']),
            'category' => ['nullable', 'string', 'max:100'],
            'priority' => ['required', Rule::in(array_keys(Announcement::PRIORITIES))],
            'published_on' => ['required', 'date'],
            'expires_on' => ['nullable', 'date', 'after_or_equal:published_on'],
            'status' => ['required', Rule::in(array_keys(Announcement::STATUSES))],
            'image' => $this->imageRules(),
            'attachment' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        unset($data['image'], $data['attachment']);
        $data['is_pinned'] = $request->boolean('is_pinned');

        return Translated::cleanRich($data, ['body']);
    }
}
