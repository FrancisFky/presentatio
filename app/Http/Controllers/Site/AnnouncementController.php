<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

/** Communiqués officiels : les épinglés d'abord, les expirés disparaissent d'eux-mêmes */
class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::visible()
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_on')
            ->orderByDesc('id')
            ->paginate(10);

        return view('site.announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement)
    {
        abort_unless(Announcement::visible()->whereKey($announcement->id)->exists(), 404);

        return view('site.announcements.show', [
            'announcement' => $announcement,
            'others' => Announcement::visible()->whereKeyNot($announcement->id)->orderByDesc('published_on')->limit(4)->get(),
        ]);
    }
}
