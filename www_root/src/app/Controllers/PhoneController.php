<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Carbon\Carbon;
use App\Models\Phone as Phone;
use App\Config\Config as Config;

class PhoneController {
    private $logger;
    private $db;
    private $table;

    /// DI via constructor
    public function __construct($Logger, $DB) {
        $this->logger = $Logger;
        $this->db = $DB;
        $this->table = $this->db->table('user_phone');
    }
    

    /// GET
    /// list phones
    public function list(Request $request, Response $response, $args){
        $this->logger->info('POST /phones');
        if( !empty($args['user_id']) ){
            /// get specific records for user
            $result[] = $this->table->where('user_id', '=', (int)$args['user_id'])->get();
        } else {
            /// get all phone records
            $result[] = $this->table->get();
        }

        return $response->withJson([
            'success' => true,
            '_metadata' => [
                'name' => Config::APP_NAME,
                'version' => Config::APP_VERSION,
                'endpoint' => '/phone'
            ],
            'result' => $result,
            'errors' => []
        ], 200);
    }


    /// PATCH
    /// update selected phone(s)
    public function update(Request $request, Response $response){
        $data = $request->getParsedBody()['data'];

        $this->logger->info('PATCH /phones', $data);
        
        $errors = [];
        $result = [];
       

        if ( !empty($data[0]) ) {
            
            foreach( $data as $items ){

                /// validate phone number
                if( false === Config::checkPhoneNumber($items['phonenumber']) ){
                    $errors[] = [
                        "data" => $items,
                        "message" => "Data error: phone number not valid!"
                    ];
                } else {

                    if( $items['isdefault'] == true ){
                        Phone::where("user_id", "=", $items['user_id'])->update([
                            'isdefault' => false
                        ]);
                    }

                    $updateValues = [];
                    foreach( $items as $key => $val ){
                        if( $key != 'user_id' ){
                            $updateValues[$key] = $val;  
                        } 
                    }
                    $updateValues['updatedat'] = Carbon::now();

                    Phone::where([
                        ["user_id", "=", $items['user_id']],
                        ["phonenumber", "=", $items['phonenumber']]
                    ])->update($updateValues);

                    $result[] = $items;
                }
                    
            }
              
            return $response->withJson([
                'success' => true,
                '_metadata' => [
                    'name' => Config::APP_NAME,
                    'version' => Config::APP_VERSION,
                    'endpoint' => '/phones'
                ],
                'result' => $result,
                'errors' => $errors
            ], 200);

        } else {
            /// Error occured
            return $response->withJson([
                'success' => false,
                'errors' => [
                    "message" => "Input data is empty!"
                ]
            ], 400);
        }
    }


    /// DELETE /phones
    /// remove selected phones(s)
    public function remove(Request $request, Response $response){
        $this->logger->info('DELETE /phones');
        $data = $request->getParsedBody()['data'];
        $errors = [];
        $result = [];

        /// DON'T FORGET! Check deleting: "isDefault" flag is True, delete not allowed
        if( !empty($data[0]) ){
            foreach ($data as $items) {
                $existsPhone = Phone::where([
                    ["user_id", "=", $items['user_id']],
                    ["phonenumber", "=", $items['phonenumber']],
                    ["isdefault", "=", true]
                ])->get();
                if( !empty($existsPhone) ){
                    $errors[] = [
                        "data" => $items,
                        "message" => "Default phonenumber not allowed for delete!"
                    ];
                } else {
                    Phone::where([
                        ["user_id", "=", $items['user_id']],
                        ["phonenumber", "=", $items['phonenumber']]
                    ])->update([
                        "deletedat" => Carbon::now()
                    ]);
                    $result[] = $items;
                }
            }

            return $response->withJson([
                'success' => true,
                '_metadata' => [
                    'name' => Config::APP_NAME,
                    'version' => Config::APP_VERSION,
                    'endpoint' => '/phones'
                ],
                'result' => $result,
                'errors' => $errors
            ], 200);
        } else {
            /// Error occured
            return $response->withJson([
                'success' => false,
                'errors' => [
                    "message" => "Input data is empty!"
                ]
            ], 400);
        }
    }


    // POST /phones
    // Create phone(s) record
    public function create(Request $request, Response $response) {
        
        $data = $request->getParsedBody()['data'];

        $this->logger->info('POST /phones/create', $data);
        
        $errors = [];
        $result = [];
       

        if ( !empty($data[0]) ) {
            
            foreach( $data as $items ){

                /// validate phone number
                if( false === Config::checkPhoneNumber($items['phonenumber']) ){
                    $errors[] = [
                        "data" => $items,
                        "message" => "Data error: phone number not valid!"
                    ];
                } else {

                    if( $items['isdefault'] == true ){
                        Phone::where("user_id", "=", $items['user_id'])->update([
                            'isdefault' => false
                        ]);
                    }

                    $newPhone = Phone::create([
                        'user_id' => $items['user_id'],
                        'phonenumber' => $items['phonenumber'],
                        'isdefault' => (bool)$items['isdefault'],
                        'createdat' => Carbon::now(),
                        'updatedat' => Carbon::now()
                    ]);

                    $result[] = [
                        'user_id' => $newPhone->user_id,
                        "phonenumber" => $newPhone->phonenumber, 
                        "isdefault" => $newPhone->isdefault
                    ];
                }
                    
            }
              
            return $response->withJson([
                'success' => true,
                '_metadata' => [
                    'name' => Config::APP_NAME,
                    'version' => Config::APP_VERSION,
                    'endpoint' => '/phones/create'
                ],
                'result' => $result,
                'errors' => $errors
            ], 200);

        } else {
            /// Error occured
            return $response->withJson([
                'success' => false,
                'errors' => $errors
            ], 400);
        }
    }
    
    
} /// END Of PhoneController Class