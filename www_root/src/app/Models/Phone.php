<?php
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phone extends Model {

	const CREATED_AT = "createdat";
    const UPDATED_AT = "updatedat";
    const DELETED_AT = "deletedat";

    use SoftDeletes;

    protected $primaryKey = null;
	public $incrementing = false;

    protected $table = 'user_phone';

    protected $fillable = [
    	'user_id', 
    	'phonenumber', 
    	'isdefault', 
    	'createdat', 
    	'updatedat',
    	'deletedat'
    ];

    // the attributes that should be mutated to dates
    protected $dates = ['createdat', 'updatedat', 'deletedat']; 

    
} /// END Of Phone Model