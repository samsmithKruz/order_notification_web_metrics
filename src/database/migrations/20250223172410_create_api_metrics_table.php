<?php
// Example SQL query: Modify this query for the specific migration.
return "
-- Drop the `api_keys` table if it exists
DROP TABLE IF EXISTS `api_keys`;

-- Create the `api_keys` table
CREATE TABLE `api_keys` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `api_key` VARCHAR(255) NOT NULL UNIQUE,  -- Unique API key for identification
  `domain` VARCHAR(255) NOT NULL,  -- Base URL/domain that the key is associated with
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Timestamp of creation
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  -- Timestamp of the last update
);

-- Drop the `metrics` table if it exists
DROP TABLE IF EXISTS `metrics`;

-- Create the `metrics` table
CREATE TABLE `metrics` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `api_key_id` INT NOT NULL,  -- Foreign key linking to `api_keys` table
  `session_id` VARCHAR(255) NOT NULL,  -- Unique session ID
  `page` VARCHAR(255) NOT NULL,  -- URL path of the page
  `referrer` VARCHAR(255) DEFAULT NULL,  -- Referring page
  `browser` VARCHAR(255) NOT NULL,  -- Browser used by the user
  `device` VARCHAR(255) NOT NULL,  -- Device type (Mobile, Tablet, Desktop)
  `os` VARCHAR(255) NOT NULL,  -- Operating system
  `screen_resolution` VARCHAR(50) NOT NULL,  -- Screen resolution (e.g., 1920x1080)
  `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Timestamp of when the metric was collected
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Timestamp when record was created
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  -- Timestamp when record was last updated
  FOREIGN KEY (`api_key_id`) REFERENCES `api_keys`(`id`) ON DELETE CASCADE  -- Foreign key relationship
);

";