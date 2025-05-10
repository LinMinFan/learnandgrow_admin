<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
  title: String,
  user: Object,
})

</script>

<template>
    <Head :title="title" />

    <AdminLayout :user="user">
        <h2 class="text-xl font-bold mb-4">歡迎回來，{{ user.name }}！</h2>
        <p>這是你的儀表板。</p>
    </AdminLayout>
</template>
