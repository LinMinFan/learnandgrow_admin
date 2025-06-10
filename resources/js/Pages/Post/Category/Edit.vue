<script setup>
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CategoryForm from '@/Components/Form/CategoryForm.vue'


const { parents, category } = defineProps({
  parents: Array,
  category: Object,
})

const isEdit = ref(true)

</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
            <h1 class="text-xl font-bold mb-4">{{ isEdit ? '編輯分類' : '新增分類' }}</h1>
            <CategoryForm :parents="parents" :category="category" type="edit" />
        </div>
    </AdminLayout>
</template>
