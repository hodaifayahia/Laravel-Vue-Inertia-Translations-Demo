<script setup lang="ts">
import { ref, watch } from 'vue'
import type { ChatMessage } from '@/types/chat'
import { trans } from 'laravel-vue-i18n'
import { Send, Paperclip, X, Smile } from 'lucide-vue-next'
import EmojiPicker from './EmojiPicker.vue'

const props = defineProps<{
  channelId: number
  sending: boolean
  replyingTo?: ChatMessage | null
  editingMessage?: ChatMessage | null
}>()

const emit = defineEmits<{
  send: [content: string, file?: File]
  edit: [content: string]
  cancelReply: []
  cancelEdit: []
  typing: [isTyping: boolean]
  upload: [file: File]
}>()

const messageContent = ref('')
const fileInput = ref<HTMLInputElement>()
const selectedFile = ref<File | null>(null)
const showEmojiPicker = ref(false)
const typingTimeout = ref<NodeJS.Timeout | null>(null)

// Initialize editing message
watch(() => props.editingMessage, (message) => {
  if (message) {
    messageContent.value = message.content
  }
})

// Handle input
const handleInput = () => {
  // Emit typing indicator
  if (typingTimeout.value) {
    clearTimeout(typingTimeout.value)
  } else {
    emit('typing', true)
  }

  typingTimeout.value = setTimeout(() => {
    emit('typing', false)
    typingTimeout.value = null
  }, 2000)
}

// Handle submit
const handleSubmit = () => {
  const content = messageContent.value.trim()
  if (!content && !selectedFile.value) return

  if (props.editingMessage) {
    emit('edit', content)
  } else {
    emit('send', content, selectedFile.value || undefined)
  }

  messageContent.value = ''
  selectedFile.value = null
  if (typingTimeout.value) {
    clearTimeout(typingTimeout.value)
    typingTimeout.value = null
    emit('typing', false)
  }
}

// Handle file selection
const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    selectedFile.value = target.files[0]
  }
}

// Remove selected file
const removeFile = () => {
  selectedFile.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

// Handle emoji selection
const handleEmojiSelect = (emoji: string) => {
  messageContent.value += emoji
  showEmojiPicker.value = false
}

// Handle key press
const handleKeyPress = (event: KeyboardEvent) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault()
    handleSubmit()
  }
}
</script>

<template>
  <div class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
    <!-- Reply/Edit Banner -->
    <div
      v-if="replyingTo || editingMessage"
      class="px-4 py-2 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between"
    >
      <div class="flex items-start gap-2">
        <div class="w-1 h-full bg-indigo-500 rounded"></div>
        <div>
          <p class="text-xs font-medium text-gray-700 dark:text-gray-300">
            {{ editingMessage ? trans('chat.editing_message') : trans('chat.replying_to', { name: replyingTo?.user?.name }) }}
          </p>
          <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
            {{ (editingMessage || replyingTo)?.content }}
          </p>
        </div>
      </div>
      <button
        @click="editingMessage ? emit('cancelEdit') : emit('cancelReply')"
        class="p-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
      >
        <X class="w-4 h-4" />
      </button>
    </div>

    <!-- File Preview -->
    <div
      v-if="selectedFile"
      class="px-4 py-2 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between"
    >
      <div class="flex items-center gap-2">
        <Paperclip class="w-4 h-4 text-gray-500" />
        <span class="text-sm text-gray-700 dark:text-gray-300">
          {{ selectedFile.name }}
        </span>
        <span class="text-xs text-gray-500">
          ({{ (selectedFile.size / 1024).toFixed(2) }} KB)
        </span>
      </div>
      <button
        @click="removeFile"
        class="p-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
      >
        <X class="w-4 h-4" />
      </button>
    </div>

    <!-- Input Area -->
    <div class="p-4">
      <div class="flex items-end gap-2">
        <!-- File Upload -->
        <input
          ref="fileInput"
          type="file"
          @change="handleFileSelect"
          class="hidden"
          accept="image/*,application/pdf,.doc,.docx,.txt"
        />
        <button
          @click="fileInput?.click()"
          :disabled="sending || !!editingMessage"
          class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          :title="trans('chat.attach_file')"
        >
          <Paperclip class="w-5 h-5 text-gray-600 dark:text-gray-400" />
        </button>

        <!-- Emoji Picker -->
        <div class="relative">
          <button
            @click="showEmojiPicker = !showEmojiPicker"
            :disabled="sending"
            class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :title="trans('chat.add_emoji')"
          >
            <Smile class="w-5 h-5 text-gray-600 dark:text-gray-400" />
          </button>
          <EmojiPicker
            v-if="showEmojiPicker"
            @select="handleEmojiSelect"
            @close="showEmojiPicker = false"
          />
        </div>

        <!-- Text Input -->
        <div class="flex-1">
          <textarea
            v-model="messageContent"
            @input="handleInput"
            @keypress="handleKeyPress"
            :disabled="sending"
            :placeholder="trans('chat.type_message')"
            rows="1"
            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none disabled:opacity-50 disabled:cursor-not-allowed"
            style="max-height: 120px;"
          />
        </div>

        <!-- Send Button -->
        <button
          @click="handleSubmit"
          :disabled="sending || (!messageContent.trim() && !selectedFile)"
          class="p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          :title="trans('chat.send')"
        >
          <Send class="w-5 h-5" />
        </button>
      </div>

      <!-- Hint Text -->
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
        {{ trans('chat.press_enter_to_send') }}
      </p>
    </div>
  </div>
</template>
