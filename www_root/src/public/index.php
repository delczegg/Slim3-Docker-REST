<?php
error_reporting(E_ERROR);
ini_set('display_errors', false);

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


require '../vendor/autoload.php';


$app = (new App\App())->get();
$app->run();
