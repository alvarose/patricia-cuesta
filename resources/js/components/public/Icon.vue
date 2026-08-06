<script setup lang="ts">
interface Props {
    name:
        | 'menu'
        | 'x'
        | 'arrowRight'
        | 'mail'
        | 'chat'
        | 'check'
        | 'monitor'
        | 'lock'
        | 'clock'
        | 'heart';
    size?: number;
    stroke?: number;
}

withDefaults(defineProps<Props>(), { size: 20, stroke: 1.8 });
</script>

<template>
    <svg
        :width="size"
        :height="size"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        :stroke-width="stroke"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
    >
        <template v-if="name === 'menu'">
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="18" x2="21" y2="18" />
        </template>
        <template v-else-if="name === 'x'">
            <path d="M18 6 6 18" />
            <path d="m6 6 12 12" />
        </template>
        <template v-else-if="name === 'arrowRight'">
            <path d="M5 12h14" />
            <path d="m12 5 7 7-7 7" />
        </template>
        <template v-else-if="name === 'mail'">
            <rect width="20" height="16" x="2" y="4" rx="2" />
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
        </template>
        <template v-else-if="name === 'chat'">
            <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" />
        </template>
        <template v-else-if="name === 'check'">
            <path d="M20 6 9 17l-5-5" />
        </template>
        <template v-else-if="name === 'monitor'">
            <rect width="20" height="14" x="2" y="3" rx="2" />
            <line x1="8" y1="21" x2="16" y2="21" />
            <line x1="12" y1="17" x2="12" y2="21" />
        </template>
        <template v-else-if="name === 'lock'">
            <rect width="18" height="11" x="3" y="11" rx="2" />
            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
        </template>
        <template v-else-if="name === 'clock'">
            <circle cx="12" cy="12" r="10" />
            <polyline points="12 6 12 12 16 14" />
        </template>
        <template v-else-if="name === 'heart'">
            <path
                d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"
            />
        </template>
    </svg>
</template>
