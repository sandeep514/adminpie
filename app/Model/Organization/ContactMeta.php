<?php

namespace App\Model\Organization;

use Illuminate\Database\Eloquent\Model;
use Session;
class ContactMeta extends Model
{
	public static $breadCrumbColumn = 'id';
public function __construct()
   {	
	   	if(!empty(Session::get('organization_id')))
	   	{
	       $this->table = Session::get('organization_id').'_contact_metas';
	   	}
   }
   protected $fillable = [ 'contact_id', 'key', 'value'];
}
