<?php

namespace App\Libraries;

abstract class BaseMiddleware
{
    abstract public function handle();
}