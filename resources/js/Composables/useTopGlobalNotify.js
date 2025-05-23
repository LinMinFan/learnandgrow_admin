import { useNotification } from '@kyvg/vue3-notification'

export function useTopGlobalNotify() {
  const { notify } = useNotification()

  const successNotify = (message) => {
    notify({
      group: 'top',
      text: message,
      type: 'success',
    })
  }

  const errorNotify = (message) => {
    notify({
      group: 'top',
      text: message,
      type: 'error',
    })
  }

  return { successNotify, errorNotify }
}
