<?php

namespace App\Models\Booking;

use App\Domain\Booking\Appointment\Enums\AppointmentSource;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Booking\Slot\Support\TimeRange;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Http\Resources\Booking\Appointment\AppointmentResource;
use App\Models\Patients\Patient;
use App\Traits\HasResource;
use Carbon\CarbonImmutable;
use Database\Factories\Booking\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property int|null $patient_id
 * @property string $contact_name
 * @property string $contact_email
 * @property string|null $contact_phone
 * @property ConsultationTopic|null $topic
 * @property CarbonImmutable $starts_at
 * @property CarbonImmutable $ends_at
 * @property AppointmentStatus $status
 * @property AppointmentSource $source
 * @property string|null $notes
 * @property-read Patient|null $patient
 */
class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory, HasResource;

    const TABLE = 'appointments';

    const ID = 'id';

    const PATIENT_ID = 'patient_id';

    const CONTACT_NAME = 'contact_name';

    const CONTACT_EMAIL = 'contact_email';

    const CONTACT_PHONE = 'contact_phone';

    const TOPIC = 'topic';

    const STARTS_AT = 'starts_at';

    const ENDS_AT = 'ends_at';

    const STATUS = 'status';

    const SOURCE = 'source';

    const NOTES = 'notes';

    protected $table = self::TABLE;

    protected $guarded = [self::ID];

    /** @var class-string<JsonResource> */
    protected static string $resourceClass = AppointmentResource::class;

    protected function casts(): array
    {
        return [
            self::STARTS_AT => 'immutable_datetime',
            self::ENDS_AT => 'immutable_datetime',
            self::STATUS => AppointmentStatus::class,
            self::SOURCE => AppointmentSource::class,
            self::TOPIC => ConsultationTopic::class,
        ];
    }

    /** @return BelongsTo<Patient, $this> */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, self::PATIENT_ID);
    }

    public function range(): TimeRange
    {
        return new TimeRange($this->starts_at, $this->ends_at);
    }

    public function displayName(): string
    {
        return $this->patient?->fullName() ?? $this->contact_name;
    }
}
