<?php

namespace App\Model\Organization;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Session;

class SectionMeta extends Model
{
	protected $table = 'global_form_section_meta';
	public function __construct(){
      try{
        if(Auth::guard('org')->check()){
          if(!empty(Session::get('organization_id'))){
            $this->table = Session::get('organization_id').'_form_section_meta';
          }
        }
      }catch(\Exception $e){
        
      }
    }
    
}