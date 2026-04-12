# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Development (runs Laravel, queue, logs, and Vite concurrently)
composer dev

# Or separately:
php artisan serve        # Laravel dev server
npm run dev              # Vite HMR dev server

# First-time setup
composer setup           # install + migrate + npm build

# Database
php artisan migrate
php artisan migrate:fresh --seed

# Create admin user for Filament panel
php artisan make:filament-user

# Tests
composer test            # runs PHPUnit

# Production build
npm run build
```

## Architecture Overview

**Laravel 13 + Inertia.js + Vue 3 project management system.** The app has two surfaces:
1. **User-facing app** — Inertia + Vue 3 pages at `/`, `/dashboard`, `/projects`, `/tasks`, `/profile`, `/notifications`
2. **Admin panel** — Filament v5 at `/admin` (only `admin` and `project_manager` roles can access)

### Role System

Three roles via Spatie Permission (`admin`, `project_manager`, `developer`):
- **Admin**: full access everywhere
- **Project Manager (PM)**: manages projects, approves/rejects task reviews, accesses Filament
- **Developer**: sees only own projects & assigned tasks

Role is stored on `users.role` column AND in Spatie's `model_has_roles` table (both used).

Shared Inertia props available in every Vue page via `usePage().props`:
```js
auth.user.role          // 'admin' | 'project_manager' | 'developer'
auth.user.is_pm         // boolean shorthand
auth.user.unread_notifications_count
flash.success / flash.error
```

### Task Workflow

Status flow: `todo` → `in_progress` → `review` → `done`

- When developer moves to `review`, they upload a proof (text + optional file).
- PM then calls `POST /tasks/{id}/approve` (→ `done`) or `POST /tasks/{id}/reject` (→ back to `in_progress` + rejection_reason stored).
- All status changes fire notifications (database driver) and create `ActivityLog` entries via `TaskObserver`.

Business logic lives in `app/Services/TaskService.php`, not in the controller.

### Key File Locations

| Concern | Location |
|---|---|
| Routes | `routes/web.php` |
| Inertia shared data | `app/Http/Middleware/HandleInertiaRequests.php` |
| Task business logic | `app/Services/TaskService.php` |
| Task status update request | `app/Http/Requests/UpdateTaskStatusRequest.php` |
| Notifications | `app/Notifications/` (TaskAssigned, TaskCommented, TaskStatusChanged) |
| Task auto-events | `app/Observers/TaskObserver.php` |
| Policies | `app/Policies/` (TaskPolicy, CommentPolicy) |
| Filament resources | `app/Filament/Resources/` (Projects, Tasks, Users) |
| Vue pages | `resources/js/Pages/` |
| Main layout | `resources/js/Layouts/AppLayout.vue` |
| Reusable composables | `resources/js/composables/` (useToast.js, useFormatters.js) |

### Frontend Conventions

- All Vue pages use `AppLayout` as the wrapper layout.
- Toast notifications: use `useToast()` composable (`resources/js/composables/useToast.js`).
- Date/string formatting: use `useFormatters()` composable.
- Badge components exist for priority and status: `PriorityBadge.vue`, `TaskStatusBadge.vue`, `ProjectStatusBadge.vue`.
- Skeleton loading component: `SkeletonLoading.vue`.
- Inertia form submissions use `useForm()` from `@inertiajs/vue3`.

### Filament Admin Panel

Filament resources follow this pattern in each resource directory:
```
app/Filament/Resources/{Name}/
├── {Name}Resource.php      # Resource registration
├── Pages/                  # List, Create, Edit
├── Schemas/                # Form schema
└── Tables/                 # Table columns/filters
```

Projects also have a `RelationManagers/MembersRelationManager.php` for managing team members inline.

### Environment

Default dev config uses SQLite (`DB_CONNECTION=sqlite`). The app URL is typically `http://project-management.test` via Laragon virtual host. Queue and cache use the `database` driver. Mail is logged locally (`MAIL_MAILER=log`).
