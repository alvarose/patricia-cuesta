<?php

namespace App\Http\Controllers\Web\Admin\Dashboard;

use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Contact\Message\Contracts\ContactMessageServiceInterface;
use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Http\Controllers\Web\WebController;
use App\Http\Resources\Booking\Appointment\AppointmentResource;
use App\Http\Resources\Contact\Message\ContactMessageResource;
use App\Models\Booking\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends WebController
{
    public function __construct(
        private readonly AppointmentServiceInterface $appointments,
        private readonly ContactMessageServiceInterface $messages,
        private readonly PatientServiceInterface $patients,
    ) {}

    public function index(): Response
    {
        $this->authorize('viewAny', Appointment::class);

        $today = $this->appointments->blockingForDay(CarbonImmutable::today());
        $pending = $this->appointments->countAwaitingConfirmation();
        $unread = $this->messages->countUnread();

        return Inertia::render('admin/Dashboard', [
            'weekSummary' => $this->weekSummary($pending, $unread),
            'stats' => $this->stats($today, $unread),
            'todayAppointments' => AppointmentResource::collection($today),
            'recentMessages' => ContactMessageResource::collection($this->messages->recent(4)),
        ]);
    }

    private function weekSummary(int $pending, int $unread): string
    {
        $week = $this->appointments->countBlockingBetween(
            CarbonImmutable::now()->startOfWeek(),
            CarbonImmutable::now()->endOfWeek(),
        );

        $parts = [sprintf('Tienes %d %s esta semana', $week, $this->plural($week, 'sesión', 'sesiones'))];

        if ($pending > 0) {
            $parts[] = sprintf('%d por confirmar', $pending);
        }

        if ($unread > 0) {
            $parts[] = sprintf('%d %s sin leer', $unread, $this->plural($unread, 'mensaje', 'mensajes'));
        }

        return $this->joinNaturally($parts).'.';
    }

    /**
     * @param  Collection<int, Appointment>  $today
     * @return array<int, array{label: string, value: string, note: string}>
     */
    private function stats(Collection $today, int $unread): array
    {
        $newPatients = $this->patients->countRegisteredSince(CarbonImmutable::now()->startOfMonth());

        return [
            [
                'label' => 'Citas hoy',
                'value' => (string) $today->count(),
                'note' => $today->isNotEmpty()
                    ? 'Próxima a las '.$today->first()->starts_at->format('H:i')
                    : 'Sin sesiones hoy',
            ],
            [
                'label' => 'Mensajes sin leer',
                'value' => (string) $unread,
                'note' => $unread > 0 ? 'Te esperan en la bandeja' : 'Bandeja al día',
            ],
            [
                'label' => 'Pacientes activos',
                'value' => (string) $this->patients->countActive(),
                'note' => $newPatients > 0
                    ? sprintf('%d %s este mes', $newPatients, $this->plural($newPatients, 'empezó', 'empezaron'))
                    : 'Sin altas nuevas este mes',
            ],
        ];
    }

    private function plural(int $count, string $singular, string $plural): string
    {
        return $count === 1 ? $singular : $plural;
    }

    /** @param array<int, string> $parts */
    private function joinNaturally(array $parts): string
    {
        if (count($parts) === 1) {
            return $parts[0];
        }

        $last = array_pop($parts);

        return implode(', ', $parts).' y '.$last;
    }
}
