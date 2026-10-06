<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    index as appointmentsIndex,
    status as appointmentStatus,
} from '@/routes/admin/appointments';
import type {
    AppointmentWithContact,
    MonthCalendar,
    MonthDayCell,
    PendingAppointment,
} from '@/types';

const DOW = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];

const props = defineProps<{
    date: string;
    month: MonthCalendar;
    appointments: AppointmentWithContact[];
    pending: PendingAppointment[];
}>();

const dayTitle = computed(() => {
    const date = new Date(`${props.date}T00:00:00`);

    return date.toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    });
});

const pickDay = (date: string) => {
    router.get(
        appointmentsIndex.url({ query: { date } }),
        {},
        {
            preserveScroll: true,
            only: ['date', 'month', 'appointments'],
        },
    );
};

const cellLabel = (day: MonthDayCell) => {
    const when = new Date(`${day.date}T00:00:00`).toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'long',
    });

    if (day.absent) {
        return `${when}, ausencia`;
    }

    if (day.sessions > 0) {
        return `${when}, ${day.sessions} ${day.sessions === 1 ? 'sesión' : 'sesiones'}`;
    }

    return day.closed ? `${when}, cerrado` : when;
};

const setStatus = (id: number, status: string) => {
    router.patch(
        appointmentStatus.url(id),
        { status },
        { preserveScroll: true },
    );
};
</script>

<template>
    <Head title="Citas" />

    <div class="grid-2">
        <div class="card">
            <div class="card-head">
                <h2 class="card-title" style="text-transform: capitalize">
                    {{ dayTitle }}
                </h2>
                <span style="font-size: 12.5px; color: var(--color-ink-faint)">
                    {{ appointments.length }}
                    {{ appointments.length === 1 ? 'sesión' : 'sesiones' }}
                </span>
            </div>
            <div style="display: flex; flex-direction: column">
                <div
                    v-for="appointment in appointments"
                    :key="appointment.id"
                    class="row"
                >
                    <span class="tm">{{ appointment.time }}</span>
                    <span class="dt" :class="appointment.statusDot"></span>
                    <span class="who">
                        <span class="nm">{{ appointment.name }}</span>
                        <span class="tp">{{ appointment.topic }}</span>
                    </span>
                    <span class="chip" :class="appointment.statusBadge">
                        {{ appointment.statusLabel }}
                    </span>
                    <button
                        v-if="appointment.status === 'pending'"
                        class="a-btn"
                        @click="setStatus(appointment.id, 'confirmed')"
                    >
                        Confirmar
                    </button>
                    <button
                        v-else-if="appointment.status === 'confirmed'"
                        class="a-btn a-btn--ghost"
                        @click="setStatus(appointment.id, 'completed')"
                    >
                        Completar
                    </button>
                </div>
                <div v-if="appointments.length === 0" class="empty">
                    <h3>Día sin sesiones</h3>
                    <p>
                        Un buen día para respirar, o para abrir nuevas franjas.
                    </p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card mcal">
                <div class="mcal-head">
                    <span class="mcal-label">{{ month.label }}</span>
                    <div class="mcal-nav">
                        <button
                            class="mcal-btn mcal-btn--today"
                            :disabled="month.isCurrentMonth"
                            @click="pickDay(month.today)"
                        >
                            Hoy
                        </button>
                        <button
                            class="mcal-btn"
                            aria-label="Mes anterior"
                            @click="pickDay(month.prev)"
                        >
                            ‹
                        </button>
                        <button
                            class="mcal-btn"
                            aria-label="Mes siguiente"
                            @click="pickDay(month.next)"
                        >
                            ›
                        </button>
                    </div>
                </div>
                <div class="mcal-dow">
                    <span v-for="(name, index) in DOW" :key="index">{{
                        name
                    }}</span>
                </div>
                <div class="mcal-grid">
                    <button
                        v-for="day in month.days"
                        :key="day.date"
                        class="mcal-cell"
                        :class="{
                            'mcal-cell--out': !day.inMonth,
                            'mcal-cell--closed': day.closed,
                            'mcal-cell--absent': day.absent,
                            'mcal-cell--today': day.today,
                            'mcal-cell--on': day.date === date,
                        }"
                        :aria-label="cellLabel(day)"
                        :aria-current="day.today ? 'date' : undefined"
                        :aria-pressed="day.date === date"
                        @click="pickDay(day.date)"
                    >
                        <span class="nu">{{ day.num }}</span>
                        <span class="ct">{{
                            day.sessions > 0 ? day.sessions : ' '
                        }}</span>
                    </button>
                </div>
            </div>

            <div class="card" style="padding: 24px 26px">
                <h2 class="card-title-sm" style="margin-bottom: 4px">
                    Por confirmar
                </h2>
                <p
                    style="
                        margin: 0 0 10px;
                        font-size: 12.5px;
                        color: var(--color-ink-soft);
                    "
                >
                    Solicitudes llegadas desde la web.
                </p>
                <div style="display: flex; flex-direction: column">
                    <div
                        v-for="item in pending"
                        :key="item.id"
                        style="
                            display: flex;
                            flex-direction: column;
                            gap: 9px;
                            padding: 13px 0;
                            border-top: 1px solid #f0e9dc;
                        "
                    >
                        <span
                            style="
                                display: flex;
                                flex-direction: column;
                                line-height: 1.35;
                            "
                        >
                            <span style="font-size: 13.5px; font-weight: 700">{{
                                item.name
                            }}</span>
                            <span
                                style="
                                    font-size: 12.5px;
                                    color: var(--color-ink-soft);
                                "
                            >
                                {{ item.when }} · {{ item.topic }}
                            </span>
                        </span>
                        <span style="display: flex; gap: 8px">
                            <button
                                class="a-btn"
                                @click="setStatus(item.id, 'confirmed')"
                            >
                                Confirmar
                            </button>
                            <button
                                class="a-btn a-btn--ghost"
                                @click="setStatus(item.id, 'cancelled')"
                            >
                                Rechazar
                            </button>
                        </span>
                    </div>
                    <p v-if="pending.length === 0" class="muted-note">
                        Nada pendiente. Todo confirmado.
                    </p>
                </div>
            </div>

            <div class="banner" style="padding: 20px 24px">
                <p>
                    Al confirmar una cita, la persona recibe un email con el día
                    y la hora. Si la rechazas, el hueco vuelve a quedar libre en
                    la web.
                </p>
            </div>
        </div>
    </div>
</template>
