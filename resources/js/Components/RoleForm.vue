<script setup>
import get from 'lodash/get';
import { Link, usePage, useForm, router  } from '@inertiajs/vue3';
import { useTopGlobalNotify } from '@/Composables/useTopGlobalNotify'
import { submitFormWithNotify } from '@/Composables/formAxiosWithNotify'

const props = defineProps({
    type: {
        type: String,
        default: 'create'
    },
    role: {
        type: Array,
        default: () => ({
            id: '',
            name: '',
            display_name: '',
            permissions: []
        })
    },
    permissionGroups: {
        type: Array,
        required: true
    },
})

const { errorNotify } = useTopGlobalNotify()
const form = useForm({
    name: props.role.name || '',
    display_name: props.role.display_name || '',
    permissions: props.role.permissions || []
})

const submit = () => {
    form.clearErrors();
    
    if (!form.name.trim()) {
        form.errors.name = '角色代號為必填';
        return;
    }
    
    if (!form.display_name.trim()) {
        form.errors.display_name = '角色名稱為必填';
        return;
    }

    if (form.permissions.length < 1) {
        form.errors.permissions = '至少選擇一個權限';
        return;
    }

    const payload = {
        name: form.name,
        display_name: form.display_name,
        permissions: form.permissions,
    };

    const url = props.type === 'create'
        ? route('admin.role.store')
        : route('admin.role.update', props.role.id);

    const method = props.type === 'create' ? 'post' : 'put';

    const redirectUrl = route('admin.role');
    
    submitFormWithNotify({
        form,
        method,
        url,
        data: payload,
        redirectUrl,
    })
}

</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <!-- 角色 -->
        <div>
            <label class="block font-bold mb-1">
                角色代號 <span class="text-red-600">*</span>
            </label>
            <input v-model="form.name" type="text" class="form-input w-full" />
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                {{ form.errors.name }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">
                角色名稱 <span class="text-red-600">*</span>
            </label>
            <input v-model="form.display_name" type="text" class="form-input w-full" />
            <div v-if="form.errors.display_name" class="text-red-500 text-sm mt-1">
                {{ form.errors.display_name }}
            </div>
        </div>

        <!-- 權限選擇區塊 -->
        <div>
            <label class="block font-medium mb-2">
                權限 <span class="text-red-600">*</span>
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="(group, index) in permissionGroups" :key="index" class="border border-gray-400 rounded p-4">
                    <h3 class="font-semibold mb-2">{{ group.display_name }}</h3>
                    <div class="space-y-2">
                        <label
                            v-for="permission in group.permissions"
                            :key="permission.id"
                            class="flex items-center space-x-2"
                        >
                            <input
                                type="checkbox"
                                :value="permission.id"
                                v-model="form.permissions"
                                class="form-checkbox"
                            />
                            <span>{{ permission.display_name }}</span>
                        </label>
                    </div>
                </div>
            </div>
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
.form-checkbox {
    @apply w-4 h-4 text-blue-600 border-gray-300 rounded;
}
</style>