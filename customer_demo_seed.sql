-- Optional demo data so the customer pages have something to show.
-- Gives every existing stall 6 sample products with different stock levels.
-- Run AFTER customer_migration.sql. Delete the rows later from the vendor side.

INSERT INTO `product` (`Stall_ID`, `productname`, `description`, `price`, `stock`, `status`)
SELECT fs.Stall_ID, t.n, t.d, t.p, MOD(t.s + fs.Stall_ID * 7, 46), 'Available'
FROM `food_stall` fs
CROSS JOIN (
  SELECT 'Pork Siomai (4pcs)' AS n, 'Steamed pork dumplings served with chili garlic.' AS d, 25.00 AS p, 40 AS s
  UNION ALL SELECT 'Beef Siomai (4pcs)', 'Juicy beef dumplings with soy and calamansi.', 35.00, 14
  UNION ALL SELECT 'Chicken Tapsilog', 'Garlic rice, fried egg, and marinated chicken.', 70.00, 22
  UNION ALL SELECT 'Fish Ball Cup', 'Fried fish balls with sweet and spicy sauce.', 20.00, 9
  UNION ALL SELECT 'Iced Choco', 'Cold chocolate drink, 16 oz.', 40.00, 30
  UNION ALL SELECT 'Egg Sandwich', 'Soft bread with egg and mayo.', 30.00, 5
) t;
