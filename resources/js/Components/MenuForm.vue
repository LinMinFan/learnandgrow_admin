<script setup>
import get from 'lodash/get';
import { computed } from 'vue'
import { Link, usePage, useForm, router  } from '@inertiajs/vue3';
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { useTopGlobalNotify } from '@/Composables/useTopGlobalNotify'
import { submitFormWithNotify } from '@/Composables/formAxiosWithNotify'
import { iconList } from '@/Composables/iconList'

const props = defineProps({
    type: {
        type: String,
        default: 'create'
    },
    menu: {
        type: Object,
        default: () => ({
            id: '',
            title: '',
            icon: null,
            route: null,
            parent_id: null,
            is_active: 0,
            permission_id: null,
        })
    },
    parents: {
        type: Object,
        required: true
    },
    permissions: {
        type: Object,
        required: true
    },
})

const { errorNotify } = useTopGlobalNotify()
const iconOptions = iconList;
const form = useForm({
    title: props.menu.title || '',
    icon: props.menu.icon || null,
    route: props.menu.route || null,
    parent_id: props.menu.parent_id ?? null,
    is_active: props.menu.is_active == 1,
    permission_id: props.menu.permission_id ?? null,
})

const submit = () => {
    form.clearErrors();
    
    if (!form.title.trim()) {
        form.errors.title = '選單名稱為必填';
        return;
    }

    const payload = {
        title: form.title,
        icon: form.icon || null,
        route: form.route || null,
        parent_id: form.parent_id?.id || null,
        is_active: form.is_active ? 1 : 0,
        permission_id: form.permission_id?.id || null,
    };

    const url = props.type === 'create'
        ? route('system.menu.store')
        : route('system.menu.update', props.menu.id);

    const method = props.type === 'create' ? 'post' : 'put';

    const redirectUrl = route('system.menu');
    
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
        <!-- 父選單選擇 -->
        <div>
            <label class="block font-bold mb-1">
                選擇父選單
            </label>
            <Multiselect
                v-model="form.parent_id"
                :options="props.parents"
                :allow-empty="true"
                :clear-on-select="true"
                placeholder="請選擇父選單"
                label="title"
                track-by="id"
                :custom-label="option => option.title"
                class="multiselect"
            />
            <div v-if="form.errors.parent_id" class="text-red-500 text-sm mt-1">
                {{ form.errors.parent_id }}
            </div>
        </div>
        <!-- 選單 -->
        <div>
            <label class="block font-bold mb-1">
                選單名稱 <span class="text-red-600">*</span>
            </label>
            <input v-model="form.title" type="text" class="form-input w-full" />
            <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">
                {{ form.errors.title }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">
                選單圖示
            </label>
            <Multiselect
                v-model="form.icon"
                :options="iconOptions"
                placeholder="請選擇圖示或輸入"
                :searchable="true"
                :allow-empty="true"
                :internal-search="true"
                :show-labels="false"
                class="multiselect"
            >
                <template #option="{ option }">
                    <span class="flex items-center space-x-2">
                        <i :class="option"></i>
                        <span>{{ option }}</span>
                    </span>
                </template>
                <template #singleLabel="{ option }">
                    <span class="flex items-center space-x-2">
                        <i :class="option"></i>
                        <span>{{ option }}</span>
                    </span>
                </template>
            </Multiselect>
            <div v-if="form.errors.icon" class="text-red-500 text-sm mt-1">
                {{ form.errors.icon }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">
                選單路由
            </label>
            <input v-model="form.route" type="text" class="form-input w-full" />
            <div v-if="form.errors.route" class="text-red-500 text-sm mt-1">
                {{ form.errors.route }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">
                是否顯示 <span class="text-red-600">*</span>
            </label>
            <label class="inline-flex items-center space-x-2">
                <input v-model="form.is_active" type="checkbox" class="form-checkbox" />
                <span>顯示</span>
            </label>
            <div v-if="form.errors.is_active" class="text-red-500 text-sm mt-1">
                {{ form.errors.is_active }}
            </div>
        </div>
        <!-- 綁定權限 -->
        <div>
            <label class="block font-bold mb-1">
                綁定權限
            </label>
            <Multiselect
                v-model="form.permission_id"
                :options="props.permissions"
                :allow-empty="true"
                :clear-on-select="true"
                placeholder="請選擇權限"
                label="display_name"
                track-by="id"
                :custom-label="option => option.display_name"
                class="multiselect"
            />
            <div v-if="form.errors.permission_id" class="text-red-500 text-sm mt-1">
                {{ form.errors.permission_id }}
            </div>
        </div>
        <div class="flex justify-end space-x-2">
            <Link :href="route('system.menu')" class="btn">取消</Link>
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
    @apply h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded;
}
</style>