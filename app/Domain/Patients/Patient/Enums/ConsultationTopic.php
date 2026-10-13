<?php

namespace App\Domain\Patients\Patient\Enums;

use App\Domain\Shared\Concerns\EnumHelpers;

enum ConsultationTopic: string
{
    use EnumHelpers;

    case Duelo = 'duelo';
    case Ansiedad = 'ansiedad';
    case Depresion = 'depresion';
    case ConductasAdictivas = 'conductas_adictivas';
    case MediacionFamiliar = 'mediacion_familiar';
    case VinculosDisfuncionales = 'vinculos_disfuncionales';
    case Maltrato = 'maltrato';
    case Autoconocimiento = 'autoconocimiento';

    public function label(): string
    {
        return match ($this) {
            self::Duelo => 'Duelo',
            self::Ansiedad => 'Ansiedad y estrés',
            self::Depresion => 'Depresión',
            self::ConductasAdictivas => 'Conductas adictivas',
            self::MediacionFamiliar => 'Mediación familiar',
            self::VinculosDisfuncionales => 'Vínculos afectivos disfuncionales',
            self::Maltrato => 'Maltrato',
            self::Autoconocimiento => 'Autoconocimiento',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Duelo => 'Te acompaño en tu pérdida, a tu ritmo, en un espacio seguro y sin juicios.',
            self::Ansiedad => 'Reconducir y dotar de herramientas para regular las prioridades.',
            self::Depresion => 'Volver a conectar contigo y con lo que te importa, poco a poco.',
            self::ConductasAdictivas => 'Comprender el origen y construir nuevas formas de cuidarte.',
            self::MediacionFamiliar => 'Espacios de diálogo para dinámicas y conflictos familiares.',
            self::VinculosDisfuncionales => 'Identificar patrones y recuperar tu bienestar y tus límites.',
            self::Maltrato => 'Un lugar seguro para abordar lo vivido.',
            self::Autoconocimiento => 'Conocerte mejor y regular tus emociones con más libertad.',
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
