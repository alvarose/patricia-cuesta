<?php

namespace Database\Factories\Contact;

use App\Domain\Contact\Message\Enums\ContactPreference;
use App\Models\Contact\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ContactMessage> */
class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    public function definition(): array
    {
        return [
            ContactMessage::NAME => fake()->name(),
            ContactMessage::EMAIL => fake()->safeEmail(),
            ContactMessage::PHONE => fake()->optional(0.7)->phoneNumber(),
            ContactMessage::CONTACT_PREFERENCE => fake()->randomElement(ContactPreference::cases()),
            ContactMessage::PREFERRED_TIME => fake()->randomElement(['Mañanas (9–14h)', 'Tardes (16–20h)', 'Indiferente']),
            ContactMessage::BODY => fake()->paragraph(3),
            ContactMessage::READ_AT => now()->subDays(fake()->numberBetween(1, 10)),
        ];
    }

    public function unread(): static
    {
        return $this->state(fn () => [ContactMessage::READ_AT => null]);
    }
}
