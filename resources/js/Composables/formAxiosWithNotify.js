// resources/js/Composables/formAxiosWithNotify.js
import axios from 'axios'
import { router  } from '@inertiajs/vue3';
import { useTopGlobalNotify } from '@/Composables/useTopGlobalNotify'
import get from 'lodash/get'

const { errorNotify } = useTopGlobalNotify()

export function submitFormWithNotify({
        form,
        method = 'post',
        url,
        data = {},
        redirectUrl = null,
    }) {
    axios[method](url, data)
        .then((res) => {
            router.visit(redirectUrl, {
                data: {
                    success: res.data.message
                }
            })
        })
        .catch((errors) => {
            form.clearErrors();

            const formErrors = get(errors, 'response.data.errors', {});
            const message = get(errors, 'response.data.message', '發生錯誤，請稍後再試');

            // 驗證錯誤
            Object.entries(formErrors).forEach(([field, messages]) => {
                if (Array.isArray(messages) && messages.length > 0) {
                    form.errors[field] = messages[0]
                }
            })

            // 捕捉不到特定錯誤欄位時再顯示 notify
            if (Object.keys(formErrors).length === 0) {
                errorNotify(message)
            }
        })
}
