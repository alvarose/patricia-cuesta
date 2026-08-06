<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { appointmentChipClass } from '@/lib/chips';
import {
    index as appointmentsIndex,
    status as appointmentStatus,
} from '@/routes/admin/appointments';
import type {
    AppointmentWithContact,
    PendingAppointment,
    WeekDayCell,
} from '@/types';

const props = defineProps<{
    date: string;
    week: WeekDayCell[];
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

const weekTitle = computed(() => {
    const first = new Date(`${props.week[0].date}T00:00:00`);
    const last = new Date(`${props.week[6].date}T00:00:00`);
    const month = last.toLocaleDateString('es-ES', { month: 'long' });

    return `Semana del ${first.getDate()} al ${last.getDate()} de ${month}`;
});

const pickDay = (date: string) => {
    router.get(
        appointmentsIndex.url({ query: { date } }),
        {},
        { preserveScroll: true },
    );
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

    <div class="toolbar">
        <span class="week-title">{{ weekTitle }}</span>
        <span class="sp"></span>
        <div class="week">
            <button
                v-for="day in week"
                :key="day.date"
                class="wd"
                :class="{ on: day.date === date, has: day.hasAppointments }"
                @click="pickDay(day.date)"
            >
                <span class="dw">{{ day.dow }}</span>
                <span class="nu">{{ day.num }}</span>
                <span class="pt"></span>
            </button>
        </div>
    </div>

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
                    <span
                        class="dt"
                        :style="{
                            background:
                                appointment.status === 'confirmed'
                                    ? 'var(--color-butter-deep)'
                                    : 'var(--color-sand)',
                        }"
                    ></span>
                    <span class="who">
                        <span class="nm">{{ appointment.name }}</span>
                        <span class="tp">{{ appointment.topic }}</span>
                    </span>
                    <span
                        class="chip"
                        :class="appointmentChipClass(appointment.status)"
                    >
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
