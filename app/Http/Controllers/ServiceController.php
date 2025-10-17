<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class ServiceController extends Controller
{
    //
    public function index(): \Inertia\Response
    {
        $services = Service::all();
        return Inertia::render('Services/Index', [
            'services' => $services,
        ]);
    }

    public function show(Service $service): \Inertia\Response
    {
        $bookings = $service->bookings()
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->get(['date', 'start_time', 'end_time']);

        return Inertia::render('Services/Show', [
            'service' => $service,
            'bookings' => $bookings,
            'serverTime' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
