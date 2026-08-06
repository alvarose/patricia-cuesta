<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from '@/components/public/Icon.vue';
import SiteFooter from '@/components/public/SiteFooter.vue';
import SiteHeader from '@/components/public/SiteHeader.vue';
import { whatsappHref } from '@/lib/links';

const page = usePage();

const waHref = computed(() => whatsappHref(page.props.clinic.whatsapp));
</script>

<template>
    <div class="pc-public">
        <SiteHeader />
        <main>
            <slot />
        </main>
        <SiteFooter />
        <a
            v-if="waHref"
            class="wa-float"
            :href="waHref"
            rel="noopener"
            aria-label="Contactar por WhatsApp"
        >
            <Icon name="chat" :size="26" />
        </a>
    </div>
</template>
