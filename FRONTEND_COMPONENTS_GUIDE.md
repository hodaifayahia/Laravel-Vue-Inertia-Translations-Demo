# Chat Frontend Components Guide

## Overview
Complete Vue 3 + TypeScript frontend implementation for the Laravel Reverb chat system with real-time WebSocket support.

## Structure

### 📄 Main Page
**Location:** `resources/js/pages/Dashboard/Chat/Index.vue`
- Main chat interface layout
- Integrates all composables (useChat, useReverb, useNotifications)
- Responsive design (mobile/desktop views)
- Auto-connects to presence channel on mount
- Manages active channel state

### 🎯 Core Components

#### 1. **ChatSidebar.vue**
**Location:** `resources/js/components/Chat/ChatSidebar.vue`
- Channel list with search
- Unread message counts
- Online status badges
- Create new conversation button
- Last message preview with timestamps
- Responsive sidebar with mobile support

**Features:**
- Real-time channel search/filter
- User online status indicators
- Unread count badges
- Last message timestamps (smart formatting: just now, X minutes ago, etc.)
- Direct message and group chat support

#### 2. **ChatWindow.vue**
**Location:** `resources/js/components/Chat/ChatWindow.vue`
- Main message display area
- Infinite scroll (load more messages)
- Typing indicators
- Auto-scroll to bottom
- Message actions (reply, edit, delete, react)
- File upload support
- Online status in header

**Features:**
- Auto-scroll on new messages
- Manual scroll-to-bottom button (appears when scrolled up)
- Load more messages on scroll to top
- Mobile back button (returns to channel list)
- Group member count and online users
- Direct chat online/offline status

#### 3. **MessageInput.vue**
**Location:** `resources/js/components/Chat/MessageInput.vue`
- Text input with auto-resize
- File attachment button
- Emoji picker toggle
- Send button
- Reply/Edit banner (when active)
- Typing indicator emission

**Features:**
- Enter to send, Shift+Enter for new line
- Auto-typing indicator (stops after 2s of inactivity)
- File preview before sending
- Cancel reply/edit functionality
- Disabled state during sending

#### 4. **MessageItem.vue**
**Location:** `resources/js/components/Chat/MessageItem.vue`
- Individual message bubble
- User avatar (for non-own messages)
- Message reactions display
- Reply context display
- Edit/delete actions (own messages)
- File attachment display
- Read receipts (own messages)

**Features:**
- Different styling for own vs other messages
- Hover actions menu (reply, edit, delete)
- Edited indicator
- Deleted message state
- File download links
- Reply-to context with original message preview
- Reaction emojis with counts

### 🔧 Supporting Components

#### 5. **OnlineStatusBadge.vue**
**Location:** `resources/js/components/Chat/OnlineStatusBadge.vue`
- Small circular badge (green = online, gray = offline)
- Positioned on avatar corners
- Dark mode support

#### 6. **TypingIndicator.vue**
**Location:** `resources/js/components/Chat/TypingIndicator.vue`
- Animated dots
- Smart text formatting:
  - Single user: "John is typing"
  - Two users: "John and Jane are typing"
  - Multiple: "Multiple users are typing"

#### 7. **MessageReactions.vue**
**Location:** `resources/js/components/Chat/MessageReactions.vue`
- Groups reactions by emoji
- Shows count per emoji
- Highlights user's own reactions
- Click to add/remove reaction

#### 8. **EmojiPicker.vue**
**Location:** `resources/js/components/Chat/EmojiPicker.vue`
- Popup emoji selector (18 common emojis)
- Grid layout (6 columns)
- Click outside to close
- Positioned above input

#### 9. **EmptyState.vue**
**Location:** `resources/js/components/Chat/EmptyState.vue`
- Shown when no channel is selected
- Different messages for mobile/desktop
- Centered with icon

#### 10. **CreateChannelModal.vue**
**Location:** `resources/js/components/Chat/CreateChannelModal.vue`
- Modal dialog for creating conversations
- Toggle between Direct Message and Group Chat
- User search (min 2 characters)
- Multi-select for groups (with chips display)
- Group name input (required for groups)
- Selected user preview with avatars

**Features:**
- Real-time user search via API
- Direct message: single user selection
- Group chat: multiple user selection + name required
- User avatars with names and emails
- Validation before creation
- Cancel/Create actions

## 🎨 UI Features

### Responsive Design
- **Desktop:** Sidebar + Chat Window side-by-side
- **Mobile:** Single view, toggle between sidebar and chat

### Dark Mode
- Full dark mode support across all components
- Uses Tailwind CSS dark: classes
- Consistent colors:
  - Primary: Indigo (600/700)
  - Background: White/Gray-800
  - Borders: Gray-200/Gray-700
  - Text: Gray-900/White

### Animations
- Smooth transitions for:
  - Scroll-to-bottom button (fade + translate)
  - Typing indicator (bouncing dots)
  - Hover states (background colors)
  - Modal entrance/exit

### Accessibility
- Keyboard navigation (Enter to send)
- ARIA labels on buttons
- Focus states
- Screen reader friendly

## 🔌 Integration with Composables

### useChat
- Manages channel and message state
- Provides methods for CRUD operations
- Handles pagination
- Updates local state from WebSocket events

### useReverb
- Manages WebSocket connections
- Presence tracking (online users)
- Channel subscriptions
- Event listeners for real-time updates

### useNotifications
- Notification list and unread count
- Mark as read functionality
- Real-time notification push

### useChatPermissions
- Role-based access control
- Permission checking before actions
- Super Admin bypass logic

## 📦 Dependencies Used

- **Vue 3:** Composition API, reactivity
- **TypeScript:** Full type safety
- **Inertia.js:** Page navigation
- **Axios:** HTTP requests
- **Laravel Echo:** WebSocket client
- **Pusher JS:** WebSocket transport
- **lucide-vue-next:** Icons
- **@vueuse/core:** onClickOutside for modals
- **laravel-vue-i18n:** Translations (4 languages: en, ar, fr, lt)

## 🌍 Internationalization

All text is translated using `trans()` function with keys from `lang/{locale}/chat.php`:
- **150+ translation keys** covering all UI text
- **4 languages:** English, Arabic, French, Lithuanian
- **RTL support** for Arabic
- **Dynamic placeholders:** User names, counts, timestamps

## 🚀 Real-time Features

### WebSocket Events Handled:
1. **message.sent** - New message arrives
2. **message.read** - Message marked as read
3. **message.edited** - Message content updated
4. **message.deleted** - Message removed
5. **user.typing** - User starts/stops typing
6. **user.blocked** - User blocked
7. **user.unblocked** - User unblocked
8. **reaction.added** - Emoji reaction added
9. **reaction.removed** - Emoji reaction removed

### Presence Tracking:
- **chat.presence** channel: Global user online status
- **here** event: Initial online users list
- **joining** event: User comes online
- **leaving** event: User goes offline

### Private Channels:
- **chat.channel.{id}** - Channel-specific events
- **chat.user.{id}** - User-specific notifications

## 📝 Next Steps

To start using the chat:

1. **Start Reverb Server:**
   ```bash
   wsl -d Ubuntu-24.04 sh -c "cd ~/www/Laravel-Vue-Inertia-Translations-Demo && php artisan reverb:start"
   ```

2. **Start Dev Server:**
   ```bash
   npm run dev
   ```

3. **Access Chat:**
   - Navigate to `/chat` route
   - Select or create a conversation
   - Start messaging in real-time!

4. **Test Features:**
   - Send messages
   - Upload files
   - React with emojis
   - Edit/delete messages
   - Create groups
   - Block users (admin)

## 🛠️ Customization

### Styling
- All Tailwind classes can be customized
- Component props allow dynamic styling
- Dark mode toggleable via system preference

### Emoji Set
- Edit `EmojiPicker.vue` `emojis` array
- Add/remove emojis as needed
- Can integrate with emoji libraries (e.g., emoji-mart)

### File Upload
- Currently accepts: images, PDFs, docs, txt
- Modify `accept` attribute in `MessageInput.vue`
- Backend validation in `ChatController.php`

### Permissions
- Role-based permission matrix
- Configured via `ChatPermissionSeeder.php`
- Admin panel for runtime changes (to be implemented)

## 🐛 Troubleshooting

### TypeScript Errors
- Run `vue.action.restartServer` in VS Code
- Check `tsconfig.json` configuration
- Ensure all types are imported from `@/types/chat`

### WebSocket Not Connecting
- Verify Reverb server is running
- Check `.env` REVERB configuration
- Ensure `VITE_REVERB_*` variables are set
- Restart `npm run dev` after .env changes

### Missing Translations
- Add keys to `lang/en/chat.php`
- Copy to other language files (ar, fr, lt)
- Restart dev server

## 📊 Component Hierarchy

```
Index.vue (Main Page)
├── ChatSidebar.vue
│   ├── OnlineStatusBadge.vue
│   └── CreateChannelModal.vue
│       └── (User search & selection)
├── ChatWindow.vue
│   ├── OnlineStatusBadge.vue
│   ├── MessageItem.vue
│   │   └── MessageReactions.vue
│   ├── TypingIndicator.vue
│   └── MessageInput.vue
│       └── EmojiPicker.vue
└── EmptyState.vue
```

## ✅ Status

**Frontend: 100% Complete**
- ✅ Main page layout
- ✅ Sidebar with channels
- ✅ Chat window with messages
- ✅ Message input with file upload
- ✅ Emoji reactions
- ✅ Typing indicators
- ✅ Online status
- ✅ Create conversation modal
- ✅ Empty states
- ✅ Mobile responsive
- ✅ Dark mode
- ✅ Translations (4 languages)
- ✅ Real-time WebSocket integration

**Ready for testing with backend!**
