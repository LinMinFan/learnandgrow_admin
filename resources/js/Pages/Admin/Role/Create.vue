<script setup>
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, usePage, useForm, router  } from '@inertiajs/vue3';
import { onClickOutside  } from '@vueuse/core'
import cloneDeep from 'lodash/cloneDeep'
import axios from 'axios'
import { useNotification } from "@kyvg/vue3-notification";
import RoleForm from '@/Components/RoleForm.vue'


defineProps({ permissions: Array })

const { notify }  = useNotification()

const submit = (form) => {
    form.post(route('admin.role.store'), {
        onError: (errors) => {
            if (errors.message.length > 0) {
                notify({
                    group: 'top',
                    text: errors.message,
                    type: 'error',
                })
            }
        }
  })
}

</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
            <h1 class="text-xl font-bold mb-4">新增角色</h1>
            <RoleForm :permissions="permissions" :onSubmit="submit" />
        </div>
    </AdminLayout>
</template>
