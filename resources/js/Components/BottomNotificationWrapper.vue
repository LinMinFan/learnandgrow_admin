<!-- components/NotificationWrapper.vue -->
<script setup>

    defineProps({
        width: { type: Number, default: 250 },
        speed: { type: Number, default: 500 },
        duration: { type: Number, default: 2000 },
        max: { type: Number, default: 3 },
        baseClass: { type: String, default: 'notification' }, // 可由外部指定 class
    })
</script>

<template>
    <notifications
        group="bottom"
        position="bottom right"
        :width="width"
        :speed="speed"
        :duration="duration"
        :max="max"
        :classes="baseClass"
    />
</template>
  
<style>
.notification {
    padding: 15px 20px;
    border-radius: 6px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    font-size: 1rem;
    white-space: normal;
    margin-bottom: 10px;
    color: white;
}
  
/* 類型樣式由 notify 的 type 自動套用 */
.notification.success { 
    background-color: #27ae60; 
}
.notification.error { 
    background-color: #c0392b; 
}
.notification.warn { 
    background-color: #f39c12; 
}
.notification.info { 
    background-color: #2980b9; 
}
</style>
  