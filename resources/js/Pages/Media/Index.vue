<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useTopGlobalNotify } from '@/Composables/useTopGlobalNotify'
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import cloneDeep from 'lodash/cloneDeep'
import draggable from 'vuedraggable'
import axios from 'axios'

const props = defineProps({
    mediaData: Array,
})


</script>

<template>
    <AdminLayout>
        <div>
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">媒體庫</h1>
            </div>

        </div>
    </AdminLayout>
</template>

<style scoped>

</style>