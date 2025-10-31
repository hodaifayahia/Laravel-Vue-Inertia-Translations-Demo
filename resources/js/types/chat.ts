export interface User {
  id: number
  name: string
  email: string
  avatar?: string | null
  is_online?: boolean
}

export interface ChatChannel {
  id: number
  type: 'direct' | 'group'
  name?: string | null
  avatar?: string | null
  latest_message?: ChatMessage | null
  unread_count: number
  updated_at: string
  users: User[]
}

export interface ChatMessage {
  id: number
  channel_id: number
  user_id: number
  content: string
  message?: string | null
  type: 'text' | 'file' | 'system'
  file_path?: string | null
  file_name?: string | null
  attachment_path?: string | null
  attachment_url?: string | null
  attachment_name?: string | null
  attachment_type?: string | null
  attachment_size?: number | null
  formatted_attachment_size?: string | null
  is_image?: boolean
  is_video?: boolean
  is_audio?: boolean
  is_document?: boolean
  reply_to_message_id?: number | null
  is_edited: boolean
  edited_at?: string | null
  deleted_at?: string | null
  created_at: string
  updated_at: string
  user: User
  reply_to?: {
    id: number
    content: string
    message?: string | null
    user: User
  } | null
  reactions?: ChatMessageReaction[]
  is_read?: boolean
  read_by_count?: number
  reads?: Array<{ user_id: number; read_at: string }>
}

export interface ChatMessageReaction {
  id: number
  message_id: number
  user_id: number
  user_name: string
  emoji: string
  created_at: string
}

export interface ChatNotification {
  id: number
  user_id: number
  type: string
  data: any
  read_at?: string | null
  created_at: string
}

export interface ChatIssue {
  id: number
  channel_id: number
  reported_by: number
  assigned_to?: number | null
  status: 'open' | 'in_progress' | 'resolved'
  priority: 'low' | 'medium' | 'high'
  description: string
  resolution_notes?: string | null
  resolved_at?: string | null
  created_at: string
  reporter?: User
  assignee?: User | null
}

export interface TypingUser {
  user_id: number
  user_name: string
}

export interface ChatPermission {
  from_role: string
  to_role: string
  can_initiate: boolean
  can_receive: boolean
}
