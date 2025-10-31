<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Bell } from 'lucide-vue-next'
import { useNotifications } from '@/composables/useNotifications'
import { trans } from 'laravel-vue-i18n'
import { Link, usePage } from '@inertiajs/vue3'
import { onClickOutside } from '@vueuse/core'

const notifications = useNotifications()
const page = usePage()
const showDropdown = ref(false)

// Get current user ID
const userId = computed(() => page.props.auth?.user?.id)

// Connect to notifications on mount
onMounted(() => {
  if (userId.value) {
    notifications.fetchUnreadCount()
    notifications.listenToNotifications(userId.value)
  }
})

// Toggle dropdown
const toggleDropdown = () => {
  if (!showDropdown.value) {
    notifications.fetchNotifications()
  }
  showDropdown.value = !showDropdown.value
}

// Mark as read and close
const markAsReadAndClose = (notificationId: number) => {
  notifications.markAsRead(notificationId)
}

// Mark all as read
const handleMarkAllRead = () => {
  notifications.markAllAsRead()
}

// Format time
const formatTime = (timestamp: string) => {
  const date = new Date(timestamp)
  const now = new Date()
  const diff = now.getTime() - date.getTime()
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)

  if (minutes < 1) return trans('chat.just_now')
  if (minutes < 60) return trans('chat.minutes_ago', { count: String(minutes) })
  if (hours < 24) return trans('chat.hours_ago', { count: String(hours) })
  if (days < 7) return trans('chat.days_ago', { count: String(days) })
  return date.toLocaleDateString()
}

// Close dropdown when clicking outside
const dropdownRef = ref<HTMLElement>()

onClickOutside(dropdownRef, () => {
  showDropdown.value = false
})
</script>

<template>
  <div class="relative">
    <!-- Bell Button -->
    <button
      @click="toggleDropdown"
      class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
      :title="trans('chat.notification_settings')"
    >
      <Bell class="w-5 h-5" />
      
      <!-- Unread Badge -->
      <span
        v-if="notifications.unreadCount.value > 0"
        class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse"
      >
        {{ notifications.unreadCount.value > 9 ? '9+' : notifications.unreadCount.value }}
      </span>
    </button>

    <!-- Dropdown -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-1"
    >
      <div
        v-if="showDropdown"
        ref="dropdownRef"
        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
      >
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
            {{ trans('chat.new_message') }}
          </h3>
          <button
            v-if="notifications.unreadCount.value > 0"
            @click="handleMarkAllRead"
            class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300"
          >
            {{ trans('chat.mark_all_read') }}
          </button>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
          <div v-if="notifications.loading.value" class="p-4 text-center text-gray-500">
            {{ trans('chat.loading') }}...
          </div>

          <div v-else-if="notifications.notifications.value.length === 0" class="p-4 text-center text-gray-500">
            {{ trans('chat.no_notifications') }}
          </div>

          <div v-else>
            <Link
              v-for="notification in notifications.sortedNotifications.value"
              :key="notification.id"
              :href="notification.data.url || '/chat'"
              @click="markAsReadAndClose(notification.id)"
              class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-200 dark:border-gray-700 last:border-b-0"
              :class="{
                'bg-indigo-50 dark:bg-indigo-900/10': !notification.read_at
              }"
            >
              <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <Bell class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ notification.data.title }}
                  </p>
                  <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 truncate">
                    {{ notification.data.message }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                    {{ formatTime(notification.created_at) }}
                  </p>
                </div>
                <div v-if="!notification.read_at" class="flex-shrink-0">
                  <div class="w-2 h-2 bg-indigo-600 rounded-full"></div>
                </div>
              </div>
            </Link>
          </div>
        </div>

        <!-- Footer -->
        <div class="p-3 border-t border-gray-200 dark:border-gray-700">
          <Link
            href="/chat"
            @click="closeDropdown"
            class="block w-full text-center text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium"
          >
            {{ trans('chat.view_all_conversations') }}
          </Link>
        </div>
      </div>
    </Transition>
  </div>
</template>
