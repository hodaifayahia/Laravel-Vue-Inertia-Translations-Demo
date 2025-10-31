<script setup lang="ts">
defineProps<{
  isOnline: boolean
}>()
</script>

<template>
  <div
    class="w-3 h-3 rounded-full border-2 border-white dark:border-gray-800"
    :class="{
      'bg-green-500': isOnline,
      'bg-gray-400': !isOnline
    }"
  />
</template>
