<?php

namespace App\Models\Booking;

use App\Models\Core\HistoryModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property int $appointment_id */
class AppointmentHistory extends HistoryModel
{
    const TABLE = 'appointment_history';

    const APPOINTMENT_ID = 'appointment_id';

    protected $table = self::TABLE;

    /** @return BelongsTo<Appointment, $this> */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, self::APPOINTMENT_ID);
    }
}
