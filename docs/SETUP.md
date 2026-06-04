# Electro-V2 — local setup (collaborators)

## Error: `Connection refused` on `127.0.0.1:3306`

Laravel is trying to reach **MySQL** and nothing is listening on that port. Fix MySQL first, then configure `.env`.

---

## 1. Prerequisites

- PHP 8.2+ with extensions: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `curl`
- Composer
- **MySQL or MariaDB running** (XAMPP / Laragon / WAMP / standalone MySQL)

### XAMPP (Windows)

1. Open **XAMPP Control Panel**
2. Start **Apache** and **MySQL** (MySQL must show green “Running”)
3. Default: host `127.0.0.1`, port `3306`, user `root`, password *(empty)*

If MySQL uses another port (e.g. `3307`), set `DB_PORT=3307` in `.env`.

---

## 2. Clone and install

```bash
cd Electro-V2
composer install
copy .env.example .env    # Windows
# cp .env.example .env    # Mac/Linux
php artisan key:generate
```

---

## 3. Configure `.env`

Edit `.env` — **do not commit this file**. Match your local MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Shop-Project
DB_USERNAME=root
DB_PASSWORD=
```

- Create the database if it does not exist (phpMyAdmin → Databases → `Shop-Project`, or SQL below).
- Use your real MySQL username/password if not `root` / empty.

```sql
CREATE DATABASE IF NOT EXISTS `Shop-Project` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

This project uses the database for **sessions**, **cache**, and **queues** (`SESSION_DRIVER=database`, etc.). MySQL must be running before you open the site.

---

## 4. Migrate and seed

```bash
php artisan migrate --seed
php artisan storage:link
```

---

## 5. Run the app

**Option A — XAMPP:** put the project under `htdocs` and open  
`http://localhost/Electro-V2/public/`

**Option B — Laravel dev server:**

```bash
php artisan serve
```

Open `http://127.0.0.1:8000`

---

## 6. Still failing?

| Symptom | Check |
|--------|--------|
| `Connection refused` | MySQL service not started |
| `Access denied` | Wrong `DB_USERNAME` / `DB_PASSWORD` in `.env` |
| `Unknown database` | Create `Shop-Project` in phpMyAdmin |
| Changed `.env` but no effect | Run `php artisan config:clear` |

Test MySQL from terminal:

```bash
mysql -h 127.0.0.1 -P 3306 -u root -p -e "SHOW DATABASES;"
```

---

## 7. Optional: share a database dump

If the team should use the same sample data, export from your machine:

```bash
mysqldump -u root Shop-Project > shop-project.sql
```

Collaborator imports:

```bash
mysql -u root -p Shop-Project < shop-project.sql
```

Or run `php artisan migrate --seed` for a fresh schema + seed data.
