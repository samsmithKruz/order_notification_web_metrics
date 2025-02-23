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

For more information on Telex integration, check the official Telex integration documentation.

Feel free to copy and paste this into your `README.md` file! Let me know if you need any further adjustments.