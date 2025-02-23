<?php

use App\Libraries\Controller;
use GuzzleHttp\Client;


if (!function_exists('assets')) {
    function asset($path)
    {
        // Adjust the base URL if necessary
        return '/public/' . ltrim($path, '/');
    }
}



if (!function_exists('url')) {
    /**
     * Generate the URL for a given path relative to the base URL.
     *
     * @param string $path
     * @return string
     */
    function url($path = '')
    {
        // Get the base URL from the .env or configuration file
        $baseUrl = rtrim(getenv('APP_URL') ?: 'http://localhost', '/');

        // Ensure the path starts with a single slash
        $path = ltrim($path, '/');

        // Return the concatenated base URL and path
        return $baseUrl . '/' . $path;
    }
}


function base_url($path = '')
{
    return 'http://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($path, '/');
}
function current_url()
{
    return "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
}
function redirect($url)
{
    header('Location: ' . base_url($url));
    exit();
}
function back($default = "/")
{
    header("location:" . ($_SERVER['HTTP_REFERER'] ?? $default));
    exit();
}
function sanitize($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
function slugify($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    return strtolower(trim($text, '-'));
}
function d($var)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
function dd($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    exit;
}
function old($key, $default = null)
{
    return isset($_POST[$key]) ? sanitize($_POST[$key]) : $default;
}
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function validate_csrf($token)
{
    return hash_equals($_SESSION['csrf_token'], $token);
}
function flashMessage($data)
{
    $_SESSION[APP]->flashMessage = (object)$data;
}

function access($roles)
{
    return in_array($_SESSION[APP]->user->role, $roles);
}

function safe_data($data)
{
    $sanitize = function ($value) {
        return addslashes(htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8'));
    };
    if (is_array($data)) {
        return array_map($sanitize, $data);
    }
    return $sanitize($data);
}
function view($view, $data = [])
{
    require_once __DIR__ . "/Controller.php";
    $controller = new Controller;

    return $controller->view($view, $data);
}
function get_data()
{
    // Get the raw POST data
    $rawData = file_get_contents("php://input");

    // Decode the JSON data and return as an object
    return json_decode($rawData);
}


/**
 * Helper function to send json response to user and also payload
 * @param array $data
 * @param int $statusCode
 * @return never
 */
function jsonResponse($data, $statusCode = 200)
{
    header('content-type: application/json');
    http_response_code($statusCode);
    echo json_encode((array)$data);
    exit();
}


/**
 * Sends an HTTP request using Guzzle.
 *
 * @param string $url The URL to send the request to.
 * @param string $method The HTTP method to use (default is 'GET').
 * @param array $data The data to send in the request body (default is an empty array).
 * @param array $headers The headers to include in the request (default is an empty array).
 *
 * @return array The JSON-decoded response body, or an error message if the request fails.
 */
function sendRequest($url, $method = 'GET', $data = [], $headers = [])
{
    // Create a Guzzle client
    $client = new Client();

    // Set default headers
    $headers = array_merge($headers, [
        'Content-Type' => 'application/json',
    ]);

    // Prepare the options for the request
    $options = [
        'headers' => $headers,
    ];

    // Add the data to the body for POST, PUT, PATCH requests
    if (!empty($data)) {
        $options['json'] = $data;
    }

    try {
        // Send the HTTP request using Guzzle
        $response = $client->request($method, $url, $options);

        // Return the JSON-decoded response body
        return json_decode($response->getBody(), true);
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        // Handle Guzzle exception if request fails
        return ['error' => $e->getMessage()];
    }
}


/**
 * Helper function to write to log
 * @param string $message
 * @param string $file
 * @return void
 */
function logMessage($message, $file = 'logs.log')
{
    $logFile = __DIR__ . '/' . basename($file);
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}
/**
 * Emit an event to a specified Telex channel.
 *
 * @param string $event_name The name of the event to represent the event.
 * @param string $message The body of the message to show in Telex.
 * @param string $username The username of the pusher to identify the event in Telex.
 * @param string $hook_url The URL of the Telex channel to send the event to.
 * @param string $status This indicates the status of event to emit 'success|error'
 */
function emit_event($event_name, $message, $status, $username, $hook_url = "")
{
    $hook_url = !empty($hook_url) ? $hook_url : getenv('WEBHOOK_URL');
    $payload = [
        "event_name" => $event_name,
        "message" => $message,
        "status" => $status,
        "username" => $username
    ];

    return sendRequest($hook_url ?: "", "POST", $payload, ['content-type: application/json']);
}
/**
 * Formats the given input string by applying the specified format options.
 *
 * @param string $input The input string to be formatted.
 * @param array $options An associative array of format options. Supported options include:
 *                       - 'uppercase' (bool): If true, converts the string to uppercase.
 *                       - 'lowercase' (bool): If true, converts the string to lowercase.
 *                       - 'capitalize' (bool): If true, capitalizes the first letter of each word.
 *                       - 'trim' (bool): If true, trims whitespace from the beginning and end of the string.
 *                       - 'prefix' (string): A string to prepend to the input string.
 *                       - 'suffix' (string): A string to append to the input string.
 *
 * @return string The formatted string.
 */
function formatOutput($data, $headings)
{
    $data = (array)$data;
    $output = "";
    foreach ($data as $item) {
        $item = (array)$item;
        foreach ($headings as $heading => $key) {
            $output .= "$heading: {$item[$key]}\n";
        }
        $output .= "-------------------------\n";
    }
    return $output;
}
