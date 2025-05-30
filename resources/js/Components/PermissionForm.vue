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
    permissionGroups: {
        type: Object,
        required: true
    },
    permission: {
        type: Object,
        default: () => ({
            id: '',
            name: '',
            display_name: '',
            permission_group_id: ''
        })
    },
})

const { errorNotify } = useTopGlobalNotify()
const form = useForm({
    permission_group_id: props.permission.permission_group_id || '',
    name: props.permission.name || '',
    display_name: props.permission.display_name || ''
})

const submit = () => {
    if (!form.permission_group_id) {
        form.errors.permission_group_id = '權限群組為必選';
        return;
    }

    if (!form.name.trim()) {
        form.errors.name = '權限代號為必填';
        return;
    }

    if (!form.display_name.trim()) {
        form.errors.display_name = '權限名稱為必填';
        return;
    }

    const payload = {
        name: form.name,
        display_name: form.display_name,
        permission_group_id: form.permission_group_id,
    };

    const url = props.type === 'create'
        ? route('admin.permission.store')
        : route('admin.permission.update', props.permission.id);

    const method = props.type === 'create' ? 'post' : 'put';

    const redirectUrl = route('admin.permission');
    
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
        <div>
            <label class="block font-bold mb-1">
                所屬群組 <span class="text-red-600">*</span>
            </label>
            <select v-model="form.permission_group_id" class="form-select w-full">
                <option value="" disabled>請選擇群組</option>
                <option v-for="group in permissionGroups" :key="group.id" :value="group.id">
                    {{ group.display_name }}
                </option>
            </select>
            <div v-if="form.errors.permission_group_id" class="text-red-500 text-sm mt-1">
                {{ form.errors.permission_group_id }}
            </div>
        </div>

        <div>
            <label class="block font-bold mb-1">
                權限代號 <span class="text-red-600">*</span>
            </label>
            <input v-model="form.name" type="text" class="form-input w-full" />
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                {{ form.errors.name }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">顯示名稱</label>
            <input v-model="form.display_name" type="text" class="form-input w-full" />
            <div v-if="form.errors.display_name" class="text-red-500 text-sm mt-1">
                {{ form.errors.display_name }}
            </div>
        </div>

        <div class="flex justify-end space-x-2">
            <Link :href="route('admin.permission')" class="btn">取消</Link>
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