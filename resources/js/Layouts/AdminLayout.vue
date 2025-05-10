 
<script setup>
    import { Link } from '@inertiajs/vue3'
    import Navbar from '@/Components/Navbar.vue'

    defineProps({
        user: Object,
    })
</script>

<template>
    <!-- navbar -->
    <Navbar />

    <!-- sidebar -->
    
    <!-- Page Content -->
    <main class="flex-1 p-6 overflow-auto bg-gray-50">
        <slot />
    </main>
</template>
  