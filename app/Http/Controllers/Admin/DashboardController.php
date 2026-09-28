<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\Event;
use App\Models\Message;
use App\Models\News;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'current' => 'dashboard',
            'stats' => [
                ['label' => 'Rendez-vous en attente', 'value' => Appointment::pending()->count(), 'icon' => 'calendar-check', 'route' => route('appointments.index', ['status' => 'pending'])],
                ['label' => 'Messages non lus', 'value' => Message::unread()->count(), 'icon' => 'envelope-simple', 'route' => route('messages.index', ['status' => 'unread'])],
                ['label' => 'Articles publiés', 'value' => News::published()->count(), 'icon' => 'newspaper', 'route' => route('news.index')],
                ['label' => 'Événements à venir', 'value' => Event::upcoming()->count(), 'icon' => 'calendar-star', 'route' => route('events.index')],
            ],
            'appointments' => Appointment::with('service')->pending()->orderBy('preferred_date')->orderBy('preferred_time')->limit(6)->get(),
            'messages' => Message::unread()->latest()->limit(5)->get(),
            'upcomingEvents' => Event::upcoming()->limit(4)->get(),
            'activeAnnouncements' => Announcement::visible()->count(),
            'downloads' => Document::published()->sum('download_count'),
            'drafts' => News::where('status', News::STATUS_DRAFT)->latest('updated_at')->limit(4)->get(),
        ]);
    }
}
