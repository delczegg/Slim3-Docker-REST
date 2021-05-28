<?php
namespace App\Routes;

/**
 * Routing definitions for /users endpoint
 * **/

class User {
    function __construct($app) {

        $app->map(
        	['GET', 'POST'],
        	'/users[/{id:[0-9]+}]', 
        	'\App\Controllers\UserController:list'
        );

        $app->patch(
        	'/users', 
        	'\App\Controllers\UserController:update'
        );

        $app->delete(
        	'/users', 
        	'\App\Controllers\UserController:remove'
        );

        $app->post(
        	'/users/create', 
        	'\App\Controllers\UserController:create'
        );

    }
} /// END Of User entity routing