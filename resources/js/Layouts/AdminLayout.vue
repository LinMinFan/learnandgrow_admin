 
<script setup>
    import { Link, Head, usePage, router } from '@inertiajs/vue3'
    import { ref, onMounted, computed, watch, nextTick } from 'vue'
    import Navbar from '@/Components/Navbar.vue'
    import Sidebar from '@/Components/Sidebar.vue'
    import Footer from '@/Components/Footer.vue'
    // simple-datatables
    import 'simple-datatables/dist/style.css'
    // fontawesome
    import '@fortawesome/fontawesome-free/css/all.min.css'
    import { useTopGlobalNotify } from '@/Composables/useTopGlobalNotify'

    const isSidebarOpen = ref(window.innerWidth >= 1024)

    const { successNotify, errorNotify } = useTopGlobalNotify()

    // 獲取 flash 訊息
    const page = usePage();
    const flash = computed(() => page.props.flash);

    onMounted(() => {
        window.addEventListener('resize', () => {
            if (window.innerWidth < 1024) {
              isSidebarOpen.value = false
            }
        })
    })

    watch(
        () => page.props.flash,
        (newFlash) => {
            if (newFlash?.success || newFlash?.error) {
                // 使用 nextTick 確保 DOM 更新完成，再加上小延遲確保通知系統準備就緒
                nextTick(() => {
                    setTimeout(() => {
                        if (newFlash?.success) {
                            successNotify(newFlash.success);
                        }
                        if (newFlash?.error) {
                            errorNotify(newFlash.error);
                        }
                    }, 100); // 100ms 延遲
                });
            }
        },
        { immediate: true, deep: true }
    );

</script>

<template>
    <Head title="控制台" />

    <div class="min-h-screen flex flex-col">
        <!-- navbar -->
        <Navbar @toggleSidebar="isSidebarOpen = !isSidebarOpen" />

        <!-- Sidebar + Main -->
        <div class="flex flex-1">

            <!-- Sidebar -->
            <Sidebar :isSidebarOpen="isSidebarOpen" />

            <div 
                :class="[
                  'flex flex-col w-full transition-all duration-300',
                  isSidebarOpen ? 'lg:ml-64' : 'lg:ml-0'
                ]"
            >
            
                <!-- Main -->
                <main class="px-4 py-6 flex-1">
                    <slot />
                </main>

                <!-- footer -->
                <Footer />
            </div>
        </div>
    </div>
</template>

<style lang="postcss">
select.datatable-selector {
    @apply pl-4 pr-7;
}
</style>