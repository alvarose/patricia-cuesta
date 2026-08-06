<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { hrefUrl } from '@/lib/links';
import { home, logout } from '@/routes';
import { dashboard } from '@/routes/admin';
import { index as appointmentsIndex } from '@/routes/admin/appointments';
import { index as availabilityIndex } from '@/routes/admin/availability';
import { index as messagesIndex } from '@/routes/admin/messages';
import { index as patientsIndex } from '@/routes/admin/patients';
import { edit as settingsEdit } from '@/routes/admin/settings';
import type { AdminNavItem } from '@/types';

const page = usePage();

const counts = computed(() => page.props.adminCounts);
const user = computed(() => page.props.auth.user);

const NAV = computed<AdminNavItem[]>(() => [
    {
        label: 'Resumen',
        href: dashboard(),
        eyebrow: 'Panel',
        heading: 'Buenos días, Patricia',
    },
    {
        label: 'Citas',
        href: appointmentsIndex(),
        eyebrow: 'Agenda',
        heading: 'Citas',
        badge: counts.value?.pendingAppointments || undefined,
    },
    {
        label: 'Pacientes',
        href: patientsIndex(),
        eyebrow: 'Acompañamientos',
        heading: 'Pacientes',
    },
    {
        label: 'Mensajes',
        href: messagesIndex(),
        eyebrow: 'Contacto',
        heading: 'Mensajes',
        badge: counts.value?.unreadMessages || undefined,
    },
    {
        label: 'Disponibilidad',
        href: availabilityIndex(),
        eyebrow: 'Agenda',
        heading: 'Disponibilidad',
    },
    {
        label: 'Ajustes',
        href: settingsEdit(),
        eyebrow: 'Configuración',
        heading: 'Ajustes',
    },
]);

const current = computed(() => {
    const path = page.url.split('?')[0];
    const dashboardPath = hrefUrl(dashboard());

    return (
        NAV.value.find((item) => hrefUrl(item.href) === path) ??
        NAV.value.find((item) => {
            const url = hrefUrl(item.href);

            return url !== dashboardPath && path.startsWith(url);
        }) ??
        NAV.value[0]
    );
});

const todayLabel = new Date().toLocaleDateString('es-ES', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
});
</script>

<template>
    <div class="pc-admin">
        <aside class="sb">
            <a class="sb-logo" :href="home.url()">
                <img src="/images/logo-mark.svg" alt="" />
                <span style="display: flex; flex-direction: column">
                    <span class="n">Patricia Cuesta</span>
                    <span class="r">Admin</span>
                </span>
            </a>

            <nav class="sb-nav">
                <Link
                    v-for="item in NAV"
                    :key="item.label"
                    class="sb-item"
                    :class="{ on: current.label === item.label }"
                    :href="item.href"
                >
                    <span class="dot"></span>
                    <span class="lbl">{{ item.label }}</span>
                    <span v-if="item.badge" class="sb-badge">{{
                        item.badge
                    }}</span>
                </Link>
            </nav>

            <a class="sb-web" :href="home.url()" target="_blank"
                >Ver la web →</a
            >
            <div class="sb-user">
                <img src="/images/patricia.png" alt="" />
                <span
                    style="display: flex; flex-direction: column; min-width: 0"
                >
                    <span class="nm">{{ user.name }}</span>
                    <span class="em">{{ user.email }}</span>
                    <button
                        class="sb-logout"
                        @click="router.post(logout.url())"
                    >
                        Cerrar sesión
                    </button>
                </span>
            </div>
        </aside>

        <main class="main">
            <header class="hd">
                <div style="display: flex; flex-direction: column; gap: 2px">
                    <span class="hd-eyebrow">{{ current.eyebrow }}</span>
                    <h1 class="hd-title">{{ current.heading }}</h1>
                </div>
                <span class="hd-date">{{ todayLabel }}</span>
            </header>

            <section class="content">
                <slot />
            </section>
        </main>
    </div>
</template>
