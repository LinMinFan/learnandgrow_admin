<script setup>
import get from 'lodash/get';
import { Link, usePage, useForm, router  } from '@inertiajs/vue3';
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { useTopGlobalNotify } from '@/Composables/useTopGlobalNotify'
import { submitFormWithNotify } from '@/Composables/formAxiosWithNotify'
import MediaModal from '@/Components/Media/MediaModal.vue'
import { useMediaPicker } from '@/Composables/Media/useMediaPicker'
import TipTapEditor from '@/Components/TipTap/TipTapEditor.vue'

const props = defineProps({
    type: {
        type: String,
        default: 'create'
    },
    categories: {
        type: Array,
        required: true
    },
    article: {
        type: Object,
        default: () => ({
            id: '',
            category_id: null,
            title: '',
            slug: '',
            excerpt: '',
            content: '',
            cover_image: '',
            status: 'draft',
            published_at: '',
            is_top: 0,
        })
    },
})

const { errorNotify } = useTopGlobalNotify()
const form = useForm({
    category_id: props.article.category_id ?? null,
    title: props.article.title || '',
    slug: props.article.slug || '',
    excerpt: props.article.excerpt || '',
    content: props.article.content || '',
    cover_image: props.article.cover_image || '',
    status: props.article.status || 'draft',
    is_top: props.article.is_top || 0,
})

const { 
    showMediaModal, 
    openMediaPicker, 
    handleSelectFromMedia, 
} = useMediaPicker()

function onMediaSelected(media, field) {
    const url = `${media.model.name}/${media.file_name}`

    if (field === 'cover_image') {
        form.cover_image = url
    }
}

function clearImage(field) {
    if (field === 'cover_image') {
        form.cover_image = ''
    }
}

const submit = () => {
    if (!form.category_id) {
        form.errors.category_id = '文章分類為必選';
        return;
    }

    if (!form.title.trim()) {
        form.errors.title = '文章標題為必填';
        return;
    }

    if (!form.slug.trim()) {
        form.errors.slug = '文章代號為必填';
        return;
    }

    const payload = {
        category_id: form.category_id?.id,
        title: form.title,
        slug: form.slug,
        excerpt: form.excerpt,
        content: form.content,
        cover_image: form.cover_image,
        status: form.status,
        published_at: form.published_at,
        is_top: form.is_top,
    };

    const url = props.type === 'create'
        ? route('post.article.store')
        : route('post.article.update', props.article.id);

    const method = props.type === 'create' ? 'post' : 'put';

    const redirectUrl = route('post.article');
    
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
                所屬分類 <span class="text-red-600">*</span>
            </label>
            <Multiselect
                v-model="form.category_id"
                :options="props.categories"
                :allow-empty="true"
                :clear-on-select="true"
                placeholder="請選擇分類"
                label="name"
                track-by="id"
                :custom-label="option => option.name"
                class="multiselect"
            />
            <div v-if="form.errors.category_id" class="text-red-500 text-sm mt-1">
                {{ form.errors.category_id }}
            </div>
        </div>

        <label class="block font-bold mb-1">文章主圖</label>
        <div class="flex space-x-2 items-center">
            <!-- 圖片預覽 -->
            <div class="w-24 h-24 border rounded overflow-hidden bg-gray-100 flex items-center justify-center">
                <img
                    :src="form.cover_image ? `/storage/upload/${form.cover_image}` : '/images/noimage.jpg'"
                    alt="圖片預覽"
                    class="object-cover w-full h-full cursor-pointer"
                    @click="() => openMediaPicker('cover_image', onMediaSelected)"
                />
            </div>

            <!-- 隱藏 input 仍然綁定資料，確保能送出 -->
            <input 
                v-model="form.cover_image" 
                type="hidden"
            />

            <button 
                type="button" 
                @click="() => openMediaPicker('cover_image', onMediaSelected)" 
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg shadow-sm border border-gray-300 transition"
            >
                選擇圖片
            </button>
            <!-- 清除圖片按鈕 -->
            <button 
                type="button" 
                @click="() => clearImage('cover_image')" 
                class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg shadow-sm border border-red-300 transition"
            >
                移除圖片
            </button>
        </div>

        <div>
            <label class="block font-bold mb-1">
                文章標題 <span class="text-red-600">*</span>
            </label>
            <input v-model="form.title" type="text" class="form-input w-full" />
            <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">
                {{ form.errors.title }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">
                文章代號 <span class="text-red-600">*</span>
            </label>
            <input v-model="form.slug" type="text" class="form-input w-full" />
            <div v-if="form.errors.slug" class="text-red-500 text-sm mt-1">
                {{ form.errors.slug }}
            </div>
        </div>
        <div>
            <label class="block font-bold mb-1">
                文章摘要
            </label>
            <input v-model="form.excerpt" type="text" class="form-input w-full" />
            <div v-if="form.errors.excerpt" class="text-red-500 text-sm mt-1">
                {{ form.errors.excerpt }}
            </div>
        </div>

        <!-- 使用 TipTap 編輯器替換原本的 textarea -->
        <div>
            <label class="block font-bold mb-1">
                文章內容
            </label>
            <!-- <TipTapEditor
                v-model="form.content" 
                placeholder="開始輸入文章內容..."
                class="min-h-[300px]"
            /> -->
            <textarea
                v-model="form.content"
                placeholder="請輸入文章內容"
                rows="10"
                class="form-input w-full"
            >
            </textarea>
            <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">
                {{ form.errors.content }}
            </div>
        </div>

        <div>
            <label class="block font-bold mb-1">
                文章狀態 <span class="text-red-600">*</span>
            </label>
            <select v-model="form.status" class="form-select w-full">
                <option value="" disabled>請選擇狀態</option>
                <option value="draft">
                    草稿
                </option>
                <option value="published">
                    發佈
                </option>
            </select>
            <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">
                {{ form.errors.status }}
            </div>
        </div>

        <div>
            <label class="block font-bold mb-1">
                是否置頂
            </label>
            <label class="inline-flex items-center space-x-2">
                <input v-model="form.is_top" type="checkbox" class="form-checkbox" />
                <span>置頂</span>
            </label>
            <div v-if="form.errors.is_top" class="text-red-500 text-sm mt-1">
                {{ form.errors.is_top }}
            </div>
        </div>

        <div class="flex justify-end space-x-2">
            <Link :href="route('post.article')" class="btn">取消</Link>
            <button type="submit" class="btn btn-primary" :disabled="form.processing">儲存</button>
        </div>
    </form>
    <!-- MediaModal：只掛載一個 -->
    <MediaModal
        v-model:show="showMediaModal"
        @select="handleSelectFromMedia"
    />
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