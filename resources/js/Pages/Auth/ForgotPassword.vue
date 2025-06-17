<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const RESETPASSWORD_ENABLED = false;

const submit = () => {
    // 關閉非管理員重設密碼
     if (!RESETPASSWORD_ENABLED) {
        return;
    }
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="忘記密碼" />

    <div class="min-h-screen bg-blue-600 flex items-center justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white shadow-lg rounded-lg mt-10">
                <div class="border-b px-4 py-2">
                    <h3 class="text-center text-2xl font-semibold text-gray-700">密碼恢復</h3>
                </div>
                <div v-if="status" class="text-center mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>
                <div class="px-4 py-2">
                    <p class="text-sm text-gray-500 mb-4">
                        輸入您的電子郵件地址，我們將向您發送重設密碼的連結。
                    </p>
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
                        <div class="flex items-center justify-between mt-6">
                            <Link href="/" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span>回登入</span>
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                電子郵件密碼重設鏈接
                            </PrimaryButton>
                        </div>
                    </form>
                    <div class="border-t text-center mt-4 py-2 text-sm text-gray-600">
                        <Link :href="route('register')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span>註冊帳號</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
