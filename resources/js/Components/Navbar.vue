<script setup>
    import { ref, defineEmits  } from 'vue'
    import { onClickOutside  } from '@vueuse/core'
    import { Link } from '@inertiajs/vue3'
    import NavDropdownLink from '@/Components/NavDropdownLink.vue';

    const open = ref(false)
    const dropdownRef = ref(null)
    const emit = defineEmits(['toggleSidebar'])

    onClickOutside (dropdownRef, () => open.value = false)
</script>

<template>
    <nav class="bg-gray-800 text-white px-4 py-3 flex items-center h-16 z-50">
        <!-- Navbar Brand -->
        <Link class="text-xl font-semibold pl-4 order-1 min-w-[100px] lg:order-1 sm:w-[225px]" href="/">控制台</Link>

        <!-- Sidebar Toggle -->
        <div class="ml-0 mr-4 focus:outline-none order-3 min-w-[30px] lg:ml-4  lg:order-2 lg:min-w-[225px]">
            <button class="text-white" @click="emit('toggleSidebar')">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Navbar Dropdown -->
        <div class="relative px-2 mr-6 order-2 lg:order-3 ml-auto min-w-[50px]" ref="dropdownRef">
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

