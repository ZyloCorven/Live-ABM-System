# AuctionHub — Live Auction Bidding + POS (CodeIgniter 4)

Built on top of your uploaded CI4 skeleton. Everything below is new.

## 1. Setup

```bash
cd Ci4
composer install          # vendor/ is already included, but run this if anything's missing
cp .env .env               # already created for you with dev defaults — edit DB creds if needed
```

Edit `.env` (already pre-filled, just adjust if your MySQL differs):
```
database.default.hostname = localhost
database.default.database = auction_hub
database.default.username = root
database.default.password = root
database.default.DBDriver = MySQLi
```

Create the database, then run migrations + seed demo data:

```bash
php spark migrate
php spark db:seed DatabaseSeeder
php spark serve
```

Visit `http://localhost:8080`. Demo logins (password `Password123` for all):
`admin` · `sarah_seller` · `mike_seller` · `bella_bidder` · `carlos_bidder` · `nina_bidder`

The seeded "Antique Oak Writing Desk" auction ends in ~4 minutes — bid on it as a
different user (e.g. `bella_bidder`) to see the automatic extension trigger live.

### Keeping auctions moving without a cron worker
The app opportunistically runs the start/close sweep (`AuctionService::runSweep()`)
whenever the home page or auction list loads, so a demo works fine without any
scheduler. For production, wire up a real cron/scheduler instead:
```bash
php spark auctions:sweep   # run every minute
```

## 2. Where the "hard requirements" live

- **Max-heap**: `app/Services/MaxHeap.php` — a real binary max-heap (siftUp/siftDown,
  O(log n) insert/extract, O(n) heapify). `BidService` builds a heap over an auction's
  bids (comparator: amount DESC, then earliest timestamp, then lowest id) to determine
  the current leader and full leaderboard.
- **Bid validation + auto-extension**: `app/Services/BidService::placeBid()` — checks
  auction status, ownership, amount/increment, auth, and rate-limits rapid resubmits;
  then, inside a DB transaction, inserts the bid, recomputes the heap root, and extends
  `end_time` (+ logs to `auction_extensions`) if the bid landed inside the configurable
  window (`settings` table: `extension_window_minutes` / `extension_duration_minutes`,
  editable at `/admin/settings`).
- **Real-time UI**: `public/assets/js/bid.js` — polls `/auctions/{id}/state` every 4s
  and submits bids via `fetch()`, no full page reload. Client-side countdown ticks
  every second between polls.
- **POS**: `app/Services/PosService.php` — auto-creates a pending `order` when an
  auction closes as "sold" (`AuctionService::closeAuction()`), records (partial)
  payments, generates a printable invoice (`/pos/orders/{id}/invoice` → browser
  Print-to-PDF), and supports manual non-auction sales.

## 3. Structure

```
app/Services/       MaxHeap, BidService, AuctionService, PosService  (business logic)
app/Models/          One per table, thin — heavy lifting stays in Services
app/Controllers/     Auth, Auctions, Bids (AJAX), Dashboard, Pos, Admin/*
app/Filters/         AuthFilter (must be logged in), RoleFilter (role:admin,seller...)
app/Database/        Migrations (7 tables, exact schema from spec) + Seeders (demo data)
app/Views/           Bootstrap 5 UI — layouts/main.php is the shared shell
public/assets/       app.css (indigo/purple + gold theme) and bid.js
```

## 4. Notes / things to double-check before shipping

- **Not test-executed**: this container has no PHP runtime, so the code was written
  and manually reviewed but not run end-to-end. Run `php spark migrate` and click
  through the flows — most likely issues (if any) will be in the demo seed data
  timestamps going stale, not the core logic.
- CSRF protection is on globally (`Config/Filters::$globals['before']`); token
  regeneration is disabled (`Config/Security::$regenerate = false`) specifically so
  the bidding page's embedded CSRF token keeps working across repeated AJAX
  submissions without a page refresh.
- Uploaded auction images go to `public/uploads/` (publicly served), not `writable/`.
- All money fields are `DECIMAL(12,2)` — avoid floating point drift.
