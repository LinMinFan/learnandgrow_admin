<script setup>
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ArticleForm from '@/Components/Form/ArticleForm.vue'


const { categories } = defineProps({
    categories: Array,
    article: Object
})

const isEdit = ref(true)


</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
            <h1 class="text-xl font-bold mb-4">{{ isEdit ? '編輯文章' : '新增文章' }}</h1>
            <ArticleForm :categories="categories" :article="article" type="edit" />
        </div>
    </AdminLayout>
</template>
