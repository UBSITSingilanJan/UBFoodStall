# Customer module: back-end notes

The pages print plain HTML on purpose. The front-end person can restyle the markup.
Do not change the PHP at the top of each page or the variable names.

## Pages and URLs
| Page | URL | Query values |
|---|---|---|
| Stall list | customer/index.php | q, status (all, open, closed), sort (number, rating, price, name) |
| Stall | customer/stall.php?id=STALL_ID | q, state (all, available, low, unavailable), sort (name, rating, price-asc, price-desc) |
| Product | customer/product.php?id=PRODUCT_ID | none |

Search, filter and sort run in PHP (Catalog::filterStalls and Catalog::filterProducts). No JavaScript is needed.

## Posting a review
stall.php and product.php accept a POST with these fields:
- csrf (hidden, from csrf_token())
- rating (1 to 5, required)
- comment (optional, 500 characters max)
One review per customer per stall and per product. Posting again updates it.
After a post the page redirects back to #reviews and shows a one-time message.

## Variables the front-end can use
- index.php: $stalls (each row has Stall_ID, stallname, location, status, product_count, available_count, low_count, min_price, avg_rating, review_count)
- stall.php: $stall, $products (each row has Product_ID, productname, price, stock, status, state, avg_rating, review_count), $counts, $summary
- product.php: $product (adds state, stall_status, stallname, location), $summary, $stock
- review block (inc/reviews.php): $rvSummary (count, avg, dist), $rvReviews, $rvMine
- $state is available, low, or unavailable. Use state_label() for the text.

## Stock rule
Unavailable when status is not Available or stock is 0. Low stock at 10 or fewer (Catalog::LOW_STOCK). Otherwise Available.

## Files
- classes/catalog.php: all queries, filtering, and review saving
- customer/inc/bootstrap.php: login check, helpers (e, peso, csrf, flash)
- customer/inc/header.php, footer.php, reviews.php: shared plain markup
- sql/customer_migration.sql: run once. sql/customer_demo_seed.sql: optional sample data
