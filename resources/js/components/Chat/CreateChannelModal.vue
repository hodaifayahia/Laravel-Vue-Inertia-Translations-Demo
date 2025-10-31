<script setup lang="ts">
import { ref, computed } from 'vue'
import { trans } from 'laravel-vue-i18n'
import { X, Search, Users } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import type { User } from '@/types/chat'

const emit = defineEmits<{
  create: [type: 'direct' | 'group', userIds: number[], name?: string]
  close: []
}>()

const channelType = ref<'direct' | 'group'>('direct')
const groupName = ref('')
const searchQuery = ref('')
const users = ref<User[]>([])
const selectedUsers = ref<User[]>([])
const loading = ref(false)

// Search users
const searchUsers = async () => {
  if (searchQuery.value.length < 2) {
    users.value = []
    return
  }

  loading.value = true
  try {
    const response = await axios.get('/chat/users/search', {
      params: { q: searchQuery.value }
    })
    users.value = response.data.users
  } catch (error) {
    console.error('Failed to search users:', error)
  } finally {
    loading.value = false
  }
}

// Toggle user selection
const toggleUser = (user: User) => {
  const index = selectedUsers.value.findIndex(u => u.id === user.id)
  if (index > -1) {
    selectedUsers.value.splice(index, 1)
  } else {
    if (channelType.value === 'direct' && selectedUsers.value.length > 0) {
      selectedUsers.value = [user]
    } else {
      selectedUsers.value.push(user)
    }
  }
}

// Check if user is selected
const isSelected = (user: User) => {
  return selectedUsers.value.some(u => u.id === user.id)
}

// Can create channel
const canCreate = computed(() => {
  if (selectedUsers.value.length === 0) return false
  if (channelType.value === 'group' && !groupName.value.trim()) return false
  return true
})

// Handle create
const handleCreate = () => {
  if (!canCreate.value) return

  const userIds = selectedUsers.value.map(u => u.id)
  const name = channelType.value === 'group' ? groupName.value : undefined

  emit('create', channelType.value, userIds, name)
}
</script>

<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md max-h-[90vh] flex flex-col">
      <!-- Header -->
      <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
          {{ trans('chat.new_conversation') }}
        </h3>
        <button
          @click="emit('close')"
          class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Content -->
      <div class="flex-1 overflow-y-auto p-4 space-y-4">
        <!-- Channel Type -->
        <div class="flex gap-2">
          <button
            @click="channelType = 'direct'; selectedUsers = []"
            class="flex-1 py-2 px-4 rounded-lg border transition-colors"
            :class="{
              'bg-indigo-600 text-white border-indigo-600': channelType === 'direct',
              'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600': channelType !== 'direct'
            }"
          >
            {{ trans('chat.direct_message') }}
          </button>
          <button
            @click="channelType = 'group'; selectedUsers = []"
            class="flex-1 py-2 px-4 rounded-lg border transition-colors flex items-center justify-center gap-2"
            :class="{
              'bg-indigo-600 text-white border-indigo-600': channelType === 'group',
              'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600': channelType !== 'group'
            }"
          >
            <Users class="w-4 h-4" />
            {{ trans('chat.group_chat') }}
          </button>
        </div>

        <!-- Group Name -->
        <div v-if="channelType === 'group'" class="space-y-2">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ trans('chat.group_name') }}
          </label>
          <input
            v-model="groupName"
            type="text"
            :placeholder="trans('chat.enter_group_name')"
            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <!-- Search Users -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ channelType === 'direct' ? trans('chat.select_user') : trans('chat.select_members') }}
          </label>
          <div class="relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              v-model="searchQuery"
              @input="searchUsers"
              type="text"
              :placeholder="trans('chat.search_users')"
              class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>
        </div>

        <!-- Selected Users -->
        <div v-if="selectedUsers.length > 0" class="flex flex-wrap gap-2">
          <span
            v-for="user in selectedUsers"
            :key="user.id"
            class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full text-sm"
          >
            <img
              :src="user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}`"
              :alt="user.name"
              class="w-5 h-5 rounded-full"
            />
            {{ user.name }}
            <button
              @click="toggleUser(user)"
              class="hover:bg-indigo-200 dark:hover:bg-indigo-800 rounded-full p-0.5"
            >
              <X class="w-3 h-3" />
            </button>
          </span>
        </div>

        <!-- User List -->
        <div v-if="searchQuery.length >= 2" class="space-y-2">
          <div v-if="loading" class="text-center py-4 text-gray-500">
            {{ trans('chat.loading') }}...
          </div>

          <div v-else-if="users.length === 0" class="text-center py-4 text-gray-500">
            {{ trans('chat.no_users_found') }}
          </div>

          <div v-else class="space-y-1">
            <button
              v-for="user in users"
              :key="user.id"
              @click="toggleUser(user)"
              class="w-full flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              :class="{
                'bg-indigo-50 dark:bg-indigo-900/20': isSelected(user)
              }"
            >
              <img
                :src="user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}`"
                :alt="user.name"
                class="w-10 h-10 rounded-full"
              />
              <div class="flex-1 text-left">
                <p class="font-medium text-gray-900 dark:text-white">{{ user.name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</p>
              </div>
              <div
                v-if="isSelected(user)"
                class="w-5 h-5 bg-indigo-600 rounded-full flex items-center justify-center"
              >
                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
        <button
          @click="emit('close')"
          class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
        >
          {{ trans('chat.cancel') }}
        </button>
        <button
          @click="handleCreate"
          :disabled="!canCreate"
          class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ trans('chat.create') }}
        </button>
      </div>
    </div>
  </div>
</template>
