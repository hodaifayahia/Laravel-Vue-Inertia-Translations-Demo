# Laravel Vue Inertia Translations Demo

A modern, full-stack application showcasing **Laravel 12**, **Vue 3**, and **Inertia.js** with comprehensive **multi-language support**, **real-time chat system**, and **appointment booking functionality**.

## 🌟 Features

### 🔐 User & Role Management
- **Role-Based Access Control (RBAC)** using Spatie Laravel Permission
- Granular permissions system with 4 default roles: `super-admin`, `admin`, `manager`, `user`
- User management interface with role and permission assignment
- OAuth social authentication support

### 💬 Real-Time Chat System
- **WebSocket-powered** chat using Laravel Reverb (not Pusher)
- Direct messaging and group chats
- Real-time typing indicators and online status
- File attachments with drag-and-drop support (10MB limit)
- Message reactions, editing, and soft deletes
- Advanced permission matrix for role-to-role communication
- User blocking system with admin override
- Support ticket system integrated with chat
- Read receipts and notification system

### 📅 Appointment Booking System
- Provider-patient appointment scheduling
- Specialization-based provider categorization
- Dynamic time slot availability calculation
- Appointment status workflow (pending → confirmed → completed/cancelled)
- Provider profile management with schedules
- Patient booking interface with calendar view

### 🌍 Multi-Language Support
- **4 Languages**: English, Arabic, French, Lithuanian
- **RTL Support** for Arabic with proper layout adjustments
- 150+ translation keys per language
- Real-time language switching
- Localized date/time formatting

### 🎨 Modern UI/UX
- **TailwindCSS** with dark/light theme support
- **Radix Vue** components for accessibility
- **Responsive design** for all screen sizes
- **TypeScript** support throughout frontend
- **Vue 3 Composition API** with modern patterns

## 🚀 Quick Start

### Prerequisites
- **PHP 8.2+**
- **Node.js 18+**
- **Composer**
- **SQLite** (default) or MySQL/PostgreSQL

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/hodaifayahia/Laravel-Vue-Inertia-Translations-Demo.git
   cd Laravel-Vue-Inertia-Translations-Demo
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   # Create SQLite database (default)
   touch database/database.sqlite
   
   # Run migrations and seeders
   php artisan migrate:fresh --seed
   ```

6. **Generate Reverb credentials** (for WebSocket)
   ```bash
   php artisan reverb:install
   ```

7. **Clear permission cache**
   ```bash
   php artisan permission:cache-reset
   ```

### Development Server

**Option 1: All-in-one development server (Recommended)**
```bash
composer dev
```
This runs concurrently:
- Laravel development server (`php artisan serve`)
- Queue worker (`php artisan queue:listen`)
- Log monitoring (`php artisan pail`)
- Vite development server (`npm run dev`)

**Option 2: Manual setup**
```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite development server
npm run dev

# Terminal 3: WebSocket server
php artisan reverb:start

# Terminal 4: Queue worker (for notifications)
php artisan queue:work
```

Visit `http://localhost:8000` to see the application.

### Default Login Credentials
- **Email**: `test@example.com`
- **Password**: `password`

## 🏗️ Architecture Overview

### Backend Stack
- **Laravel 12** - PHP framework with modern features
- **Spatie Laravel Permission** - Role and permission management
- **Laravel Reverb** - WebSocket server for real-time features
- **Laravel Fortify** - Authentication scaffolding
- **Laravel Socialite** - OAuth provider integration

### Frontend Stack
- **Vue 3** - Progressive JavaScript framework
- **Inertia.js** - Modern monolith approach (no API needed)
- **TypeScript** - Type safety and better developer experience
- **TailwindCSS** - Utility-first CSS framework
- **Radix Vue** - Unstyled, accessible UI components
- **Laravel Echo** - WebSocket client integration

### Database Schema
The application uses **9 chat-related tables** and **5 booking-related tables**:

**Chat System:**
- `chat_channels` - Conversation containers
- `chat_channel_users` - Membership with blocking capabilities
- `chat_messages` - Messages with file attachments
- `chat_message_reads` - Read receipt tracking
- `chat_message_reactions` - Emoji reactions
- `chat_permissions` - Role-to-role communication rules
- `chat_user_assignments` - User-specific permission overrides
- `chat_issues` - Support ticket system
- `chat_notifications` - Real-time notification storage

**Booking System:**
- `specializations` - Medical/service specializations
- `provider_profiles` - Provider information and bio
- `provider_schedules` - Weekly availability templates
- `appointments` - Booking records with status tracking
- Plus standard Laravel tables (users, roles, permissions, etc.)

## 🔧 Configuration

### Environment Variables

**Database (SQLite default):**
```env
DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite
```

**WebSocket (Laravel Reverb):**
```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

**Multi-language:**
```env
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

**OAuth (Optional):**
```env
GITHUB_CLIENT_ID=your-github-client-id
GITHUB_CLIENT_SECRET=your-github-client-secret
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
```

## 📱 Usage Guide

### User Management
1. Navigate to `/users` to manage users
2. Assign roles and permissions to users
3. Create custom roles with specific permissions
4. View user activity and manage access levels

### Chat System
1. Access chat at `/chat`
2. Start direct conversations or create group chats
3. Send text messages, files, and reactions
4. Use typing indicators and see online status
5. Block/unblock users (admins can override)
6. Report issues through the integrated support system

### Appointment Booking
1. **As a Provider:**
   - Set up your provider profile at `/provider/profile`
   - Configure your weekly schedule at `/provider/schedule`
   - Manage incoming appointment requests

2. **As a Patient:**
   - Browse providers by specialization at `/book`
   - Select available time slots
   - Book and manage your appointments at `/appointments`

### Multi-Language
- Use the language switcher in the navigation
- All text content adapts to selected language
- Arabic enables RTL layout automatically
- Date/time formats adjust per locale

## 🧪 Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Test Coverage
- **Feature Tests**: Full request cycle testing including permissions
- **Unit Tests**: Model relationships and business logic
- **WebSocket Tests**: Real-time functionality with broadcasting fakes
- **Permission Tests**: Role-based access control verification

## 🚢 Deployment

### Production Setup
1. **Environment configuration:**
   ```bash
   cp .env.example .env.production
   # Configure production database, mail, etc.
   ```

2. **Optimize for production:**
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   npm run build
   ```

3. **Database migration:**
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=RolePermissionSeeder --force
   ```

4. **WebSocket server:**
   - Configure Reverb for production environment
   - Use process manager (PM2, Supervisor) for WebSocket server
   - Set up proper SSL certificates for WSS

### Server Requirements
- **PHP 8.2+** with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **Web server**: Apache/Nginx
- **Database**: SQLite/MySQL/PostgreSQL
- **Node.js** for asset compilation
- **WebSocket support** for real-time features

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards for PHP
- Use TypeScript for all new frontend code
- Write tests for new features
- Update translations for all supported languages
- Document API changes

## 📝 API Documentation

### Chat WebSocket Events
- `MessageSent` - New message broadcast
- `MessageRead` - Read receipt updates
- `UserTyping` - Typing indicator
- `UserOnlineStatus` - Online/offline status
- `MessageReactionAdded` - Emoji reactions
- `IssueCreated` - Support ticket creation

### Permission System
**Roles:**
- `super-admin` - Full system access
- `admin` - User and content management
- `manager` - Limited management capabilities
- `user` - Basic authenticated access

**Key Permissions:**
- User Management: `view users`, `create users`, `edit users`, `delete users`
- Chat System: `view chat`, `send messages`, `manage chat`
- Booking System: `can-book`, `book-sys`, `manage bookings`

## 🛠️ Troubleshooting

### Common Issues

**WebSocket Connection Failed:**
```bash
# Check Reverb server status
php artisan reverb:start --debug

# Verify environment variables
echo $VITE_REVERB_APP_KEY
```

**Permission Denied Errors:**
```bash
# Clear permission cache
php artisan permission:cache-reset

# Re-seed permissions
php artisan db:seed --class=RolePermissionSeeder
```

**Translation Missing:**
```bash
# Clear translation cache
php artisan cache:clear

# Verify translation files exist
ls -la lang/en/
```

**Database Issues:**
```bash
# Reset database with fresh data
php artisan migrate:fresh --seed
```

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🙏 Acknowledgments

- **Laravel Team** - For the amazing framework
- **Vue.js Team** - For the progressive frontend framework
- **Inertia.js** - For bridging Laravel and Vue seamlessly
- **Spatie** - For the excellent Laravel Permission package
- **Community Contributors** - For testing and feedback

---

**Built with ❤️ using Laravel, Vue, and Inertia.js**

For questions or support, please open an issue or contact [hodaifayahia](https://github.com/hodaifayahia).
