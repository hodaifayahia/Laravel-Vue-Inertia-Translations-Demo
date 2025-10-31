import { router } from '@inertiajs/vue3'
import type {
  ChatChannel,
  ChatIssue,
  ChatMessage,
  ChatNotification,
  TypingUser,
} from '@/types/chat'
import { computed, ref } from 'vue'

export function useChat() {
  // State
  const channels = ref<ChatChannel[]>([])
  const activeChannel = ref<ChatChannel | null>(null)
  const messages = ref<ChatMessage[]>([])
  const typingUsers = ref<TypingUser[]>([])
  const loading = ref(false)
  const sendingMessage = ref(false)
  const hasMoreMessages = ref(false)
  const currentPage = ref(1)

  // Computed
  const unreadCount = computed(() => {
    return channels.value.reduce((total, channel) => total + channel.unread_count, 0)
  })

  const sortedChannels = computed(() => {
    return [...channels.value].sort(
      (a, b) => new Date(b.updated_at).getTime() - new Date(a.updated_at).getTime(),
    )
  })

  const sortedMessages = computed(() => {
    return [...messages.value].sort(
      (a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime(),
    )
  })

  // Methods
  const fetchChannels = async () => {
    try {
      const response = await window.axios.get('/chat/channels')
      channels.value = response.data.channels
    } catch (error) {
      console.error('Error fetching channels:', error)
    }
  }

  const fetchMessages = async (channelId: number, page = 1) => {
    if (loading.value) return

    loading.value = true
    try {
      const response = await window.axios.get(`/chat/channels/${channelId}/messages`, {
        params: { page, per_page: 50 },
      })

      if (page === 1) {
        messages.value = response.data.messages
      } else {
        messages.value = [...response.data.messages, ...messages.value]
      }

      hasMoreMessages.value = response.data.has_more
      currentPage.value = response.data.current_page
    } catch (error) {
      console.error('Error fetching messages:', error)
    } finally {
      loading.value = false
    }
  }

  const loadMoreMessages = async () => {
    if (!activeChannel.value || !hasMoreMessages.value || loading.value) return
    await fetchMessages(activeChannel.value.id, currentPage.value + 1)
  }

  const selectChannel = async (channel: ChatChannel) => {
    // Clear messages from previous channel immediately
    messages.value = []
    typingUsers.value = []
    
    activeChannel.value = channel
    currentPage.value = 1
    hasMoreMessages.value = false
    
    // Fetch messages for the new channel
    await fetchMessages(channel.id)
  }

  const sendMessage = async (
    channelId: number,
    content: string,
    file?: File,
    replyToId?: number,
  ) => {
    if (sendingMessage.value) return

    sendingMessage.value = true
    try {
      const formData = new FormData()

      if (content) {
        formData.append('message', content)
      }

      if (file) {
        formData.append('file', file)
      }

      if (replyToId) {
        formData.append('reply_to_message_id', replyToId.toString())
      }

      const response = await window.axios.post(
        `/chat/channels/${channelId}/messages`,
        formData,
        {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        },
      )

      // Add message immediately to your own view
      const newMessage = response.data.message
      if (!messages.value.find(m => m.id === newMessage.id)) {
        messages.value.push(newMessage)
      }

      return newMessage
    } catch (error) {
      console.error('Error sending message:', error)
      throw error
    } finally {
      sendingMessage.value = false
    }
  }

  const editMessage = async (messageId: number, content: string) => {
    try {
      const response = await window.axios.put(`/chat/messages/${messageId}`, {
        message: content,
      })

      // Update message in local state
      const index = messages.value.findIndex((m) => m.id === messageId)
      if (index !== -1) {
        messages.value[index] = response.data.message
      }

      return response.data.message
    } catch (error) {
      console.error('Error editing message:', error)
      throw error
    }
  }

  const deleteMessage = async (messageId: number) => {
    try {
      await window.axios.delete(`/chat/messages/${messageId}`)

      // Remove message from local state
      messages.value = messages.value.filter((m) => m.id !== messageId)
    } catch (error) {
      console.error('Error deleting message:', error)
      throw error
    }
  }

  const markAsRead = async (channelId: number, messageIds: number[]) => {
    try {
      await window.axios.post(`/chat/channels/${channelId}/read`, {
        message_ids: messageIds,
      })

      // Update local state
      messageIds.forEach((id) => {
        const message = messages.value.find((m) => m.id === id)
        if (message) {
          message.is_read = true
        }
      })
    } catch (error) {
      console.error('Error marking as read:', error)
    }
  }

  const sendTypingIndicator = async (channelId: number, isTyping: boolean) => {
    try {
      await window.axios.post(`/chat/channels/${channelId}/typing`, {
        is_typing: isTyping,
      })
    } catch (error) {
      console.error('Error sending typing indicator:', error)
    }
  }

  const reactToMessage = async (messageId: number, emoji: string) => {
    try {
      const response = await window.axios.post(`/chat/messages/${messageId}/react`, {
        emoji,
      })

      // Update message reactions
      const message = messages.value.find((m) => m.id === messageId)
      if (message && response.data.action === 'added') {
        if (!message.reactions) {
          message.reactions = []
        }
        message.reactions.push(response.data.reaction)
      } else if (message && response.data.action === 'removed') {
        message.reactions = message.reactions?.filter((r) => r.emoji !== emoji || r.user_id !== response.data.reaction.user_id)
      }

      return response.data
    } catch (error) {
      console.error('Error reacting to message:', error)
      throw error
    }
  }

  const createChannel = async (type: 'direct' | 'group', userIds: number[], name?: string) => {
    try {
      const response = await window.axios.post('/chat/channels', {
        type,
        user_ids: userIds,
        name,
      })

      const newChannel = response.data.channel
      
      // Add to channels list
      channels.value.unshift(newChannel)
      
      // Automatically select the new channel
      await selectChannel(newChannel)
      
      console.log('✅ Channel created and selected:', newChannel)
      
      return newChannel
    } catch (error) {
      console.error('Error creating channel:', error)
      throw error
    }
  }

  const uploadFile = async (file: File) => {
    try {
      const formData = new FormData()
      formData.append('file', file)

      const response = await window.axios.post('/chat/upload', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      return response.data
    } catch (error) {
      console.error('Error uploading file:', error)
      throw error
    }
  }

  const addTypingUser = (userId: number, userName: string) => {
    if (!typingUsers.value.find((u) => u.user_id === userId)) {
      typingUsers.value.push({ user_id: userId, user_name: userName })
    }
  }

  const removeTypingUser = (userId: number) => {
    typingUsers.value = typingUsers.value.filter((u) => u.user_id !== userId)
  }

  const addMessage = (message: ChatMessage) => {
    // Check if message already exists
    if (!messages.value.find((m) => m.id === message.id)) {
      messages.value.push(message)
    }

    // Update channel's latest message
    const channel = channels.value.find((c) => c.id === message.channel_id)
    if (channel) {
      channel.latest_message = message
      channel.updated_at = message.created_at
    }
  }

  const updateMessage = (messageId: number, updates: Partial<ChatMessage>) => {
    const index = messages.value.findIndex((m) => m.id === messageId)
    if (index !== -1) {
      messages.value[index] = { ...messages.value[index], ...updates }
    }
  }

  const removeMessage = (messageId: number) => {
    messages.value = messages.value.filter((m) => m.id !== messageId)
  }

  return {
    // State
    channels,
    activeChannel,
    messages,
    typingUsers,
    loading,
    sendingMessage,
    hasMoreMessages,

    // Computed
    unreadCount,
    sortedChannels,
    sortedMessages,

    // Methods
    fetchChannels,
    fetchMessages,
    loadMoreMessages,
    selectChannel,
    sendMessage,
    editMessage,
    deleteMessage,
    markAsRead,
    sendTypingIndicator,
    reactToMessage,
    createChannel,
    uploadFile,
    addTypingUser,
    removeTypingUser,
    addMessage,
    updateMessage,
    removeMessage,
  }
}
