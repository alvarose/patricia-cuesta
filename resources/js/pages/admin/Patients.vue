<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { patientChipClass } from '@/lib/chips';
import {
    index as patientsIndex,
    store as storePatient,
    update as updatePatient,
} from '@/routes/admin/patients';
import type {
    Patient,
    PatientFilters,
    StatusOption,
    TopicOption,
} from '@/types';

const props = defineProps<{
    filters: PatientFilters;
    topics: TopicOption[];
    statuses: StatusOption[];
    patients: Patient[];
}>();

const search = ref(props.filters.search);
const status = ref(props.filters.status);

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const applyFilters = () => {
    router.get(
        patientsIndex.url({
            query: {
                search: search.value || undefined,
                status: status.value !== 'all' ? status.value : undefined,
            },
        }),
        {},
        { preserveState: true, preserveScroll: true, replace: true },
    );
};

watch(search, () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(applyFilters, 300);
});

const pickStatus = (value: string) => {
    status.value = status.value === value ? 'all' : value;
    applyFilters();
};

const modalOpen = ref(false);
const editing = ref<Patient | null>(null);

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    topic: '',
    notes: '',
    status: 'active',
});

const openNew = () => {
    editing.value = null;
    form.reset();
    form.clearErrors();
    modalOpen.value = true;
};

const openEdit = (patient: Patient) => {
    editing.value = patient;
    form.clearErrors();
    const [first, ...rest] = patient.name.split(' ');
    form.first_name = first;
    form.last_name = rest.join(' ');
    form.email = patient.email ?? '';
    form.phone = patient.phone ?? '';
    form.topic = patient.topicValue ?? '';
    form.notes = patient.notes ?? '';
    form.status = patient.status;
    modalOpen.value = true;
};

const submit = () => {
    const transform = (data: Record<string, unknown>) => ({
        ...data,
        last_name: data.last_name || null,
        email: data.email || null,
        phone: data.phone || null,
        topic: data.topic || null,
        notes: data.notes || null,
    });

    if (editing.value) {
        form.transform(transform).patch(updatePatient.url(editing.value.id), {
            preserveScroll: true,
            onSuccess: () => (modalOpen.value = false),
        });
    } else {
        form.transform(transform).post(storePatient.url(), {
            preserveScroll: true,
            onSuccess: () => (modalOpen.value = false),
        });
    }
};
</script>

<template>
    <Head title="Pacientes" />

    <div class="toolbar">
        <input
            v-model="search"
            type="search"
            class="search"
            placeholder="Buscar por nombre o motivo…"
        />
        <div style="display: flex; gap: 8px; flex-wrap: wrap">
            <button
                v-for="item in statuses"
                :key="item.value"
                class="fpill"
                :class="{ on: status === item.value }"
                @click="pickStatus(item.value)"
            >
                {{ item.label }}
            </button>
        </div>
        <span class="sp"></span>
        <button class="a-btn a-btn--lg" @click="openNew">
            + Nuevo paciente
        </button>
    </div>

    <div class="card tbl">
        <div class="tbl-head">
            <span>Paciente</span><span>Motivo</span><span>Sesiones</span>
            <span>Próxima cita</span><span>Estado</span>
        </div>
        <div
            v-for="(patient, index) in patients"
            :key="patient.id"
            class="tbl-row"
            @click="openEdit(patient)"
        >
            <span
                style="
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    min-width: 0;
                "
            >
                <span
                    class="avatar"
                    :class="{ 'avatar--sage': index % 3 === 1 }"
                >
                    {{ patient.initials }}
                </span>
                <span
                    style="
                        min-width: 0;
                        display: flex;
                        flex-direction: column;
                        line-height: 1.3;
                    "
                >
                    <span
                        style="
                            font-size: 14.5px;
                            font-weight: 700;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                        "
                    >
                        {{ patient.name }}
                    </span>
                    <span
                        style="font-size: 12px; color: var(--color-ink-faint)"
                    >
                        Desde {{ patient.since }}
                    </span>
                </span>
            </span>
            <span
                style="
                    font-size: 13.5px;
                    color: var(--color-ink-soft);
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                "
            >
                {{ patient.topic }}
            </span>
            <span
                style="
                    font-family: var(--font-display);
                    font-size: 16px;
                    font-weight: 500;
                "
            >
                {{ patient.sessions }}
            </span>
            <span
                style="font-size: 13.5px"
                :style="{
                    color: patient.next
                        ? 'var(--color-ink)'
                        : 'var(--color-ink-faint)',
                }"
            >
                {{ patient.next ?? 'Sin programar' }}
            </span>
            <span>
                <span class="chip" :class="patientChipClass(patient.status)">
                    {{ patient.statusLabel }}
                </span>
            </span>
        </div>
        <div v-if="patients.length === 0" class="empty">
            <h3>Sin resultados</h3>
            <p>Prueba con otro nombre o cambia el filtro.</p>
        </div>
    </div>
    <p style="margin: 0; font-size: 12.5px; color: var(--color-ink-faint)">
        {{ patients.length }}
        {{ patients.length === 1 ? 'paciente' : 'pacientes' }}
    </p>

    <div
        v-if="modalOpen"
        class="veil"
        @click="
            (event) => {
                if (event.target === event.currentTarget) modalOpen = false;
            }
        "
    >
        <div
            role="dialog"
            aria-modal="true"
            aria-label="Ficha de paciente"
            class="modal"
        >
            <button class="x" aria-label="Cerrar" @click="modalOpen = false">
                ×
            </button>
            <span class="eyebrow-sm">Pacientes</span>
            <h3 v-if="!editing">Nuevo <em class="emph">paciente</em></h3>
            <h3 v-else>{{ editing.name }}</h3>
            <p class="sub">
                Solo el nombre es imprescindible; el resto puedes completarlo
                más adelante.
            </p>
            <div class="body">
                <div class="frm-grid" style="grid-template-columns: 1fr 1.3fr">
                    <div class="fld">
                        <label>Nombre</label>
                        <input
                            v-model="form.first_name"
                            type="text"
                            placeholder="¿Cómo se llama?"
                        />
                        <p v-if="form.errors.first_name" class="err">
                            {{ form.errors.first_name }}
                        </p>
                    </div>
                    <div class="fld">
                        <label>Apellidos</label>
                        <input
                            v-model="form.last_name"
                            type="text"
                            placeholder="Opcional"
                        />
                    </div>
                </div>
                <div class="frm-grid">
                    <div class="fld">
                        <label>Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="Opcional"
                        />
                        <p v-if="form.errors.email" class="err">
                            {{ form.errors.email }}
                        </p>
                    </div>
                    <div class="fld">
                        <label>Teléfono</label>
                        <input
                            v-model="form.phone"
                            type="tel"
                            placeholder="Opcional"
                        />
                    </div>
                </div>
                <div class="fld">
                    <label>Motivo de consulta</label>
                    <select v-model="form.topic">
                        <option value="">Sin especificar</option>
                        <option
                            v-for="topic in topics"
                            :key="topic.value"
                            :value="topic.value"
                        >
                            {{ topic.label }}
                        </option>
                    </select>
                </div>
                <div v-if="editing" class="fld">
                    <label>Estado</label>
                    <select v-model="form.status">
                        <option
                            v-for="item in statuses"
                            :key="item.value"
                            :value="item.value"
                        >
                            {{ item.label }}
                        </option>
                    </select>
                </div>
                <div class="fld">
                    <label>Notas</label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        placeholder="Contexto inicial, derivación, disponibilidad…"
                    ></textarea>
                </div>
                <div
                    style="
                        display: flex;
                        align-items: center;
                        gap: 14px;
                        flex-wrap: wrap;
                        padding-top: 4px;
                    "
                >
                    <button
                        class="a-btn a-btn--lg"
                        :disabled="form.processing || !form.first_name"
                        @click="submit"
                    >
                        {{ editing ? 'Guardar cambios' : 'Crear paciente' }}
                    </button>
                    <button
                        class="a-btn a-btn--ghost a-btn--lg"
                        @click="modalOpen = false"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
