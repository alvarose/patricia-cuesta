<?php

namespace App\Domain\Patients\Patient\Enums;

use App\Domain\Core\Shared\Concerns\EnumHelpers;

enum ConsultationTopic: string
{
    use EnumHelpers;

    case Grief = 'duelo';
    case Anxiety = 'ansiedad';
    case Depression = 'depresion';
    case AddictiveBehaviour = 'conductas_adictivas';
    case FamilyMediation = 'mediacion_familiar';
    case DysfunctionalBonds = 'vinculos_disfuncionales';
    case Abuse = 'maltrato';
    case SelfKnowledge = 'autoconocimiento';

    public function label(): string
    {
        return match ($this) {
            self::Grief => 'Duelo',
            self::Anxiety => 'Ansiedad y estrés',
            self::Depression => 'Depresión',
            self::AddictiveBehaviour => 'Conductas adictivas',
            self::FamilyMediation => 'Mediación familiar',
            self::DysfunctionalBonds => 'Vínculos afectivos disfuncionales',
            self::Abuse => 'Maltrato',
            self::SelfKnowledge => 'Autoconocimiento',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Grief => 'Te acompaño en tu pérdida, a tu ritmo, en un espacio seguro y sin juicios.',
            self::Anxiety => 'Reconducir y dotar de herramientas para regular las prioridades.',
            self::Depression => 'Volver a conectar contigo y con lo que te importa, poco a poco.',
            self::AddictiveBehaviour => 'Comprender el origen y construir nuevas formas de cuidarte.',
            self::FamilyMediation => 'Espacios de diálogo para dinámicas y conflictos familiares.',
            self::DysfunctionalBonds => 'Identificar patrones y recuperar tu bienestar y tus límites.',
            self::Abuse => 'Un lugar seguro para abordar lo vivido.',
            self::SelfKnowledge => 'Conocerte mejor y regular tus emociones con más libertad.',
        };
    }

    /** @return array<string> */
    public static function matching(string $term): array
    {
        $needle = mb_strtolower(trim($term));

        if ($needle === '') {
            return [];
        }

        return array_values(array_map(
            fn (self $topic): string => $topic->value,
            array_filter(
                self::cases(),
                fn (self $topic): bool => str_contains(mb_strtolower($topic->label()), $needle),
            ),
        ));
    }

    public static function labelOrDefault(?self $topic): string
    {
        return $topic?->label() ?? 'Sin especificar';
    }

    /** @return array<array{value: string, label: string, description: string}> */
    public static function options(): array
    {
        return array_map(fn (self $topic) => [
            'value' => $topic->value,
            'label' => $topic->label(),
            'description' => $topic->description(),
        ], self::cases());
    }
}
