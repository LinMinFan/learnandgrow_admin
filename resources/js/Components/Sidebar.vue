<script setup>
    import { ref, reactive, watchEffect } from 'vue'
    import { onClickOutside  } from '@vueuse/core'
    import { Link } from '@inertiajs/vue3'

    defineProps({
        isSidebarOpen: Boolean,
    })

    const expanded = reactive({
      charts: false,
      tables: false,
    })

    function toggle(section) {
      expanded[section] = !expanded[section]
    }

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

                <div>
                    <div class="text-xs uppercase text-gray-400 mb-2"></div>
                    <Link href="/" class="flex items-center space-x-2 text-sm hover:bg-gray-700 px-2 py-2 rounded">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>儀錶板</span>
                    </Link>
                </div>
  
                <div>
                    <div class="text-xs uppercase text-gray-400 mb-2">頁面管理</div>
                    <Link href="/charts" class="flex items-center space-x-2 text-sm hover:bg-gray-700 px-2 py-2 rounded">
                        <i class="fas fa-chart-bar"></i>
                        <span>Charts</span>
                    </Link>
                    <Link href="/tables" class="flex items-center space-x-2 text-sm hover:bg-gray-700 px-2 py-2 rounded">
                        <i class="fas fa-table"></i>
                        <span>Tables</span>
                    </Link>
                </div>

                <!-- 可展開選單項目範例 -->
                <!-- <div>
                    <button 
                        @click="toggle('admin')"
                        class="w-full px-2 py-2 flex items-center space-x-2 px-2 hover:bg-gray-700 rounded"
                    >
                        <i class="fas fa-sliders-h"></i>
                        <span>網站管理</span>
                        <i :class="expanded.admin ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                    </button>
                    <div v-show="expanded.admin" class="ml-4 mt-1">
                        <Link href="/charts/line" class="block space-x-2 px-2 py-1 hover:bg-gray-700 rounded">
                            <i class="fas fa-cogs"></i>
                            <span>網站設定</span>
                        </Link>
                    </div>
                </div> -->

                <div>
                    <button 
                        @click="toggle('admin')"
                        class="w-full px-2 py-2 flex items-center space-x-2 px-2 hover:bg-gray-700 rounded"
                    >
                        <i class="fas fa-sliders-h"></i>
                        <span>網站管理</span>
                        <i :class="expanded.admin ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                    </button>
                    <div v-show="expanded.admin" class="ml-4 mt-1">
                        <Link href="/charts/line" class="block space-x-2 px-2 py-1 hover:bg-gray-700 rounded">
                            <i class="fas fa-cogs"></i>
                            <span>網站設定</span>
                        </Link>
                        <Link href="/charts/bar" class="block space-x-2 px-2 py-1 hover:bg-gray-700 rounded">
                            <i class="fas fa-user-cog"></i>
                            <span>帳號管理</span>
                        </Link>
                        <Link href="/charts/bar" class="block space-x-2 px-2 py-1 hover:bg-gray-700 rounded">
                            <i class="fas fa-user-shield"></i>
                            <span>權限設定</span>
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