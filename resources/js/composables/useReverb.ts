import type {
  ChatChannel,
  ChatMessage,
  ChatMessageReaction,
  TypingUser,
  User,
} from '@/types/chat'
import { onMounted, onUnmounted, ref } from 'vue'

export function useReverb(channelId?: number) {
  const isConnected = ref(false)
  const channel = ref<any>(null)
  const presenceChannel = ref<any>(null)
  const onlineUsers = ref<User[]>([])

  // Connect to chat presence channel
  const connectToPresence = () => {
    if (!window.Echo) {
      console.error('Echo not initialized')
      return
    }

    presenceChannel.value = window.Echo.join('chat.presence')
      .here((users: User[]) => {
        onlineUsers.value = users
        isConnected.value = true
      })
      .joining((user: User) => {
        if (!onlineUsers.value.find((u) => u.id === user.id)) {
          onlineUsers.value.push(user)
        }
      })
      .leaving((user: User) => {
        onlineUsers.value = onlineUsers.value.filter((u) => u.id !== user.id)
      })
      .error((error: any) => {
        console.error('Presence channel error:', error)
        isConnected.value = false
      })
  }

  // Connect to specific chat channel
  const connectToChannel = (
    id: number,
    callbacks: {
      onMessageSent?: (data: { message: ChatMessage }) => void
      onMessageRead?: (data: {
        message_id: number
        user_id: number
        user_name: string
        read_at: string
      }) => void
      onMessageEdited?: (data: {
        message_id: number
        message: string
        is_edited: boolean
        edited_at: string
      }) => void
      onMessageDeleted?: (data: { message_id: number; channel_id: number }) => void
      onUserTyping?: (data: { user_id: number; user_name: string; is_typing: boolean }) => void
      onUserBlocked?: (data: {
        blocked_user_id: number
        blocked_by_id: number
        blocked_by_name: string
        channel_id: number
        reason?: string
        blocked_at: string
      }) => void
      onUserUnblocked?: (data: {
        unblocked_user_id: number
        unblocked_by_id: number
        unblocked_by_name: string
        channel_id: number
      }) => void
      onReactionAdded?: (data: { reaction: ChatMessageReaction }) => void
      onReactionRemoved?: (data: {
        reaction_id: number
        message_id: number
        user_id: number
      }) => void
    },
  ) => {
    if (!window.Echo) {
      console.error('Echo not initialized')
      return
    }

    channel.value = window.Echo.join(`chat.channel.${id}`)
      .here((users: User[]) => {
        console.log('Channel members:', users)
      })
      .joining((user: User) => {
        console.log('User joined:', user.name)
      })
      .leaving((user: User) => {
        console.log('User left:', user.name)
      })

    // Listen to events
    if (callbacks.onMessageSent) {
      channel.value.listen('.message.sent', callbacks.onMessageSent)
    }

    if (callbacks.onMessageRead) {
      channel.value.listen('.message.read', callbacks.onMessageRead)
    }

    if (callbacks.onMessageEdited) {
      channel.value.listen('.message.edited', callbacks.onMessageEdited)
    }

    if (callbacks.onMessageDeleted) {
      channel.value.listen('.message.deleted', callbacks.onMessageDeleted)
    }

    if (callbacks.onUserTyping) {
      channel.value.listen('.user.typing', callbacks.onUserTyping)
    }

    if (callbacks.onUserBlocked) {
      channel.value.listen('.user.blocked', callbacks.onUserBlocked)
    }

    if (callbacks.onUserUnblocked) {
      channel.value.listen('.user.unblocked', callbacks.onUserUnblocked)
    }

    if (callbacks.onReactionAdded) {
      channel.value.listen('.message.reaction.added', callbacks.onReactionAdded)
    }

    if (callbacks.onReactionRemoved) {
      channel.value.listen('.message.reaction.removed', callbacks.onReactionRemoved)
    }
  }

  // Disconnect from channel
  const disconnectFromChannel = () => {
    if (channel.value) {
      window.Echo.leave(`chat.channel.${channelId}`)
      channel.value = null
    }
  }

  // Disconnect from presence
  const disconnectFromPresence = () => {
    if (presenceChannel.value) {
      window.Echo.leave('chat.presence')
      presenceChannel.value = null
      isConnected.value = false
    }
  }

  // Check if user is online
  const isUserOnline = (userId: number): boolean => {
    return onlineUsers.value.some((u) => u.id === userId)
  }

  // Cleanup on unmount
  onUnmounted(() => {
    disconnectFromChannel()
    disconnectFromPresence()
  })

  return {
    isConnected,
    onlineUsers,
    connectToPresence,
    connectToChannel,
    disconnectFromChannel,
    disconnectFromPresence,
    isUserOnline,
  }
}
