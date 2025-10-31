<script setup lang="ts">
import { trans } from 'laravel-vue-i18n'
import { MessageCircle } from 'lucide-vue-next'

defineProps<{
  isMobile: boolean
}>()
</script>

<template>
  <div class="flex flex-col items-center justify-center h-full text-gray-500 dark:text-gray-400">
    <MessageCircle class="w-16 h-16 mb-4 opacity-50" />
    <h3 class="text-lg font-semibold mb-2">{{ trans('chat.no_chat_selected') }}</h3>
    <p class="text-sm text-center max-w-sm">
      {{ isMobile ? trans('chat.select_conversation_mobile') : trans('chat.select_conversation_desktop') }}
    </p>
  </div>
</template>
