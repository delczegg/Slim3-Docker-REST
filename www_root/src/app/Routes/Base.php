<?php 

namespace App\Routes;

class Base {
    function __construct($app) {
        $app->any('/', '\App\Controllers\BaseController:index');
    }
}