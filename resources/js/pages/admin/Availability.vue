<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import {
    destroy as destroyAbsence,
    store as storeAbsence,
} from '@/routes/admin/absences';
import { update as updateAvailability } from '@/routes/admin/availability';
import type {
    AbsenceItem,
    AvailabilityDay,
    BookingSettingsPayload,
    Stat,
} from '@/types';

const props = defineProps<{
    days: AvailabilityDay[];
    settings: BookingSettingsPayload;
    absences: AbsenceItem[];
    stats: Stat[];
}>();

const form = useForm({
    days: props.days.map((day) => ({
        weekday: day.weekday,
        enabled: day.enabled,
        windows: day.windows.map((window) => ({ ...window })),
    })),
    settings: { ...props.settings },
});

const editing = reactive<{
    day: number | null;
    index: number | null;
    start: string;
    end: string;
}>({
    day: null,
    index: null,
    start: '',
    end: '',
});

const startEdit = (dayIndex: number, windowIndex: number) => {
    const window = form.days[dayIndex].windows[windowIndex];
    editing.day = dayIndex;
    editing.index = windowIndex;
    editing.start = window.start;
    editing.end = window.end;
};

const saveEdit = () => {
    if (editing.day === null || editing.index === null) {
        return;
    }

    if (editing.start && editing.end && editing.start < editing.end) {
        form.days[editing.day].windows[editing.index] = {
            start: editing.start,
            end: editing.end,
        };
    }

    editing.day = null;
    editing.index = null;
};

const cancelEdit = () => {
    editing.day = null;
    editing.index = null;
};

const addWindow = (dayIndex: number) => {
    const windows = form.days[dayIndex].windows;
    windows.push(
        windows.length === 0
            ? { start: '10:00', end: '14:00' }
            : { start: '16:00', end: '20:00' },
    );
    startEdit(dayIndex, windows.length - 1);
};

const removeWindow = (dayIndex: number, windowIndex: number) => {
    form.days[dayIndex].windows.splice(windowIndex, 1);
};

const OPTIONS = {
    session_minutes: [30, 45, 50, 60],
    buffer_minutes: [0, 10, 15, 30],
    min_notice_hours: [0, 12, 24, 48],
    max_advance_days: [30, 60, 90],
} as const;

const OPTION_LABELS: Record<string, string> = {
    session_minutes: 'Duración de la sesión',
    buffer_minutes: 'Descanso entre sesiones',
    min_notice_hours: 'Antelación mínima',
    max_advance_days: 'Reservas hasta',
};

const optionLabel = (key: string, value: number) =>
    ({
        session_minutes: `${value} min`,
        buffer_minutes: value === 0 ? 'Sin pausa' : `${value} min`,
        min_notice_hours: value === 0 ? 'Sin mínimo' : `${value} h`,
        max_advance_days: `${value} días`,
    })[key] ?? String(value);

const saved = ref(false);

const save = () => {
    form.put(updateAvailability.url(), {
        preserveScroll: true,
        onSuccess: () => {
            saved.value = true;
            setTimeout(() => (saved.value = false), 2500);
        },
    });
};

const absenceForm = useForm({ starts_on: '', ends_on: '', note: '' });
const addingAbsence = ref(false);

const submitAbsence = () => {
    if (!absenceForm.ends_on) {
        absenceForm.ends_on = absenceForm.starts_on;
    }

    absenceForm.post(storeAbsence.url(), {
        preserveScroll: true,
        onSuccess: () => {
            absenceForm.reset();
            addingAbsence.value = false;
        },
    });
};

const removeAbsence = (id: number) => {
    router.delete(destroyAbsence.url(id), { preserveScroll: true });
};
</script>

<template>
    <Head title="Disponibilidad" />

    <div class="stats">
        <div v-for="stat in stats" :key="stat.label" class="stat">
            <span class="lb">{{ stat.label }}</span>
            <span class="vl">{{ stat.value }}</span>
            <span class="nt">{{ stat.note }}</span>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-head" style="margin-bottom: 6px">
                <h2 class="card-title">
                    Horario <em class="emph">semanal</em>
                </h2>
                <span v-if="saved" class="sent-ok">✓ Guardado</span>
            </div>
            <p
                style="
                    margin: 0 0 14px;
                    font-size: 13px;
                    color: var(--color-ink-soft);
                "
            >
                Estas franjas son las que se ofrecen en el calendario de
                reservas de la web.
            </p>
            <p
                v-if="form.errors.days"
                class="err"
                style="color: #b3452f; font-size: 13px; margin: 0 0 10px"
            >
                {{ form.errors.days }}
            </p>
            <div style="display: flex; flex-direction: column">
                <div
                    v-for="(day, dayIndex) in form.days"
                    :key="day.weekday"
                    class="day-row"
                >
                    <button
                        class="tg"
                        :class="{ on: day.enabled }"
                        :aria-label="`Activar ${props.days[dayIndex].label}`"
                        @click="day.enabled = !day.enabled"
                    >
                        <span class="knob"></span>
                    </button>
                    <span class="dnm" :class="{ off: !day.enabled }">
                        {{ props.days[dayIndex].label }}
                    </span>
                    <div class="slots">
                        <template v-if="day.enabled">
                            <template
                                v-for="(window, windowIndex) in day.windows"
                                :key="windowIndex"
                            >
                                <span
                                    v-if="
                                        editing.day !== dayIndex ||
                                        editing.index !== windowIndex
                                    "
                                    class="slot-chip"
                                >
                                    <button
                                        title="Editar franja"
                                        @click="
                                            startEdit(dayIndex, windowIndex)
                                        "
                                    >
                                        {{ window.start }} – {{ window.end }}
                                    </button>
                                    <button
                                        class="x"
                                        aria-label="Quitar franja"
                                        @click="
                                            removeWindow(dayIndex, windowIndex)
                                        "
                                    >
                                        ×
                                    </button>
                                </span>
                                <span v-else class="slot-edit">
                                    <input
                                        v-model="editing.start"
                                        type="time"
                                    />
                                    <span
                                        style="
                                            font-size: 12px;
                                            color: var(--color-ink-faint);
                                        "
                                        >–</span
                                    >
                                    <input v-model="editing.end" type="time" />
                                    <button
                                        class="ok"
                                        aria-label="Guardar franja"
                                        @click="saveEdit"
                                    >
                                        ✓
                                    </button>
                                    <button
                                        class="no"
                                        aria-label="Cancelar"
                                        @click="cancelEdit"
                                    >
                                        ×
                                    </button>
                                </span>
                            </template>
                            <button
                                class="a-btn a-btn--dashed"
                                @click="addWindow(dayIndex)"
                            >
                                + Añadir franja
                            </button>
                        </template>
                        <span v-else class="rest">Descanso</span>
                    </div>
                </div>
            </div>
            <div style="margin-top: 18px">
                <button
                    class="a-btn a-btn--lg"
                    :disabled="form.processing"
                    @click="save"
                >
                    Guardar cambios
                </button>
            </div>
        </div>

        <div class="col">
            <div
                class="card"
                style="
                    padding: 24px 26px;
                    display: flex;
                    flex-direction: column;
                    gap: 18px;
                "
            >
                <h2 class="card-title-sm">Sesiones</h2>
                <div
                    v-for="(options, key) in OPTIONS"
                    :key="key"
                    class="opt-group"
                >
                    <span class="lb">{{ OPTION_LABELS[key] }}</span>
                    <div class="opts">
                        <button
                            v-for="option in options"
                            :key="option"
                            class="fpill"
                            :class="{ on: form.settings[key] === option }"
                            @click="form.settings[key] = option"
                        >
                            {{ optionLabel(key, option) }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="card" style="padding: 24px 26px">
                <h2 class="card-title-sm" style="margin-bottom: 4px">
                    Ausencias
                </h2>
                <p
                    style="
                        margin: 0 0 12px;
                        font-size: 12.5px;
                        color: var(--color-ink-soft);
                    "
                >
                    Días en los que no se ofrecerán citas.
                </p>
                <div style="display: flex; flex-direction: column">
                    <div
                        v-for="absence in absences"
                        :key="absence.id"
                        style="
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            padding: 10px 0;
                            border-top: 1px solid #f0e9dc;
                        "
                    >
                        <span
                            style="
                                width: 7px;
                                height: 7px;
                                border-radius: 50%;
                                background: var(--color-sand);
                                flex: none;
                            "
                        ></span>
                        <span
                            style="
                                flex: 1;
                                min-width: 0;
                                display: flex;
                                flex-direction: column;
                                line-height: 1.35;
                            "
                        >
                            <span style="font-size: 13.5px; font-weight: 700">{{
                                absence.range
                            }}</span>
                            <span
                                v-if="absence.note"
                                style="
                                    font-size: 12px;
                                    color: var(--color-ink-soft);
                                "
                            >
                                {{ absence.note }}
                            </span>
                        </span>
                        <button
                            class="no"
                            aria-label="Quitar ausencia"
                            style="
                                border: none;
                                background: transparent;
                                color: var(--color-ink-faint);
                                font-size: 15px;
                                padding: 2px 6px;
                            "
                            @click="removeAbsence(absence.id)"
                        >
                            ×
                        </button>
                    </div>
                </div>

                <div
                    v-if="addingAbsence"
                    style="
                        display: flex;
                        flex-direction: column;
                        gap: 10px;
                        margin-top: 12px;
                    "
                >
                    <div class="frm-grid">
                        <div class="fld">
                            <label>Desde</label>
                            <input
                                v-model="absenceForm.starts_on"
                                type="date"
                            />
                        </div>
                        <div class="fld">
                            <label>Hasta</label>
                            <input v-model="absenceForm.ends_on" type="date" />
                        </div>
                    </div>
                    <div class="fld">
                        <label>Nota</label>
                        <input
                            v-model="absenceForm.note"
                            type="text"
                            placeholder="Vacaciones, formación…"
                        />
                    </div>
                    <div style="display: flex; gap: 8px">
                        <button
                            class="a-btn"
                            :disabled="
                                absenceForm.processing || !absenceForm.starts_on
                            "
                            @click="submitAbsence"
                        >
                            Añadir
                        </button>
                        <button
                            class="a-btn a-btn--ghost"
                            @click="addingAbsence = false"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
                <button
                    v-else
                    class="a-btn a-btn--dashed"
                    style="margin-top: 12px; width: 100%; padding: 9px 14px"
                    @click="addingAbsence = true"
                >
                    + Añadir ausencia
                </button>
            </div>
        </div>
    </div>
</template>
