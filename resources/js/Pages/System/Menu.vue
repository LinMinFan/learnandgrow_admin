<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, reactive } from 'vue'

defineProps({
  title: String,
  user: Object,
  menus: Array,
})

</script>

<template>
    <Head :title="title" />

    <AdminLayout :user="user">
        
    </AdminLayout>
</template>
