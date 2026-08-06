<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import {
    patient as linkPatient,
    read as markRead,
    reply as sendReply,
} from '@/routes/admin/messages';
import type { ContactMessageDetail, ContactMessageListItem } from '@/types';

const props = defineProps<{
    messages: ContactMessageListItem[];
    selected: ContactMessageDetail | null;
}>();

// Abrir un mensaje lo marca como leído, así que es un POST, no una lectura.
const pick = (id: number) => {
    router.post(markRead.url(id), {}, { preserveScroll: true });
};

const form = useForm({ body: '' });
const sent = ref(false);

watch(
    () => props.selected?.id,
    () => {
        form.reset();
        form.clearErrors();
        sent.value = false;
    },
);

const reply = () => {
    if (!props.selected) {
        return;
    }

    form.post(sendReply.url(props.selected.id), {
        preserveScroll: true,
        onSuccess: () => {
            sent.value = true;
            form.reset();
        },
    });
};

const createPatient = () => {
    if (!props.selected) {
        return;
    }

    router.post(
        linkPatient.url(props.selected.id),
        {},
        { preserveScroll: true },
    );
};
</script>

<template>
    <Head title="Mensajes" />

    <div class="msg-grid">
        <div class="card msg-list">
            <button
                v-for="message in messages"
                :key="message.id"
                class="msg-item"
                :class="{
                    unread: message.unread,
                    on: selected?.id === message.id,
                }"
                @click="pick(message.id)"
            >
                <span class="dt"></span>
                <span class="bd">
                    <span class="tp-row">
                        <span class="nm">{{ message.name }}</span>
                        <span class="wh">{{ message.when }}</span>
                    </span>
                    <span class="pv">{{ message.preview }}</span>
                </span>
            </button>
            <p
                v-if="messages.length === 0"
                class="muted-note"
                style="border: none"
            >
                Sin mensajes todavía.
            </p>
        </div>

        <div
            v-if="selected"
            class="card"
            style="display: flex; flex-direction: column; gap: 16px"
        >
            <div
                style="
                    display: flex;
                    align-items: flex-start;
                    gap: 14px;
                    flex-wrap: wrap;
                "
            >
                <div
                    style="
                        display: flex;
                        flex-direction: column;
                        gap: 3px;
                        min-width: 0;
                        flex: 1;
                    "
                >
                    <h2 class="card-title" style="font-size: 22px">
                        {{ selected.name }}
                    </h2>
                    <span
                        style="font-size: 12.5px; color: var(--color-ink-soft)"
                    >
                        {{ selected.when }}
                    </span>
                </div>
                <button
                    v-if="!selected.hasPatient"
                    class="a-btn a-btn--ghost"
                    style="padding: 9px 16px; font-size: 13px"
                    @click="createPatient"
                >
                    Crear paciente
                </button>
                <span v-else class="chip chip--done">Paciente creado</span>
            </div>

            <div style="display: flex; gap: 8px; flex-wrap: wrap">
                <span class="tag">{{ selected.email }}</span>
                <span v-if="selected.phone" class="tag">{{
                    selected.phone
                }}</span>
                <span class="tag tag--pref"
                    >Prefiere: {{ selected.preference }}</span
                >
                <span v-if="selected.preferredTime" class="tag">{{
                    selected.preferredTime
                }}</span>
            </div>

            <p class="msg-body">{{ selected.body }}</p>

            <div style="display: flex; flex-direction: column; gap: 10px">
                <label
                    style="
                        font-size: 12.5px;
                        font-weight: 700;
                        color: var(--color-ink-soft);
                    "
                >
                    Tu respuesta
                </label>
                <div class="fld">
                    <textarea
                        v-model="form.body"
                        rows="4"
                        placeholder="Escribe con calma; se enviará por el medio que prefiere."
                    ></textarea>
                    <p v-if="form.errors.body" class="err">
                        {{ form.errors.body }}
                    </p>
                </div>
                <div
                    style="
                        display: flex;
                        align-items: center;
                        gap: 14px;
                        flex-wrap: wrap;
                    "
                >
                    <button
                        class="a-btn a-btn--lg"
                        :disabled="form.processing || !form.body"
                        @click="reply"
                    >
                        Responder por {{ selected.preference }}
                    </button>
                    <span v-if="sent || selected.replied" class="sent-ok"
                        >✓ Respondido</span
                    >
                </div>
            </div>
        </div>

        <div
            v-else
            class="card empty"
            style="border-top: 1px solid var(--color-sand)"
        >
            <h3>Sin mensajes</h3>
            <p>Cuando alguien escriba desde la web, lo verás aquí.</p>
        </div>
    </div>
</template>
