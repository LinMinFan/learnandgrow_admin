<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import { Link, usePage  } from '@inertiajs/vue3';

const props = defineProps({ parents: Array })

const form = useForm({
  title: '',
  icon: '',
  route: '',
  parent_id: null,
  sort: 0,
})

function submit() {
  form.post('/system/menus')
}
</script>

<template>
  <Head title="新增選單" />

  <div>
    <h1 class="text-2xl font-bold mb-4">新增選單</h1>

    <form @submit.prevent="submit" class="space-y-4 bg-white p-6 rounded shadow">
      <div>
        <label class="block text-sm">名稱</label>
        <input v-model="form.title" class="form-input w-full" />
      </div>

      <div>
        <label class="block text-sm">圖示 (class)</label>
        <input v-model="form.icon" class="form-input w-full" />
      </div>

      <div>
        <label class="block text-sm">路由</label>
        <input v-model="form.route" class="form-input w-full" />
      </div>

      <div>
        <label class="block text-sm">父層選單</label>
        <select v-model="form.parent_id" class="form-select w-full">
          <option :value="null">無</option>
          <option v-for="item in parents" :value="item.id">{{ item.title }}</option>
        </select>
      </div>

      <div>
        <label class="block text-sm">排序</label>
        <input type="number" v-model="form.sort" class="form-input w-full" />
      </div>

      <div class="flex space-x-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">儲存</button>
        <Link href="/system/menus" class="text-gray-500 underline">返回列表</Link>
      </div>
    </form>
  </div>
</template>
