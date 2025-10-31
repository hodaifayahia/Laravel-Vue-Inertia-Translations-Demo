<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { Plus, Edit, Trash2, Stethoscope, Users } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import * as specializationsRoutes from '@/routes/specializations'

interface Specialization {
  id: number
  name: string
  description: string | null
  slug: string
  icon: string | null
  is_active: boolean
  provider_profiles_count: number
}

interface Props {
  specializations: Specialization[]
}

const props = defineProps<Props>()

const showDialog = ref(false)
const editingSpecialization = ref<Specialization | null>(null)

const form = useForm({
  name: '',
  description: '',
  slug: '',
  icon: '',
  is_active: true,
})

const openCreateDialog = () => {
  editingSpecialization.value = null
  form.reset()
  form.clearErrors()
  showDialog.value = true
}

const openEditDialog = (specialization: Specialization) => {
  editingSpecialization.value = specialization
  form.name = specialization.name
  form.description = specialization.description || ''
  form.slug = specialization.slug
  form.icon = specialization.icon || ''
  form.is_active = specialization.is_active
  form.clearErrors()
  showDialog.value = true
}

const generateSlug = () => {
  if (form.name && !editingSpecialization.value) {
    form.slug = form.name
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)/g, '')
  }
}

const saveSpecialization = () => {
  if (editingSpecialization.value) {
    form.put(specializationsRoutes.update.url(editingSpecialization.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showDialog.value = false
        form.reset()
      },
    })
  } else {
    form.post(specializationsRoutes.store.url(), {
      preserveScroll: true,
      onSuccess: () => {
        showDialog.value = false
        form.reset()
      },
    })
  }
}

const deleteSpecialization = (specialization: Specialization) => {
  if (specialization.provider_profiles_count > 0) {
    alert(`Cannot delete specialization with ${specialization.provider_profiles_count} active providers. Please reassign providers first.`)
    return
  }
  
  if (confirm(`Are you sure you want to delete "${specialization.name}"?`)) {
    router.delete(specializationsRoutes.destroy.url(specialization.id), {
      preserveScroll: true,
    })
  }
}

const activeSpecializations = computed(() => 
  props.specializations.filter(s => s.is_active)
)

const inactiveSpecializations = computed(() => 
  props.specializations.filter(s => !s.is_active)
)
</script>

<template>
  <AppLayout>
    <Head title="Manage Specializations" />

    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Specializations</h1>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            Manage medical specializations and service areas
          </p>
        </div>
        <Button @click="openCreateDialog" class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:shadow-lg">
          <Plus class="w-4 h-4 mr-2" />
          Add Specialization
        </Button>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
              <Stethoscope class="w-6 h-6 text-white" />
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ specializations.length }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
              <Stethoscope class="w-6 h-6 text-white" />
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Active</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ activeSpecializations.length }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center">
              <Users class="w-6 h-6 text-white" />
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Total Providers</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ specializations.reduce((sum, s) => sum + s.provider_profiles_count, 0) }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Active Specializations -->
      <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Active Specializations</h2>
        <div v-if="activeSpecializations.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="spec in activeSpecializations"
            :key="spec.id"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all p-6 relative group"
          >
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-start space-x-3">
                <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                  <Stethoscope class="w-6 h-6 text-white" />
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ spec.name }}</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400">{{ spec.slug }}</p>
                </div>
              </div>
              <div class="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button
                  @click="openEditDialog(spec)"
                  class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                >
                  <Edit class="w-4 h-4" />
                </button>
                <button
                  @click="deleteSpecialization(spec)"
                  class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                  :disabled="spec.provider_profiles_count > 0"
                  :class="spec.provider_profiles_count > 0 ? 'opacity-50 cursor-not-allowed' : ''"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </div>
            <p v-if="spec.description" class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
              {{ spec.description }}
            </p>
            <div class="flex items-center justify-between">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                Active
              </span>
              <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ spec.provider_profiles_count }} provider{{ spec.provider_profiles_count !== 1 ? 's' : '' }}
              </span>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl">
          <Stethoscope class="w-12 h-12 text-gray-400 mx-auto mb-4" />
          <p class="text-gray-600 dark:text-gray-400">No active specializations yet</p>
        </div>
      </div>

      <!-- Inactive Specializations -->
      <div v-if="inactiveSpecializations.length > 0">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Inactive Specializations</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="spec in inactiveSpecializations"
            :key="spec.id"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all p-6 relative group opacity-60"
          >
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-start space-x-3">
                <div class="w-12 h-12 bg-gray-400 rounded-lg flex items-center justify-center flex-shrink-0">
                  <Stethoscope class="w-6 h-6 text-white" />
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ spec.name }}</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400">{{ spec.slug }}</p>
                </div>
              </div>
              <div class="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button
                  @click="openEditDialog(spec)"
                  class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                >
                  <Edit class="w-4 h-4" />
                </button>
                <button
                  @click="deleteSpecialization(spec)"
                  class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </div>
            <p v-if="spec.description" class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
              {{ spec.description }}
            </p>
            <div class="flex items-center justify-between">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                Inactive
              </span>
              <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ spec.provider_profiles_count }} provider{{ spec.provider_profiles_count !== 1 ? 's' : '' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Dialog -->
    <Dialog v-model:open="showDialog">
      <DialogContent class="sm:max-w-[500px]">
        <DialogHeader>
          <DialogTitle>
            {{ editingSpecialization ? 'Edit Specialization' : 'Create Specialization' }}
          </DialogTitle>
        </DialogHeader>
        <form @submit.prevent="saveSpecialization" class="space-y-4">
          <div>
            <Label for="name">Name *</Label>
            <Input
              id="name"
              v-model="form.name"
              @input="generateSlug"
              placeholder="e.g., Cardiology"
              required
            />
            <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
          </div>

          <div>
            <Label for="slug">Slug *</Label>
            <Input
              id="slug"
              v-model="form.slug"
              placeholder="e.g., cardiology"
              required
            />
            <p v-if="form.errors.slug" class="text-sm text-red-600 mt-1">{{ form.errors.slug }}</p>
            <p class="text-xs text-gray-500 mt-1">Used in URLs, must be unique</p>
          </div>

          <div>
            <Label for="description">Description</Label>
            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              placeholder="Brief description of this specialization..."
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
            />
            <p v-if="form.errors.description" class="text-sm text-red-600 mt-1">{{ form.errors.description }}</p>
          </div>

          <div>
            <Label for="icon">Icon (optional)</Label>
            <Input
              id="icon"
              v-model="form.icon"
              placeholder="e.g., heart, brain, tooth"
            />
            <p class="text-xs text-gray-500 mt-1">Icon name for display (not implemented yet)</p>
          </div>

          <div class="flex items-center space-x-2">
            <input
              id="is_active"
              v-model="form.is_active"
              type="checkbox"
              class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
            />
            <Label for="is_active" class="cursor-pointer">Active (visible to users)</Label>
          </div>

          <DialogFooter>
            <Button
              type="button"
              variant="outline"
              @click="showDialog = false"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              :disabled="form.processing"
              class="bg-gradient-to-r from-indigo-500 to-purple-600"
            >
              {{ form.processing ? 'Saving...' : editingSpecialization ? 'Update' : 'Create' }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
