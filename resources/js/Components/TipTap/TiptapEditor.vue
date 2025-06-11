<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Editor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import Table from '@tiptap/extension-table'
import TableRow from '@tiptap/extension-table-row'
import TableCell from '@tiptap/extension-table-cell'
import TableHeader from '@tiptap/extension-table-header'

const imageSrc = ref('')

const editor = new Editor({
    content: '<p>請輸入文章內容...</p>',
    extensions: [
        StarterKit,
        Image,
        Table.configure({ resizable: true }),
        TableRow,
        TableCell,
        TableHeader,
    ],
})

onBeforeUnmount(() => {
    editor.destroy()
})
</script>

<template>
    <div>
        <!-- 範例按鈕 -->
        <div class="mt-2 space-x-2">
            <button @click="editor.chain().focus().toggleBold().run()">粗體</button>
            <button @click="editor.chain().focus().toggleItalic().run()">斜體</button>
            <button @click="editor.chain().focus().setImage({ src: imageSrc }).run()">插入圖片</button>
            <button
                @click="editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()">插入表格</button>
        </div>

        <!-- 圖片 URL 手動輸入或配合媒體庫 -->
        <input v-model="imageSrc" placeholder="圖片路徑" class="border mt-2" />
        <editor-content :editor="editor" />
    </div>
</template>
