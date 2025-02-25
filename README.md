# Telex Integration for Order and Metrics Tracking

This project integrates with [Telex.im](https://telex.im) to track user metrics and order statuses, sending the collected data to Telex channels. The system captures user interactions such as page views, browser details, and device information, as well as order-related data. It outputs the results to a specified Telex channel via a webhook.

## Features
- **Telex Integration**: Send order and metrics data to a Telex channel.
- **Metrics Tracking**: Collect browser, device, and session data.
- **Order Management**: Track and store order information like transaction ID and status.

## Setup and Testing

### 1. Clone the repository

```bash
git clone https://github.com/your-repository.git
```

### 2. Install Dependencies
In the project root directory, run the following command to install the required dependencies:

```bash
composer install
```
### 3. Set up Environment
An .env file will be generated. Open this file and update the database settings as per your configuration.

### 4. Set up the Database
Navigate to the src/database directory:

``` bash
cd src/database
```
Run the migration script:

``` bash
php script.php migrate
```
Seed the database with example data:

``` bash
php script.php seed
```
### 5. Start the Project
Return to the root directory and start the PHP server:

```bash
cd ../../
php -S localhost:8800
```
Navigate to the root URL of your server to generate the API key. You can copy this script and paste it into your demo site or:

1. Go to the database and retrieve the seeded API key.
2. Visit Telex.im and add the integration.
3. In the Telex integration settings, paste the generated API key in the API field and use the webhook URL for your channel in the Webhook field.

## Database Schema
- Orders Table: Stores order information, including transaction ID, product details, and order status.

- Metrics Table: Tracks user metrics like browser, device, page views, and session data.


## Telex Integration
This project sends both order and metrics data to a Telex channel via a webhook. The integration works by tracking page views and user actions, then forwarding the collected data to the designated Telex channel in real time.


# Integration Testing

## Overview

This section provides guidelines for testing the `/integration/` endpoints of the order management API. The API includes functionalities such as listing, placing, canceling, deleting, processing, and backdating orders, as well as webhook handling.

## Base URL

```
https://your-api-domain.com/integration/
```

## Endpoints

### 1. List Orders

**Endpoint:**

```
GET /integration/listOrders
```

**Description:** Fetches all orders from the system. 

**Expected Response:**

```json
{
  "orders": [
    {
      "order_id": 1,
      "txn_id": "123456",
      "product_id": "001",
      "description": "Sample product",
      "amount": 100.0,
      "user_id": 10
    }
  ]
}
```

### 2. Place Order

**Endpoint:**

```
POST /integration/placeOrder
```

**Required Payload:**

```json
{
  "txn_id": "123456",
  "product_id": "001",
  "description": "Sample product",
  "amount": 100.0,
  "user_id": 10
}
```

**Expected Response:**

```json
{
  "message": "Order placed successfully"
}
```

### 3. Cancel Order

**Endpoint:**

```
POST /integration/cancelOrder/{order_id}
```

**Expected Response:**

```json
{
  "message": "Order cancelled successfully"
}
```

### 4. Delete Order

**Endpoint:**

```
DELETE /integration/deleteOrder/{order_id}
```

**Expected Response:**

```json
{
  "message": "Order deleted successfully"
}
```

### 5. Process Order

**Endpoint:**

```
POST /integration/processOrder/{order_id}
```

**Expected Response:**

```json
{
  "message": "Order processed successfully"
}
```


### 6. Tick API
Use this endpoint to get a summary of all orders and also page metrics

**Endpoint:**

```
POST /integration/tick
```

**Required Payload:**

```json
{
  "settings": [
    { "label": "API Key", "default": "your_api_key" },
    { "label": "Notification Webhook URL", "default": "https://your-webhook.com" }
  ]
}
```

**Expected Response:**

```json
{
  "message": "Tick received successfully",
  "status": "success",
  "report": "Report details here"
}
```
## Error Handling

Common error responses include:

- `405 Method Not Allowed` if the request method is incorrect.
- `422 Unprocessable Entity` if required parameters are missing.
- `500 Internal Server Error` if an unexpected error occurs.

## Testing Tools

Use tools like Postman or Curl for API testing. Example Curl command:

```bash
curl -X POST "https://space.otecfx.com/integration/placeOrder" \
-H "Content-Type: application/json" \
-d '{"txn_id":"123456","product_id":"001","description":"Sample product","amount":100.0,"user_id":10}'
```

# Output Sample of Test

<img width="1280" alt="Screenshot 2025-02-25 at 10 29 46" src="https://github.com/user-attachments/assets/341cbdd1-cb2d-4608-9026-cdb8a448891b" />
<img width="1280" alt="Screenshot 2025-02-25 at 10 30 04" src="https://github.com/user-attachments/assets/abf60e4e-6f63-463d-91a4-14cbd878e250" />



## Conclusion

Follow the above steps to ensure all endpoints work as expected. Verify success and error responses for a comprehensive test.






For more information on Telex integration, check the official Telex integration documentation.

Feel free to copy and paste this into your `README.md` file! Let me know if you need any further adjustments.
