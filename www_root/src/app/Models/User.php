<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Model {

	const CREATED_AT = "createdat";
    const UPDATED_AT = "updatedat";
    const DELETED_AT = "deletedat";

    use SoftDeletes; 

    protected $table = 'users';
    
    // for mass creation
    protected $fillable = [
    	'name', 
    	'email', 
    	'dateofbirth', 
    	'isactive', 
    	'createdat', 
    	'updatedat', 
    	'deletedat'
    ]; 

    // the attributes that should be mutated to dates
    protected $dates = ['createdat', 'updatedat', 'deletedat']; 
    
    
    
} /// END Of User Model