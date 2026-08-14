<?php

namespace App\Http\Requests\Booking\Availability;

use App\Domain\Booking\Availability\DTO\TimeWindowData;
use App\Domain\Booking\Availability\DTO\UpdateWeeklyScheduleData;
use App\Domain\Booking\Availability\DTO\WeekdayScheduleData;
use App\Domain\Booking\Slot\Support\BookingSettings;
use App\Models\Booking\AvailabilityDay;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', AvailabilityDay::class) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'days' => ['required', 'array', 'size:7'],
            'days.*.weekday' => ['required', 'integer', 'between:1,7'],
            'days.*.enabled' => ['required', 'boolean'],
            'days.*.windows' => ['array'],
            'days.*.windows.*.start' => ['required', 'date_format:H:i'],
            'days.*.windows.*.end' => ['required', 'date_format:H:i', 'after:days.*.windows.*.start'],
            'settings.session_minutes' => ['required', 'integer', Rule::in(BookingSettings::OPTIONS['session_minutes'])],
            'settings.buffer_minutes' => ['required', 'integer', Rule::in(BookingSettings::OPTIONS['buffer_minutes'])],
            'settings.min_notice_hours' => ['required', 'integer', Rule::in(BookingSettings::OPTIONS['min_notice_hours'])],
            'settings.max_advance_days' => ['required', 'integer', Rule::in(BookingSettings::OPTIONS['max_advance_days'])],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [$this->ensureWindowsDoNotOverlap(...)];
    }

    public function toData(): UpdateWeeklyScheduleData
    {
        $settings = $this->validated('settings');

        return new UpdateWeeklyScheduleData(
            days: array_map(
                fn (array $day): WeekdayScheduleData => new WeekdayScheduleData(
                    weekday: (int) $day['weekday'],
                    enabled: (bool) $day['enabled'],
                    windows: array_map(
                        fn (array $window): TimeWindowData => new TimeWindowData($window['start'], $window['end']),
                        $day['windows'] ?? [],
                    ),
                ),
                $this->validated('days'),
            ),
            settings: new BookingSettings(
                sessionMinutes: (int) $settings['session_minutes'],
                bufferMinutes: (int) $settings['buffer_minutes'],
                minNoticeHours: (int) $settings['min_notice_hours'],
                maxAdvanceDays: (int) $settings['max_advance_days'],
            ),
        );
    }

    private function ensureWindowsDoNotOverlap(Validator $validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $reference = CarbonImmutable::today();

        foreach ($this->toData()->days as $day) {
            $ranges = array_map(
                fn (TimeWindowData $window) => $window->rangeOn($reference),
                $day->windows,
            );

            foreach ($ranges as $i => $range) {
                foreach (array_slice($ranges, $i + 1) as $other) {
                    if ($range->overlaps($other)) {
                        $validator->errors()->add(
                            'days',
                            'Las franjas del '.AvailabilityDay::labelFor($day->weekday).' se solapan.',
                        );

                        continue 3;
                    }
                }
            }
        }
    }
}
