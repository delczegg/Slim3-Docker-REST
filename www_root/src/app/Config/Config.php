<?php
namespace App\Config;

class Config {

    const APP_NAME = "BF RestAPI Test";
    const APP_VERSION = "v1.0";


    /// .hu phone area numbers
    /// specific data for phonenum checks
    private function areaNumbers(){
        return [1,22,23,24,25,26,27,28,29,32,33,34,35,36,37,42,44,45,46,47,48,49,52,53,54,56,57,59,62,63,66,68,69,72,73,74,75,76,77,78,79,82,83,84,85,87,88,89,92,93,94,95,96,99,20,21,30,31,40,50,70,80];
    }


    /// Check phone number, specified for .hu areas
    public function checkPhoneNumber( $data = "" ){
        $ret = false;
        if( preg_match( "'^\+36-(\d{1,2})-(\d{4})-(\d{3})$'s", trim($data) ) ){
            if( in_array(explode("-", $data)[1], self::areaNumbers()) ){
                $ret = true;
            }
        }
        return $ret;
    }


    // DB setup
    public function db() {
        return [
            'driver' => 'pgsql',
            'host' => 'bigfish_db_1',
            'database' => 'bigfish',
            'username' => 'bigfish_dba',
            'password' => 'B1gF!s4@',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ];
    }

    // Slim setup
    public function slim() {
        return [
            'settings' => [
                'determineRouteBeforeAppMiddleware' => false,
                'displayErrorDetails' => true,
                'db' => self::db()
            ],
        ];
    }

    // Auth setup, soon in v1.1...
    ///public function auth() {
    ///    return [
    ///        'secret' => 'WeryBigSecretKey!',
    ///        'expires' => 30, // in minutes
    ///        'hash' => PASSWORD,
    ///        'jwt' => 'HS256'
    ///    ];
    ///}
}