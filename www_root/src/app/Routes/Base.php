<?php 

namespace App\Routes;


/**
 * Routing for base route "/"
 * **/
class Base {
    function __construct($app) {
        $app->any('/', '\App\Controllers\BaseController:index');
    }
}