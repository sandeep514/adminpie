<?php

namespace App\Model\Organization;

use Illuminate\Database\Eloquent\Model;
use Session;
use Auth;
class UsersMeta extends Model
{
	public static $breadCrumbColumn = 'id';
   public function __construct()
   {	
	   	if(!empty(Session::get('organization_id')))
	   	{
	       $this->table = Session::get('organization_id').'_users_metas';
	   	}
   }
   
   protected $fillable = ['user_id', 'key', 'value', 'type'];

   // public function users()
   // {
   //  return $this->belongsTo('')
   // }

public function get_user_meta(){
  return $this->hasMany('App\Model\Organization\UsersMeta' ,'user_id' ,'user_id');
}
   public static function saveDataListSetting($url,$params){
	   if(Auth::guard('org')->check()){
	   		$user = Auth::guard('org')->user()->id;
	   		$model = self::where(['key'=>$url,'user_id'=>$user])->first();
	   		if($model == null){
	   			$model = new self;
	   			$model->key = $url;
	   			$model->value = (@$params[1])?$params[1]:'';
	   			$model->user_id = $user;
	   			$model->save();
	   		}else{
	   			$model->key = $url;
	   			$model->value = (@$params[1])?$params[1]:'';
	   			$model->user_id = $user;
	   			$model->save();
	   		}
		   	return $params;	
	   }
   }

   public static function getDataListSettings($url){
   	if(Auth::guard('admin')->check()){
   		$user = Auth::guard('admin')->user()->id;
   	}else{
   		$user = Auth::guard('org')->user()->id;
   	}
   		$model = self::where(['key'=>$url,'user_id'=>$user])->first();
   		if($model != null){
   			return $model->value;
   		}else{
   			return null;
   		}
   }

   public static function getUserMeta($metaKey){
		$model = self::where(['user_id'=>Auth::guard('org')->user()->id,'key'=>$metaKey])->first();
		return @$model->value;
   }

   public function user(){
   		return $this->belongsTo('App\Model\Organization\User','user_id','id');
   }

   public function scopeDepartment($query){
        return $this->belongsTo('App\Model\Organization\Department','value','id');
    }

    public function scopeDesignation($query){
        return $this->belongsTo('App\Model\Organization\Designation','value','id');
    }

    public function payscale(){
      return $this->belongsTo('App\Model\Organization\Payscale','value','id');
    }
    // public function payscale

}
