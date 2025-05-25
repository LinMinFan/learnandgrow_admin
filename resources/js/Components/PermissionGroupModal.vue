<script setup>
import { defineProps, defineEmits } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import axios from 'axios'
import get from 'lodash/get'

const props = defineProps({
    show: Boolean,
})

const emit = defineEmits(['close'])

const form = useForm({
    name: '',
    display_name: '',
    description: '',
})

const close = () => emit('close')

const submit = async () => {
    form.clearErrors()

    if (!form.name.trim()) {
        form.errors.name = '群組代號必填';
        return;
    }

    if (!form.display_name.trim()) {
        form.errors.display_name = '群組名稱必填';
        return;
    }

    await axios.post(route('admin.permission.addGroup'), form)
        .then((res) => {
            close()
            console.log(res.data.message);
            router.visit(route('admin.permission'), {
                data: {
                    success: res.data.message
                }
            })
        })
        .catch((errors) => {
            form.clearErrors();

            const formErrors = get(errors, 'response.data.errors', {});
            const message = get(errors, 'response.data.message', '發生錯誤，請稍後再試');

            // 驗證錯誤
            if (formErrors.name) {
                form.errors.name = formErrors.name[0]
            }

            if (formErrors.display_name) {
                form.errors.display_name = formErrors.display_name[0]
            }

            if (!formErrors.name && !formErrors.display_name) {
                errorNotify(message)
            }
        })
}
</script>

<template>
    <Transition name="fade">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- 背景遮罩 -->
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="close"></div>

            <!-- 對話框 -->
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 z-10 overflow-hidden">
                <!-- 標題 -->
                <div class="bg-gray-100 px-6 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">新增權限群組</h3>
                </div>
                <!-- 表單內容 -->
                <form @submit.prevent="submit">
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                群組代稱 <span class="text-red-600">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300"
                            />
                            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                                {{ form.errors.name }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                顯示名稱 <span class="text-red-600">*</span>
                            </label>
                            <input
                                v-model="form.display_name"
                                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300"
                            />
                            <div v-if="form.errors.display_name" class="text-red-500 text-sm mt-1">
                                {{ form.errors.display_name }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">描述</label>
                            <textarea
                              v-model="form.description"
                              class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300"
                            >
                            </textarea>
                        </div>
                    </div>
                    <!-- 按鈕 -->
                    <div class="px-6 py-3 bg-gray-50 flex justify-end gap-2 border-t">
                        <button
                            type="button"
                            @click="close"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-gray-800 transition"
                        >
                            取消
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition"
                        >
                            新增
                        </button>
                    </div>
                </form>
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
  