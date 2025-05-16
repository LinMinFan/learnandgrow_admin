<script setup>
    import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
    import { onClickOutside  } from '@vueuse/core'
    import { Link, usePage  } from '@inertiajs/vue3';

    defineProps({
        isSidebarOpen: Boolean,
    })

    const page = usePage()
    const menus = page.props.menus ?? []
    const currentUrl = computed(() => page.url)

    const expanded = reactive({})

    function toggle(id) {
      expanded[id] = !expanded[id]
    }

    // 判斷是否為 active 狀態：前綴比對
    function isActive(route) {
        if (!route) return false
        return currentUrl.value.startsWith(route)
    }

    onMounted(() => {
        menus.forEach(menu => {
            expanded[menu.id] = false

            // 自動展開有子選單且當前頁面路由在其子選單裡的情況
            if (menu.children?.some(child => isActive(child.route))) {
                expanded[menu.id] = true
            }
        })
    })

</script>

<template>
    <transition name="slide">
        <aside 
            :class="[ 
                'bg-gray-800 text-white h-screen w-64 z-40 transition-transform duration-300',
                isSidebarOpen 
                    ? 'translate-x-0' 
                    : '-translate-x-full lg:-translate-x-full',
                'fixed top-0 left-0'
            ]"
        >
            <nav class="mt-16 p-4 space-y-4 overflow-y-auto" :style="{'height': 'calc(100vh - 4rem)'}">
                <!-- 側邊選單項目範例 -->
                <!-- <div>
                    <div class="text-xs uppercase text-gray-400 mb-2"></div>
                    <Link href="/" class="flex items-center space-x-2 text-sm hover:bg-gray-700 px-2 py-2 rounded">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>儀錶板</span>
                    </Link>
                </div> -->

                <!-- 動態選單 -->
                <div v-for="menu in menus" :key="menu.id">
                    <!-- 有子選單 -->
                    <div v-if="menu.children && menu.children.length">
                        <button 
                            @click="toggle(menu.id)"
                            class="w-full px-2 py-2 flex items-center space-x-2 hover:bg-gray-700 rounded"
                        >
                            <i :class="menu.icon"></i>
                            <span>{{ menu.title }}</span>
                            <i :class="expanded[menu.id] ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                        </button>
                        <div v-show="expanded[menu.id]" class="ml-4 mt-1">
                            <Link
                                v-for="child in menu.children"
                                :key="child.id"
                                :href="child.route"
                                :class="[
                                    'block space-x-2 px-2 py-1 hover:bg-gray-700 rounded',
                                    isActive(child.route) ? 'bg-gray-900' : ''
                                ]"
                            >
                                <i :class="child.icon"></i>
                                <span>{{ child.title }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- 沒有子選單 -->
                    <div v-else>
                        <Link
                            :href="menu.route"
                            :class="[
                                'flex items-center space-x-2 text-sm hover:bg-gray-700 px-2 py-2 rounded',
                                isActive(menu.route) ? 'bg-gray-900' : ''
                            ]"
                            
                        >
                            <i :class="menu.icon"></i>
                            <span>{{ menu.title }}</span>
                        </Link>
                    </div>
                </div>
            </nav>
        </aside>
    </transition>
</template>
  
<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}
.slide-enter-from, .slide-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}
</style>