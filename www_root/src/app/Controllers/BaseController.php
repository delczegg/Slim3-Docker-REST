<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Config\Config as Config;


class BaseController {
    private $logger;

    /// DI via constructor
    public function __construct($Logger) {
        $this->logger = $Logger;
    }
    

    /// GET
    /// list phones
    public function index(Request $request, Response $response){
        $this->logger->info('CALLED ENDPOINT /');
        return $response->withJson([
                'success' => true,
                '_metadata' => Config::metadata($request->getUri()->getPath()),
                'result' => [
                    "Default Page :: Index"
                ],
                'errors' => []
            ], 200);
    }

    /**
     * 
     * 
     
"_metadata": 
  {
      "page": 5,
      "per_page": 20,
      "page_count": 20,
      "total_count": 521,
      "Links": [
        {"self": "/products?page=5&per_page=20"},
        {"first": "/products?page=0&per_page=20"},
        {"previous": "/products?page=4&per_page=20"},
        {"next": "/products?page=6&per_page=20"},
        {"last": "/products?page=26&per_page=20"},
      ]
  }

     * 
     * 
     */
    
    
    
} /// END Of PhoneController Class