<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
    customClass: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <Link
        :href="href"
        :class="customClass"
    >
        <slot />
    </Link>
</template>
