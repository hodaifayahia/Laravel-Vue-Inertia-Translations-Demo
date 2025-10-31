<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { useChat } from '@/composables/useChat'
import { useReverb } from '@/composables/useReverb'
import { useNotifications } from '@/composables/useNotifications'
import type { ChatChannel, User } from '@/types/chat'
import { onMounted, ref, computed, watch } from 'vue'
import ChatSidebar from '@/components/Chat/ChatSidebar.vue'
import ChatWindow from '@/components/Chat/ChatWindow.vue'
import EmptyState from '@/components/Chat/EmptyState.vue'
import ConnectionStatus from '@/components/Chat/ConnectionStatus.vue'
import { trans } from 'laravel-vue-i18n'

const props = defineProps<{
  channels: ChatChannel[]
  totalUnread: number
  allUsers?: User[]
  isAdmin?: boolean
  auth: {
    user: User
  }
}>()
const chat = useChat()
const reverb = useReverb()
const notifications = useNotifications()

const isMobileView = ref(false)
const showMobileChat = ref(false)

// Initialize channels from props
chat.channels.value = props.channels

// Check mobile view
const checkMobileView = () => {
  isMobileView.value = window.innerWidth < 768
}

// Watch active channel for mobile
watch(() => chat.activeChannel.value, (newChannel) => {
  if (isMobileView.value && newChannel) {
    showMobileChat.value = true
  }
})

// Back to channels list on mobile
const backToChannelsList = () => {
  showMobileChat.value = false
  chat.activeChannel.value = null
}

// Watch active channel and connect to it
watch(() => chat.activeChannel.value, (newChannel, oldChannel) => {
  // Disconnect from old channel
  if (oldChannel) {
    reverb.disconnectFromChannel()
  }

  // Connect to new channel
  if (newChannel) {
    reverb.connectToChannel(newChannel.id, {
      onMessageSent: (data) => {
        // Clear typing indicator for user who sent the message
        chat.typingUsers.value = chat.typingUsers.value.filter(u => u.user_id !== data.message.user_id)
        
        // Only add message if it belongs to the currently active channel
        if (chat.activeChannel.value?.id === data.message.channel_id) {
          if (!chat.messages.value.find(m => m.id === data.message.id)) {
            chat.messages.value.push(data.message)
            console.log('✅ Message added via WebSocket')
          }
        }
      },
      onMessageRead: (data) => {
        // Update read status
        const message = chat.messages.value.find(m => m.id === data.message_id)
        if (message && message.reads) {
          message.reads.push({
            user_id: data.user_id,
            read_at: data.read_at
          })
        }
      },
      onMessageEdited: (data) => {
        // Update edited message
        const message = chat.messages.value.find(m => m.id === data.message_id)
        if (message) {
          message.content = data.message
          message.is_edited = data.is_edited
          message.edited_at = data.edited_at
        }
      },
      onMessageDeleted: (data) => {
        // Remove deleted message
        chat.messages.value = chat.messages.value.filter(m => m.id !== data.message_id)
      },
      onUserTyping: (data) => {
        // Update typing users
        if (data.is_typing) {
          if (!chat.typingUsers.value.find(u => u.user_id === data.user_id)) {
            chat.typingUsers.value.push({
              user_id: data.user_id,
              user_name: data.user_name
            })
          }
        } else {
          chat.typingUsers.value = chat.typingUsers.value.filter(u => u.user_id !== data.user_id)
        }
      }
    })

    if (isMobileView.value) {
      showMobileChat.value = true
    }
  }
})

// Handle incoming messages from notifications
const handleIncomingMessage = (data: any) => {
  // Clear typing indicator for user who sent the message
  chat.typingUsers.value = chat.typingUsers.value.filter(u => u.user_id !== data.message.user_id)
  
  // Update channel in the list
  const channel = chat.channels.value.find(c => c.id === data.message.channel_id)
  if (channel) {
    // Update last message preview
    channel.latest_message = data.message
    channel.updated_at = data.message.created_at
    
    // Increment unread count if not in active channel
    if (!chat.activeChannel.value || chat.activeChannel.value.id !== channel.id) {
      channel.unread_count = (channel.unread_count || 0) + 1
    } else {
      // If channel is active, add message to the list
      if (!chat.messages.value.find(m => m.id === data.message.id)) {
        chat.messages.value.push(data.message)
      }
    }
    
    // Move channel to top
    chat.channels.value = [
      channel,
      ...chat.channels.value.filter(c => c.id !== channel.id)
    ]
  } else {
    // New channel, fetch channels list
    chat.fetchChannels()
  }
}

// Polling fallback for when WebSocket is not available
let pollingInterval: NodeJS.Timeout | null = null
let lastActiveChannelId: number | null = null

const startPolling = () => {
  // Poll for new messages every 2 seconds as fallback
  pollingInterval = setInterval(async () => {
    if (chat.activeChannel.value && !chat.loading.value) {
      const currentChannelId = chat.activeChannel.value.id
      const existingMessageIds = new Set(chat.messages.value.map(m => m.id))
      
      try {
        // Only fetch if we're still on the same channel
        if (currentChannelId === chat.activeChannel.value?.id) {
          const response = await window.axios.get(`/chat/channels/${currentChannelId}/messages`, {
            params: { page: 1, per_page: 50 }
          })
          
          // Only update if we're STILL on the same channel after the request
          if (currentChannelId === chat.activeChannel.value?.id) {
            const fetchedMessages = response.data.messages
            
            // Find NEW messages that we don't have yet
            const newMessages = fetchedMessages.filter(msg => !existingMessageIds.has(msg.id))
            
            // Add only new messages to the end
            if (newMessages.length > 0) {
              chat.messages.value.push(...newMessages)
              console.log(`✅ ${newMessages.length} new message(s) added via polling`)
            }
          }
        }
      } catch (error) {
        console.error('Polling error:', error)
      }
    }
    
    // Also update channel list (less frequently)
    if (!chat.loading.value) {
      chat.fetchChannels()
    }
  }, 2000) // Poll every 2 seconds
}

const stopPolling = () => {
  if (pollingInterval) {
    clearInterval(pollingInterval)
    pollingInterval = null
  }
}

// Connect to presence channel
onMounted(() => {
  reverb.connectToPresence()
  notifications.fetchUnreadCount()
  notifications.listenToNotifications(props.auth.user.id, handleIncomingMessage)
  checkMobileView()
  window.addEventListener('resize', checkMobileView)
  
  // Start polling as fallback
  startPolling()
  console.log('✅ Polling started for real-time updates')
})

// Cleanup on unmount
import { onUnmounted } from 'vue'
onUnmounted(() => {
  stopPolling()
  window.removeEventListener('resize', checkMobileView)
})
</script>

<template>
  <AppLayout :title="trans('chat.title')">
    <Head :title="trans('chat.title')" />

    <div class="py-6">
      <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
          <!-- Connection Status -->
          <ConnectionStatus />
          
          <div class="flex h-[calc(100vh-12rem)]">
            <!-- Sidebar - Hidden on mobile when chat is active -->
            <div 
              v-show="!isMobileView || !showMobileChat"
              class="w-full md:w-80 lg:w-96 border-r border-gray-200 dark:border-gray-700 flex flex-col"
            >
              <ChatSidebar 
                :channels="chat.sortedChannels.value"
                :active-channel="chat.activeChannel.value"
                :unread-count="chat.unreadCount.value"
                :loading="chat.loading.value"
                :online-users="reverb.onlineUsers.value"
                :all-users="props.allUsers || []"
                :is-admin="props.isAdmin || false"
                @select-channel="chat.selectChannel"
                @create-channel="chat.createChannel"
              />
            </div>

            <!-- Main Chat Area - Hidden on mobile when no chat is active -->
            <div 
              v-show="!isMobileView || showMobileChat"
              class="flex-1 flex flex-col"
            >
              <ChatWindow
                v-if="chat.activeChannel.value"
                :channel="chat.activeChannel.value"
                :messages="chat.sortedMessages.value"
                :typing-users="chat.typingUsers.value"
                :loading="chat.loading.value"
                :sending-message="chat.sendingMessage.value"
                :has-more-messages="chat.hasMoreMessages.value"
                :online-users="reverb.onlineUsers.value"
                :current-user="props.auth.user"
                :is-mobile="isMobileView"
                @send-message="chat.sendMessage"
                @edit-message="chat.editMessage"
                @delete-message="chat.deleteMessage"
                @react-to-message="chat.reactToMessage"
                @load-more="chat.loadMoreMessages"
                @typing="chat.sendTypingIndicator"
                @upload-file="chat.uploadFile"
                @back="backToChannelsList"
              />
              <EmptyState 
                v-else
                :is-mobile="isMobileView"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

