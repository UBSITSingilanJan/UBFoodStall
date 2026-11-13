-- UBFoodStall: changes needed by the customer pages.
-- Run once in phpMyAdmin (database: ubfoodstall).

-- 1. Products need a stock count and a short description.
ALTER TABLE `product`
  ADD COLUMN `description` varchar(255) NOT NULL DEFAULT '' AFTER `productname`,
  ADD COLUMN `stock` int(11) NOT NULL DEFAULT 0 AFTER `price`;

-- 2. Product reviews (one review per customer per product).
--    Stall reviews keep using the existing `review` table.
CREATE TABLE IF NOT EXISTS `product_review` (
  `ProductReview_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Product_ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `review_text` varchar(500) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ProductReview_ID`),
  UNIQUE KEY `uq_product_customer` (`Product_ID`,`Customer_ID`),
  KEY `Customer_ID` (`Customer_ID`),
  CONSTRAINT `product_review_ibfk_1` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_review_ibfk_2` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. The customer table requires a Stall_ID, but registering a customer does not
--    send one, so the insert fails. Make it optional.
ALTER TABLE `customer` MODIFY `Stall_ID` int(11) NULL DEFAULT NULL;
