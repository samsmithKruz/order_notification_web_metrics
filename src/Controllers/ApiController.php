<?php

use App\Libraries\Controller;
use App\Libraries\Database;
use App\Libraries\Helpers;
use App\Middleware\ApiMiddleware;

class ApiController extends Controller
{
    private Database $db;
    public function __construct()
    {
        $this->addMiddleware([ApiMiddleware::class]);
        $this->db = new Database();
    }
    public function index()
    {
        JsonResponse(['message' => 'api is alive']);
    }
    public function metrics()
    {
        $data = get_data();
    }
    public function key(){
        $data = get_data();
        $api_key = $data?->apiKey;
        $url = safe_data($data?->url);
        $this->db->query("SELECT id from api_keys WHERE api_key = :api_key OR domain = :url")
        ->bind(":api_key", $api_key)
        ->bind(":url", $url)
        ->execute();
        if($this->db->rowCount() > 0){
            JsonResponse(['message' => 'API key already exists'],400);
        }
        $this->db->query("INSERT INTO api_keys (api_key, domain) VALUES (:api_key, :url)")
        ->bind(":api_key", $api_key)
        ->bind(":url", $url)
        ->execute();
        if($this->db->rowCount() == 0){
            JsonResponse(['message' => 'Failed to save API key'],500);
        }
        JsonResponse(['message' => 'API key saved successfully']);
    }
}
