<script setup lang="ts">
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import type { ChatChannel, ChatMessage, TypingUser, User } from '@/types/chat'
import { trans } from 'laravel-vue-i18n'
import { ArrowLeft, MoreVertical } from 'lucide-vue-next'
import MessageItem from './MessageItem.vue'
import MessageInput from './MessageInput.vue'
import TypingIndicator from './TypingIndicator.vue'
import OnlineStatusBadge from './OnlineStatusBadge.vue'

const props = defineProps<{
  channel: ChatChannel
  messages: ChatMessage[]
  typingUsers: TypingUser[]
  loading: boolean
  sendingMessage: boolean
  hasMoreMessages: boolean
  onlineUsers: User[]
  currentUser: User
  isMobile: boolean
}>()

const emit = defineEmits<{
  sendMessage: [channelId: number, content: string, file?: File, replyToId?: number]
  editMessage: [messageId: number, content: string]
  deleteMessage: [messageId: number]
  reactToMessage: [messageId: number, emoji: string]
  loadMore: []
  typing: [channelId: number, isTyping: boolean]
  uploadFile: [file: File]
  back: []
}>()

const messagesContainer = ref<HTMLElement>()
const showScrollButton = ref(false)
const isLoadingMore = ref(false)
const replyingTo = ref<ChatMessage | null>(null)
const editingMessage = ref<ChatMessage | null>(null)

const channelName = computed(() => {
  if (props.channel.name) return props.channel.name
  if (props.channel.type === 'direct' && props.channel.users) {
    const otherUser = props.channel.users[0]
    return otherUser?.name || trans('chat.unknown_user')
  }
  return trans('chat.unnamed_channel')
})

const channelAvatar = computed(() => {
  if (props.channel.type === 'direct' && props.channel.users) {
    const otherUser = props.channel.users[0]
    return otherUser?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(otherUser?.name || 'U')}`
  }
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(props.channel.name || 'G')}&background=6366f1&color=fff`
})

const isOtherUserOnline = computed(() => {
  if (props.channel.type === 'direct' && props.channel.users) {
    const otherUserId = props.channel.users[0]?.id
    return props.onlineUsers.some(user => user.id === otherUserId)
  }
  return false
})

const onlineUsersCount = computed(() => {
  if (props.channel.type === 'group') {
    return props.channel.users?.filter(user => 
      props.onlineUsers.some(onlineUser => onlineUser.id === user.id)
    ).length || 0
  }
  return 0
})

// Scroll to bottom
const scrollToBottom = (smooth = true) => {
  if (!messagesContainer.value) return
  messagesContainer.value.scrollTo({
    top: messagesContainer.value.scrollHeight,
    behavior: smooth ? 'smooth' : 'auto'
  })
}

// Check scroll position
const checkScrollPosition = () => {
  if (!messagesContainer.value) return
  const { scrollTop, scrollHeight, clientHeight } = messagesContainer.value
  showScrollButton.value = scrollHeight - scrollTop - clientHeight > 200
}

// Handle scroll
const handleScroll = () => {
  checkScrollPosition()
  
  // Load more messages when scrolled to top
  if (!messagesContainer.value || isLoadingMore.value || !props.hasMoreMessages) return
  
  if (messagesContainer.value.scrollTop < 100) {
    isLoadingMore.value = true
    const oldScrollHeight = messagesContainer.value.scrollHeight
    
    emit('loadMore')
    
    nextTick(() => {
      if (messagesContainer.value) {
        const newScrollHeight = messagesContainer.value.scrollHeight
        messagesContainer.value.scrollTop = newScrollHeight - oldScrollHeight
      }
      isLoadingMore.value = false
    })
  }
}

// Handle send message
const handleSendMessage = (content: string, file?: File) => {
  emit('sendMessage', props.channel.id, content, file, replyingTo.value?.id)
  replyingTo.value = null
  editingMessage.value = null
  scrollToBottom()
}

// Handle edit message
const handleEditMessage = (content: string) => {
  if (editingMessage.value) {
    emit('editMessage', editingMessage.value.id, content)
    editingMessage.value = null
  }
}

// Handle typing
const handleTyping = (isTyping: boolean) => {
  emit('typing', props.channel.id, isTyping)
}

// Watch messages for auto-scroll
watch(() => props.messages.length, async () => {
  await nextTick()
  if (!showScrollButton.value) {
    scrollToBottom()
  }
})

// Watch channel change
watch(() => props.channel.id, () => {
  replyingTo.value = null
  editingMessage.value = null
  nextTick(() => scrollToBottom(false))
})

// Initial scroll
onMounted(() => {
  scrollToBottom(false)
})
</script>

<template>
  <div class="flex flex-col h-full">
    <!-- Header -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <button
          v-if="isMobile"
          @click="emit('back')"
          class="p-2 -ml-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
        >
          <ArrowLeft class="w-5 h-5" />
        </button>

        <div class="relative">
          <img
            :src="channelAvatar"
            :alt="channelName"
            class="w-10 h-10 rounded-full"
          />
          <OnlineStatusBadge
            v-if="channel.type === 'direct'"
            :is-online="isOtherUserOnline"
            class="absolute bottom-0 right-0"
          />
        </div>

        <div>
          <h3 class="font-semibold text-gray-900 dark:text-white">
            {{ channelName }}
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            <span v-if="channel.type === 'direct'">
              {{ isOtherUserOnline ? trans('chat.online') : trans('chat.offline') }}
            </span>
            <span v-else>
              {{ trans('chat.members_count', { count: channel.users?.length || 0 }) }}
              <span v-if="onlineUsersCount > 0">
                · {{ trans('chat.online_count', { count: onlineUsersCount }) }}
              </span>
            </span>
          </p>
        </div>
      </div>

      <button
        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
      >
        <MoreVertical class="w-5 h-5 text-gray-600 dark:text-gray-400" />
      </button>
    </div>

    <!-- Messages -->
    <div
      ref="messagesContainer"
      @scroll="handleScroll"
      class="flex-1 overflow-y-auto p-4 space-y-4"
    >
      <!-- Loading more indicator -->
      <div
        v-if="isLoadingMore"
        class="text-center py-2"
      >
        <span class="text-sm text-gray-500">{{ trans('chat.loading') }}...</span>
      </div>

      <!-- Load more button -->
      <div
        v-else-if="hasMoreMessages"
        class="text-center py-2"
      >
        <button
          @click="emit('loadMore')"
          class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
        >
          {{ trans('chat.load_more_messages') }}
        </button>
      </div>

      <!-- Messages list -->
      <MessageItem
        v-for="message in messages"
        :key="message.id"
        :message="message"
        :current-user="currentUser"
        :channel="channel"
        @reply="replyingTo = message"
        @edit="editingMessage = message"
        @delete="emit('deleteMessage', message.id)"
        @react="(emoji: string) => emit('reactToMessage', message.id, emoji)"
      />

      <!-- Typing indicator -->
      <TypingIndicator
        v-if="typingUsers.length > 0"
        :typing-users="typingUsers"
      />
    </div>

    <!-- Scroll to bottom button -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-2"
    >
      <div
        v-if="showScrollButton"
        class="absolute bottom-24 right-8"
      >
        <button
          @click="scrollToBottom()"
          class="p-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-lg transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
          </svg>
        </button>
      </div>
    </Transition>

    <!-- Message Input -->
    <MessageInput
      :channel-id="channel.id"
      :sending="sendingMessage"
      :replying-to="replyingTo"
      :editing-message="editingMessage"
      @send="handleSendMessage"
      @edit="handleEditMessage"
      @cancel-reply="replyingTo = null"
      @cancel-edit="editingMessage = null"
      @typing="handleTyping"
      @upload="emit('uploadFile', $event)"
    />
  </div>
</template>
