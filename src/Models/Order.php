<?php

namespace App\Models;

use App\Libraries\Helpers;
use App\Libraries\Model;

class Order extends Model
{
    public function initTable()
    {
        $this->db->query("
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
        ")->execute();
    }
    public function listOrder()
    {
        return $this->db->query("SELECT * FROM orders")->resultSet();
    }
    public function getDailyOrderSummary()
    {
        return $this->db->query("SELECT * FROM orders  WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY")->resultSet();
    }
    public function placeOrder($data)
    {
        $data = (object)$data;
        $this->db->query("
        INSERT INTO orders(txn_id,product_id,description,amount,status)
        values(:txn_id,:product_id,:description,:amount,:status)
        ")
            ->bind(":txn_id", safe_data($data->txn_id))
            ->bind(":product_id", safe_data($data->product_id))
            ->bind(":description", safe_data($data->description))
            ->bind(":amount", safe_data($data->amount))
            ->bind(":status", "pending")
            ->execute();
        return $this->db->rowCount() ? $this->db->lastInsertId() : null;
    }
    public function processOrder($order_id)
    {
        $this->db->query("UPDATE orders SET status='completed' WHERE order_id=:order_id")
            ->bind(":order_id", safe_data($order_id))
            ->execute();
        return $this->db->rowCount();
    }
    public function cancelOrder($order_id)
    {
        $this->db->query("UPDATE orders SET status='cancel' WHERE order_id=:order_id")
            ->bind(":order_id", safe_data($order_id))
            ->execute();
        return $this->db->rowCount();
    }
    public function deleteOrder($order_id)
    {
        $this->db->query("DELETE FROM orders WHERE order_id=:order_id")
            ->bind(":order_id", safe_data($order_id))
            ->execute();
        return $this->db->rowCount();
    }
    public function backDateOrder($order_id)
    {
        $this->db->query("UPDATE orders SET created_at=DATE_SUB(CURDATE(), INTERVAL 1 DAY) WHERE order_id=:id")->bind(":id", $order_id)->execute();
        return $this->db->rowCount()  ?: null;
    }
    public function reportTelex($api, $data)
    {
        $daily_orders = $this->getDailyOrderSummary();
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $order_message = empty($daily_orders) ? "No order found for yesterder - {$yesterday}" : "The order summary for {$yesterday} is:\n" . formatOutput($daily_orders, [
            'Order ID' => 'order_id',
            'Transaction ID' => 'txn_id',
            'Product ID' => 'product_id',
            'Description' => 'description',
            'Amount' => 'amount',
            'Status' => 'status',
            'Created At' => 'created_at'
        ]);
        $metrics = $this->getWebsiteMetrics($api);
        return $order_message."\n".$metrics;
    }
    function getWebsiteMetrics($api_key)
    {
        $result = $this->db->query("SELECT 
                            DATE(m.timestamp) AS date,
                            COUNT(DISTINCT m.session_id) AS unique_visitors,
                            COUNT(m.id) AS total_page_views,
                            COUNT(DISTINCT m.referrer) AS unique_referrers,
                            COUNT(DISTINCT m.browser) AS unique_browsers,
                            COUNT(DISTINCT m.device) AS unique_devices,
                            COUNT(DISTINCT m.os) AS unique_os,
                            COUNT(DISTINCT m.screen_resolution) AS unique_resolutions
                          FROM metrics m
                          JOIN api_keys ak ON m.api_key_id = ak.id
                          WHERE ak.api_key = :api_key
                          GROUP BY DATE(m.timestamp)
                          ORDER BY DATE(m.timestamp) DESC
                          LIMIT 1")
            ->bind(':api_key', $api_key)
            ->single();

        if (!$result) {
            return "No metrics found for the given API key.";
        }
        $result = (array)$result;

        return "📊 Website Metrics for {$result['date']}:
    - Unique Visitors: {$result['unique_visitors']}
    - Total Page Views: {$result['total_page_views']}
    - Unique Referrers: {$result['unique_referrers']}
    - Unique Browsers: {$result['unique_browsers']}
    - Unique Devices: {$result['unique_devices']}
    - Unique Operating Systems: {$result['unique_os']}
    - Unique Screen Resolutions: {$result['unique_resolutions']}
    ";
    }
}
