// Composables/Media/useMediaNavigation.js
import { router } from '@inertiajs/vue3'

export function useMediaNavigation() {
    function navigateToFolder(folderId) {
        router.get(route('media.show', folderId))
    }

    function navigateToBreadcrumb(folderId) {
        const routeName = folderId ? 'media.show' : 'media.index'
        const params = folderId ? [folderId] : []
        router.get(route(routeName, ...params))
    }

    return {
        navigateToFolder,
        navigateToBreadcrumb
    }
}