# VAMS — Vehicle Access Monitoring System

**Automated Vehicle Access Monitoring Framework for Campus Security Using QR Code Technology**
Federal University of Technology, Minna (FUT Minna)

A web-based system that replaces manual, paper-based vehicle access control at campus gates with encrypted QR code authentication, real-time verification, and centralized access logging.

---

## 1. Project Overview

VAMS digitizes the process of registering vehicles, issuing tamper-resistant QR credentials to their owners, and verifying those credentials at campus gates in real time. Every scan — granted or denied — is logged automatically, giving administrators full visibility into vehicle movement without relying on handwritten logbooks or physical pass cards.

This implementation was built to satisfy the system design specified in **Chapter 3 (System Analysis and Design)** of the accompanying final-year project documentation, and is grounded in the aim, objectives, and scope defined in **Chapters 1–2**.

---

## 2. Tech Stack

| Layer | Technology | Notes |
|---|---|---|
| Backend Framework | Laravel 12 (PHP 8.2) | MVC architecture, Eloquent ORM |
| Database | MySQL 8 | Relational schema, foreign key constraints |
| Frontend | Blade templates, custom CSS (Tailwind utility layer) | Responsive sidebar with mobile drawer navigation |
| QR Generation | `simplesoftwareio/simple-qrcode` | SVG output, embedded encrypted payloads |
| QR Scanning (client) | `html5-qrcode` (JS) | Browser camera access, HTTPS required |
| Encryption | Laravel `Crypt` facade (AES-256-CBC) | Encrypts QR payload JSON before encoding |
| PDF Export | `barryvdh/laravel-dompdf` | Report generation |
| Email | Laravel Mail (SMTP via Mailtrap) | QR code delivery to registered users |
| Local Environment | XAMPP (Apache, MySQL, PHP) | Development only |

> **Note on tooling deviation:** the original project document (§3.8) specifies Bootstrap 5, Axios, and `chillerlan/php-qrcode`. This implementation uses a custom CSS design system, native `fetch()`, and `simplesoftwareio/simple-qrcode` respectively — functionally equivalent substitutions that satisfy the same non-functional requirements (responsive UI, asynchronous scan verification, multi-format QR generation) without changing the system's behavior or architecture.

---

## 3. Database Schema

Five core tables, matching the Entity-Relationship Diagram in §3.6.4 of the project document:

| Table | Purpose |
|---|---|
| `users` | Login accounts for **Admin** and **Security Officer** roles only |
| `registered_users` | Vehicle owners — students, staff, or visitors. Do not log in. |
| `vehicles` | Registered vehicles, each linked to one `registered_user` |
| `qr_codes` | One or more QR credentials per vehicle; supports revoke/reissue and expiry |
| `gate_points` | Configurable campus gate locations |
| `access_logs` | Every scan event: decision, direction, gate, operator, timestamp |

**Implementation additions beyond the original schema** (documented here for transparency against §3.7):
- `access_logs.direction` (`in`/`out`) — needed to distinguish entry from exit scans, since the original schema logs one row per scan rather than one row per visit.
- `access_logs.is_acknowledged`, `acknowledged_at`, `acknowledged_by` — needed to support the alert notification requirement (§3.4.2, item ix); tracks whether an admin has reviewed a denied-access event.

---

## 4. Security Implementation (§3.9)

Each QR code encodes an **AES-256 encrypted JSON payload** (via Laravel's `Crypt::encryptString()`), containing:

```json
{
  "user_id": 12,
  "vehicle_id": 34,
  "qr_code_id": 56,
  "token": "randomly generated 16-char token",
  "expiry": "2026-08-01T00:00:00+00:00"
}
```

On scan, the payload is decrypted server-side, matched against the `qr_codes` table by ID and token, and validated for:
1. **Existence** — does it match a real record?
2. **Status** — has it been revoked?
3. **Expiry** — has it passed its expiry date (for temporary/visitor passes)?

Only if all three checks pass is access granted and logged.

---

## 5. Functional Requirements Mapping (§3.4.2)

| # | Requirement | Status | Implementation |
|---|---|---|---|
| i | Register users with personal/vehicle/category details | ✅ | Registered Users module |
| ii | Temporary visitor passes with expiry | ✅ | Optional expiry date at vehicle registration; enforced at scan time |
| iii | Generate unique encrypted QR per registration | ✅ | `QrCode::issueFor()` |
| iv | QR delivery via download, email, or print | ✅ | All three implemented (SVG download, Mailtrap email, print view) |
| v | Deactivate/suspend QR codes independently | ✅ | Revoke & Reissue action; full QR history retained per vehicle |
| vi | QR scanning at multiple gate locations | ✅ | Gate Points module + gate selection at scan time |
| vii | Validate scans, return grant/deny in real time | ✅ | `ScanController::verify()` |
| viii | Auto-record scan events with full detail | ✅ | Access Logs table, populated on every scan |
| ix | Alert notifications for denied/flagged attempts | ✅ | Alerts module with sidebar badge, polled every 15s |
| x | View, filter, export access logs | ✅ | Filterable log viewer + CSV/PDF export |

---

## 6. Admin Portal Modules (§3.10.1)

- **Dashboard** — registered users, vehicles, active QR codes, today's events, today's denied attempts, recent activity feed
- **Registered Users** — CRUD, search, category filter
- **Vehicles** — CRUD, linked to a registered user, QR issuance/reissue/email/download/print
- **Gate Points** — CRUD for campus gate locations
- **Access Logs** — filterable by plate, officer, gate, date, decision; exportable
- **Alerts** — denied-access review queue with acknowledgement tracking
- **Reports** — daily/weekly/monthly entries, exits, denials, frequently visited vehicles, revoked QR history

## 7. Gate Scanning Interface (§3.10.2)

Single-screen officer interface at `/officer/scan`:
- Gate selection (required before scanning)
- Live camera QR scanning (`html5-qrcode`)
- Manual code entry fallback
- Immediate green (granted) / amber (exit) / red (denied) visual response with vehicle and owner details

---

## 8. Local Setup

**Requirements:** XAMPP (PHP 8.2+, MySQL), Composer, Node.js/npm

```bash
# Clone/copy project, then:
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate
# Edit .env: set DB_DATABASE, DB_USERNAME, DB_PASSWORD, MAIL_* credentials

# Database
php artisan migrate --seed

# Build frontend assets
npm run build

# Run
php artisan serve
```

Visit `http://127.0.0.1:8000`.

### Seeded test accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@vams.test` | `password` |
| Officer | `officer1@vams.test` (also officer2–4) | `password` |

The seeder also creates 3 gate points, 15 registered users (students/staff/visitors), their vehicles with issued QR codes, and two weeks of realistic access log history.

---

## 9. Known Limitations

Consistent with the theoretical/conceptual scope defined in §1.6 of the project document:

- No physical gate hardware (motorized barriers, turnstiles) — authentication and logging only
- QR camera scanning requires HTTPS or `localhost`; not testable over plain HTTP on a local network without a tunnel (e.g. ngrok)
- Email delivery is verified through a sandboxed testing environment (Mailtrap), not a live production mail domain
- No automated handling for a vehicle that enters but is never scanned on exit (remains "inside" indefinitely until manually reconciled)
- A full cybersecurity penetration analysis of the network layer is outside this project's scope, per §1.6

---

## 10. Author

Olamide — Final Year Student, Department of Information Technology, Federal University of Technology, Minna
