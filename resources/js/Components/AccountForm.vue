<script setup>
import get from 'lodash/get';
import { computed } from 'vue'
import { Link, usePage, useForm, router  } from '@inertiajs/vue3';
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { useTopGlobalNotify } from '@/Composables/useTopGlobalNotify'
import { submitFormWithNotify } from '@/Composables/formAxiosWithNotify'

const props = defineProps({
    type: {
        type: String,
        default: 'create'
    },
    user: {
        type: Object,
        default: () => ({
            id: '',
            name: '',
            email: '',
            password: '',
        })
    },
    roles: {
        type: Object,
        required: true
    },
})

const { errorNotify } = useTopGlobalNotify()
const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    password: props.user.password || '',
    password_confirmation: '',
    roles: props.user.roles ?? []
})

const roleOptions = computed(() => Object.values(props.roles))

const submit = () => {
    form.clearErrors();
    
    if (!form.name.trim()) {
        form.errors.name = '帳號名稱為必填';
        return;
    }
    
    if (!form.email.trim()) {
        form.errors.email = '帳號信箱為必填';
        return;
    }

    // 密碼檢查依 type 動態調整
    if (props.type === 'create') {
        if (!form.password.trim()) {
            form.errors.password = '密碼為必填';
            return;
        }

        if (form.password !== form.password_confirmation) {
            form.errors.password_confirmation = '密碼與確認密碼不一致';
            return;
        }
    }

    if (props.type === 'edit') {
        if (form.password.trim() && form.password !== form.password_confirmation) {
            form.errors.password_confirmation = '密碼與確認密碼不一致';
            return;
        }
    }

    const payload = {
        name: form.name,
        email: form.email,
        roles: form.roles.map(role => role.id),
    };

    if (form.password.trim()) {
        payload.password = form.password;
        payload.password_confirmation = form.password_confirmation;
    }

    const url = props.type === 'create'
        ? route('admin.account.store')
        : route('admin.account.update', props.user.id);

    const method = props.type === 'create' ? 'post' : 'put';

    const redirectUrl = route('admin.account');
    
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
                帳號名稱 <span class="text-red-600">*</span>
            </label>
            <input v-model="form.name" type="text" class="form-input w-full" />
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                {{ form.errors.name }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">
                帳號信箱 <span class="text-red-600">*</span>
            </label>
            <input v-model="form.email" type="text" class="form-input w-full" :readonly="type === 'edit'" />
            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                {{ form.errors.email }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">
                帳號密碼 
                <span class="text-red-600" v-if="type === 'create'">*</span>
                <span class="text-gray-500 text-sm" v-else>（若不修改則留空）</span>
            </label>
            <input v-model="form.password" type="password" class="form-input w-full" />
            <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                {{ form.errors.password }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">
                確認密碼 
                <span class="text-red-600" v-if="type === 'create'">*</span>
            </label>
            <input v-model="form.password_confirmation" type="password" class="form-input w-full" />
            <div v-if="form.errors.password_confirmation" class="text-red-500 text-sm mt-1">
                {{ form.errors.password_confirmation }}
            </div>
        </div>

        <!-- 角色選擇 -->
        <div>
            <label class="block font-bold mb-1">
                選擇角色
            </label>
            <Multiselect
                v-model="form.roles"
                :options="roleOptions"
                :multiple="true"
                :close-on-select="false"
                :clear-on-select="false"
                :preserve-search="true"
                placeholder="請選擇角色"
                label="display_name"
                track-by="id"
                class="multiselect"
            />
            <div v-if="form.errors.roles" class="text-red-500 text-sm mt-1">
                {{ form.errors.roles }}
            </div>
        </div>

        <div class="flex justify-end space-x-2">
            <Link :href="route('admin.account')" class="btn">取消</Link>
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