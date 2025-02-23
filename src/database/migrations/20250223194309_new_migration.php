<?php
// Example SQL query: Modify this query for the specific migration.
return "
DROP TABLE IF EXISTS orders;
        CREATE TABLE orders (
            order_id INT AUTO_INCREMENT PRIMARY KEY,
            txn_id VARCHAR(255) NOT NULL,
            product_id INT NOT NULL,
            description TEXT,
            amount DECIMAL(10, 2) NOT NULL,
            status ENUM('pending', 'completed', 'cancel') NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
";