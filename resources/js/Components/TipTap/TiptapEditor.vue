<!-- TipTapEditor.vue -->
<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { Editor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import Link from '@tiptap/extension-link'
import Placeholder from '@tiptap/extension-placeholder'
import MediaModal from '@/Components/Media/MediaModal.vue'
import { useMediaPicker } from '@/Composables/Media/useMediaPicker'

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: '開始輸入內容...'
    },
    editable: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['update:modelValue'])

const editor = ref(null)

const {
    showMediaModal,
    openMediaPicker,
    handleSelectFromMedia,
} = useMediaPicker()

onMounted(() => {
    editor.value = new Editor({
        content: props.modelValue,
        editable: props.editable,
        extensions: [
            StarterKit.configure({
                // 確保所有功能都開啟
                heading: {
                    levels: [1, 2, 3, 4, 5, 6],
                },
                bulletList: {
                    keepMarks: true,
                    keepAttributes: false,
                },
                orderedList: {
                    keepMarks: true,
                    keepAttributes: false,
                },
                blockquote: {
                    HTMLAttributes: {
                        class: 'editor-blockquote',
                    },
                },
            }),
            Image.configure({
                inline: false,
                allowBase64: true,
                HTMLAttributes: {
                    class: 'editor-image',
                },
            }),
            Link.configure({
                openOnClick: false,
                HTMLAttributes: {
                    class: 'editor-link',
                },
            }),
            Placeholder.configure({
                placeholder: props.placeholder,
            }),
        ],
        onUpdate: ({ editor }) => {
            emit('update:modelValue', editor.getHTML())
        },
        editorProps: {
            attributes: {
                class: 'prose prose-sm sm:prose lg:prose-lg xl:prose-2xl mx-auto focus:outline-none min-h-[200px] p-4',
            }
        }
    })
})

onBeforeUnmount(() => {
    if (editor.value) {
        editor.value.destroy()
    }
})

watch(() => props.modelValue, (newValue) => {
    if (editor.value && editor.value.getHTML() !== newValue) {
        editor.value.commands.setContent(newValue, false)
    }
})

watch(() => props.editable, (newValue) => {
    if (editor.value) {
        editor.value.setEditable(newValue)
    }
})

watch(() => props.placeholder, (newValue) => {
    const placeholderExt = editor.value.extensionManager.extensions.find(ext => ext.name === 'placeholder');
    if (placeholderExt) {
        placeholderExt.options.placeholder = newValue;
    }
})

// 添加圖片 - 使用 MediaModal
const addImageFromModal = () => {
    openMediaPicker('editor_image', onImageSelected)
}

// 處理媒體選擇
const onImageSelected = (media, field) => {
    if (media && editor.value) {
        const url = `/storage/upload/${media.model.name}/${media.file_name}`
        editor.value.chain().focus().setImage({ 
            src: url,
            alt: media.alt || media.file_name || '圖片'
        }).run()
    }
}

// 傳統的 URL 輸入方式（備用）
const addImageByUrl = () => {
    const url = window.prompt('請輸入圖片 URL:')
    if (url && editor.value) {
        editor.value.chain().focus().setImage({ src: url }).run()
    }
}

// 添加連結
const addLink = () => {
    const previousUrl = editor.value?.getAttributes('link').href || ''
    const url = window.prompt('連結 URL:', previousUrl)

    if (url === null) {
        return
    }

    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
        return
    }

    editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
}

// 檢查按鈕是否應該啟用
const isActive = (name, attributes = {}) => {
    if (!editor.value) return false
    return editor.value.isActive(name, attributes)
}

// 執行命令的輔助函數
const runCommand = (command) => {
    if (editor.value) {
        command(editor.value.chain().focus())
    }
}
</script>

<template>
    <div class="tiptap-editor">
        <!-- 工具列 -->
        <div class="toolbar" v-if="editor">
            <!-- 文字格式 -->
            <button 
                @click="runCommand((chain) => chain.toggleBold().run())" 
                :class="{ 'is-active': isActive('bold') }"
                type="button" 
                class="toolbar-btn"
                title="粗體"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 4h8a4 4 0 0 1 4 4 3.5 3.5 0 0 1-1.5 2.92A4 4 0 0 1 18 15a4 4 0 0 1-4 4H6V4zm2 2v5h6a2 2 0 1 0 0-4H8zm0 7v5h6a2 2 0 1 0 0-4H8z" />
                </svg>
            </button>

            <button 
                @click="runCommand((chain) => chain.toggleItalic().run())" 
                :class="{ 'is-active': isActive('italic') }"
                type="button" 
                class="toolbar-btn"
                title="斜體"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 4v3h2.21l-3.42 8H6v3h8v-3h-2.21l3.42-8H18V4h-8z" />
                </svg>
            </button>

            <button 
                @click="runCommand((chain) => chain.toggleStrike().run())" 
                :class="{ 'is-active': isActive('strike') }"
                type="button" 
                class="toolbar-btn"
                title="刪除線"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.154 14c.23.516.346 1.09.346 1.72 0 1.342-.524 2.392-1.571 3.147C14.88 19.622 13.433 20 11.586 20c-1.64 0-3.263-.381-4.87-1.144V16.6c1.52.877 3.075 1.316 4.666 1.316 2.551 0 3.83-.732 3.839-2.197a2.21 2.21 0 0 0-.648-1.603l-.12-.117H3v-2h18v2h-3.846zM9.5 11.5c0 .44-.09.84-.27 1.2h-1.8c-.18-.36-.27-.76-.27-1.2 0-.44.09-.84.27-1.2h1.8c.18.36.27.76.27 1.2zm7 0c0 .44-.09.84-.27 1.2h-1.8c-.18-.36-.27-.76-.27-1.2 0-.44.09-.84.27-1.2h1.8c.18.36.27.76.27 1.2z" />
                </svg>
            </button>

            <div class="toolbar-divider"></div>

            <!-- 標題 -->
            <button 
                @click="runCommand((chain) => chain.toggleHeading({ level: 1 }).run())"
                :class="{ 'is-active': isActive('heading', { level: 1 }) }" 
                type="button" 
                class="toolbar-btn"
                title="標題 1"
            >
                H1
            </button>

            <button 
                @click="runCommand((chain) => chain.toggleHeading({ level: 2 }).run())"
                :class="{ 'is-active': isActive('heading', { level: 2 }) }" 
                type="button" 
                class="toolbar-btn"
                title="標題 2"
            >
                H2
            </button>

            <button 
                @click="runCommand((chain) => chain.toggleHeading({ level: 3 }).run())"
                :class="{ 'is-active': isActive('heading', { level: 3 }) }" 
                type="button" 
                class="toolbar-btn"
                title="標題 3"
            >
                H3
            </button>

            <div class="toolbar-divider"></div>

            <!-- 列表 -->
            <button 
                @click="runCommand((chain) => chain.toggleBulletList().run())"
                :class="{ 'is-active': isActive('bulletList') }" 
                type="button" 
                class="toolbar-btn"
                title="無序列表"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 5h14v2H7zm0 8v-2h14v2zm0 6v-2h14v2zM3 4.5A1.5 1.5 0 1 1 4.5 6 1.5 1.5 0 0 1 3 4.5zm0 6A1.5 1.5 0 1 1 4.5 12 1.5 1.5 0 0 1 3 10.5zm0 6A1.5 1.5 0 1 1 4.5 18 1.5 1.5 0 0 1 3 16.5z" />
                </svg>
            </button>

            <button 
                @click="runCommand((chain) => chain.toggleOrderedList().run())"
                :class="{ 'is-active': isActive('orderedList') }" 
                type="button" 
                class="toolbar-btn"
                title="有序列表"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M2 17h2v.5H3v1h1v.5H2v1h3v-4H2v1zm1-9h1V4H2v1h1v3zm-1 3h1.8L2 13.1v.9h3v-1H3.2L5 10.9V10H2v1zm5-6v2h14V5H7zm0 14h14v-2H7v2zm0-6h14v-2H7v2z" />
                </svg>
            </button>

            <div class="toolbar-divider"></div>

            <!-- 引用和分隔線 -->
            <button 
                @click="runCommand((chain) => chain.toggleBlockquote().run())"
                :class="{ 'is-active': isActive('blockquote') }" 
                type="button" 
                class="toolbar-btn"
                title="引用"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 17h3l2-4V7h-6v6h3M6 17h3l2-4V7H5v6h3l-2 4z" />
                </svg>
            </button>

            <button 
                @click="runCommand((chain) => chain.setHorizontalRule().run())" 
                type="button" 
                class="toolbar-btn"
                title="分隔線"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13H5v-2h14v2z" />
                </svg>
            </button>

            <div class="toolbar-divider"></div>

            <!-- 媒體 -->
            <button 
                @click="addImageFromModal" 
                type="button" 
                class="toolbar-btn"
                title="從媒體庫添加圖片"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M5 8.5c0-.828.672-1.5 1.5-1.5s1.5.672 1.5 1.5c0 .829-.672 1.5-1.5 1.5S5 9.329 5 8.5zM9 11l3 3.5 2.5-3 4.5 6H5l4-6.5z" />
                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM5 19l3.5-4.5 2.5 3.01L14.5 14l4.5 5H5z" />
                </svg>
            </button>

            <button 
                @click="addImageByUrl" 
                type="button" 
                class="toolbar-btn"
                title="通過 URL 添加圖片"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </button>

            <button 
                @click="addLink" 
                :class="{ 'is-active': isActive('link') }" 
                type="button" 
                class="toolbar-btn"
                title="添加連結"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z" />
                </svg>
            </button>
        </div>

        <!-- 編輯器內容區域 -->
        <div class="editor-content">
            <editor-content :editor="editor" />
        </div>

        <!-- 媒體選擇模態框 -->
        <MediaModal
            v-model:show="showMediaModal"
            @select="handleSelectFromMedia"
        />
    </div>
</template>

<style lang="postcss">
.tiptap-editor {
    @apply border border-gray-300 rounded-lg overflow-hidden bg-white;
}

.toolbar {
    @apply flex items-center gap-1 p-2 border-b border-gray-200 bg-gray-50;
    flex-wrap: wrap;
}

.toolbar-btn {
    @apply p-2 rounded hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center;
    min-width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    cursor: pointer;
    color: #374151;
    font-size: 12px;
    font-weight: 600;
}

.toolbar-btn:hover {
    @apply bg-gray-200;
}

.toolbar-btn.is-active {
    @apply bg-blue-100 text-blue-600;
}

.toolbar-divider {
    @apply w-px h-6 bg-gray-300 mx-1;
}

.editor-content {
    @apply min-h-[200px];
}

/* TipTap 編輯器樣式 */
:deep(.ProseMirror) {
    @apply outline-none min-h-[200px] p-4;
}

/* Placeholder 樣式 */
:deep(.ProseMirror p.is-editor-empty:first-child::before) {
    content: attr(data-placeholder);
    @apply text-gray-400 pointer-events-none;
    float: left;
    height: 0;
}

/* 圖片樣式 */
:deep(.editor-image) {
    @apply max-w-full h-auto rounded-lg shadow-sm my-4;
    display: block;
}

/* 連結樣式 */
:deep(.editor-link) {
    @apply text-blue-600 underline hover:text-blue-800;
}

/* 標題樣式 */
:deep(.ProseMirror h1) {
    @apply text-3xl font-bold mt-6 mb-4 first:mt-0;
}

:deep(.ProseMirror h2) {
    @apply text-2xl font-bold mt-5 mb-3 first:mt-0;
}

:deep(.ProseMirror h3) {
    @apply text-xl font-bold mt-4 mb-2 first:mt-0;
}

:deep(.ProseMirror h4) {
    @apply text-lg font-bold mt-3 mb-2 first:mt-0;
}

:deep(.ProseMirror h5) {
    @apply text-base font-bold mt-3 mb-2 first:mt-0;
}

:deep(.ProseMirror h6) {
    @apply text-sm font-bold mt-3 mb-2 first:mt-0;
}

/* 引用樣式 */
:deep(.ProseMirror blockquote),
:deep(.editor-blockquote) {
    @apply border-l-4 border-blue-400 pl-4 italic text-gray-700 my-4 bg-blue-50 py-2;
}

/* 列表樣式 */
:deep(.ProseMirror ul) {
    @apply list-disc ml-6 my-4;
}

:deep(.ProseMirror ol) {
    @apply list-decimal ml-6 my-4;
}

:deep(.ProseMirror li) {
    @apply my-1;
}

:deep(.ProseMirror li p) {
    @apply my-0;
}

/* 分隔線樣式 */
:deep(.ProseMirror hr) {
    @apply border-0 border-t-2 border-gray-300 my-6;
}

/* 代碼樣式 */
:deep(.ProseMirror code) {
    @apply bg-gray-100 px-2 py-1 rounded text-sm font-mono text-red-600;
}

:deep(.ProseMirror pre) {
    @apply bg-gray-100 p-4 rounded-lg overflow-x-auto my-4;
}

:deep(.ProseMirror pre code) {
    @apply bg-transparent p-0 text-gray-800;
}

/* 段落間距 */
:deep(.ProseMirror p) {
    @apply my-3 first:mt-0 last:mb-0;
}

/* 選中樣式 */
:deep(.ProseMirror .ProseMirror-selectednode) {
    @apply ring-2 ring-blue-400 ring-opacity-50;
}
</style>