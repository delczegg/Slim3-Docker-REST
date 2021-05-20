<?php
namespace App;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Config\Config as Config;


class Dependencies {
    private $container;
    
    function __construct($app) {
        $this->container = $app->getContainer(); /// DI container
        $this->dependencies(); /// Load deps into container
        $this->inject($app); /// Inject deps into controllers
        $this->handlers(); /// Main handlers
    }
    
    /// Setup dependency container
    function dependencies() {
        
        /// Monolog
        $this->container['logger'] = function($c) {
            $logFileName = '../logs/'.date('Ymd-H').'_app.log';
            $logger = new Logger('appLogger');
            $file_handler = new StreamHandler($logFileName, Logger::INFO);
            $logger->pushHandler($file_handler);
            return $logger;
        };

        /// ORM
        $this->container['db'] = function($c) {
            $cap = new \Illuminate\Database\Capsule\Manager;
            $cap->addConnection($c['settings']['db']);
            $cap->setAsGlobal();
            $cap->bootEloquent();
            return $cap;
        };

        /// awurth/SlimValidation 
        /// (removed while php7 vs php8 issue!!!)
        ///$this->container['validator'] = function($c) {
        ///    return new \Awurth\SlimValidation\Validator();
        ///};

    }
    
    /// Inject deps. to ctrl
    function inject($app) {

        /// User
        $this->container['\App\Controllers\UserController'] = function($c) use ($app) {
            return new \App\Controllers\UserController($c->get('logger'), $c->get('db'));
        };

        /// Phone
        $this->container['\App\Controllers\PhoneController'] = function($c) use ($app) {
            return new \App\Controllers\PhoneController($c->get('logger'), $c->get('db'));
        };        

        /// Default
        $this->container['\App\Controllers\BaseController'] = function($c) use ($app) {
            return new \App\Controllers\BaseController($c->get('logger'));
        };

    }
    

    /// Main handlers
    function handlers() {

        /// 404 
        $this->container['notFoundHandler'] = function($c) {
            return function($request, $response) use ($c) {
                return $c['response']->withJson([
                    'success' => false,
                    '_metadata' => [
                        'name' => Config::APP_NAME,
                        'version' => Config::APP_VERSION
                    ],
                    'result' => [], 
                    'errors' => [
                        "Resource not found" => $request->getUri()->getPath()
                    ]
                ], 404);
            };
        };

        $this->container['notAllowedHandler'] = function($c) {
            return function($request, $response) use ($c) {
                return $c['response']->withJson([
                    'success' => false,
                    '_metadata' => [
                        'name' => Config::APP_NAME,
                        'version' => Config::APP_VERSION
                    ],
                    'result' => [], 
                    'errors' => [
                        "Method not allowed" => $request->getMethod(), 
                        "Path" => $request->getUri()->getPath()
                    ]
                ], 404);
            };
        };


    }

} /// END Of Dependencies Class