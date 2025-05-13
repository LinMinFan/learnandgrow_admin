 
<script setup>
    import { Link, Head } from '@inertiajs/vue3'
    import { ref, onMounted } from 'vue'
    import Navbar from '@/Components/Navbar.vue'
    import Sidebar from '@/Components/Sidebar.vue'
    import Footer from '@/Components/Footer.vue'
    // simple-datatables
    import 'simple-datatables/dist/style.css'
    // fontawesome
    import '@fortawesome/fontawesome-free/css/all.min.css'

    const isSidebarOpen = ref(window.innerWidth >= 1024)

    onMounted(() => {
        window.addEventListener('resize', () => {
            if (window.innerWidth < 1024) {
              isSidebarOpen.value = false
            }
        })
    })

    defineProps({

    })
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
  