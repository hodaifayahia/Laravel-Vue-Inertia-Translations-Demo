<script setup lang="ts">
import type { TypingUser } from '@/types/chat'
import { trans } from 'laravel-vue-i18n'
import { computed } from 'vue'

const props = defineProps<{
  typingUsers: TypingUser[]
}>()

const typingText = computed(() => {
  const count = props.typingUsers.length
  if (count === 0) return ''
  if (count === 1) return trans('chat.user_is_typing', { name: props.typingUsers[0].name })
  if (count === 2) return trans('chat.users_are_typing', { 
    name1: props.typingUsers[0].name,
    name2: props.typingUsers[1].name 
  })
  return trans('chat.multiple_users_typing', { count })
})
</script>

<template>
  <div class="flex items-center gap-2 px-4 py-2">
    <div class="flex gap-1">
      <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></div>
      <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></div>
      <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></div>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">
      {{ typingText }}
    </p>
  </div>
</template>
