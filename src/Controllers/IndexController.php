<?php


use App\Libraries\Controller;
use App\Middleware\ApiMiddleware;

class IndexController extends Controller
{
    private $data;
    public function __construct()
    {
        $this->data = [];
        $this->addMiddleware([ApiMiddleware::class]);
        // $this->model("User");
    }
    public function index()
    {
        $this->view('index', $this->data);
    }
}
