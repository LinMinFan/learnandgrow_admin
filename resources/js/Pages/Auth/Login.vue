<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

</script>

<template>
    <Head title="控制台" />

    <div class="min-h-screen bg-blue-600 flex items-center justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mt-10">
                <div class="bg-gray-100 px-6 py-4">
                    <h3 class="text-center text-2xl font-semibold text-gray-700">管理員登入</h3>
                </div>
                <div v-if="status" class="text-center mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>
                <div class="px-6 py-4">
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <InputLabel for="email" value="Email 信箱" />

                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                placeholder="name@example.com"
                                required
                                autofocus
                                autocomplete="username"
                            />

                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>
  
                        <div class="mb-4">
                            <InputLabel for="password" value="密碼" />

                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full"
                                v-model="form.password"
                                placeholder="Password"
                                required
                                autocomplete="current-password"
                            />

                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>
  
                        <div class="block mt-4">
                            <label class="flex items-center">
                                <Checkbox name="remember" v-model:checked="form.remember" />
                                <span class="ms-2 text-sm text-gray-600">記住密碼</span>
                            </label>
                        </div>
  
                        <div class="flex items-center justify-end mt-4">
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                忘記密碼?
                            </Link>

                            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                登入
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-100 text-center px-6 py-3">
                    <p class="text-sm">
                        <Link
                            :href="route('register')"
                            class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                        >
                            註冊帳號
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
  
<style scoped>

</style>
  