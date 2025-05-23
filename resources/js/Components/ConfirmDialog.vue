<script setup>
import { defineProps, defineEmits, ref } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: '確認'
    },
    confirmMessage: {
        type: String,
        required: true
    },
    confirmButtonText: {
        type: String,
        default: '確認'
    },
    cancelButtonText: {
        type: String,
        default: '取消'
    },
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['confirm', 'cancel']);

const confirmAction = () => {
  emit('confirm');
};

const cancelAction = () => {
  emit('cancel');
};
</script>

<template>
    <Transition name="fade">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- 背景遮罩 -->
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="cancelAction"></div>
      
            <!-- 對話框 -->
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 z-10 overflow-hidden">
                <!-- 標題 -->
                <div class="bg-gray-100 px-6 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
                </div>
        
                <!-- 內容 -->
                <div class="px-6 py-4">
                    <p class="text-gray-700">{{ confirmMessage }}</p>
                </div>
        
                <!-- 按鈕 -->
                <div class="px-6 py-3 bg-gray-50 flex justify-end space-x-3">
                    <button 
                        @click="cancelAction"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-gray-800 transition-colors"
                    >
                        {{ cancelButtonText }}
                    </button>
                    <button 
                        @click="confirmAction"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded text-white transition-colors"
                    >
                        {{ confirmButtonText }}
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>