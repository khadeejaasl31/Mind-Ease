# MindEase XAMPP database setup

## 1. Put the project in XAMPP

Your project is already in the correct place:

`C:\xampp\htdocs\Emotional-First aid\`

Keep `db.php`, `register.php`, `login.php`, `journal_api.php`, `reflection_api.php`, and `schema.sql` in this main project folder.

## 2. Start XAMPP

Open the XAMPP Control Panel and start **Apache** and **MySQL**. Then open the website through Apache, not by double-clicking an HTML file:

`http://localhost/Emotional-First%20aid/index.html`

## 3. Create the database in phpMyAdmin

Open `http://localhost/phpmyadmin`, choose the **SQL** tab, open `schema.sql` from this project, paste all of its contents into the SQL box, then click **Go**.

This creates:

- `users` — account name, email, and securely hashed password.
- `journal_entries` — one saved private journal draft for each signed-in user.
- `reflections` — the short reflection notes from the Profile page.

## 4. Connection settings

`db.php` is configured for the standard XAMPP defaults:

- host: `localhost`
- username: `root`
- password: empty
- database: `mindease`

If you set a MySQL root password in XAMPP, change only `$pass` in `db.php`.

## How data is used

Registration saves users through `register.php`; sign-in reads them through `login.php`. A signed-in user’s journal and profile reflections are saved and loaded with `journal_api.php` and `reflection_api.php`. Guests retain the existing browser-only journal fallback.

Never store plain-text passwords in phpMyAdmin. The website uses PHP `password_hash()` and `password_verify()`.

## Upgrading an older database

If mindease.users already exists from an older version and does not have an id column, run schema_upgrade.sql once in phpMyAdmin before using the updated sign-in and account-backed features.

## Create the first administrator

After importing `schema.sql`, visit `http://localhost/Emotional-First%20aid/admin/install.php` and create the first administrator account. The setup form is disabled once an admin account exists. Sign in at `admin/login.php` afterwards. Administrator accounts are separate from normal site users.