<script setup lang="ts">
import type { ChatMessageReaction } from '@/types/chat'
import { computed } from 'vue'

const props = defineProps<{
  reactions: ChatMessageReaction[]
  currentUserId: number
}>()

const emit = defineEmits<{
  react: [emoji: string]
}>()

interface GroupedReaction {
  emoji: string
  count: number
  userIds: number[]
  hasReacted: boolean
}

const groupedReactions = computed<GroupedReaction[]>(() => {
  const groups = new Map<string, GroupedReaction>()

  props.reactions.forEach(reaction => {
    const existing = groups.get(reaction.emoji)
    if (existing) {
      existing.count++
      existing.userIds.push(reaction.user_id)
      if (reaction.user_id === props.currentUserId) {
        existing.hasReacted = true
      }
    } else {
      groups.set(reaction.emoji, {
        emoji: reaction.emoji,
        count: 1,
        userIds: [reaction.user_id],
        hasReacted: reaction.user_id === props.currentUserId
      })
    }
  })

  return Array.from(groups.values())
})
</script>

<template>
  <div v-if="groupedReactions.length > 0" class="flex flex-wrap gap-1 mt-2">
    <button
      v-for="reaction in groupedReactions"
      :key="reaction.emoji"
      @click="emit('react', reaction.emoji)"
      class="px-2 py-1 rounded-full text-sm flex items-center gap-1 transition-colors"
      :class="{
        'bg-indigo-100 dark:bg-indigo-900/30 border border-indigo-300 dark:border-indigo-700': reaction.hasReacted,
        'bg-gray-100 dark:bg-gray-700/50 hover:bg-gray-200 dark:hover:bg-gray-700': !reaction.hasReacted
      }"
    >
      <span>{{ reaction.emoji }}</span>
      <span class="text-xs font-medium">{{ reaction.count }}</span>
    </button>
  </div>
</template>
