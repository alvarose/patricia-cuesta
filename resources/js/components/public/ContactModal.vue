<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref, watch } from 'vue';
import Icon from '@/components/public/Icon.vue';
import { legal } from '@/routes';
import { store as submitContact } from '@/routes/contact';

const props = defineProps<{ open: boolean }>();
const emit = defineEmits<{ close: [] }>();

const sent = ref(false);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    contact_preference: 'whatsapp',
    preferred_time: 'Indiferente',
    body: '',
    privacy: false,
});

const PREFS = [
    { value: 'whatsapp', label: 'WhatsApp' },
    { value: 'call', label: 'Llamada' },
    { value: 'email', label: 'Email' },
];

const TIMES = ['Mañanas (9–14h)', 'Tardes (16–20h)', 'Indiferente'];

const submit = () => {
    form.post(submitContact.url(), {
        preserveScroll: true,
        onSuccess: () => {
            sent.value = true;
            form.reset();
        },
    });
};

const onKey = (event: KeyboardEvent) => {
    if (event.key === 'Escape') {
        emit('close');
    }
};

watch(
    () => props.open,
    (value) => {
        document.body.style.overflow = value ? 'hidden' : '';

        if (value) {
            sent.value = false;
            form.clearErrors();
            window.addEventListener('keydown', onKey);
        } else {
            window.removeEventListener('keydown', onKey);
        }
    },
);

onBeforeUnmount(() => {
    document.body.style.overflow = '';
    window.removeEventListener('keydown', onKey);
});
</script>

<template>
    <div
        v-if="open"
        class="modal-veil"
        @click="
            (event) => {
                if (event.target === event.currentTarget) emit('close');
            }
        "
    >
        <div
            class="modal"
            role="dialog"
            aria-modal="true"
            aria-label="Enviar mensaje"
        >
            <button class="modal-x" aria-label="Cerrar" @click="emit('close')">
                <Icon name="x" :size="20" />
            </button>

            <div v-if="sent" class="modal-sent">
                <span class="ic"><Icon name="check" :size="22" /></span>
                <h3 class="modal-title">
                    Mensaje <em class="emph">enviado</em>
                </h3>
                <p class="modal-sub">
                    Gracias por escribirme. Te responderé lo antes posible,
                    normalmente en 24–48 horas.
                </p>
                <button class="btn btn--secondary" @click="emit('close')">
                    Cerrar
                </button>
            </div>

            <form v-else @submit.prevent="submit">
                <p class="eyebrow" style="margin-bottom: 0">Enviar mensaje</p>
                <h3 class="modal-title">
                    Cuéntame qué <em class="emph">necesitas</em>
                </h3>
                <p class="modal-sub">
                    Sin compromiso. Leo cada mensaje con calma y te respondo
                    personalmente.
                </p>
                <div class="modal-body">
                    <div class="field">
                        <label for="cf-name">Tu nombre</label>
                        <input
                            id="cf-name"
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="¿Cómo te llamas?"
                        />
                        <p v-if="form.errors.name" class="field-error">
                            {{ form.errors.name }}
                        </p>
                    </div>
                    <div class="field">
                        <label for="cf-mail">Tu email</label>
                        <input
                            id="cf-mail"
                            v-model="form.email"
                            type="email"
                            required
                            placeholder="Para poder responderte"
                        />
                        <p v-if="form.errors.email" class="field-error">
                            {{ form.errors.email }}
                        </p>
                    </div>
                    <div class="field">
                        <label for="cf-tel">Tu teléfono</label>
                        <input
                            id="cf-tel"
                            v-model="form.phone"
                            type="tel"
                            placeholder="Si prefieres que te escriba o llame"
                        />
                    </div>
                    <div class="field">
                        <label>¿Cómo prefieres que te contacte?</label>
                        <div
                            class="seg"
                            role="radiogroup"
                            aria-label="Medio de contacto preferido"
                        >
                            <button
                                v-for="pref in PREFS"
                                :key="pref.value"
                                type="button"
                                role="radio"
                                :aria-checked="
                                    form.contact_preference === pref.value
                                "
                                class="seg-opt"
                                :class="{
                                    on: form.contact_preference === pref.value,
                                }"
                                @click="form.contact_preference = pref.value"
                            >
                                {{ pref.label }}
                            </button>
                        </div>
                    </div>
                    <div class="field">
                        <label>¿En qué horario te viene mejor?</label>
                        <div
                            class="seg"
                            role="radiogroup"
                            aria-label="Horario preferido"
                        >
                            <button
                                v-for="time in TIMES"
                                :key="time"
                                type="button"
                                role="radio"
                                :aria-checked="form.preferred_time === time"
                                class="seg-opt"
                                :class="{ on: form.preferred_time === time }"
                                @click="form.preferred_time = time"
                            >
                                {{ time }}
                            </button>
                        </div>
                    </div>
                    <div class="field">
                        <label for="cf-msg">Tu mensaje</label>
                        <textarea
                            id="cf-msg"
                            v-model="form.body"
                            rows="5"
                            required
                            placeholder="Cuéntame, con tus palabras, qué te gustaría trabajar."
                        ></textarea>
                        <p v-if="form.errors.body" class="field-error">
                            {{ form.errors.body }}
                        </p>
                    </div>
                    <div class="field">
                        <label class="privacy-check">
                            <input
                                v-model="form.privacy"
                                type="checkbox"
                                required
                            />
                            <span>
                                He leído y acepto la
                                <a
                                    :href="legal.url('privacidad')"
                                    target="_blank"
                                    >política de privacidad</a
                                >. Tus datos solo se usan para responderte.
                            </span>
                        </label>
                        <p v-if="form.errors.privacy" class="field-error">
                            {{ form.errors.privacy }}
                        </p>
                    </div>
                    <div class="modal-actions">
                        <button
                            type="submit"
                            class="btn btn--primary"
                            :disabled="form.processing"
                        >
                            <Icon name="mail" :size="18" />
                            Enviar mensaje
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
