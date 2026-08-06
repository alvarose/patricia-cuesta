<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { homeSection, whatsappHref } from '@/lib/links';
import { legal } from '@/routes';

const page = usePage();
const clinic = computed(() => page.props.clinic);
const waHref = computed(() => whatsappHref(clinic.value.whatsapp));

const NAV = [
    { label: 'Inicio', anchor: 'inicio' },
    { label: 'Cómo puedo ayudarte', anchor: 'ayuda' },
    { label: 'Sobre mí', anchor: 'sobre-mi' },
    { label: 'Terapia online', anchor: 'online' },
    { label: '¿Hablamos?', anchor: 'contacto' },
];

const year = new Date().getFullYear();
</script>

<template>
    <footer class="footer">
        <div class="wrap">
            <div class="fcol fcol--brand">
                <a
                    class="logo"
                    :href="homeSection('inicio')"
                    aria-label="Patricia Cuesta Psicología"
                >
                    <img src="/images/logo-mark.svg" alt="" />
                    <span
                        style="
                            display: flex;
                            flex-direction: column;
                            line-height: 1;
                        "
                    >
                        <span class="n">Patricia Cuesta</span>
                        <span class="r">Psicología</span>
                    </span>
                </a>
                <p class="fblurb">
                    Psicóloga sanitaria y forense. Terapia online para
                    acompañarte en duelo, ansiedad, depresión y dificultades
                    emocionales.
                </p>
            </div>
            <div class="fcol">
                <h5>Navegación</h5>
                <ul>
                    <li v-for="item in NAV" :key="item.anchor">
                        <a :href="homeSection(item.anchor)">{{ item.label }}</a>
                    </li>
                </ul>
            </div>
            <div class="fcol">
                <h5>Contacto</h5>
                <ul>
                    <li v-if="waHref">
                        <a :href="waHref" rel="noopener">WhatsApp</a>
                    </li>
                    <li>
                        <a :href="`mailto:${clinic.email}`">{{
                            clinic.email
                        }}</a>
                    </li>
                    <li>
                        <p>Nº de colegiada: {{ clinic.license }}</p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="wrap footer-bottom">
            <p>© {{ year }} Patricia Cuesta Psicología</p>
            <div class="legal">
                <a :href="legal.url('aviso-legal')">Aviso legal</a>
                <a :href="legal.url('privacidad')">Privacidad</a>
                <a :href="legal.url('cookies')">Cookies</a>
            </div>
        </div>
    </footer>
</template>
