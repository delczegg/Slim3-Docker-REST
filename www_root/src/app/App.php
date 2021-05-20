<?php 

namespace App;

class App {

	private $app;

	public function __construct(){

		$this->app = new \Slim\App(\App\Config\Config::slim());

		/// init
		$this->dependencies();
        $this->middleware();
        $this->routes(); 
		
	}



	/// Api object
	public function get() {
        return $this->app;
    }


    /// Deps.
	private function dependencies() {
        return new \App\Dependencies($this->app);
    }


    /// Middleware
    private function middleware() {
        return new \App\Middleware($this->app);
    }
    
    /// Routing
    private function routes() {
        return [
            new \App\Routes\User($this->app), 
            new \App\Routes\Phone($this->app),
            new \App\Routes\Base($this->app)
        ];
    }



} /// END Of App Class