<script setup>
import get from 'lodash/get';
import { computed, onMounted } from 'vue'
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
    category: {
        type: Object,
        default: () => ({
            id: '',
            name: '',
            slug: '',
            description: '',
            parent_id: null,
            is_active: 0,
        })
    },
    parents: {
        type: Array,
        required: true
    },
})

const { errorNotify } = useTopGlobalNotify()
const form = useForm({
    name: props.category.name || '',
    slug: props.category.slug || '',
    description: props.category.description || '',
    parent_id: props.category.parent_id ?? null,
    is_active: props.category.is_active == 1,
})

const submit = () => {
    form.clearErrors();
    
    if (!form.name.trim()) {
        form.errors.name = '分類名稱為必填';
        return;
    }
    
    if (!form.slug.trim()) {
        form.errors.slug = '分類代號為必填';
        return;
    }
    
    if (form.parent_id == null) {
        form.errors.parent_id = '父分類為必選';
        return;
    }

    const payload = {
        name: form.name,
        slug: form.slug,
        description: form.description || '',
        parent_id: form.parent_id.id,
        is_active: form.is_active ? 1 : 0,
    };

    const url = props.type === 'create'
        ? route('post.category.store')
        : route('post.category.update', props.category.id);

    const method = props.type === 'create' ? 'post' : 'put';

    const redirectUrl = route('post.category');
    
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
        <!-- 父分類選擇 -->
        <div>
            <label class="block font-bold mb-1">
                選擇父分類 <span class="text-red-600">*</span>
            </label>
            <Multiselect
                v-model="form.parent_id"
                :options="props.parents"
                :allow-empty="true"
                :clear-on-select="true"
                placeholder="請選擇父分類"
                label="name"
                track-by="id"
                :custom-label="option => option.name"
                class="multiselect"
            />
            <div v-if="form.errors.parent_id" class="text-red-500 text-sm mt-1">
                {{ form.errors.parent_id }}
            </div>
        </div>
        <!-- 分類 -->
        <div>
            <label class="block font-bold mb-1">
                分類名稱 <span class="text-red-600">*</span>
            </label>
            <input v-model="form.name" type="text" class="form-input w-full" />
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                {{ form.errors.name }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">
                分類代號 <span class="text-red-600">*</span>
            </label>
            <input v-model="form.slug" type="text" class="form-input w-full" />
            <div v-if="form.errors.slug" class="text-red-500 text-sm mt-1">
                {{ form.errors.slug }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">
                分類說明
            </label>
            <input v-model="form.description" type="text" class="form-input w-full" />
            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                {{ form.errors.description }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">
                是否顯示
            </label>
            <label class="inline-flex items-center space-x-2">
                <input v-model="form.is_active" type="checkbox" class="form-checkbox" />
                <span>顯示</span>
            </label>
            <div v-if="form.errors.is_active" class="text-red-500 text-sm mt-1">
                {{ form.errors.is_active }}
            </div>
        </div>
        <div class="flex justify-end space-x-2">
            <Link :href="route('post.category')" class="btn">取消</Link>
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