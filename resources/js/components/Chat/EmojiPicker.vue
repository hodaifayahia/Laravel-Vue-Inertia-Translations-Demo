<script setup lang="ts">
import { ref } from 'vue'
import { onClickOutside } from '@vueuse/core'

const emit = defineEmits<{
  select: [emoji: string]
  close: []
}>()

const pickerRef = ref<HTMLElement>()

const emojis = [
  '👍', '❤️', '😂', '😮', '😢', '😠',
  '🎉', '🔥', '👏', '✅', '❌', '💯',
  '🚀', '⭐', '💡', '🤔', '👀', '🙏'
]

const handleSelect = (emoji: string) => {
  emit('select', emoji)
  emit('close')
}

onClickOutside(pickerRef, () => emit('close'))
</script>

<template>
  <div
    ref="pickerRef"
    class="absolute bottom-full mb-2 left-0 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-2 z-50"
  >
    <div class="grid grid-cols-6 gap-1">
      <button
        v-for="emoji in emojis"
        :key="emoji"
        @click="handleSelect(emoji)"
        class="w-10 h-10 flex items-center justify-center text-2xl hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors"
      >
        {{ emoji }}
      </button>
    </div>
  </div>
</template>
