<?php
namespace App;

class Middleware {
    private $app;
    private $container;
    
    function __construct($app) {
        $this->app = $app;
        $this->container = $app->getContainer(); // DI container
        $this->cors();
    }
    
    // CORS
    function cors() {
        $this->app->add(function ($req, $res, $next) {
            $response = $next($req, $res);
            return $response->withHeader('Access-Control-Allow-Origin', '*')
                    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PATCH');
        });
    }
        
} /// END Of Middleware