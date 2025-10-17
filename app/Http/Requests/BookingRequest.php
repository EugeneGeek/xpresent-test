<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\Booking;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        Log::channel('booking')->info('--- Новый запрос на бронирование ---', [
            'payload' => $this->all(),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        return [
            'service_id'   => ['required', 'exists:services,id'],
            'client_name'  => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:255'],
            'date'         => ['required', 'date'],
            'start_time'   => ['required', 'date_format:H:i'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            try {
                $service = Service::find($this->service_id);
                if (!$service) {
                    $validator->errors()->add('service_id', 'Услуга не найдена.');
                    return;
                }

                $start = Carbon::parse($this->date . ' ' . $this->start_time);
                $end = $start->copy()->addMinutes($service->duration + 30);

                if ($start->isPast()) {
                    $validator->errors()->add('date', 'Нельзя бронировать прошедшее время.');
                }

                if ($start->isSunday()) {
                    $validator->errors()->add('date', 'В воскресенье бронирование недоступно.');
                }

                // --- Проверка пересечения по времени для этой услуги ---
                $overlap = Booking::where('service_id', $service->id)
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

                if ($overlap) {
                    $validator->errors()->add('start_time', 'Это время уже занято.');
                }

                // --- Новая проверка: у клиента не должно быть другого бронирования на то же время ---
                $clientConflict = Booking::where('client_phone', $this->client_phone)
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

                if ($clientConflict) {
                    $validator->errors()->add('client_phone', 'У вас уже есть бронирование на это время.');
                }

                if ((int) $start->minute % 30 !== 0) {
                    $validator->errors()->add('start_time', 'Можно бронировать только на целые или получасовые интервалы.');
                }

                if ($validator->errors()->isNotEmpty()) {
                    Log::channel('booking')->warning('Ошибки валидации', [
                        'errors' => $validator->errors()->toArray(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::channel('booking')->error('Ошибка при валидации', [
                    'exception' => $e->getMessage(),
                ]);
            }
        });
    }


    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        Log::channel('booking')->warning('Ошибки валидации (Inertia)', [
            'errors' => $errors->toArray(),
        ]);

        throw new ValidationException($validator, Inertia::render('Services/Show', [
            'service'  => \App\Models\Service::find($this->service_id),
            'bookings' => \App\Models\Booking::where('service_id', $this->service_id)->get(),
        ])->with('errors', $errors));
    }

}
