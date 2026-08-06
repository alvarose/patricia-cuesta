<?php

use App\Domain\Contact\Message\DTO\CreateContactMessageData;
use App\Domain\Contact\Message\Enums\ContactPreference;
use App\Domain\Contact\Message\UseCases\LinkPatientToMessage;
use App\Domain\Contact\Message\UseCases\MarkMessageAsRead;
use App\Domain\Contact\Message\UseCases\ReplyToContactMessage;
use App\Domain\Contact\Message\UseCases\SubmitContactMessage;
use App\Mail\ContactMessageReceived;
use App\Mail\ContactMessageReply;
use App\Models\Contact\ContactMessage;
use App\Models\Patients\Patient;
use Illuminate\Support\Facades\Mail;

it('registra el mensaje y avisa a Patricia', function () {
    Mail::fake();

    $message = app(SubmitContactMessage::class)->execute(new CreateContactMessageData(
        name: 'Marta Ruiz',
        email: 'marta@example.com',
        phone: '600333444',
        contactPreference: ContactPreference::Whatsapp,
        preferredTime: 'Tardes (16–20h)',
        body: 'Me gustaría empezar terapia.',
    ));

    expect($message->name)->toBe('Marta Ruiz')
        ->and($message->isRead())->toBeFalse();

    Mail::assertQueued(ContactMessageReceived::class);
});

it('envía la respuesta por correo a quien prefiere el email', function () {
    Mail::fake();

    $message = ContactMessage::factory()->create([
        ContactMessage::CONTACT_PREFERENCE => ContactPreference::Email,
        ContactMessage::EMAIL => 'marta@example.com',
    ]);

    app(ReplyToContactMessage::class)->execute($message, 'Hablamos pronto.');

    Mail::assertQueued(
        ContactMessageReply::class,
        fn (ContactMessageReply $mail) => $mail->hasTo('marta@example.com'),
    );
});

it('no envía correo a quien prefiere WhatsApp o llamada', function (ContactPreference $preference) {
    Mail::fake();

    $message = ContactMessage::factory()->create([ContactMessage::CONTACT_PREFERENCE => $preference]);

    app(ReplyToContactMessage::class)->execute($message, 'Te llamo esta tarde.');

    Mail::assertNothingQueued();
})->with([ContactPreference::Whatsapp, ContactPreference::Call]);

it('deja constancia del medio por el que se respondió', function (ContactPreference $preference) {
    Mail::fake();

    $message = ContactMessage::factory()->create([ContactMessage::CONTACT_PREFERENCE => $preference]);

    app(ReplyToContactMessage::class)->execute($message, 'Respondido.');

    expect($message->refresh()->replied_at)->not->toBeNull()
        ->and($message->replied_via)->toBe($preference->value);
})->with([ContactPreference::Whatsapp, ContactPreference::Call, ContactPreference::Email]);

it('marca como leído sin reescribir la fecha si ya lo estaba', function () {
    $message = ContactMessage::factory()->create([ContactMessage::READ_AT => now()->subWeek()]);
    $original = $message->read_at;

    app(MarkMessageAsRead::class)->execute($message);

    expect($message->refresh()->read_at->toDateTimeString())->toBe($original->toDateTimeString());
});

it('crea la ficha de paciente desde un mensaje', function () {
    $message = ContactMessage::factory()->create([
        ContactMessage::NAME => 'Marta Ruiz',
        ContactMessage::EMAIL => 'marta@example.com',
    ]);

    app(LinkPatientToMessage::class)->execute($message);

    $patient = Patient::sole();

    expect($message->refresh()->patient_id)->toBe($patient->id)
        ->and($patient->first_name)->toBe('Marta')
        ->and($patient->last_name)->toBe('Ruiz');
});
