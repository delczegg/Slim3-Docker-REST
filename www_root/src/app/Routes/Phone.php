<?php
namespace App\Routes;


/**
 * Routing definitions for /phones endpoint
 * **/

class Phone {
    function __construct($app) {
        $app->get('/phones[/{user_id}]', '\App\Controllers\PhoneController:list');
        $app->patch('/phones/update', '\App\Controllers\PhoneController:update');
        $app->delete('/phones/remove', '\App\Controllers\PhoneController:remove');
        $app->post('/phones/create', '\App\Controllers\PhoneController:create');
    }
}