import { useNotification } from '@kyvg/vue3-notification'

export function useBottomGlobalNotify() {
  const { notify } = useNotification()

  const successNotify = (message) => {
    notify({ group: 'bottom', text: message, type: 'success' })
  }

  const errorNotify = (message) => {
    notify({ group: 'bottom', text: message, type: 'error' })
  }

  return { successNotify, errorNotify }
}
