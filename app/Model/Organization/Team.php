<?php

namespace App\Model\Organization;

use Illuminate\Database\Eloquent\Model;
use Session;
use App\Model\Organization\User;

class Team extends Model
{
   public static $breadCrumbColumn = 'id';
   public function __construct()
   {	
	   	if(!empty(Session::get('organization_id')))
	   	{
	       $this->table = Session::get('organization_id').'_teams';
	   	}
   }
   protected $fillable = ['title', 'description', 'member_ids'];

   public static function teamsList(){

   		return self::orderBy('id','asc')->pluck('title','id');
   }
   public static function getTeamById($data=null){
   // dd($data);
      $model = self::where('id',$data)->get();
      return $model;
   }
   public function getTeamMembers()
   {
      return $this->hasMany(App\Model\Organization\User , 'member_ids','id');
   }
}