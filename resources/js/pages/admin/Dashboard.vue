<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { index as appointmentsIndex } from '@/routes/admin/appointments';
import { index as availabilityIndex } from '@/routes/admin/availability';
import { index as messagesIndex } from '@/routes/admin/messages';
import type { Appointment, ContactMessageListItem, Stat } from '@/types';

defineProps<{
    weekSummary: string;
    stats: Stat[];
    todayAppointments: Appointment[];
    recentMessages: ContactMessageListItem[];
}>();
</script>

<template>
    <Head title="Resumen" />

    <div class="banner">
        <h2>Esta semana</h2>
        <p>{{ weekSummary }}</p>
        <Link
            :href="availabilityIndex()"
            class="card-link"
            style="align-self: flex-start"
        >
            Revisar disponibilidad →
        </Link>
    </div>

    <div class="stats">
        <div v-for="stat in stats" :key="stat.label" class="stat">
            <span class="lb">{{ stat.label }}</span>
            <span class="vl">{{ stat.value }}</span>
            <span class="nt">{{ stat.note }}</span>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-head">
                <h2 class="card-title">
                    Sesiones de <em class="emph">hoy</em>
                </h2>
                <Link :href="appointmentsIndex()" class="card-link"
                    >Ver agenda →</Link
                >
            </div>
            <div style="display: flex; flex-direction: column">
                <div
                    v-for="appointment in todayAppointments"
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
                </div>
                <div v-if="todayAppointments.length === 0" class="empty">
                    <h3>Día sin sesiones</h3>
                    <p>
                        Un buen día para respirar, o para abrir nuevas franjas.
                    </p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card" style="padding: 24px 26px">
                <div class="card-head">
                    <h2 class="card-title-sm">Mensajes recientes</h2>
                    <Link :href="messagesIndex()" class="card-link"
                        >Ver todos →</Link
                    >
                </div>
                <div style="display: flex; flex-direction: column">
                    <div
                        v-for="message in recentMessages"
                        :key="message.id"
                        class="row"
                        style="align-items: flex-start; padding: 11px 0"
                    >
                        <span
                            class="dt"
                            :class="message.unread ? 'dt--live' : 'dt--idle'"
                            style="margin-top: 6px"
                        ></span>
                        <span class="who">
                            <span
                                class="tp-row"
                                style="
                                    display: flex;
                                    justify-content: space-between;
                                    gap: 8px;
                                "
                            >
                                <span class="nm" style="font-size: 13.5px">{{
                                    message.name
                                }}</span>
                                <span
                                    style="
                                        font-size: 11.5px;
                                        color: var(--color-ink-faint);
                                        flex: none;
                                    "
                                >
                                    {{ message.when }}
                                </span>
                            </span>
                            <span
                                class="tp"
                                style="
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                    white-space: nowrap;
                                "
                            >
                                {{ message.preview }}
                            </span>
                            <span
                                style="
                                    font-size: 11.5px;
                                    color: var(--color-ink-faint);
                                "
                            >
                                Prefiere: {{ message.preference }}
                            </span>
                        </span>
                    </div>
                    <p v-if="recentMessages.length === 0" class="muted-note">
                        Sin mensajes todavía.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
