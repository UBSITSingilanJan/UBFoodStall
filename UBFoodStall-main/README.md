# UB Food Stall

A PHP + MySQL (PDO) web app with a Bootstrap 5 design. Three roles: **customer**, **vendor**, **admin**.

## Setup (XAMPP)
1. Copy this folder into `htdocs/`.
2. Start Apache and MySQL, open phpMyAdmin, and **import `ubfoodstall.sql`** (it creates the database and replaces the old tables).
3. Check `dbconn.php`: the port is `3308`. Change it to `3306` if your MySQL uses the default.
4. Open `http://localhost/UBFoodStall-main/`.

## Test accounts (password `123` unless noted)
| Role | Email |
|---|---|
| Customer | janellesambahon@gmail.com |
| Vendor | cristinaperido@gmail.com |
| Admin | admin@gmail.com / `admin123` |

## What each role can do
- **Customer (student)**
  - *Page 1 - Dashboard:* every food stall as a card with a cover (stall name), **Open / Closed** status, location and average rating.
  - *Page 2 - Stall page:* product availability, a "What can I afford?" budget check, and reviews (read everyone's, post / edit / delete your own - one review per stall).
- **Vendor** - open / close the stall, add products, update stock, delete products, see low-stock alerts and the inventory value.
- **Admin** - add, update and delete vendors (and their stalls).

## Folder guide
- `includes/` - shared `helpers.php` (escaping, role guard), `header.php` / `footer.php` (Bootstrap layout)
- `classes/` - `Customer`, `Vendor`, `Product`, `Stall`, `Review`, `MenuAnalyzer`, `InventoryAnalyzer`
- `admin/`, `vendor/`, `customer/` - the dashboards
