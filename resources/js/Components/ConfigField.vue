<script setup>
import { ref } from 'vue'
import MediaModal from '@/Components/Media/MediaModal.vue'
import { useMediaPicker } from '@/Composables/Media/useMediaPicker'

const props = defineProps({
    fieldKey: String,
    fieldValue: Object,
    fieldType: String,
    fieldLabel: String,
    fieldPlaceholder: {
        type: String,
        default: null
    },
    modelValue: Object,
})

const { 
    showMediaModal, 
    openMediaPicker, 
    handleSelectFromMedia, 
    clearImageField 
} = useMediaPicker()

const emit = defineEmits(['update:modelValue'])

function onMediaSelected(media, field) {
    const key = props.fieldKey
    const subKey = field
    const url = `${media.model.name}/${media.file_name}`

    if (key && subKey && props.modelValue[key]) {
        props.modelValue[key][subKey] = url
        emit('update:modelValue', props.modelValue)
    }
}

function clearImage(field) {
    clearImageField(props.modelValue, props.fieldKey, field)
    emit('update:modelValue', props.modelValue)
}

</script>

<template>
    <div class="mb-4">
        <label class="block font-bold mb-1">{{ fieldLabel }}</label>

        <!-- image、favicon 共用這段 -->
        <template v-if="['image', 'favicon'].includes(fieldType)">
            <div class="flex space-x-2 items-center">
                <!-- 圖片預覽 -->
                <div class="w-24 h-24 border rounded overflow-hidden bg-gray-100 flex items-center justify-center">
                    <img
                        :src="modelValue[fieldKey].url ? `/storage/upload/${modelValue[fieldKey].url}` : '/images/noimage.jpg'"
                        alt="圖片預覽"
                        class="object-cover w-full h-full cursor-pointer"
                        @click="() => openMediaPicker('url', onMediaSelected)"
                    />
                </div>

                <!-- 隱藏 input 仍然綁定資料，確保能送出 -->
                <input 
                    v-model="modelValue[fieldKey].url" 
                    type="hidden"
                />

                <button 
                    type="button" 
                    @click="() => openMediaPicker('url', onMediaSelected)" 
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg shadow-sm border border-gray-300 transition"
                >
                    選擇圖片
                </button>
                <!-- 清除圖片按鈕 -->
                <button 
                    type="button" 
                    @click="() => clearImage('url')" 
                    class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg shadow-sm border border-red-300 transition"
                >
                    移除圖片
                </button>
            </div>
        </template>

        <!-- 純文字 -->
        <input 
            v-else-if="fieldType === 'text'" 
            v-model="modelValue[fieldKey].text" 
            type="text" 
            class="form-input w-full" 
            :placeholder="fieldPlaceholder || ''"
        />

        <!-- 多行文字 -->
        <textarea 
            v-else-if="fieldType === 'textarea'" 
            v-model="modelValue[fieldKey].text" 
            class="form-textarea w-full"
            rows="3"
            :placeholder="fieldPlaceholder || ''" 
        ></textarea>

        <!-- checkbox 開關 -->
        <div 
            v-else-if="fieldType === 'switch'" 
            class="flex items-center space-x-2"
        >
            <input 
                v-model="modelValue[fieldKey].enabled" 
                type="checkbox" 
                class="form-checkbox" 
            />
            <span>啟用維護模式</span>
        </div>

        <!-- OG Meta -->
        <div v-else-if="fieldType === 'og-meta'" class="space-y-2">
            <input v-model="modelValue[fieldKey].title" type="text" placeholder="OG 標題" class="form-input w-full" />
            <textarea v-model="modelValue[fieldKey].description" placeholder="OG 描述" class="form-textarea w-full" rows="2"></textarea>
            <div class="flex space-x-2 items-center">
                <!-- 預覽圖片 -->
                <div class="w-24 h-24 border rounded overflow-hidden bg-gray-100 flex items-center justify-center">
                    <img
                        :src="modelValue[fieldKey].image ? `/storage/upload/${modelValue[fieldKey].image}` : '/images/noimage.jpg'"
                        alt="OG 圖片預覽"
                        class="object-cover w-full h-full cursor-pointer"
                        @click="() => openMediaPicker('image', onMediaSelected)"
                    />
                </div>

                <!-- 隱藏 input 確保圖片路徑仍會送出 -->
                <input 
                    v-model="modelValue[fieldKey].image" 
                    type="hidden"
                />

                <button 
                    type="button" 
                    @click="() => openMediaPicker('image', onMediaSelected)"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg shadow-sm border border-gray-300 transition"
                >
                    選擇圖片
                </button>
                <!-- 清除圖片按鈕 -->
                <button 
                    type="button" 
                    @click="() => clearImage('image')" 
                    class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg shadow-sm border border-red-300 transition"
                >
                    移除圖片
                </button>
            </div>
        </div>

        <!-- MediaModal：只掛載一個 -->
        <MediaModal
            v-model:show="showMediaModal"
            @select="handleSelectFromMedia"
        />
    </div>
</template>

