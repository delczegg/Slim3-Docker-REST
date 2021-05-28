<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Carbon\Carbon;
use App\Models\User as User;
use App\Models\Phone as Phone;
use App\Config\Config as Config;



class UserController {

    private $log;
    private $db;
    private $table;

    // DI via constructor
    public function __construct($Logger, $DB) {
        $this->log = $Logger;
        $this->db = $DB;
        $this->table = $this->db->table('users');
    }


    /// GET
    /// list users
    public function list(Request $request, Response $response, $args){
        
        $this->log->info('POST /users', $args);
        $data = $request->getParsedBody()['data'];
        if( !empty($data) ) $this->log->info('POST /users', $data);
        
        $errors = [];
        $result = [];
        
        /// simple listing
        if( $request->isGet() ){

            if( isset($args['id']) AND !empty($args['id']) ) $result = User::find((int)$args['id']);
            else $result = $this->table->get();

        } else if( $request->isPost() AND !isset($args['id']) ){ /// advanced listing: sort records
            
            $this->table->select("users.*",  "user_phone.phonenumber as default_phone");
            $this->table->join("user_phone", "user_phone.user_id", "=", "users.id");
            
            if( isset($data[0]['sort']) AND !empty($data[0]['sort']) ){
                
                foreach( $data[0]['sort'] as $v ) {
                    if( strpos($v, "::") ){
                        $ordering = explode("::", $v);
                        if( !empty($ordering[1]) AND 
                            (strtoupper($ordering[1]) == 'ASC' OR strtoupper($ordering[1]) == 'DESC') ){

                            $orderBy = strtoupper($ordering[1]);
                        } else {
                            $orderBy = "ASC";
                        }
                    } else {
                        $ordering[0] = $v;
                        $orderBy = "ASC";
                    }
                    $this->table->orderBy($ordering[0], $orderBy);
                }

            }
            
            if( isset($data[0]['getdeleted']) AND false == $data[0]['getdeleted'] ){
                $this->table->where('deletedat',  null);
            }

            $result = $this->table->get();
            
        } else {
            $errors[] = "Specific user id and mass query not working both! ;)";
        }

        return $response->withJson([
            'success' => true,
            '_metadata' => Config::metadata($request->getUri()->getPath()),
            'result' => $result, 
            'errors' => $errors
        ], 200);
    }


    /// PATCH
    /// update selected user(s)
    public function update(Request $request, Response $response, $args){
        
        $data = $request->getParsedBody()['data'];
        
        $this->log->info('PATCH /users', $data);

        $errors = [];
        $result = [];


        if( !empty($data[0]) ){
            foreach( $data as $items ){
                $id = (int)$items['id'];
                if( !empty($id) ){
                    $tmpError = self::checkInputData($items, false);

                    if( empty($tmpError) ){

                        $updateValues = [];
                        foreach( $items as $key => $val ){
                            if( !in_array($key, ['id', 'default_phone']) ){
                                $updateValues[$key] = $val;  
                            } 
                        }
                        $updateValues['updatedat'] = Carbon::now();

                        User::where("id", "=", $id)->update($updateValues);

                        if( !empty($items['default_phone']) ){

                            Phone::where("user_id", "=", $id)
                            ->update([
                                'isdefault' => false
                            ]);

                            $existsPhone = Phone::where([
                                ["user_id", "=", $id],
                                ["phonenumber", "=", $items['default_phone']]
                            ])->get();

                            if( !empty($existsPhone) ){
                                Phone::where([
                                    ["user_id", "=", $id],
                                    ["phonenumber", "=", $items['default_phone']]
                                ])->update([
                                    'isdefault' => true,
                                    'updatedat' => Carbon::now()
                                ]);
                            } else {
                                $newPhone = Phone::create([
                                    'user_id' => $id,
                                    'phonenumber' => $items['default_phone'],
                                    'isdefault' => true,
                                    'createdat' => Carbon::now(),
                                    'updatedat' => Carbon::now()
                                ]);
                            }
                        }

                        $result[] = $items;

                    } else {
                        $errors[] = $tmpError;
                    }
                }
            }
        }


        return $response->withJson([
            'success' => true,
            '_metadata' => Config::metadata($request->getUri()->getPath()),
            'result' => $result, 
            'errors' => $errors
        ], 200);
    }
    


    /// DELETE /users
    /// remove selected user(s)
    public function remove(Request $request, Response $response, $args){
        
        $data = $request->getParsedBody()['data'];
        
        $this->log->info('DELETE /users', $data);

        $errors = [];
        $result = [];

        if( !empty($data) ){
            foreach( $data as $items ){
                User::where("id", "=", (int)$items['id'])->update([
                    'deletedat' => Carbon::now()
                ]);
                Phone::where("user_id", "=", (int)$items['id'])->update([
                    'deletedat' => Carbon::now()
                ]);
                $result[] = $items;
            }
        }

        return $response->withJson([
            'success' => true,
            '_metadata' => Config::metadata($request->getUri()->getPath()),
            'result' => $result, 
            'errors' => $errors
        ], 200);
    }


    // POST /users
    // Create user
    public function create(Request $request, Response $response){

        $data = $request->getParsedBody()['data'];
        
        $this->log->info('POST /users/create', $data);
        
        $errors = [];
        $result = [];


        if( !empty($data[0]) ){
            foreach( $data as $items ){

                $tmpError = self::checkInputData($items);

                if( empty($tmpError) ){
                    $newUser = User::create([
                        'name' => $items['name'],
                        'email' => $items['email'],
                        'dateofbirth' => $items['dateofbirth'],
                        'isactive' => $items['isactive'], 
                        'createdat' => Carbon::now(),
                        'updatedat' => Carbon::now()
                    ]);

                    $newPhone = Phone::create([
                        'user_id' => $newUser->id,
                        'phonenumber' => $items['default_phone'],
                        'isdefault' => true,
                        'createdat' => Carbon::now(),
                        'updatedat' => Carbon::now()
                    ]);

                    $result[] = [
                        'id' => $newUser->id,
                        'name' => $newUser->name,
                        'email' => $newUser->email,
                        'dateofbirth' => $newUser->dateofbirth,
                        'default_phone' => $items['default_phone'],
                        'isactive' => $newUser->isactive, 
                        'createdat' => $newUser->createdat,
                        'updatedat' => $newUser->updatedat
                    ];
                } else {
                    $errors[] = $tmpError;
                }

            } /// END Of foreach


            return $response->withJson([
                'success' => true,
                '_metadata' => Config::metadata($request->getUri()->getPath()),
                'result' => $result,
                'errors' => $errors
            ], 200);

        } else {

            // Error occured
            return $response->withJson([
                'success' => false,
                'errors' => [
                    "message" => "Request error: Input data is empty!"
                ]
            ], 400);

        }
    } /// END Of create fn



    private function checkInputData( $items = [], $checkExistsEmail = true ){
        $tmpError = [];
        /// when create user
        if( $checkExistsEmail AND User::where(['email' => $items['email']])->first() ){
            $tmpError[] = [
                "data" => $items,
                "message" => "Duplication error: The email address is exists!"
            ];
        }

        /// when create user
        if( $checkExistsEmail AND 
            (
                empty($items['email']) OR empty($items['name']) OR empty($items['dateofbirth']) 
                OR empty($items['default_phone']) OR empty($items['isactive'])
            ) 
        ){

            $tmpError[] = [
                "data" => $items,
                "message" => "Data error: required field(s) is empty!"
            ];

        }


        if( !empty($items['default_phone']) 
            AND false === Config::checkPhoneNumber($items['default_phone']) ){

            $tmpError[] = [
                "data" => $items,
                "message" => "Data error: phone number not valid!"
            ];
        }

        return $tmpError;
    }
    


} /// END Of UserController Class