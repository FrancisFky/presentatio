<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        return view('site.services.index', [
            'services' => Service::published()->ordered()->get(),
        ]);
    }

    public function show(Service $service)
    {
        abort_unless($service->isPublished(), 404);

        return view('site.services.show', [
            'service' => $service,
            'others' => Service::published()->ordered()->whereKeyNot($service->id)->get(),
        ]);
    }
}
