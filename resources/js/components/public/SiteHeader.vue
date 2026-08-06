<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import Icon from '@/components/public/Icon.vue';
import { homeSection } from '@/lib/links';

const NAV = [
    { label: 'Inicio', anchor: 'inicio' },
    { label: 'Cómo puedo ayudarte', anchor: 'ayuda' },
    { label: 'Sobre mí', anchor: 'sobre-mi' },
    { label: 'Terapia online', anchor: 'online' },
    { label: '¿Hablamos?', anchor: 'contacto' },
];

const scrolled = ref(false);
const open = ref(false);

const onScroll = () => {
    scrolled.value = window.scrollY > 12;
};

onMounted(() => {
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', onScroll);
    document.body.style.overflow = '';
});

watch(open, (value) => {
    document.body.style.overflow = value ? 'hidden' : '';
});
</script>

<template>
    <header class="hdr" :class="{ scrolled }">
        <div class="wrap hdr-in">
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
            <nav class="nav">
                <a
                    v-for="item in NAV"
                    :key="item.anchor"
                    :href="homeSection(item.anchor)"
                    >{{ item.label }}</a
                >
            </nav>
            <div class="hdr-cta">
                <a class="btn btn--primary" :href="homeSection('contacto')"
                    >Pedir cita</a
                >
                <button
                    class="burger"
                    aria-label="Abrir menú"
                    @click="open = true"
                >
                    <Icon name="menu" :size="26" />
                </button>
            </div>
        </div>

        <div class="msheet" :class="{ open }">
            <div class="top">
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
                <button
                    class="burger"
                    aria-label="Cerrar menú"
                    @click="open = false"
                >
                    <Icon name="x" :size="26" />
                </button>
            </div>
            <nav>
                <a
                    v-for="item in NAV"
                    :key="item.anchor"
                    :href="homeSection(item.anchor)"
                    @click="open = false"
                >
                    {{ item.label }}
                </a>
            </nav>
            <a
                class="btn btn--primary"
                :href="homeSection('contacto')"
                @click="open = false"
                >Pedir cita</a
            >
        </div>
    </header>
</template>
