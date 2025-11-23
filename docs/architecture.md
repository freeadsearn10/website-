# IPRN SMS Platform Architecture

This document captures the high-level system you described and maps it to concrete components in this repository.

---

## 1. System Architecture

### 1.1 Core Infrastructure

- Load-balanced web servers
  - `backend/` service (REST API, Admin APIs, User APIs)
  - Scales horizontally behind a load balancer
- Database cluster
  - Primary: PostgreSQL (see `infrastructure/docker-compose.yml`)
  - Intended to support clustering / read replicas
- Message processing servers
  - `message-processor/` service for incoming/outgoing SMS traffic
- Redundant connectivity
  - Outbound SMS (simulated via pluggable providers in `backend/src/integrations/`)
  - Designed to support multiple telecom providers / aggregators

### 1.2 Technical Components

- SMS gateway interfaces
  - SMPP/HTTP/REST abstractions in `backend/src/integrations/smsGateway.ts` (stubs)
- Number provisioning system
  - Core entities and routes under `backend/src/modules/numbers/`
- Message routing engine
  - `backend/src/modules/messages/` (routing placeholders)
- Billing and revenue calculation
  - `backend/src/modules/billing/` (revenue per message calculation pipeline, stubbed)

---

## 2. Software Modules

### 2.1 Admin Panel

- To be implemented as a separate frontend (e.g., React / Next.js) consuming Admin APIs.
- Current backend endpoints:
  - `/admin/users` – user management (stub)
  - `/admin/numbers` – inventory management (stub)
  - `/admin/reports/revenue` – revenue reporting (stub)
- Fraud detection
  - `backend/src/modules/fraud/` – placeholder for rules and checks.

### 2.2 User Portal

- To be implemented as a separate frontend.
- Backend endpoints:
  - `/user/account` – account + profile info (stub)
  - `/user/numbers` – list / purchase numbers (stub)
  - `/user/messages` – view messages (stub)
  - `/user/balance` – balance and earnings (stub)
  - `/user/api-keys` – API key management (stub)
  - `/user/support` – support ticket endpoints (stub)

### 2.3 API System

- Public REST API:
  - `/api/v1/messages` – message-related endpoints (stub)
  - `/api/v1/numbers` – number-related endpoints (stub)
- Webhook support:
  - `/webhooks/inbound-sms` – inbound SMS callbacks
  - `/webhooks/dlr` – delivery receipts (DLR)
- SDK libraries (future)
  - To be provided as separate packages wrapping REST API.

---

## 3. Database Structure

Logical schemas (backed by PostgreSQL in `infrastructure/docker-compose.yml`):

- `users`
  - Authentication, contact info, status, roles.
- `numbers`
  - IPRN inventory: MSISDN, country, rate, status, owner.
- `messages`
  - Incoming / outgoing SMS metadata, timestamps, revenue attribution.
- `transactions`
  - Top-ups, payouts, revenue allocations.
- `carriers`
  - Provider configuration and SMPP/HTTP endpoints.
- `logs`
  - System events and security logs.

In the scaffold, entities are defined via TypeScript interfaces in `backend/src/domain/models.ts`. No real migrations are included yet.

---

## 4. Business Operations

### 4.1 Financial Structure

- Payment processing
  - Placeholder service: `backend/src/modules/payments/`.
- Revenue sharing calculation
  - Hooks in `backend/src/modules/billing/`.
- Withdrawals and invoicing
  - Placeholders in `backend/src/modules/finance/`.

### 4.2 Compliance Framework

- User verification
  - Placeholder flows in `backend/src/modules/compliance/kyc.ts`.
- Content monitoring
  - Hooks in `backend/src/modules/fraud/contentMonitoring.ts`.
- Abuse reporting
  - `/user/support` routes (stub).
- Regulatory compliance and retention
  - Configurable policy placeholders in `backend/src/config/compliance.ts`.

---

## 5. Network Relationships

- Carrier configuration
  - `backend/src/modules/carriers/` for managing provider records.
- Quality monitoring
  - Hooks in message processor for tracking delivery rates and latency.
- Redundancy
  - Design supports multiple provider configurations and failover per route.

---

## 6. Security Measures

- Sensitive-data handling:
  - Configuration via environment variables.
  - Encryption hooks provided (but not fully implemented) in `backend/src/config/security.ts`.
- Two-factor authentication
  - Extension point in `backend/src/modules/auth/`.
- IP-based access control
  - Middleware placeholder in `backend/src/middleware/ipAccessControl.ts`.
- Audit logging
  - `backend/src/modules/audit/` for system actions.

---

## 7. Scaling Considerations

- Horizontal scaling
  - `backend/` and `message-processor/` are stateless and can be scaled out.
- Geographic distribution
  - Architecture allows multiple regions (web/API + DB + SMS processors).
- Database sharding
  - Left as an advanced extension; interface boundaries keep it possible.
- Caching
  - Placeholder cache adapter in `backend/src/integrations/cache.ts`.
- Backups
  - To be orchestrated outside this repo (cloud snapshots / backup jobs).

---

## Next Steps

This repo is a minimal, working scaffold. To turn it into a production system, you would:

1. Implement real database migrations and persistence.
2. Integrate with actual SMPP/HTTP SMS providers.
3. Build Admin and User frontends (e.g., React/Next.js) using the APIs.
4. Flesh out billing, KYC, fraud detection, and compliance logic.
5. Harden security (secrets management, TLS, WAF, etc.).