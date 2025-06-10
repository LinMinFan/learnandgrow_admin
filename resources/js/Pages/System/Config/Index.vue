<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import cloneDeep from 'lodash/cloneDeep'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'
import ConfigField from '@/Components/Form/ConfigField.vue'
import { useTopGlobalNotify } from '@/Composables/useTopGlobalNotify'

const { successNotify, errorNotify } = useTopGlobalNotify()

const props = defineProps({
    configs: Object,
    keyLabels: Object, // 包含 label + type
})

const form = useForm(cloneDeep(props.configs))
const showUpdateConfirm = ref(false)

function handleModelValueUpdate(fieldKey, val) {
  // 只更新 form 中對應的欄位資料
  form[fieldKey] = val[fieldKey];
}

const handleConfirmUpdate = () => {
    if (!showUpdateConfirm.value) return
    showUpdateConfirm.value = false
    axios.put(route('system.config.update'), form.data())
    .then((res) => {
            router.visit(route('system.config'), {
                data: {
                    success: res.data.message
                }
            })
        })
        .catch((error) => {
            router.visit(route('system.config'), {
                data: {
                    error: error.response?.data?.message
                }
            })
        })
}

const handleUpdateClick = () => {
    showUpdateConfirm.value = true
}

const handleCancelUpdate = () => {
    showUpdateConfirm.value = false;
};
</script>

<template>
    <AdminLayout>
        <form @submit.prevent="handleUpdateClick">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-6 rounded shadow">
                <template v-for="(value, key) in form" :key="key">
                    <ConfigField 
                        v-if="props.keyLabels[key]" 
                        :fieldKey="key" 
                        :fieldLabel="props.keyLabels[key].label"
                        :fieldType="props.keyLabels[key].type" 
                        :fieldPlaceholder="props.keyLabels[key].placeholder || null" 
                        :fieldValue="value" 
                        :modelValue="form"
                        @update:modelValue="val => handleModelValueUpdate(key, val)" 
                    />
                </template>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    儲存設定
                </button>
            </div>
        </form>

        <ConfirmDialog 
            :show="showUpdateConfirm" 
            title="更新系統參數" 
            confirmMessage="確定要更新系統參數嗎？" 
            confirmButtonText="確定！"
            @confirm="handleConfirmUpdate" 
            @cancel="handleCancelUpdate" 
        />
    </AdminLayout>
</template>
