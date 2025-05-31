<script setup>
defineProps({
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

defineEmits(['update:modelValue'])
</script>

<template>
    <div class="mb-4">
        <label class="block font-bold mb-1">{{ fieldLabel }}</label>

        <input 
            v-if="fieldType === 'text'" 
            v-model="modelValue[fieldKey].text" 
            type="text" 
            class="form-input w-full" 
            :placeholder="fieldPlaceholder || ''"
        />

        <textarea 
            v-else-if="fieldType === 'textarea'" 
            v-model="modelValue[fieldKey].text" 
            class="form-textarea w-full"
            rows="3"
            :placeholder="fieldPlaceholder || ''"
        >
        </textarea>

        <div 
            v-else-if="fieldType === 'favicon'" 
            class="space-y-2"
        >
            <input 
                v-model="modelValue[fieldKey].url" 
                type="text" 
                placeholder="網站圖標 URL" 
                class="form-input w-full" 
            />
        </div>

        <div 
            v-else-if="fieldType === 'image'" 
            class="space-y-2"
        >
            <input 
                v-model="modelValue[fieldKey].url" 
                type="text" 
                :placeholder="fieldPlaceholder || ''"
                class="form-input w-full" 
            />
        </div>

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

        <div v-else-if="fieldType === 'og-meta'" class="space-y-2">
            <input v-model="modelValue[fieldKey].title" type="text" placeholder="OG 標題" class="form-input w-full" />
            <textarea v-model="modelValue[fieldKey].description" placeholder="OG 描述" class="form-textarea w-full"
                rows="2"></textarea>
            <input v-model="modelValue[fieldKey].image" type="text" placeholder="OG 圖片 URL" class="form-input w-full" />
        </div>
    </div>
</template>
