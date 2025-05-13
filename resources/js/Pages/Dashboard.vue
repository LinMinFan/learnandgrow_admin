<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useIntervalFn } from '@vueuse/core'
// chart.js
import Chart from 'chart.js/auto'

defineProps({
    
})

const stats = [
    { label: '用戶數', target: 1124, color: 'bg-blue-600' },
    { label: '警告事件', target: 23, color: 'bg-yellow-500' },
    { label: '成功登入', target: 985, color: 'bg-green-500' },
    { label: '異常錯誤', target: 7, color: 'bg-red-600' },
]

const counters = stats.map(s => ref(0))

onMounted(() => {
    counters.forEach((counter, i) => {
        const { pause } = useIntervalFn(() => {
            if (counter.value < stats[i].target) {
                counter.value += Math.ceil(stats[i].target / 40)
            } else {
                counter.value = stats[i].target
                pause()
            }
        }, 30)
    })

    const areaCtx = document.getElementById('myAreaChart')
    const barCtx = document.getElementById('myBarChart')

    new Chart(areaCtx, {
        type: 'line',
        data: {
            labels: ['一月', '二月', '三月', '四月'],
            datasets: [{
                label: '流量',
                data: [120, 200, 150, 300],
                backgroundColor: 'rgba(59,130,246,0.3)',
                borderColor: '#3B82F6',
                fill: true
            }]
        }
    })

    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['A', 'B', 'C', 'D'],
            datasets: [{
                label: '下載數',
                data: [15, 30, 45, 20],
                backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#3B82F6']
            }]
        }
    })
})
</script>

<template>
    <AdminLayout>
        <div class="px-4 py-6">
            <h1 class="text-2xl font-bold mb-2">儀錶板</h1>
            <nav class="text-sm text-gray-500 mb-6">
                <ol class="list-reset flex space-x-2">
                    <li>Dashboard</li>
                </ol>
            </nav>

            <!-- Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div
                  v-for="(stat, i) in stats"
                  :key="stat.label"
                  :class="`${stat.color} text-white rounded-lg shadow p-4 transform transition duration-500 ease-in-out hover:scale-105`"
                >
                    <div class="text-lg font-semibold mb-2">{{ stat.label }}</div>
                    <div class="text-3xl font-bold">
                        {{ counters[i].value }}
                    </div>
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <a href="#" class="underline hover:text-white/80">查看更多</a>
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center mb-4 text-gray-700 font-semibold">
                        <i class="fas fa-chart-area mr-2"></i> 網站流量
                    </div>
                    <canvas id="myAreaChart" class="w-full h-40"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center mb-4 text-gray-700 font-semibold">
                        <i class="fas fa-chart-bar mr-2"></i> 檔案下載
                    </div>
                    <canvas id="myBarChart" class="w-full h-40"></canvas>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
