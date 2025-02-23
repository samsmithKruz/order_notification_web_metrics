<?php


use App\Libraries\Controller;
use App\Libraries\Helpers;
use App\Middleware\ApiMiddleware;

class IntegrationController extends Controller
{
    private $data;
    private $webhook_url;
    public function __construct()
    {
        $this->data = [];
        $this->addMiddleware([ApiMiddleware::class]);
        $this->model("Order");
    }
    public function index()
    {
        JsonResponse([
            "data" => [
                "date" => [
                    "created_at" => "2025-02-23",
                    "updated_at" => "2025-02-23"
                ],
                "descriptions" => [
                    "app_name" => "Order and Metrics Notifier",
                    "app_description" => "Sends notifications for order events and daily website metrics.",
                    "app_logo" => DOMAIN."/logo.png",
                    "app_url" => DOMAIN,
                    "background_color" => "#ffffff"
                ],
                "integration_category" => "E-commerce & Retail",
                "integration_type" => "interval",
                "is_active" => true,
                "key_features" => [
                    "Real-time order event notifications.",
                    "Daily summaries of website metrics.",
                    "Customizable notification settings."
                ],
                "settings" => [
                    [
                        "label"=> "interval",
                        "type"=> "text",
                        "required"=> true,
                        "default"=> "* * * * *"
                    ],
                    [
                        "label" => "API Key",
                        "type" => "text",
                        "required" => true,
                        "default" => ""
                    ],
                    [
                        "label" => "Notification Webhook URL",
                        "type" => "text",
                        "required" => true,
                        "default" => ""
                    ]
                ],
                "tick_url" => DOMAIN."/integration/tick",
                "target_url" => DOMAIN."/integration/webhook",
            ]
        ]);
    }
    public function listOrders()
    {
        $orders = $this->model->listOrder();
        jsonResponse($orders);
    }

    public function placeOrder()
    {
        $data = get_data();
        if (!Helpers::isMethod("POST")) {
            jsonResponse([
                'message' => "This route requires POST method to add seed",
                'status' => 'error'
            ], 405);
        }
        if (
            !isset($data->txn_id) ||
            !isset($data->product_id) ||
            !isset($data->description) ||
            !isset($data->amount) ||
            !isset($data->user_id)
        ) {
            jsonResponse([
                'message' => "Payload to place order must contain [txn_id,product_id,description,amount,user_id]"
            ], 422);
        }
        if ($this->model->placeOrder($data)) {
            emit_event(
                event_name: "Placed Order",
                message: "An order has been placed for product: #{$data->product_id}, amount:" . $data->amount . ", with a description of: " . $data->description,
                status: 'success',
                username: 'order-placer'
            );
            jsonResponse(['message' => 'Order placed successfully'], 201);
        }
        emit_event(
            event_name: "Placed Order",
            message: "An error was encountered while trying place order for product: #" . $data->product_id . ", amount:" . $data->amount,
            status: 'error',
            username: 'order-placer'
        );
        jsonResponse(['message' => 'An error occurred while placing your order'], 500);
    }
    public function cancelOrder($params)
    {
        $order_id = @$params[0];
        if (!isset($order_id)) {
            jsonResponse([
                'message' => "You must pass order ID to cancel Order",
                'status' => 'error'
            ], 422);
        }
        if ($this->model->cancelOrder($order_id)) {
            emit_event(
                event_name: "Canceled Order",
                message: "An order has been canceled for order:{$order_id}",
                status: 'success',
                username: 'order-placer'
            );
            jsonResponse(['message' => 'Order cancelled successfully'], 201);
        }
        emit_event(
            event_name: "Placed Order",
            message: "An error while trying to cancel an order for order_id: {$order_id}",
            status: 'error',
            username: 'order-placer'
        );
        jsonResponse(['message' => 'An error occurred while cancelling your order'], 500);
    }
    public function deleteOrder($params)
    {
        $order_id = @$params[0];
        if (!isset($order_id)) {
            jsonResponse([
                'message' => "You must pass order ID to delete Order",
                'status' => 'error'
            ], 422);
        }
        if ($this->model->deleteOrder($order_id)) {
            emit_event(
                event_name: "Delete Order",
                message: "An order has been deleted for order:{$order_id}",
                status: 'success',
                username: 'order-placer'
            );
            jsonResponse(['message' => 'Order deleted successfully'], 201);
        }
        emit_event(
            event_name: "Delete Order",
            message: "An error while trying to delete an order for order_id: {$order_id}",
            status: 'error',
            username: 'order-placer'
        );
        jsonResponse(['message' => 'An error occurred while deleting your order'], 500);
    }
    public function processOrder($params)
    {
        $order_id = @$params[0];
        if (!isset($order_id)) {
            jsonResponse([
                'message' => "You must pass order ID to process Order",
                'status' => 'error'
            ], 422);
        }
        if ($this->model->processOrder($order_id)) {
            emit_event(
                event_name: "Process Order",
                message: "An order has been processed - order:{$order_id}",
                status: 'success',
                username: 'order-placer'
            );
            jsonResponse(['message' => 'Order processed successfully'], 201);
        }
        emit_event(
            event_name: "Process Order",
            message: "An error while trying to process an order for order_id: {$order_id}",
            status: 'error',
            username: 'order-placer'
        );
        jsonResponse(['message' => 'An error occurred while processing your order'], 500);
    }
    public function tick() {
        if(!Helpers::isMethod("POST")){
            jsonResponse([
                'message' => 'Method not allowed',
                'status' => 'success'
            ]);
        }
        $rawData = file_get_contents("php://input");
        $data = json_decode($rawData,true); // This returns an object
        
        // Convert settings into an associative array (label => default)
        $settings = (array) array_column($data['settings'], 'default', 'label');
    
        // Extract API Key
        $apiKey = $data['settings']['API Key'] ?? null;
    
        if (!$apiKey) {
            logMessage("API Key not found in settings.");
            exit();
        }

        $this->webhook_url = $data['settings']['Notification Webhook URL'];
        
        // Pass settings and API key to the model's method
        $report = $this->model->reportTelex($apiKey, $settings);
        emit_event(
            event_name: "Order Notification and Web Metrics",
            message: $report,
            status: 'success',
            username: 'reporter',
            hook_url: $this->webhook_url
        );
        jsonResponse([
            'message' => 'Tick received successfully',
            'status' => 'success',
            'report' => $report
        ]);
    }
    public function webhook(){
        jsonResponse([
            'message' => 'Webhook received successfully',
            'status' => 'success'
        ]);
    }
    public function backDateOrder($params)
    {
        $order_id = @$params[0];
        if (!isset($order_id)) {
            jsonResponse([
                'message' => "You must pass order ID to process Order",
                'status' => 'error'
            ], 422);
        }
        if ($this->model->backDateOrder($order_id)) {
            emit_event(
                event_name: "Backdate Order",
                message: "An order has been backdated: order:{$order_id}",
                status: 'success',
                username: 'order-placer'
            );
            jsonResponse(['message' => 'Order processed successfully'], 201);
        }
        emit_event(
            event_name: "Backdate Order",
            message: "An error while trying to backdate an order for order_id: {$order_id}",
            status: 'error',
            username: 'order-placer'
        );
        jsonResponse(['message' => 'An error occurred while processing your order'], 500);
    }
    
}
