<?php

namespace App\Model\Organization;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Session;
class forms extends Model
{
    protected $fillable = ['form_title','form_slug','form_description'];
    protected $table	= 'global_forms';
    
    public function __construct(){
    	try{
    		if(Auth::guard('org')->check()){
		    	if(!empty(Session::get('organization_id'))){
		    		$this->table = Session::get('organization_id').'_forms';
		    	}
		    }
    	}catch(\Exception $e){
    		
    	}
    }
    

    public function section(){
    	return $this->hasMany('App\Model\Organization\section','form_id','id');
    }

    public function formsMeta(){
    	return $this->hasMany('App\Model\Organization\FormsMeta','id','form_id');
    }

    public function setTable($table){

        $this->table = $table;
    }
}
