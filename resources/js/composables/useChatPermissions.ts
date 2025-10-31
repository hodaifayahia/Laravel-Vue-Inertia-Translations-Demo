import type { ChatPermission, User } from '@/types/chat'
import { computed, ref } from 'vue'

export function useChatPermissions(currentUser: User) {
  const permissions = ref<ChatPermission[]>([])
  const loading = ref(false)

  // Fetch permissions
  const fetchPermissions = async () => {
    loading.value = true
    try {
      const response = await window.axios.get('/chat/admin/permissions')
      permissions.value = response.data.permissions || []
    } catch (error) {
      console.error('Error fetching permissions:', error)
    } finally {
      loading.value = false
    }
  }

  // Check if current user can chat with another user
  const canChatWith = (otherUser: User): boolean => {
    // Super Admin can chat with anyone
    if (currentUser.roles?.some((r: any) => r.name === 'Super Admin')) {
      return true
    }

    // Get user roles
    const currentUserRoles = currentUser.roles?.map((r: any) => r.name) || []
    const otherUserRoles = otherUser.roles?.map((r: any) => r.name) || []

    // Check permissions for each role combination
    for (const currentRole of currentUserRoles) {
      for (const otherRole of otherUserRoles) {
        const permission = permissions.value.find(
          (p) => p.from_role === currentRole && p.to_role === otherRole,
        )

        if (permission?.can_initiate && permission?.can_receive) {
          return true
        }
      }
    }

    return false
  }

  // Check if user can initiate conversation
  const canInitiate = (fromRole: string, toRole: string): boolean => {
    const permission = permissions.value.find(
      (p) => p.from_role === fromRole && p.to_role === toRole,
    )
    return permission?.can_initiate || false
  }

  // Check if user can receive messages
  const canReceive = (fromRole: string, toRole: string): boolean => {
    const permission = permissions.value.find(
      (p) => p.from_role === fromRole && p.to_role === toRole,
    )
    return permission?.can_receive || false
  }

  // Update permission
  const updatePermission = async (
    fromRole: string,
    toRole: string,
    canInitiate: boolean,
    canReceive: boolean,
  ) => {
    try {
      await window.axios.put('/chat/admin/permissions', {
        from_role: fromRole,
        to_role: toRole,
        can_initiate: canInitiate,
        can_receive: canReceive,
      })

      // Update local state
      const index = permissions.value.findIndex(
        (p) => p.from_role === fromRole && p.to_role === toRole,
      )

      if (index !== -1) {
        permissions.value[index].can_initiate = canInitiate
        permissions.value[index].can_receive = canReceive
      } else {
        permissions.value.push({
          from_role: fromRole,
          to_role: toRole,
          can_initiate: canInitiate,
          can_receive: canReceive,
        })
      }
    } catch (error) {
      console.error('Error updating permission:', error)
      throw error
    }
  }

  return {
    permissions,
    loading,
    fetchPermissions,
    canChatWith,
    canInitiate,
    canReceive,
    updatePermission,
  }
}
