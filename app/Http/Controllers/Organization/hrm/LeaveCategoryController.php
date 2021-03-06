<?php

namespace App\Http\Controllers\Organization\hrm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Organization\Designation As DES;
use App\Model\Organization\User;
use App\Model\Organization\CategoryMeta as CM;
use App\Repositories\Category\CategoryRepositoryContract;
use App\Model\Organization\Leave as LV; 
use App\Model\Organization\Category as CAT;
use App\Model\Organization\UsersRole as Role;
use Auth;
use Session;

  
/**
 *  @last_modified 2017-06-11 Day sunday
 *  modififed by Paljinder Singh
 */

class LeaveCategoryController extends Controller
{
    protected $catRepo;
    /**
     * [__construct add dependncy of category]
     * @param CategoryRepositoryContract $CategoryRepositoryContract [description]
     */
    public function __construct(CategoryRepositoryContract $CategoryRepositoryContract)
    {
      $this->catRepo = $CategoryRepositoryContract;
    }
    /**
     * [index listing ]
     * @return [html array] [category list _+ list data]
     */
    public function index(Request $request){
        $data =  $this->catRepo->list_category("leave", $request);
      	return view('organization.leave_category.list_category',$data );
     }
     /**
      * [save category]
      * @param  Request $request [all form data]
      * @return [type]           [back category list]
      */
    public function save(Request $request)
    {
       // dd($request->all());
      // $request['name'] = $request['addleavecat'];
      $tbl = Session::get('organization_id');
      $valid_fields = [
                          'name'          => 'required|unique:'.$tbl.'_categories',
                          
                      ];
      $this->validate($request , $valid_fields);
        $request->request->add(['type' => 'leave']);
        $this->catRepo->create($request);
        return redirect()->route('leave.categories');
     }
     /**
      * [manage_status change enable disable]
      * @param  Request $request [request data]
      * @return [type]           [description]
      */
    public function manage_status(Request $request){
          $this->catRepo->manage_status($request);
     }
     /**
      * [categoryMeta description]
      * @param  Request $request [description]
      * @param  [type]  $id      [description]
      * @return [type]           [description]
      */
    public function categoryMeta(Request $request , $cat_id=null)
     {  
        if(Auth::guard('org')->check()){
          $id = Auth::guard('org')->user()['id'];
        }else{
          $id = Auth::guard('admin')->user()['id'];
        }
      if($request->isMethod('post')){
            $this->catRepo->category_meta_save($request);  
            return back();      
          }
        $select =[];
        $data['cat'] = $this->catRepo->category_data_by_id($cat_id);
        $cm = CM::where('category_id',$cat_id)->get();
        $data['data'] = $cm->pluck('value','key')->toArray();
        if(!empty($data['data']['include_designation']))
        {
          $include_designation = array_map('intval',json_decode($data['data']['include_designation']));
          $user_data = $this->user_by_designation($include_designation);
         $data['user_include'] = $user_data['user_include'];  
         $data['user_exclude'] = $user_data['user_exclude'];  
        }else{
         $data['userData'] = $data['user_include'] = $data['user_exclude']= User::where('id','!=',$cat_id)->pluck('name','id');
        }
        $data['id'] =$cat_id;
        $data['designationData'] = DES::where('status',1)->pluck('name','id');
        $data['roles'] = Role::where('status',1)->pluck('name','id');
        return view('organization.leave_category.leave_rule',['data'=>$data ,'select'=>$select]);
     }

    public function delete($id){
        CAT::where('id',$id)->delete();
        return back();
    }

    public function editLeaveCat(Request $request){
      unset($request['_token']);
      CAT::where('id' , $request->id)->update(['name'=>$request['name'] , 'description'=>$request['description']]);
      $newMeta = $request->except('id','name','description');
        CM::where('category_id', $request->id)->delete();
        foreach($newMeta as $key => $value){
          $data = new CM;
          $data->category_id = $request->id;
          $data->key = $key;
            if(is_array($value)){
              $data->value = json_encode($value);
            }elseif($value == ""){
              $data->value = "";
            }else{
              $data->value = $value;
            }
            $data->save();
        }
        return back();
    }

    protected function user_by_designation($designation_id){
      $user_exclude =  User::with('metas')->whereHas('metas', function($query) use($designation_id) {
          $query->where('key','designation')->whereIn('value',$designation_id);
          })->where('user_type','employee')->pluck('name','id');
      $user_include =  User::with('metas')->whereHas('metas', function($query) use($designation_id) {
          $query->where('key','designation')->whereNotIn('value',$designation_id);
          })->where('user_type','employee')->pluck('name','id');
      return compact('user_exclude','user_include');
    }

    public function get_user_by_designation(Request $request){
        $designation_id = $request['des_id'];
        
        $user_exclude =  User::with('metas')->whereHas('metas', function($query) use($designation_id) {
          $query->where('key','designation')->whereIn('value',$designation_id);
          })->where('user_type','employee')->pluck('name','id');
        $user_include =  User::with('metas')->whereHas('metas', function($query) use($designation_id) {
          $query->where('key','designation')->whereNotIn('value',$designation_id);
          })->where('user_type','employee')->pluck('name','id');

         return view('organization.leave_category.user_drop_down',compact('user_exclude','user_include'));
    }

}

