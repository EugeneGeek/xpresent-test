<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function store(BookingRequest $request)
    {
        $log = Log::channel('booking');
        $log->info('➡️ Начало бронирования', ['payload' => $request->all()]);

        try {
            $service = Service::findOrFail($request->service_id);
            $start = Carbon::parse($request->date . ' ' . $request->start_time);
            $end = $start->copy()->addMinutes($service->duration + 30);

            $exists = Booking::where('service_id', $service->id)
                ->whereDate('date', $start->toDateString())
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_time', [$start->format('H:i:s'), $end->format('H:i:s')])
                        ->orWhereBetween('end_time', [$start->format('H:i:s'), $end->format('H:i:s')])
                        ->orWhere(function ($sub) use ($start, $end) {
                            $sub->where('start_time', '<=', $start->format('H:i:s'))
                                ->where('end_time', '>=', $end->format('H:i:s'));
                        });
                })
                ->exists();

            if ($exists) {
                $log->warning('Слот уже занят', ['date' => $request->date, 'time' => $request->start_time]);
                return back()->withErrors([
                    'start_time' => 'Этот слот уже занят. Выберите другое время.',
                ]);
            }

            $booking = DB::transaction(function () use ($request, $service, $start, $end, $log) {
                $booking = Booking::create([
                    'service_id' => $service->id,
                    'client_name' => $request->client_name,
                    'client_phone' => $request->client_phone,
                    'date' => $start->toDateString(),
                    'start_time' => $start->format('H:i:s'),
                    'end_time' => $end->format('H:i:s'),
                ]);

                $log->info('✅ Бронирование создано', ['id' => $booking->id]);
                return $booking;
            });

            $log->info('🏁 Успешное завершение бронирования', ['id' => $booking->id]);

            // ✅ редирект на страницу списка услуг (Inertia-friendly)
            return Inertia::location(route('services.index'));
        } catch (\Throwable $e) {
            $log->error('Ошибка при бронировании', ['error' => $e->getMessage()]);
            return back()->withErrors([
                'general' => 'Ошибка при бронировании. Попробуйте позже.',
            ]);
        }
    }

    public function byDate(Service $service, string $date)
    {
        $bookings = Booking::query()
            ->where('service_id', $service->id)
            ->whereDate('date', $date)
            ->select('id', 'date', 'start_time')
            ->get();

        // Возвращаем только обновлённые данные через Inertia partial reload
        return Inertia::render('Services/Show', [
            'bookings' => $bookings,
        ]);
    }
}
