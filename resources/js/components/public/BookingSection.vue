<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import Icon from '@/components/public/Icon.vue';
import { legal } from '@/routes';
import {
    slots as bookingSlots,
    store as submitBooking,
} from '@/routes/booking';
import type { TopicOption } from '@/types';

const props = defineProps<{
    topics: TopicOption[];
    sessionMinutes: number;
}>();

const MONTHS = [
    'enero',
    'febrero',
    'marzo',
    'abril',
    'mayo',
    'junio',
    'julio',
    'agosto',
    'septiembre',
    'octubre',
    'noviembre',
    'diciembre',
];
const DOW = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];

const today = new Date();
const offset = ref(0);
const selectedDate = ref<string | null>(null);
const selectedSlot = ref<string | null>(null);
const slotsByDate = ref<Record<string, string[]>>({});
const loading = ref(false);
const booked = ref(false);
const showForm = ref(false);

const base = computed(
    () => new Date(today.getFullYear(), today.getMonth() + offset.value, 1),
);
const year = computed(() => base.value.getFullYear());
const month = computed(() => base.value.getMonth());

const cells = computed<(number | null)[]>(() => {
    const first = new Date(year.value, month.value, 1);
    const startDow = (first.getDay() + 6) % 7;
    const days = new Date(year.value, month.value + 1, 0).getDate();

    return [
        ...Array.from({ length: startDow }, () => null),
        ...Array.from({ length: days }, (_, i) => i + 1),
    ];
});

const dateKey = (day: number) =>
    `${year.value}-${String(month.value + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

const isAvailable = (day: number) =>
    (slotsByDate.value[dateKey(day)] ?? []).length > 0;

const daySlots = computed(() =>
    selectedDate.value ? (slotsByDate.value[selectedDate.value] ?? []) : [],
);

const selectedDayLabel = computed(() => {
    if (!selectedDate.value) {
        return '';
    }

    const [, m, d] = selectedDate.value.split('-').map(Number);

    return `${d} de ${MONTHS[m - 1]}`;
});

async function fetchSlots() {
    loading.value = true;

    const lastDay = new Date(year.value, month.value + 1, 0);
    const firstDay = new Date(year.value, month.value, 1);
    const from = offset.value === 0 ? today : firstDay;
    const iso = (date: Date) =>
        `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

    try {
        const response = await fetch(
            bookingSlots.url({ query: { from: iso(from), to: iso(lastDay) } }),
            { headers: { Accept: 'application/json' } },
        );

        if (response.ok) {
            slotsByDate.value = {
                ...slotsByDate.value,
                ...(await response.json()),
            };
        }
    } finally {
        loading.value = false;
    }
}

onMounted(fetchSlots);
watch(offset, () => {
    selectedDate.value = null;
    selectedSlot.value = null;
    showForm.value = false;
    fetchSlots();
});

const goMonth = (delta: number) => {
    offset.value = Math.max(0, offset.value + delta);
};

const pickDay = (day: number) => {
    selectedDate.value = dateKey(day);
    selectedSlot.value = null;
    showForm.value = false;
};

const form = useForm({
    name: '',
    email: '',
    phone: '',
    topic: '',
    start: '',
    privacy: false,
});

const submit = () => {
    if (!selectedDate.value || !selectedSlot.value) {
        return;
    }

    form.transform((data) => ({
        ...data,
        topic: data.topic || null,
        start: `${selectedDate.value} ${selectedSlot.value}`,
    })).post(submitBooking.url(), {
        preserveScroll: true,
        onSuccess: () => {
            booked.value = true;
            slotsByDate.value = {};
            fetchSlots();
        },
    });
};

const resetBooking = () => {
    booked.value = false;
    showForm.value = false;
    selectedDate.value = null;
    selectedSlot.value = null;
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <div class="booking">
        <div class="cal-mock">
            <div class="cal-aside">
                <div class="cal-host">
                    <img src="/images/logo-mark.svg" alt="" />
                    <div>
                        <p class="cal-host-name">Patricia Cuesta</p>
                        <p class="cal-host-role">
                            Psicóloga sanitaria y forense
                        </p>
                    </div>
                </div>
                <h4 class="cal-event">Sesión online</h4>
                <ul class="cal-meta">
                    <li>
                        <Icon name="clock" :size="16" />
                        {{ props.sessionMinutes }} min
                    </li>
                    <li><Icon name="monitor" :size="16" /> Videollamada</li>
                    <li>
                        <Icon name="lock" :size="16" /> Espacio confidencial
                    </li>
                </ul>
                <p class="cal-note">
                    Primera toma de contacto, sin compromiso.
                </p>
            </div>

            <div class="cal-grid-pane">
                <div class="cal-month">
                    <span>{{ MONTHS[month] }} {{ year }}</span>
                    <div class="cal-nav">
                        <button
                            class="cal-nav-btn cal-nav-today"
                            :disabled="offset === 0"
                            @click="offset = 0"
                        >
                            Hoy
                        </button>
                        <button
                            class="cal-nav-btn"
                            aria-label="Mes anterior"
                            :disabled="offset === 0"
                            @click="goMonth(-1)"
                        >
                            <Icon
                                name="arrowRight"
                                :size="15"
                                style="transform: rotate(180deg)"
                            />
                        </button>
                        <button
                            class="cal-nav-btn"
                            aria-label="Mes siguiente"
                            @click="goMonth(1)"
                        >
                            <Icon name="arrowRight" :size="15" />
                        </button>
                    </div>
                </div>
                <div class="cal-dow">
                    <span v-for="d in DOW" :key="d">{{ d }}</span>
                </div>
                <div class="cal-days">
                    <template v-for="(day, index) in cells" :key="index">
                        <span v-if="day === null" class="cal-empty"></span>
                        <button
                            v-else
                            class="cal-day"
                            :class="{
                                avail: isAvailable(day),
                                sel: selectedDate === dateKey(day),
                            }"
                            :disabled="!isAvailable(day)"
                            @click="pickDay(day)"
                        >
                            {{ day }}
                        </button>
                    </template>
                </div>
            </div>

            <div class="cal-slots-pane">
                <div v-if="booked" class="modal-sent" style="margin: auto 0">
                    <span class="ic"><Icon name="check" :size="22" /></span>
                    <h3 class="modal-title" style="font-size: 1.3rem">
                        Solicitud <em class="emph">enviada</em>
                    </h3>
                    <p class="modal-sub" style="margin-bottom: 0">
                        Gracias por dar el paso. Te confirmaré la cita por email
                        lo antes posible.
                    </p>
                    <button class="btn btn--secondary" @click="resetBooking">
                        Reservar otra cita
                    </button>
                </div>

                <template v-else-if="selectedDate">
                    <p class="cal-slots-head">{{ selectedDayLabel }}</p>

                    <div v-if="!showForm" class="cal-slots">
                        <button
                            v-for="slot in daySlots"
                            :key="slot"
                            class="cal-slot"
                            :class="{ sel: selectedSlot === slot }"
                            @click="selectedSlot = slot"
                        >
                            {{ slot }}
                        </button>
                    </div>

                    <form v-else class="cal-form" @submit.prevent="submit">
                        <div class="field">
                            <label for="bk-name">Tu nombre</label>
                            <input
                                id="bk-name"
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
                            <label for="bk-mail">Tu email</label>
                            <input
                                id="bk-mail"
                                v-model="form.email"
                                type="email"
                                required
                                placeholder="Para confirmarte la cita"
                            />
                            <p v-if="form.errors.email" class="field-error">
                                {{ form.errors.email }}
                            </p>
                        </div>
                        <div class="field">
                            <label for="bk-tel">Tu teléfono</label>
                            <input
                                id="bk-tel"
                                v-model="form.phone"
                                type="tel"
                                placeholder="Opcional"
                            />
                        </div>
                        <div class="field">
                            <label for="bk-topic"
                                >¿Qué te gustaría trabajar?</label
                            >
                            <select id="bk-topic" v-model="form.topic">
                                <option value="">
                                    Prefiero contarlo en sesión
                                </option>
                                <option
                                    v-for="topic in props.topics"
                                    :key="topic.value"
                                    :value="topic.value"
                                >
                                    {{ topic.label }}
                                </option>
                            </select>
                        </div>
                        <div class="field">
                            <label class="privacy-check">
                                <input
                                    v-model="form.privacy"
                                    type="checkbox"
                                    required
                                />
                                <span>
                                    Acepto la
                                    <a
                                        :href="legal.url('privacidad')"
                                        target="_blank"
                                        >política de privacidad</a
                                    >.
                                </span>
                            </label>
                            <p v-if="form.errors.privacy" class="field-error">
                                {{ form.errors.privacy }}
                            </p>
                        </div>
                        <p v-if="form.errors.start" class="field-error">
                            {{ form.errors.start }}
                        </p>
                        <button
                            type="submit"
                            class="btn btn--primary cal-confirm"
                            :disabled="form.processing"
                        >
                            Reservar el {{ selectedDayLabel }} a las
                            {{ selectedSlot }}
                        </button>
                        <button
                            type="button"
                            class="cal-nav-btn cal-nav-today"
                            style="align-self: center"
                            @click="showForm = false"
                        >
                            ← Cambiar hora
                        </button>
                    </form>

                    <button
                        v-if="!showForm"
                        class="btn btn--primary cal-confirm"
                        :disabled="!selectedSlot"
                        @click="showForm = true"
                    >
                        {{
                            selectedSlot
                                ? `Reservar a las ${selectedSlot}`
                                : 'Elige una hora'
                        }}
                    </button>
                </template>

                <div v-else-if="loading" class="cal-loading">
                    Cargando disponibilidad…
                </div>

                <div v-else class="cal-empty-state">
                    <span class="cal-empty-dot"></span>
                    <p>Elige un día disponible para ver las horas libres.</p>
                </div>
            </div>
        </div>
    </div>
</template>
