import type { ChatNotification } from '@/types/chat'
import { computed, ref } from 'vue'

export function useNotifications() {
  const notifications = ref<ChatNotification[]>([])
  const unreadCount = ref(0)
  const loading = ref(false)

  // Computed
  const unreadNotifications = computed(() => {
    return notifications.value.filter((n) => !n.read_at)
  })

  const sortedNotifications = computed(() => {
    return [...notifications.value].sort(
      (a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime(),
    )
  })

  // Methods
  const fetchNotifications = async () => {
    loading.value = true
    try {
      const response = await window.axios.get('/chat/notifications')
      notifications.value = response.data.data || []
    } catch (error) {
      console.error('Error fetching notifications:', error)
    } finally {
      loading.value = false
    }
  }

  const fetchUnreadCount = async () => {
    try {
      const response = await window.axios.get('/chat/notifications/unread-count')
      unreadCount.value = response.data.unread_count
    } catch (error) {
      console.error('Error fetching unread count:', error)
    }
  }

  const markAsRead = async (notificationId: number) => {
    try {
      await window.axios.put(`/chat/notifications/${notificationId}/read`)

      // Update local state
      const notification = notifications.value.find((n) => n.id === notificationId)
      if (notification) {
        notification.read_at = new Date().toISOString()
      }

      unreadCount.value = Math.max(0, unreadCount.value - 1)
    } catch (error) {
      console.error('Error marking notification as read:', error)
    }
  }

  const markAllAsRead = async () => {
    try {
      await window.axios.post('/chat/notifications/read-all')

      // Update all notifications
      notifications.value.forEach((n) => {
        n.read_at = new Date().toISOString()
      })

      unreadCount.value = 0
    } catch (error) {
      console.error('Error marking all as read:', error)
    }
  }

  const deleteNotification = async (notificationId: number) => {
    try {
      await window.axios.delete(`/chat/notifications/${notificationId}`)

      // Remove from local state
      const notification = notifications.value.find((n) => n.id === notificationId)
      if (notification && !notification.read_at) {
        unreadCount.value = Math.max(0, unreadCount.value - 1)
      }

      notifications.value = notifications.value.filter((n) => n.id !== notificationId)
    } catch (error) {
      console.error('Error deleting notification:', error)
    }
  }

  const addNotification = (notification: ChatNotification) => {
    // Check if already exists
    if (!notifications.value.find((n) => n.id === notification.id)) {
      notifications.value.unshift(notification)
      if (!notification.read_at) {
        unreadCount.value++
      }
    }
  }

  // Listen to user-specific notification channel
  const listenToNotifications = (userId: number, onMessageReceived?: (data: any) => void) => {
    if (!window.Echo) {
      console.error('Echo not initialized')
      return
    }

    window.Echo.private(`chat.user.${userId}`)
      .listen('.message.sent', (data: any) => {
        addNotification(data.notification)
        // Call callback to update channels/messages
        if (onMessageReceived) {
          onMessageReceived(data)
        }
      })
      .listen('.user.blocked', (data: any) => {
        addNotification({
          id: Date.now(),
          user_id: userId,
          type: 'user_blocked',
          data,
          read_at: null,
          created_at: new Date().toISOString(),
        })
      })
      .listen('.issue.assigned', (data: any) => {
        addNotification({
          id: Date.now(),
          user_id: userId,
          type: 'issue_assigned',
          data,
          read_at: null,
          created_at: new Date().toISOString(),
        })
      })
  }

  return {
    notifications,
    unreadCount,
    loading,
    unreadNotifications,
    sortedNotifications,
    fetchNotifications,
    fetchUnreadCount,
    markAsRead,
    markAllAsRead,
    deleteNotification,
    addNotification,
    listenToNotifications,
  }
}
