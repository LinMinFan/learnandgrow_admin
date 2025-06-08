<!-- Components\ContactForm\ContactFormModal.vue -->
<script setup>
const props = defineProps({
    show: Boolean,
    data: Object
});

const emit = defineEmits(['close']);
</script>

<template>
    <div v-if="show && data?.message" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">
            <!-- 關閉按鈕 -->
            <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-800" @click="$emit('close')" title="關閉">
                <i class="fas fa-times"></i>
            </button>

            <!-- 標題 -->
            <h2 class="text-xl font-bold mb-4">留言內容</h2>

            <!-- 顯示留言 -->
            <div class="text-gray-800 whitespace-pre-line text-sm">
                {{ data.message }}
            </div>
        </div>
    </div>
</template>
  