<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import BookingSection from '@/components/public/BookingSection.vue';
import ContactModal from '@/components/public/ContactModal.vue';
import Icon from '@/components/public/Icon.vue';
import { useReveal } from '@/composables/useReveal';
import { whatsappHref } from '@/lib/links';
import type { TopicOption } from '@/types';

const props = defineProps<{
    topics: TopicOption[];
    sessionMinutes: number;
}>();

useReveal();

const page = usePage();
const mailOpen = ref(false);

const waHref = computed(() => whatsappHref(page.props.clinic.whatsapp));

const CREDS = [
    'Duelo complicado',
    'Mediación familiar',
    'Relaciones tóxicas',
    'Maltrato',
    'Ansiedad y depresión',
    'Conductas adictivas',
];

const FEATURES = [
    { icon: 'monitor', text: 'Desde donde estés, en tu propio entorno.' },
    { icon: 'lock', text: 'Un espacio seguro, privado y confidencial.' },
    { icon: 'clock', text: 'Horarios flexibles que se adaptan a tu día.' },
] as const;

const STEPS = [
    {
        n: '1',
        title: 'Me escribes',
        text: 'Cuéntame qué te ocurre por WhatsApp o email, sin compromiso.',
    },
    {
        n: '2',
        title: 'Valoramos tu situación',
        text: 'Vemos juntas cómo puedo acompañarte y resolvemos tus dudas.',
    },
    {
        n: '3',
        title: 'Iniciamos el proceso',
        text: 'Empezamos la terapia online y avanzamos a tu propio ritmo.',
    },
];
</script>

<template>
    <Head title="Psicóloga sanitaria y forense · Terapia online">
        <meta
            name="description"
            content="Soy Patricia Cuesta, psicóloga sanitaria y forense. Terapia online para acompañarte en duelo, ansiedad, depresión y dificultades emocionales. Deshaciendo el nudo, paso a paso."
        />
    </Head>

    <!-- Hero -->
    <section id="inicio" class="hero">
        <div class="wrap hero-grid">
            <div class="hero-copy reveal stagger">
                <p class="eyebrow" style="--i: 0">
                    Psicóloga sanitaria y forense · Terapia online
                </p>
                <h1 class="h-display" style="--i: 1">
                    Deshaciendo <em class="emph">el nudo</em>,<br />
                    <span class="thread">paso a paso</span>
                </h1>
                <p class="lead hero-sub" style="--i: 2">
                    Soy Patricia Cuesta, psicóloga sanitaria y forense. Te
                    acompaño en procesos de duelo, ansiedad, depresión, estrés y
                    dificultades emocionales desde un espacio seguro, cercano y
                    profesional.
                </p>
                <div class="hero-cta" style="--i: 3">
                    <a class="btn btn--primary" href="#contacto"
                        >Empezar terapia online</a
                    >
                    <a class="btn btn--secondary" href="#ayuda"
                        >Conoce cómo puedo ayudarte</a
                    >
                </div>
            </div>

            <div class="hero-figure reveal">
                <div class="blob-bg b1"></div>
                <div class="blob-bg b2"></div>
                <div class="hero-photo">
                    <img
                        class="photo-img"
                        src="/images/patricia.png"
                        alt="Patricia Cuesta"
                    />
                </div>
                <div class="hero-badge">
                    <span class="ic"><Icon name="heart" :size="18" /></span>
                    <p>
                        <b>Acompañamiento</b> a tu ritmo, sin prisa y con
                        respeto.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sobre mí -->
    <section id="sobre-mi" class="section sand">
        <div class="wrap about-grid">
            <div class="about-photo reveal">
                <img
                    class="photo-img"
                    src="/images/patricia.png"
                    alt="Patricia Cuesta"
                    style="object-position: 50% 20%"
                />
                <span class="dot"></span>
            </div>
            <div class="about-text reveal">
                <p class="eyebrow">Sobre mí</p>
                <h2 class="h-sec">Soy <em class="emph">Patricia Cuesta</em></h2>
                <div class="prose" style="margin-top: 24px">
                    <p class="body">
                        Soy psicóloga sanitaria y forense, especializada en el
                        acompañamiento de personas que atraviesan momentos
                        difíciles.
                    </p>
                    <p class="body" style="margin-top: 18px">
                        Cuento con formación específica en duelo complicado,
                        mediación familiar, relaciones tóxicas, maltrato,
                        ansiedad, depresión, estrés y conductas adictivas.
                    </p>
                    <p class="body" style="margin-top: 18px">
                        Mi forma de trabajar parte de la escucha, la comprensión
                        y el respeto por el ritmo de cada persona.
                    </p>
                </div>
                <div class="creds reveal stagger">
                    <span
                        v-for="(cred, index) in CREDS"
                        :key="cred"
                        class="chip"
                        :style="{ '--i': index }"
                    >
                        {{ cred }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Cómo puedo ayudarte -->
    <section id="ayuda" class="section">
        <div class="wrap">
            <div class="sec-head center reveal">
                <p class="eyebrow">En qué trabajo contigo</p>
                <h2 class="h-sec">
                    Cómo puedo <span class="thread">ayudarte</span>
                </h2>
                <p class="lead help-intro">
                    Si estás pasando por dificultades emocionales, vivir contigo
                    este proceso mientras conseguimos que obtengas las
                    herramientas necesarias para combatirlas es sin duda mi
                    objetivo; también para quienes quieran conocerse, entenderse
                    más y enfrentarse a sus miedos.
                </p>
                <p class="help-claim">
                    El objetivo no es solo dejar de sufrir, sino ser funcionales
                    <span class="thread">«deshaciendo el nudo»</span>.
                </p>
            </div>
            <div class="help-grid reveal stagger">
                <article
                    v-for="(topic, index) in props.topics"
                    :key="topic.value"
                    class="topic"
                    :style="{ '--i': index }"
                >
                    <span class="mk">
                        <i
                            :style="
                                index % 4 === 2
                                    ? { background: 'var(--sage-deep)' }
                                    : undefined
                            "
                        ></i>
                    </span>
                    <h3>{{ topic.label }}</h3>
                    <p>{{ topic.description }}</p>
                    <span
                        class="corner"
                        :style="
                            index % 4 === 2
                                ? { background: 'var(--sage)' }
                                : undefined
                        "
                    ></span>
                </article>
            </div>
        </div>
    </section>

    <!-- Terapia online -->
    <section id="online" class="section tinted">
        <div class="wrap online-grid">
            <div class="online-photo reveal">
                <div class="photo-ph">
                    <span>Espacio de terapia online</span>
                </div>
            </div>
            <div class="reveal">
                <p class="eyebrow">Terapia online</p>
                <h2 class="h-sec">
                    Terapia online, <em class="emph">estés donde estés</em>
                </h2>
                <div class="prose" style="margin-top: 22px">
                    <p class="body">
                        Realizo terapia online para que puedas iniciar tu
                        proceso desde un entorno cómodo, seguro y familiar para
                        ti.
                    </p>
                    <p class="body" style="margin-top: 16px">
                        Solo necesitas un espacio tranquilo, conexión a internet
                        y un momento para ti.
                    </p>
                </div>
                <div class="feature-list stagger reveal">
                    <div
                        v-for="(feature, index) in FEATURES"
                        :key="feature.icon"
                        class="feature"
                        :style="{ '--i': index }"
                    >
                        <span class="ic"
                            ><Icon :name="feature.icon" :size="17"
                        /></span>
                        <p>{{ feature.text }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cómo empezamos -->
    <section class="section">
        <div class="wrap">
            <div class="sec-head reveal">
                <p class="eyebrow">Cómo empezamos</p>
                <h2 class="h-sec">Empezar es más sencillo de lo que parece</h2>
            </div>
            <div class="steps reveal stagger">
                <div
                    v-for="(step, index) in STEPS"
                    :key="step.n"
                    class="step"
                    :style="{ '--i': index }"
                >
                    <span class="connector"></span>
                    <div class="num">{{ step.n }}</div>
                    <h4>{{ step.title }}</h4>
                    <p>{{ step.text }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ¿Hablamos? / Reserva -->
    <section id="contacto" class="section sand">
        <div class="wrap contact">
            <div class="reveal">
                <p class="eyebrow">¿Hablamos?</p>
                <h2 class="h-sec">
                    Si sientes que este puede ser tu
                    <span class="thread">momento</span>
                </h2>
                <p class="lead">
                    Reserva una primera sesión online cuando mejor te venga. Sin
                    compromiso, a tu ritmo y desde el primer paso.
                </p>
            </div>
            <div class="booking-frame reveal">
                <BookingSection
                    :topics="props.topics"
                    :session-minutes="props.sessionMinutes"
                />
            </div>
            <div class="contact-alt reveal">
                <span>¿Prefieres escribirme primero?</span>
                <div class="contact-cta">
                    <a
                        v-if="waHref"
                        class="btn btn--primary"
                        :href="waHref"
                        rel="noopener"
                    >
                        <Icon name="chat" :size="19" />
                        Contactar por WhatsApp
                    </a>
                    <button class="btn btn--secondary" @click="mailOpen = true">
                        <Icon name="mail" :size="19" />
                        Enviar email
                    </button>
                </div>
            </div>
        </div>
        <ContactModal :open="mailOpen" @close="mailOpen = false" />
    </section>
</template>
