<?php

namespace App\Models\Patients;

use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Domain\Patients\Patient\Enums\PatientStatus;
use App\Http\Resources\Patients\Patient\PatientResource;
use App\Models\Booking\Appointment;
use App\Models\Contact\ContactMessage;
use App\Traits\HasResource;
use Carbon\CarbonImmutable;
use Database\Factories\Patients\PatientFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property ConsultationTopic|null $topic
 * @property string|null $notes
 * @property PatientStatus $status
 * @property CarbonImmutable $created_at
 * @property-read Appointment|null $nextAppointment
 */
class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory, HasResource, SoftDeletes;

    const TABLE = 'patients';

    const ID = 'id';

    const FIRST_NAME = 'first_name';

    const LAST_NAME = 'last_name';

    const EMAIL = 'email';

    const PHONE = 'phone';

    const TOPIC = 'topic';

    const NOTES = 'notes';

    const STATUS = 'status';

    const CREATED_AT = 'created_at';

    protected $table = self::TABLE;

    protected $guarded = [self::ID];

    /** @var class-string<JsonResource> */
    protected static string $resourceClass = PatientResource::class;

    protected function casts(): array
    {
        return [
            self::TOPIC => ConsultationTopic::class,
            self::STATUS => PatientStatus::class,
        ];
    }

    /** @return Attribute<string|null, string|null> */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value): ?string => $value === null ? null : Str::lower(trim($value)),
        );
    }

    /** @return HasMany<Appointment, $this> */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, Appointment::PATIENT_ID);
    }

    /** @return HasMany<ContactMessage, $this> */
    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class, ContactMessage::PATIENT_ID);
    }

    /** @return HasOne<Appointment, $this> */
    public function nextAppointment(): HasOne
    {
        return $this->hasOne(Appointment::class, Appointment::PATIENT_ID)->ofMany(
            [Appointment::STARTS_AT => 'min'],
            $this->constrainToUpcomingBlocking(...),
        );
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function initials(): string
    {
        $initials = mb_substr($this->first_name, 0, 1);

        if ($this->last_name !== null && $this->last_name !== '') {
            $initials .= mb_substr($this->last_name, 0, 1);
        }

        return mb_strtoupper($initials);
    }

    /** @param Builder<Appointment> $query */
    private function constrainToUpcomingBlocking(Builder $query): void
    {
        $query->whereIn(Appointment::STATUS, AppointmentStatus::blocking())
            ->where(Appointment::STARTS_AT, '>=', now());
    }
}
