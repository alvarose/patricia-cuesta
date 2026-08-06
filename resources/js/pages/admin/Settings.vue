<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { update as updateSettings } from '@/routes/admin/settings';
import type { ClinicProfile } from '@/types';

const props = defineProps<{
    profile: ClinicProfile;
    booking: {
        calcom_url: string | null;
    };
}>();

const form = useForm({
    name: props.profile.name,
    license_number: props.profile.license_number,
    email: props.profile.email,
    phone: props.profile.phone,
    whatsapp: props.profile.whatsapp,
    calcom_url: props.booking.calcom_url ?? '',
    photo: null as File | null,
});

const saved = ref(false);
const photoInput = ref<HTMLInputElement | null>(null);
const photoPreview = ref<string | null>(null);

const pickPhoto = () => photoInput.value?.click();

const onPhoto = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.photo = file;
    photoPreview.value = file ? URL.createObjectURL(file) : null;
};

const save = () => {
    form.transform((data) => ({
        ...data,
        calcom_url: data.calcom_url || null,
    })).post(updateSettings.url(), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            saved.value = true;
            form.photo = null;
            setTimeout(() => (saved.value = false), 2500);
        },
    });
};
</script>

<template>
    <Head title="Ajustes" />

    <div class="grid-2" style="grid-template-columns: 1fr 1fr">
        <div class="col">
            <div
                class="card"
                style="display: flex; flex-direction: column; gap: 16px"
            >
                <h2 class="card-title">Perfil</h2>
                <div style="display: flex; align-items: center; gap: 16px">
                    <img
                        :src="
                            photoPreview ??
                            profile.photo_url ??
                            '/images/patricia.png'
                        "
                        alt=""
                        style="
                            width: 64px;
                            height: 64px;
                            border-radius: 50%;
                            object-fit: cover;
                            object-position: 50% 15%;
                        "
                    />
                    <div
                        style="
                            display: flex;
                            flex-direction: column;
                            gap: 2px;
                            min-width: 0;
                        "
                    >
                        <span style="font-size: 14.5px; font-weight: 700"
                            >Foto de perfil</span
                        >
                        <span
                            style="
                                font-size: 12.5px;
                                color: var(--color-ink-soft);
                            "
                        >
                            Se muestra en la web y en el calendario de reservas.
                        </span>
                    </div>
                    <span style="flex: 1"></span>
                    <input
                        ref="photoInput"
                        type="file"
                        accept="image/*"
                        style="display: none"
                        @change="onPhoto"
                    />
                    <button
                        class="a-btn a-btn--ghost"
                        style="flex: none"
                        @click="pickPhoto"
                    >
                        Cambiar
                    </button>
                </div>
                <p
                    v-if="form.errors.photo"
                    class="err"
                    style="color: #b3452f; font-size: 12px; margin: 0"
                >
                    {{ form.errors.photo }}
                </p>
                <div class="frm-grid">
                    <div class="fld">
                        <label>Nombre</label>
                        <input v-model="form.name" type="text" />
                        <p v-if="form.errors.name" class="err">
                            {{ form.errors.name }}
                        </p>
                    </div>
                    <div class="fld">
                        <label>Nº de colegiada</label>
                        <input v-model="form.license_number" type="text" />
                    </div>
                    <div class="fld">
                        <label>Email</label>
                        <input v-model="form.email" type="email" />
                        <p v-if="form.errors.email" class="err">
                            {{ form.errors.email }}
                        </p>
                    </div>
                    <div class="fld">
                        <label>Teléfono / WhatsApp</label>
                        <input
                            v-model="form.whatsapp"
                            type="tel"
                            placeholder="Con prefijo, p. ej. 34600111222"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div
                class="card"
                style="display: flex; flex-direction: column; gap: 14px"
            >
                <h2 class="card-title">Web y reservas</h2>
                <div class="fld">
                    <label>Email de avisos</label>
                    <input v-model="form.email" type="email" disabled />
                    <span
                        style="font-size: 12px; color: var(--color-ink-faint)"
                    >
                        Las solicitudes de cita y los mensajes de la web llegan
                        a este email.
                    </span>
                </div>
                <div class="fld">
                    <label>Enlace de cal.com (opcional)</label>
                    <input
                        v-model="form.calcom_url"
                        type="text"
                        placeholder="usuario/evento — déjalo vacío para usar el calendario propio"
                    />
                    <span
                        style="font-size: 12px; color: var(--color-ink-faint)"
                    >
                        Si lo rellenas, la web puede incrustar tu calendario de
                        cal.com en lugar del sistema de reservas propio.
                    </span>
                </div>
                <div
                    style="
                        display: flex;
                        align-items: center;
                        gap: 14px;
                        margin-top: 4px;
                    "
                >
                    <button
                        class="a-btn a-btn--lg"
                        :disabled="form.processing"
                        @click="save"
                    >
                        Guardar ajustes
                    </button>
                    <span v-if="saved" class="sent-ok">✓ Guardado</span>
                </div>
            </div>
        </div>
    </div>
</template>
