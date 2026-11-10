<?php

namespace App\Models\Contact;

use App\Domain\Contact\Message\Enums\ContactPreference;
use App\Http\Resources\Contact\Message\ContactMessageResource;
use App\Models\Patients\Patient;
use Carbon\CarbonImmutable;
use Database\Factories\Contact\ContactMessageFactory;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property ContactPreference $contact_preference
 * @property string|null $preferred_time
 * @property string $body
 * @property CarbonImmutable|null $read_at
 * @property CarbonImmutable|null $replied_at
 * @property string|null $replied_via
 * @property int|null $patient_id
 * @property CarbonImmutable $created_at
 */
#[UseResource(ContactMessageResource::class)]
class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    const TABLE = 'contact_messages';

    const ID = 'id';

    const NAME = 'name';

    const EMAIL = 'email';

    const PHONE = 'phone';

    const CONTACT_PREFERENCE = 'contact_preference';

    const PREFERRED_TIME = 'preferred_time';

    const BODY = 'body';

    const READ_AT = 'read_at';

    const REPLIED_AT = 'replied_at';

    const REPLIED_VIA = 'replied_via';

    const PATIENT_ID = 'patient_id';

    const CREATED_AT = 'created_at';

    protected $table = self::TABLE;

    protected $guarded = [self::ID];

    protected function casts(): array
    {
        return [
            ContactMessage::CONTACT_PREFERENCE => ContactPreference::class,
            ContactMessage::READ_AT => 'immutable_datetime',
            ContactMessage::REPLIED_AT => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<Patient, $this> */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, self::PATIENT_ID);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function isReplied(): bool
    {
        return $this->replied_at !== null;
    }
}
