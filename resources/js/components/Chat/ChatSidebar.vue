<script setup lang="ts">
import { ref, computed } from 'vue'
import type { ChatChannel, User } from '@/types/chat'
import { trans } from 'laravel-vue-i18n'
import { Search, Plus, MessageCircle, ChevronDown, ChevronRight, Users } from 'lucide-vue-next'
import OnlineStatusBadge from './OnlineStatusBadge.vue'
import CreateChannelModal from './CreateChannelModal.vue'

const props = defineProps<{
  channels: ChatChannel[]
  activeChannel: ChatChannel | null
  unreadCount: number
  loading: boolean
  onlineUsers: User[]
  allUsers: User[]
  isAdmin: boolean
}>()

const emit = defineEmits<{
  selectChannel: [channel: ChatChannel]
  createChannel: [type: 'direct' | 'group', userIds: number[], name?: string]
}>()

const searchQuery = ref('')
const showCreateModal = ref(false)
const showAllUsers = ref(false)
const collapsedRoles = ref<Record<string, boolean>>({})

const filteredChannels = computed(() => {
  if (!searchQuery.value) return props.channels

  const query = searchQuery.value.toLowerCase()
  return props.channels.filter(channel => {
    return channel.name?.toLowerCase().includes(query) ||
           channel.users?.some(user => user.name.toLowerCase().includes(query))
  })
})

const isUserOnline = (userId: number) => {
  return props.onlineUsers.some(user => user.id === userId)
}

const getChannelName = (channel: ChatChannel) => {
  if (channel.name) return channel.name
  if (channel.type === 'direct' && channel.users) {
    const otherUser = channel.users[0]
    return otherUser?.name || trans('chat.unknown_user')
  }
  return trans('chat.unnamed_channel')
}

const getChannelAvatar = (channel: ChatChannel) => {
  if (channel.type === 'direct' && channel.users) {
    const otherUser = channel.users[0]
    return otherUser?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(otherUser?.name || 'U')}`
  }
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(channel.name || 'G')}&background=6366f1&color=fff`
}

const formatLastMessageTime = (timestamp: string) => {
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

const filteredAllUsers = computed(() => {
  if (!searchQuery.value || !showAllUsers.value) return props.allUsers

  const query = searchQuery.value.toLowerCase()
  return props.allUsers.filter(user => {
    return user.name.toLowerCase().includes(query) ||
           user.email.toLowerCase().includes(query)
  })
})

// Group users by their roles
const usersByRole = computed(() => {
  const grouped: Record<string, User[]> = {}
  
  filteredAllUsers.value.forEach(user => {
    // Get user's roles (assuming user has a roles property that's an array)
    const userRoles = (user as any).roles || []
    
    if (userRoles.length === 0) {
      // If user has no roles, add to "No Role" group
      if (!grouped['no-role']) {
        grouped['no-role'] = []
      }
      grouped['no-role'].push(user)
    } else {
      // Add user to each of their role groups
      userRoles.forEach((role: string) => {
        if (!grouped[role]) {
          grouped[role] = []
        }
        grouped[role].push(user)
      })
    }
  })
  
  return grouped
})

// Get sorted role names
const sortedRoles = computed(() => {
  const roles = Object.keys(usersByRole.value)
  // Custom sort order: super-admin, admin, manager, user, viewer, others, no-role
  const priority = ['super-admin', 'admin', 'manager', 'user', 'viewer']
  
  return roles.sort((a, b) => {
    const aIndex = priority.indexOf(a)
    const bIndex = priority.indexOf(b)
    
    if (aIndex !== -1 && bIndex !== -1) return aIndex - bIndex
    if (aIndex !== -1) return -1
    if (bIndex !== -1) return 1
    if (a === 'no-role') return 1
    if (b === 'no-role') return -1
    return a.localeCompare(b)
  })
})

// Toggle role section collapse
const toggleRole = (role: string) => {
  collapsedRoles.value[role] = !collapsedRoles.value[role]
}

// Check if role is collapsed
const isRoleCollapsed = (role: string) => {
  return collapsedRoles.value[role] || false
}

// Get role display name
const getRoleDisplayName = (role: string) => {
  if (role === 'no-role') return 'No Role'
  return role.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

// Get role color
const getRoleColor = (role: string) => {
  const colors: Record<string, string> = {
    'super-admin': 'from-purple-500 to-pink-500',
    'admin': 'from-red-500 to-orange-500',
    'manager': 'from-blue-500 to-cyan-500',
    'user': 'from-green-500 to-emerald-500',
    'viewer': 'from-gray-500 to-slate-500',
    'no-role': 'from-gray-400 to-gray-500',
  }
  return colors[role] || 'from-indigo-500 to-purple-500'
}

const handleSelectChannel = (channel: ChatChannel) => {
  showAllUsers.value = false
  emit('selectChannel', channel)
}

const handleCreateChannel = (type: 'direct' | 'group', userIds: number[], name?: string) => {
  emit('createChannel', type, userIds, name)
  showCreateModal.value = false
}

const handleStartChatWithUser = (user: User) => {
  // Create a direct message channel with this user
  emit('createChannel', 'direct', [user.id])
  showAllUsers.value = false
  searchQuery.value = ''
}

const toggleView = () => {
  showAllUsers.value = !showAllUsers.value
  searchQuery.value = ''
}
</script>

<template>
  <div class="flex flex-col h-full bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-950">
    <!-- Header with Glassmorphism Effect -->
    <div class="relative p-5 backdrop-blur-xl bg-white/80 dark:bg-gray-900/80 border-b border-gray-200/50 dark:border-gray-700/50">
      <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl blur-lg opacity-50 animate-pulse"></div>
            <div class="relative p-2 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-xl shadow-lg">
              <MessageCircle class="w-6 h-6 text-white" />
            </div>
          </div>
          <div>
            <h2 class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent">
              {{ trans('chat.messages') }}
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              {{ channels.length }} {{ channels.length === 1 ? 'conversation' : 'conversations' }}
            </p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span 
            v-if="unreadCount > 0"
            class="px-3 py-1 text-xs font-bold bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-full shadow-lg animate-bounce"
          >
            {{ unreadCount }}
          </span>
          <button
            @click="showCreateModal = true"
            class="group relative p-2.5 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
            :title="trans('chat.new_conversation')"
          >
            <Plus class="w-5 h-5 text-white" />
            <div class="absolute inset-0 rounded-xl bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
          </button>
        </div>
      </div>

      <!-- Enhanced Search with Animation -->
      <div class="relative group">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl blur-md opacity-0 group-focus-within:opacity-20 transition-opacity duration-300"></div>
        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors duration-300" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="showAllUsers ? trans('chat.search_users') : trans('chat.search_conversations')"
          class="relative w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all duration-300"
        />
      </div>

      <!-- Modern Toggle with Pill Design -->
      <div v-if="isAdmin && allUsers.length > 0" class="mt-4">
        <div class="relative inline-flex w-full p-1 bg-gray-100 dark:bg-gray-800 rounded-xl">
          <div
            class="absolute inset-y-1 w-[calc(50%-0.25rem)] bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg transition-transform duration-300 ease-in-out"
            :style="{ transform: showAllUsers ? 'translateX(calc(100% + 0.5rem))' : 'translateX(0)' }"
          ></div>
          <button
            @click="showAllUsers = false"
            class="relative z-10 flex-1 px-4 py-2 text-sm font-semibold rounded-lg transition-colors duration-300"
            :class="!showAllUsers ? 'text-white' : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'"
          >
            {{ trans('chat.conversations') }}
          </button>
          <button
            @click="showAllUsers = true"
            class="relative z-10 flex-1 px-4 py-2 text-sm font-semibold rounded-lg transition-colors duration-300"
            :class="showAllUsers ? 'text-white' : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'"
          >
            {{ trans('chat.all_users') }}
          </button>
        </div>
      </div>
    </div>

    <!-- All Users List with Role Grouping (Admin Only) -->
    <div v-if="showAllUsers && isAdmin" class="flex-1 overflow-y-auto px-2 py-3">
      <!-- Header Stats -->
      <div class="mb-4 p-4 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl border border-indigo-200 dark:border-indigo-800">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg">
              <Users class="w-5 h-5 text-white" />
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ trans('chat.all_system_users') }}
              </p>
              <p class="text-xs text-gray-600 dark:text-gray-400">
                {{ filteredAllUsers.length }} users in {{ sortedRoles.length }} roles
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- No Users Found -->
      <div v-if="filteredAllUsers.length === 0" class="flex flex-col items-center justify-center p-8 text-center">
        <div class="w-20 h-20 mb-4 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
          <Users class="w-10 h-10 text-gray-400" />
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400">
          {{ trans('chat.no_users_found') }}
        </p>
      </div>

      <!-- Role Groups -->
      <div v-else class="space-y-2">
        <div 
          v-for="role in sortedRoles" 
          :key="role"
          class="rounded-xl overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-300"
        >
          <!-- Role Header (Collapsible) -->
          <button
            @click="toggleRole(role)"
            class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors duration-200"
          >
            <div class="flex items-center gap-3">
              <!-- Expand/Collapse Icon -->
              <div class="flex-shrink-0">
                <ChevronDown 
                  v-if="!isRoleCollapsed(role)"
                  class="w-5 h-5 text-gray-600 dark:text-gray-400 transition-transform duration-300"
                />
                <ChevronRight 
                  v-else
                  class="w-5 h-5 text-gray-600 dark:text-gray-400 transition-transform duration-300"
                />
              </div>

              <!-- Role Badge -->
              <div 
                class="px-3 py-1.5 rounded-lg bg-gradient-to-r shadow-sm"
                :class="getRoleColor(role)"
              >
                <span class="text-sm font-bold text-white">
                  {{ getRoleDisplayName(role) }}
                </span>
              </div>

              <!-- User Count -->
              <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                ({{ usersByRole[role].length }})
              </span>
            </div>

            <!-- Online Count -->
            <div class="flex items-center gap-2">
              <div class="flex items-center gap-1">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-xs text-gray-600 dark:text-gray-400">
                  {{ usersByRole[role].filter(u => isUserOnline(u.id)).length }} online
                </span>
              </div>
            </div>
          </button>

          <!-- Users in Role (Collapsible Content) -->
          <div 
            v-show="!isRoleCollapsed(role)"
            class="border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50"
          >
            <button
              v-for="user in usersByRole[role]"
              :key="user.id"
              @click="handleStartChatWithUser(user)"
              class="group w-full p-3 hover:bg-white dark:hover:bg-gray-800 transition-all duration-200 text-left border-b border-gray-100 dark:border-gray-800 last:border-b-0"
            >
              <div class="flex items-center gap-3">
                <!-- User Avatar -->
                <div class="relative flex-shrink-0">
                  <div class="absolute inset-0 rounded-full bg-gradient-to-r opacity-0 group-hover:opacity-20 transition-opacity duration-300"
                    :class="getRoleColor(role)"></div>
                  <img
                    :src="user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}`"
                    :alt="user.name"
                    class="relative w-11 h-11 rounded-full ring-2 ring-white dark:ring-gray-800 group-hover:ring-indigo-500/50 transition-all duration-300"
                  />
                  <OnlineStatusBadge
                    :is-online="isUserOnline(user.id)"
                    class="absolute bottom-0 right-0"
                  />
                </div>

                <!-- User Info -->
                <div class="flex-1 min-w-0">
                  <h3 class="font-semibold text-sm text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ user.name }}
                  </h3>
                  <p class="text-xs text-gray-600 dark:text-gray-400 truncate">
                    {{ user.email }}
                  </p>
                </div>

                <!-- Hover Indicator -->
                <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <MessageCircle class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                </div>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Channel List with Beautiful Cards -->
    <div v-else class="flex-1 overflow-y-auto px-2 py-3 space-y-2">
      <!-- Loading State with Skeleton -->
      <div v-if="loading && channels.length === 0" class="space-y-3 p-2">
        <div v-for="i in 5" :key="i" class="animate-pulse">
          <div class="flex items-center gap-3 p-4 bg-gray-100 dark:bg-gray-800 rounded-xl">
            <div class="w-12 h-12 bg-gray-300 dark:bg-gray-700 rounded-full"></div>
            <div class="flex-1 space-y-2">
              <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
              <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/2"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State with Beautiful Illustration -->
      <div v-else-if="filteredChannels.length === 0" class="flex flex-col items-center justify-center p-8 text-center">
        <div class="relative w-32 h-32 mb-6">
          <div class="absolute inset-0 bg-gradient-to-br from-indigo-200 to-purple-200 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-full animate-pulse"></div>
          <div class="relative flex items-center justify-center w-full h-full">
            <MessageCircle class="w-16 h-16 text-indigo-400 dark:text-indigo-500" />
          </div>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
          {{ searchQuery ? trans('chat.no_results') : trans('chat.no_conversations') }}
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
          {{ searchQuery ? 'Try a different search term' : 'Start a new conversation to get started' }}
        </p>
        <button
          v-if="!searchQuery"
          @click="showCreateModal = true"
          class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
        >
          Start Chatting
        </button>
      </div>

      <!-- Conversation Cards with Modern Design -->
      <div v-else class="space-y-2">
        <button
          v-for="channel in filteredChannels"
          :key="channel.id"
          @click="handleSelectChannel(channel)"
          class="group relative w-full p-4 rounded-2xl transition-all duration-300 text-left overflow-hidden"
          :class="activeChannel?.id === channel.id
            ? 'bg-gradient-to-br from-indigo-500 to-purple-600 shadow-xl scale-[1.02]'
            : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 hover:shadow-lg hover:scale-[1.01]'"
        >
          <!-- Glowing Effect for Active Channel -->
          <div 
            v-if="activeChannel?.id === channel.id"
            class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-500 blur-xl opacity-30 animate-pulse"
          ></div>
          <div class="relative flex items-start gap-4">
            <!-- Enhanced Avatar with Ring -->
            <div class="relative flex-shrink-0">
              <div 
                class="absolute inset-0 rounded-full blur-md transition-opacity duration-300"
                :class="activeChannel?.id === channel.id 
                  ? 'bg-white/50' 
                  : 'bg-transparent group-hover:bg-indigo-500/20'"
              ></div>
              <div 
                class="relative w-14 h-14 rounded-full ring-2 transition-all duration-300"
                :class="activeChannel?.id === channel.id 
                  ? 'ring-white/50' 
                  : 'ring-gray-200 dark:ring-gray-700 group-hover:ring-indigo-500/50'"
              >
                <img
                  :src="getChannelAvatar(channel)"
                  :alt="getChannelName(channel)"
                  class="w-full h-full rounded-full object-cover"
                />
                <!-- Online Badge with Pulse Animation -->
                <div v-if="channel.type === 'direct' && channel.users && isUserOnline(channel.users[0]?.id)"
                  class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 rounded-full ring-2 ring-white dark:ring-gray-800"
                >
                  <span class="absolute inset-0 bg-green-500 rounded-full animate-ping opacity-75"></span>
                </div>
                <!-- Group Badge -->
                <div v-if="channel.type === 'group'"
                  class="absolute -bottom-1 -right-1 w-5 h-5 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-gray-800"
                >
                  <span class="text-xs text-white font-bold">{{ channel.users?.length || 0 }}</span>
                </div>
              </div>
            </div>

            <!-- Content with Better Typography -->
            <div class="relative flex-1 min-w-0">
              <div class="flex items-center justify-between mb-1.5">
                <h3 
                  class="font-semibold text-base truncate transition-colors duration-300"
                  :class="activeChannel?.id === channel.id 
                    ? 'text-white' 
                    : 'text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400'"
                >
                  {{ getChannelName(channel) }}
                </h3>
                <span 
                  v-if="channel.last_message_at"
                  class="text-xs font-medium flex-shrink-0 ml-2"
                  :class="activeChannel?.id === channel.id 
                    ? 'text-white/80' 
                    : 'text-gray-500 dark:text-gray-400'"
                >
                  {{ formatLastMessageTime(channel.last_message_at) }}
                </span>
              </div>

              <!-- Message Preview with Better Styling -->
              <div class="flex items-center justify-between gap-2">
                <p 
                  class="text-sm truncate flex-1 transition-colors duration-300"
                  :class="activeChannel?.id === channel.id 
                    ? 'text-white/90 font-medium' 
                    : channel.unread_count > 0 
                      ? 'text-gray-900 dark:text-white font-semibold' 
                      : 'text-gray-600 dark:text-gray-400'"
                >
                  <span v-if="channel.last_message?.is_file" class="inline-flex items-center gap-1">
                    📎 {{ trans('chat.file_attachment') }}
                  </span>
                  <span v-else>
                    {{ channel.last_message?.content || trans('chat.no_messages') }}
                  </span>
                </p>
                
                <!-- Unread Badge with Animation -->
                <div
                  v-if="channel.unread_count && channel.unread_count > 0"
                  class="relative flex-shrink-0"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-pink-500 rounded-full blur-md animate-pulse"></div>
                  <span
                    class="relative flex items-center justify-center min-w-[1.5rem] h-6 px-2 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold rounded-full shadow-lg"
                  >
                    {{ channel.unread_count > 99 ? '99+' : channel.unread_count }}
                  </span>
                </div>
              </div>

              <!-- Typing Indicator -->
              <div v-if="channel.is_typing" class="flex items-center gap-1 mt-1">
                <div class="flex gap-1">
                  <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                  <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                  <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
                <span 
                  class="text-xs font-medium"
                  :class="activeChannel?.id === channel.id ? 'text-white/80' : 'text-indigo-600 dark:text-indigo-400'"
                >
                  {{ trans('chat.typing') }}
                </span>
              </div>
            </div>
          </div>

          <!-- Hover Indicator -->
          <div 
            v-if="activeChannel?.id !== channel.id"
            class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
          >
            <div class="w-2 h-8 bg-gradient-to-b from-indigo-500 to-purple-600 rounded-full"></div>
          </div>
        </button>
      </div>
    </div>

    <!-- Create Channel Modal -->
    <CreateChannelModal
      v-if="showCreateModal"
      @create="handleCreateChannel"
      @close="showCreateModal = false"
    />
  </div>
</template>
