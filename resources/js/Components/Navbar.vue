<script setup>
    import { ref } from 'vue'
    import { onClickOutside  } from '@vueuse/core'
    import { Link } from '@inertiajs/vue3'
    import NavDropdownLink from '@/Components/NavDropdownLink.vue';
    // simple-datatables
    import 'simple-datatables/dist/style.css'
    // fontawesome
    import '@fortawesome/fontawesome-free/css/all.min.css'

    const open = ref(false)
    const dropdownRef = ref(null)

    onClickOutside (dropdownRef, () => open.value = false)
</script>

<template>
    <nav class="bg-gray-800 text-white px-4 py-3 flex items-center justify-between">
        <!-- Navbar Brand -->
        <Link class="text-xl font-semibold" href="/">控制台</Link>

        <!-- Sidebar Toggle -->
        <button class="text-white lg:hidden mr-4 focus:outline-none" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Navbar Dropdown -->
        <div class="relative px-2 mr-6" ref="dropdownRef">
            <button
              class="focus:outline-none"
              @click="open = !open"
            >
                <i class="fas fa-user fa-fw text-white"></i>
                <i class="fas fa-caret-down text-white text-sm"></i>
            </button>

            <!-- Dropdown Menu -->
            <ul
                v-if="open"
                class="absolute right-0 mt-2 w-48 bg-white text-gray-800 shadow-lg rounded-md py-1 z-50"
            >
                <li>
                    <NavDropdownLink 
                        :href="route('profile.edit')"
                        :customClass="'block px-4 py-2 hover:bg-gray-100'"
                    >
                        設定
                    </NavDropdownLink>
                </li>
                <li><hr class="my-1 border-gray-200" /></li>
                <li>
                    <NavDropdownLink 
                        :href="route('logout')" 
                        method="post"
                        as="button"
                        :customClass="'block px-4 py-2 hover:bg-gray-100'"
                    >
                        登出
                    </NavDropdownLink>
                </li>
            </ul>
        </div>
    </nav>
</template>

