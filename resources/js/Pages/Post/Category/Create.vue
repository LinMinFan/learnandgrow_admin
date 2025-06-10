<script setup>
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CategoryForm from '@/Components/Form/CategoryForm.vue'


const { parents, permissions } = defineProps({
  parents: Array,
})

const isEdit = ref(false)


</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
            <h1 class="text-xl font-bold mb-4">{{ isEdit ? '編輯分類' : '新增分類' }}</h1>
            <CategoryForm :parents="parents" type="create" />
        </div>
    </AdminLayout>
</template>
