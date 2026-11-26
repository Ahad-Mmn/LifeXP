# LifeXP

A gamified life-quest website. Players pick the areas of life they want to improve, receive quests, complete them to earn XP, level up, unlock badges and climb the leaderboards.

Built for module 5CS045 (Full Stack Development) with PHP, MySQL (PDO) and the Twig template engine.

## Features

- **Accounts:** registration with captcha, login, password change, email change, data export and account deletion.
- **Quests:** main, side, bonus and custom quests, each with a difficulty, a time limit and an XP reward. View, create, edit, complete, skip and delete.
- **Game rules:** XP by difficulty and time limit, streak bonuses, levels, free skips that refill by completing quests, and penalties for skipping or letting a quest expire.
- **Delivery:** quests are delivered when a page loads, so no cron job is needed.
- **Badges:** 85 rule-driven badges (category, track, streak, level and completion milestones).
- **Leaderboards:** filterable by category, with tied ranks.
- **Search:** filter quests by several criteria at the same time (text, category, type, difficulty and status).
- **Ajax:** live search, quest detail panel, completing a quest without reloading, username availability check, and the home and leaderboard widgets.
- **Admin area:** user list, role changes and user deletion.
- **Design:** responsive layout with a dark mode toggle.

## Technology

| Part | Choice |
|---|---|
| Language | PHP 8.1 or newer |
| Database | MySQL / MariaDB through PDO with real prepared statements |
| Templates | Twig 3 (autoescape on) |
| Dependencies | Composer (`twig/twig`) |
| Front end | Plain JavaScript (`fetch`), CSS variables, Chart.js, DOMPurify |

## Project structure

```
config/        configuration (config.example.php is the template)
docs/          security scan reports and notes
public/        web root: index.php router, assets (css, js)
sql/           database export and migrations
src/
  Controllers/ request handling
  Core/        database, session, CSRF, cache
  Models/      one class per table, all SQL lives here
  Services/    game rules, badges, delivery, rate limiting
templates/     Twig pages and partials
```

All requests go through `public/index.php`, which picks a controller from the `?page=` value.

## Installation

1. **Requirements:** PHP 8.1+ with PDO MySQL, a MySQL/MariaDB database, and Composer.
2. **Dependencies:** run `composer install`, or upload the `vendor/` folder if you cannot run Composer on the server.
3. **Database:** create an empty database and import `sql/lifexp_full.sql` (phpMyAdmin: Import). It creates all tables and loads the categories, quest templates and badges.
4. **Configuration:** copy `config/config.example.php` to `config/config.php` and fill in the database host, name, user and password. Keep `'debug' => false` on a live server.
5. **Web root:** point the web server at `public/`, or open `/public/` in the URL. Keep `config`, `src`, `templates`, `vendor` and `sql` unreachable from the web (the included `.htaccess` returns 403 for them).
6. **First admin:** register a normal account, then in phpMyAdmin run:
   ```sql
   UPDATE users SET role = 'admin' WHERE username = 'your_username';
   ```

## Security

| Feature | How it is done |
|---|---|
| Input filtering | Every filter and tab is checked against a whitelist. Numbers and IDs are cast, text is trimmed and length-limited. |
| Output escaping | Twig autoescape everywhere. No `\|raw`, no inline scripts, no inline `style` attributes. |
| Sessions | Pages that need a login or the admin role check the session. The session ID is regenerated on login. The admin role is re-checked in the database. |
| Captcha | Required on registration. |
| Passwords | `password_hash` and `password_verify`. |
| SQL injection | PDO prepared statements only (emulation off). |
| CSRF | A token on every POST form and Ajax request. |
| Brute force | Rate limiting by IP and username, and an activity log. |
| Headers | Content-Security-Policy, X-Frame-Options, nosniff, Referrer-Policy, Permissions-Policy, HSTS over HTTPS. |
| Ajax HTML | Fetched HTML is sanitised with DOMPurify before it is inserted. |

### Security testing

The site was scanned with OWASP ZAP and the OWASP PTK browser extension. The reports are in `docs/`, with a "before" and "after" version of each. Remaining alerts and the reason for each are listed in `docs/security-testing.md`.

## Database

12 tables. `sql/lifexp_full.sql` holds the full structure and the reference data (12 categories, 720 quest templates, 85 badges). The other files in `sql/` are the incremental migrations and seed files the full export was built from.

## Version control

The project was built in small steps with Git. The history shows the order in which each part was added.

## Author

Ahad, University of Wolverhampton, 5CS045 Full Stack Development.
