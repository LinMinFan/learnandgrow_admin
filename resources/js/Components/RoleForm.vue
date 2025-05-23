<script setup>
import get from 'lodash/get';
import { Link, usePage, useForm, router  } from '@inertiajs/vue3';
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { useTopGlobalNotify } from '@/Composables/useTopGlobalNotify'

const props = defineProps({
    type: {
        type: String,
        default: 'create'
    },
    role: {
        type: Object,
        default: () => ({ name: '', permissions: [] })
    },
    permissions: {
        type: Array,
        required: true
    },
})

const { successNotify, errorNotify } = useTopGlobalNotify()
const form = useForm({
  name: props.role.name || '',
  permissions: props.role.permissions || []
})

const submit = () => {
    if (!form.name.trim()) {
        form.errors.name = '角色名稱為必填';
        return;
    }

    const payload = {
        name: form.name,
        permissions: form.permissions,
    };

    const url = props.type === 'create'
        ? route('admin.role.store')
        : route('admin.role.update', props.role.id);

    const method = props.type === 'create' ? 'post' : 'put';
    
    axios[method](url, payload)
        .then((res) => {
            router.visit(route('admin.role'), {
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
            } else if (formErrors.permissions) {
                form.errors.permissions = formErrors.permissions[0]
            } else {
                // 自訂錯誤訊息（例外處理訊息）
                errorNotify(message)
            }

            
        })
}

</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div>
            <label class="block font-medium">
                角色名稱 <span class="text-red-600">*</span>
            </label>
            <input
                v-model="form.name"
                type="text"
                class="form-input w-full"
                :class="{ 'border-red-500': form.errors.name }"
                placeholder="請輸入角色名稱"
            />
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                {{ form.errors.name }}
            </div>
        </div>

        <div>
            <label class="block font-medium">權限</label>
            <Multiselect
                v-model="form.permissions"
                :options="permissions"
                :multiple="true"
                :group-values="'permissions'"
                :group-label="'group'"
                :close-on-select="false"
                :clear-on-select="false"
                label="label"
                track-by="id"
                placeholder="請選擇權限"
                class="w-full"
            >
                <template #tag="{ option, remove }">
                    <span class="bg-green-600 text-white px-2 py-1 rounded mr-1">
                        {{ option.label }}
                        <span class="cursor-pointer ml-1" @click="remove(option)">x</span>
                    </span>
                </template>
                
                <!-- 沒有選項 -->
                <template #noOptions>
                    <span class="text-gray-500">無可選擇項目</span>
                </template>

                <!-- 沒有符合搜尋 -->
                <template #noResult>
                    <span class="text-gray-500">查無符合的項目</span>
                </template>
            </Multiselect>
            <div v-if="form.errors.permissions" class="text-red-500 text-sm mt-1">
                {{ form.errors.permissions }}
            </div>
        </div>

        <div class="flex justify-end space-x-2">
            <Link :href="route('admin.role')" class="btn">取消</Link>
            <button type="submit" class="btn btn-primary" :disabled="form.processing">儲存</button>
        </div>
    </form>
</template>

<style lang="postcss">
.form-input {
    @apply border border-gray-300 rounded px-3 py-2;
}
.btn {
    @apply px-4 py-2 rounded border bg-gray-100 text-gray-800 hover:bg-gray-200;
}
.btn-primary {
    @apply bg-blue-600 text-white hover:bg-blue-700;
}
</style>