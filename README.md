<p align="center">
  <img src="https://laravel.com/img/logomark.min.svg" width="80" alt="Laravel logo">
</p>

# Social Media Scheduler

A production-ready **social media scheduling SaaS** built on Laravel 13 with a **Domain-Driven Modular Monolith** architecture. Compose rich-text posts, mention people by `@` handle, target multiple connected accounts, schedule at the best time windows, and publish automatically — then track everything with analytics, reports, and audit logs.

## Highlights

- **Modular monolith** — 17 self-contained domain modules under `Modules/` that are auto-discovered at boot; each ships its own migrations, routes, views, config, and translations.
- **Multi-tenant by default** — every model is business-scoped via a global `HasBusiness` scope resolved from Laravel's `Context`; background workers opt out with `withoutBusinessScope()`.
- **Rich-text editor with @mentions** — Quill 2 + quill-mention, live platform search, and platform-native mention encoding when publishing.
- **Follower import** — pull followers from connected Facebook / Instagram / X accounts straight into your contacts.
- **Best-time scheduling** — per-platform best-time windows plus an hourly `posts:publish-due` scheduler command backed by a queue.
- **Real Facebook publishing** via the Graph API; other platforms run through a simulated driver so the full pipeline is testable end to end.
- **AI-assisted writing** — rewrite, expand, shorten, or generate hashtags with OpenAI.
- **PDF & Excel reports**, media library with thumbnails, team invitations & roles, in-app notifications, full audit trail.

## Tech Stack

| Layer      | Technology                                              |
|------------|---------------------------------------------------------|
| Backend    | PHP 8.3, Laravel 13, Eloquent ORM                        |
| Database   | MySQL (default), queue/cache/session backed by DB        |
| Frontend   | Blade, Alpine.js, Tailwind CSS 4, Vite 7                 |
| Editor     | Quill 2.0.3, quill-mention 6.1.1                         |
| AI         | OpenAI Chat Completions (OpenAI-compatible base URL)     |
| Reports    | laravel-dompdf (PDF), maatwebsite/excel (Excel)          |
| Testing    | Pest, Laravel Pint, PHPUnit                              |

## Architecture

```
app/                    Application shell (providers, console kernel)
Modules/
├── Core/               Base model, traits, middleware, ModuleManager, Blade components
├── Business/           Onboarding, multi-business
├── SocialAccounts/     Connect/disconnect platform accounts, token storage
├── Posts/              Rich-text posts, mentions, multi-account delivery
├── Contacts/           Contacts + platform handles, @mention autocomplete
├── Scheduler/          Best-time windows, due-publishing command + queue jobs
├── MediaLibrary/       Uploads, thumbnails, bulk delete
├── Calendar/           Content calendar
├── Templates/          Reusable post templates
├── Ai/                 OpenAI copywriting (rewrite / expand / shorten / hashtags)
├── Analytics/          Daily analytics
├── Reports/            PDF & Excel export generation
├── Notifications/      In-app notifications
├── Teams/              Members, roles, invitations
├── Settings/           Business & notification settings
├── Audit/              Audit log viewer
└── Dashboard/          Landing dashboard
```

### How the modular monolith works

`CoreServiceProvider` (registered in `bootstrap/providers.php`) is the single boot entry point. It instantiates a `ModuleManager` that scans `Modules/*/Providers/*ServiceProvider.php`, validates each provider extends `ModuleServiceProvider`, and registers it — no manual wiring per module. `ModuleServiceProvider` then auto-loads the module's migrations, web/api routes, views, config, and translations from conventional paths.

Helpers:

- `module_path('Posts')` → absolute path to a module (`Modules/Posts`).
- `module_class('Posts', 'Models\\Post')` → `Modules\Posts\Models\Post`.

### Multi-tenancy

- `BaseModel` (`Modules/Core/Models/BaseModel.php`) composes two traits shared by every domain entity:
  - `HasBusiness` — adds a global `business` scope filtering by `Context::get('business_id')` (falls back to the authenticated user's business). Cross-tenant jobs use `Model::withoutBusinessScope(fn () => ...)`.
  - `HasAudit` — records create/update/delete activity to `audit_logs`.
- Tenant timestamps use the `BusinessDateTime` cast so business timezones are respected.

## Features

### Posts & Mentions
- Rich-text composition with hashtags and a featured image.
- Type `@` in the editor to autocomplete from your contacts **and** live search across connected platforms.
- Picking a remote result saves it as a contact and inserts the mention immediately.
- Mentions are sanitized (XSS-safe) and rendered per-platform on publish via `PostMessageBuilder`:

| Platform  | Mention format            |
|-----------|---------------------------|
| Facebook  | `@[uid:1:Name]`           |
| Instagram | `@handle`                 |
| X/Twitter | `@handle`                 |
| LinkedIn  | `@[Name](urn)`            |

### Contacts & Follower Import
- Contacts carry platform handles (Facebook, Instagram, X, LinkedIn).
- One-click **Import followers** from connected Facebook, Instagram, and X accounts — idempotent, deduped by `platform_uid`/handle.
- Live platform search powers the editor's `@` picker; platform failures degrade gracefully to empty results.

### Scheduling & Publishing
- Per-platform **best-time windows**; `BestTimeService` suggests the next optimal slot.
- `posts:publish-due` (scheduled every minute) dispatches `PublishPostJob` for due posts.
- `PublishingService` fans out to each targeted account with per-account retry backoff (`PublishPostJob`: 3 tries, 30/120/600s).
- Facebook delivers via the real Graph API driver; other platforms use a simulated driver so the pipeline can be exercised end to end.

### Security
- All user-supplied rich text passes through `HtmlSanitizer` (preserves only safe structure and `ql-mention` spans; strips scripts, iframes, event handlers, dangerous URLs).
- Access tokens are hidden from JSON/serialization by default and only exposed explicitly where a platform driver needs them.

## Requirements

- PHP **8.3+** (extensions: `pdo_mysql`, `gd` for image thumbnails, `zip` for Excel)
- Composer 2
- Node.js 18+ and npm
- A MySQL database (or switch `DB_CONNECTION` in `.env` to sqlite)

## Installation

```bash
# 1. Install dependencies & set up the environment
composer install
cp .env.example .env

# 2. Generate an app key and point .env at your database
php artisan key:generate
#   DB_DATABASE=social_media_scheduler   (create it first)

# 3. Run migrations (creates all tables, including module migrations)
php artisan migrate --force

# 4. Install & build frontend assets
npm install
npm run build
```

> `composer run setup` performs steps 1–4 for you (minus creating the database).

### One-command development environment

```bash
composer dev
```

Runs five processes together: `php artisan serve`, `queue:listen`, `pail` (logs), `schedule:work`, and `vite` — with a process manager (`concurrently`).

### Scheduling & queues (required for auto-publishing)

```bash
php artisan schedule:work   # runs posts:publish-due every minute
php artisan queue:listen    # executes PublishPostJob
```

In production, wire a real scheduler (`cron` entry for `php artisan schedule:run`) and a persistent queue worker instead.

## Configuration

`.env` options worth knowing:

```dotenv
# Database
DB_CONNECTION=mysql
DB_DATABASE=social_media_scheduler

# AI copywriting (optional; the AI module disables itself without a key)
OPENAI_API_KEY=
OPENAI_MODEL=gpt-4o-mini
OPENAI_BASE_URL=https://api.openai.com/v1

# Facebook publishing / follower import
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_GRAPH_VERSION=v23.0

# Media library
MEDIA_STORAGE_DISK=public
MAX_MEDIA_SIZE=51200
```

## Testing

The suite runs on Pest against an in-memory database (RefreshDatabase) and fakes all external HTTP calls.

```bash
composer test          # config:clear + php artisan test
php artisan test       # run the suite
vendor\bin\pint        # code style
```

Tests cover posts and mentions, Facebook publishing and mention encoding, contacts CRUD and follower import, rich-text sanitization, media library, module pages, team/business flows, and the scheduler.

## Key Commands

```bash
php artisan posts:publish-due      # publish posts whose time has arrived
php artisan migrate                # run module migrations
npm run build                      # compile production assets
npm run dev                        # Vite dev server (hot reload)
```

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
