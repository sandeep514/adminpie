<?php

namespace App\Model\Organization;

use Illuminate\Database\Eloquent\Model;
use Session;

class Document extends Model
{
	public static $breadCrumbColumn = 'id';
	protected $fillable = ['title','description','layout','template','status','order'];
    public function __construct(){
    	if(!empty(Session::get('organization_id'))){
	    	$this->table = Session::get('organization_id').'_document';
	    }
    }
    public function DocumentLayout()
    {
    	return $this->belongsTo('App\Model\Organization\DocumentLayout','layout','id');
    }
    public function DocumentTemplate()
    {
    	return $this->belongsTo('App\Model\Organization\DocumentTemplate','template','id');
    }
}
