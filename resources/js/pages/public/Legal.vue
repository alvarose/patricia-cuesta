<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    section: 'aviso-legal' | 'privacidad' | 'cookies';
}>();

const contactEmail = computed(() => usePage().props.clinic.email);

const title = computed(
    () =>
        ({
            'aviso-legal': 'Aviso legal',
            privacidad: 'Política de privacidad',
            cookies: 'Política de cookies',
        })[props.section],
);
</script>

<template>
    <Head :title="title" />

    <section class="legal-page">
        <div class="wrap">
            <p class="eyebrow">Información legal</p>
            <h1 class="h-sec">{{ title }}</h1>

            <div v-if="section === 'aviso-legal'" class="prose">
                <h2>Titular del sitio web</h2>
                <p>
                    Este sitio web pertenece a Patricia Cuesta, psicóloga
                    sanitaria y forense. Puedes contactar en {{ contactEmail }}.
                </p>
                <h2>Objeto</h2>
                <p>
                    Este sitio tiene una finalidad informativa sobre los
                    servicios de psicología ofrecidos y permite solicitar cita
                    online. El acceso y uso del sitio atribuye la condición de
                    usuario e implica la aceptación de este aviso legal.
                </p>
                <h2>Propiedad intelectual</h2>
                <p>
                    Los contenidos de este sitio (textos, imágenes y diseño) son
                    propiedad de su titular y no pueden reproducirse sin
                    autorización.
                </p>
            </div>

            <div v-else-if="section === 'privacidad'" class="prose">
                <h2>Responsable del tratamiento</h2>
                <p>
                    Patricia Cuesta es la responsable del tratamiento de los
                    datos personales que facilites a través de los formularios
                    de este sitio.
                </p>
                <h2>Finalidad</h2>
                <p>
                    Los datos que envías por los formularios de contacto y de
                    reserva de cita se usan únicamente para responderte y
                    gestionar tu cita. No se ceden a terceros ni se usan con
                    fines publicitarios.
                </p>
                <h2>Conservación</h2>
                <p>
                    Los datos se conservan solo el tiempo necesario para atender
                    tu solicitud y cumplir las obligaciones legales aplicables a
                    la actividad sanitaria.
                </p>
                <h2>Derechos</h2>
                <p>
                    Puedes ejercer tus derechos de acceso, rectificación,
                    supresión y oposición escribiendo a {{ contactEmail }}.
                </p>
            </div>

            <div v-else class="prose">
                <h2>Uso de cookies</h2>
                <p>
                    Este sitio utiliza únicamente cookies técnicas
                    imprescindibles para su funcionamiento (como la cookie de
                    sesión). No se usan cookies de análisis ni de publicidad.
                </p>
                <h2>Cómo gestionarlas</h2>
                <p>
                    Puedes configurar tu navegador para bloquear o eliminar
                    cookies. Si bloqueas las cookies técnicas, es posible que
                    algunas funciones (como la reserva de cita) no funcionen
                    correctamente.
                </p>
            </div>
        </div>
    </section>
</template>
