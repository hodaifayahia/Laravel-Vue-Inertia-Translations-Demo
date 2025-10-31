<script setup lang="ts">
import { ref, computed } from 'vue'
import type { ChatMessage, ChatChannel, User } from '@/types/chat'
import { trans } from 'laravel-vue-i18n'
import { Reply, Edit2, Trash2, MoreVertical } from 'lucide-vue-next'
import MessageReactions from './MessageReactions.vue'

const props = defineProps<{
  message: ChatMessage
  currentUser: User
  channel: ChatChannel
}>()

const emit = defineEmits<{
  reply: []
  edit: []
  delete: []
  react: [emoji: string]
}>()

const showActions = ref(false)

const isOwnMessage = computed(() => props.message.user_id === props.currentUser.id)

const messageTime = computed(() => {
  const date = new Date(props.message.created_at)
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
})

const isEdited = computed(() => {
  return props.message.created_at !== props.message.updated_at
})

const canEdit = computed(() => {
  return isOwnMessage.value && !props.message.deleted_at
})

const canDelete = computed(() => {
  return isOwnMessage.value && !props.message.deleted_at
})
</script>

<template>
  <div
    class="flex gap-3"
    :class="{ 'flex-row-reverse': isOwnMessage }"
    @mouseenter="showActions = true"
    @mouseleave="showActions = false"
  >
    <!-- Avatar -->
    <img
      v-if="!isOwnMessage"
      :src="message.user?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(message.user?.name || 'U')}`"
      :alt="message.user?.name"
      class="w-8 h-8 rounded-full flex-shrink-0"
    />

    <!-- Message Content -->
    <div
      class="flex-1 max-w-[70%]"
      :class="{ 'flex flex-col items-end': isOwnMessage }"
    >
      <!-- User Name (for group chats) -->
      <p
        v-if="!isOwnMessage && channel.type === 'group'"
        class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"
      >
        {{ message.user?.name }}
      </p>

      <!-- Reply Context -->
      <div
        v-if="message.reply_to"
        class="mb-1 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700/50 border-l-2 border-gray-400 dark:border-gray-500"
      >
        <p class="text-xs font-medium text-gray-600 dark:text-gray-400">
          {{ trans('chat.reply_to') }} {{ message.reply_to.user?.name }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-500 truncate">
          {{ message.reply_to.content }}
        </p>
      </div>

      <!-- Message Bubble -->
      <div
        class="relative group rounded-2xl px-4 py-2 break-words"
        :class="{
          'bg-indigo-600 text-white': isOwnMessage && !message.deleted_at,
          'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white': !isOwnMessage && !message.deleted_at,
          'bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400': message.deleted_at
        }"
      >
        <!-- Deleted Message -->
        <p v-if="message.deleted_at" class="italic text-sm">
          {{ trans('chat.message_deleted') }}
        </p>

        <!-- Normal Message -->
        <template v-else>
          <!-- File Attachment -->
          <div v-if="message.type === 'file' && message.attachment_url" class="mb-2">
            <!-- Image Attachment -->
            <div v-if="message.is_image" class="space-y-2">
              <a :href="message.attachment_url" target="_blank" class="block">
                <img 
                  :src="message.attachment_url" 
                  :alt="message.attachment_name"
                  class="max-w-full max-h-64 rounded-lg object-contain cursor-pointer hover:opacity-90 transition-opacity"
                />
              </a>
              <div class="flex items-center justify-between text-xs opacity-75">
                <span>{{ message.attachment_name }}</span>
                <span>{{ message.formatted_attachment_size }}</span>
              </div>
            </div>

            <!-- Video Attachment -->
            <div v-else-if="message.is_video" class="space-y-2">
              <video 
                :src="message.attachment_url" 
                controls
                class="max-w-full max-h-64 rounded-lg"
              />
              <div class="flex items-center justify-between text-xs opacity-75">
                <span>{{ message.attachment_name }}</span>
                <span>{{ message.formatted_attachment_size }}</span>
              </div>
            </div>

            <!-- Audio Attachment -->
            <div v-else-if="message.is_audio" class="space-y-2">
              <audio 
                :src="message.attachment_url" 
                controls
                class="w-full"
              />
              <div class="flex items-center justify-between text-xs opacity-75">
                <span>{{ message.attachment_name }}</span>
                <span>{{ message.formatted_attachment_size }}</span>
              </div>
            </div>

            <!-- Document/Other File -->
            <a
              v-else
              :href="message.attachment_url"
              download
              :title="message.attachment_name"
              class="flex items-center gap-3 p-3 rounded-lg bg-black/10 hover:bg-black/20 dark:bg-white/10 dark:hover:bg-white/20 transition-colors"
            >
              <!-- File Icon -->
              <div class="flex-shrink-0">
                <svg v-if="message.is_document" class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                </svg>
                <svg v-else class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" />
                </svg>
              </div>
              
              <!-- File Info -->
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ message.attachment_name }}</p>
                <p class="text-xs opacity-75">{{ message.formatted_attachment_size }}</p>
              </div>
              
              <!-- Download Icon -->
              <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </a>
          </div>

          <!-- Text Content -->
          <p v-if="message.content" class="text-sm whitespace-pre-wrap">{{ message.content }}</p>

          <!-- Message Info -->
          <div
            class="flex items-center gap-2 mt-1 text-xs"
            :class="{
              'text-indigo-200': isOwnMessage,
              'text-gray-500 dark:text-gray-400': !isOwnMessage
            }"
          >
            <span>{{ messageTime }}</span>
            <span v-if="isEdited">{{ trans('chat.edited') }}</span>
            <span v-if="isOwnMessage && message.is_read" class="flex items-center">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </span>
          </div>
        </template>

        <!-- Actions Menu -->
        <div
          v-if="!message.deleted_at"
          v-show="showActions"
          class="absolute top-0 flex items-center gap-1"
          :class="{ '-left-24': isOwnMessage, '-right-24': !isOwnMessage }"
        >
          <button
            @click="emit('reply')"
            class="p-1.5 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg shadow-sm transition-colors"
            :title="trans('chat.reply')"
          >
            <Reply class="w-4 h-4 text-gray-600 dark:text-gray-400" />
          </button>
          <button
            v-if="canEdit"
            @click="emit('edit')"
            class="p-1.5 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg shadow-sm transition-colors"
            :title="trans('chat.edit')"
          >
            <Edit2 class="w-4 h-4 text-gray-600 dark:text-gray-400" />
          </button>
          <button
            v-if="canDelete"
            @click="emit('delete')"
            class="p-1.5 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg shadow-sm transition-colors"
            :title="trans('chat.delete')"
          >
            <Trash2 class="w-4 h-4 text-red-600 dark:text-red-400" />
          </button>
        </div>
      </div>

      <!-- Reactions -->
      <MessageReactions
        v-if="!message.deleted_at"
        :reactions="message.reactions || []"
        :current-user-id="currentUser.id"
        @react="emit('react', $event)"
      />
    </div>
  </div>
</template>
