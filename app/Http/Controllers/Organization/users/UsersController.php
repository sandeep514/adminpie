<?php

namespace App\Http\Controllers\Organization\users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Hash;
use Session;
use App\Model\Group\GroupUsers as org_user;
use App\Model\Organization\UserRoleMapping;
use App\Model\Organization\UsersRole;
use App\Model\Group\GroupUserMeta;
use App\Model\Organization\Client;
use App\Model\Organization\User;
use App\Model\Organization\OrganizationSetting;
use App\Model\Organization\forms as Forms;
use App\Model\Organization\FormBuilder;


class UsersController extends Controller
{
    /**
     * undocumented function
     *
     * @return create user page
     * @author sandip
     **/
    public function createUser(){
    	$form_slug = null;
    	$additionalForm = OrganizationSetting::where(['key'=>'user_profile_form'])->first();
        if($additionalForm != null){
            $additionalForm = Forms::find($additionalForm->value);
            if($additionalForm != null){
                $form_slug = $additionalForm->form_slug;
            }
        }
      	return view('organization.user.add',['form_slug'=>$form_slug]);
    }

    /**
     * undocumented function
     *
     * @return store a new user
     * @author sandip
     **/
        public function store(Request $request)
        {
            $model = org_user::where(['email'=>$request->email])->first();

            if(count($model) > 0){
                Session::flash('error','Email already exist');
                return back();
            }else{
                $rules = ['name' => 'required', 'email' =>  'required', 'password' => 'required|min:8', 'confirm_password'=>'required|same:password'];
                $this->validate($request,$rules);
                $user = new org_user;
                $user->fill($request->only('name','email'));
                $user->password = Hash::make($request->password);
                $user->app_password = $request->password;
                $user->status = 1;
                $user->deleted_at = 0;
                $user->save();
                $user_id = $user->id;
                $org_user =  new User();
                $org_user->user_id =  $user_id;
                $org_user->save();

                $meta_data = $request->except('name','email','password','confirm-password','_token','confirm_password','role_id');
                if(!empty($meta_data) && !empty($user_id)){
                    update_user_metas($meta_data, $user_id, true);
                }
                if($request->has('role_id')){
                    foreach($request->role_id as $key => $role){
                        $roleMapping = new UserRoleMapping;
                        $roleMapping->user_id = $user_id;
                        $roleMapping->role_id = (int) $role;
                        $roleMapping->status = 1;
                        $roleMapping->save();
                    }
                }else{
                    $roleMapping = new UserRoleMapping;
                    $roleMapping->user_id = $user_id;
                    $roleMapping->role_id = 8;
                    $roleMapping->status = 1;
                    $roleMapping->save();
                }
            }
            Session::flash('success' , 'User Created Successfully');
            return redirect()->route('list.user');
        }
    /**
     * undocumented function
     *
     * @return list users
     * @author sandip
     **/
    public function index(Request $request)
    {
        
    	$datalist = [];
        if($request->has('items')){
            $perPage = $request->items;
            if($perPage == 'all'){
                $perPage = 999999999999999;
            }
        }else{
            $perPage = get_items_per_page();
        }

        $sortedBy = @$request->orderby;
        $order = $request->order;
        if($request->orderby == null || $request->orderby == ''){
          $sortedBy = 'created_at';
          $order = 'desc';
        }
          if($request->has('search')){
              if($sortedBy != ''){
                  $model = org_user::where('deleted_at',0)->where('id','!=',1)->where('id','!=',Auth::guard('org')->user()->id)->where('name','like','%'.$request->search.'%')->with(['user_role_rel','userType'])->has('organization_user')->orderBy($sortedBy,$order)->paginate($perPage);
              }else{
                  $model = org_user::where('deleted_at',0)->where('id','!=',1)->where('id','!=',Auth::guard('org')->user()->id)->where('name','like','%'.$request->search.'%')->with(['user_role_rel','userType'])->has('organization_user')->paginate($perPage);
              }
          }else{
              if($sortedBy != ''){
                  $model = org_user::where('deleted_at',0)->where('id','!=',1)->where('id' , '!=' , Auth::guard('org')->user()->id)->orderBy($sortedBy,$order)->with(['user_role_rel'=>function($query){
                      $query->with('roles');
                  },'userType'])->has('organization_user')->paginate($perPage);
              }else{
              }
          }
          $datalist =  [
                          'datalist'=>$model,
                          'showColumns' => ['name'=>'Title','email'=>'Email','status' => 'Status'],
                          'actions' => [
                                        'view'   => ['title'=>'View','route'=>'user.view','class'=>'view'],
                                        'edit'   => ['title'=>'Edit','route'=>'user.details','class'=>'edit'],
                                        'delete' => ['title'=>'Delete','route'=>'delete.user'],
                                        'change_pass'  => ['title'=>'Change Password','route' => 'changepass.user'],
                                        'status_option'  =>  ['title'=>'status option','class'=>'status_option' ,'route' =>'change.user.status']
                                       ]
                      ];
        return view('organization.user.list',$datalist);

    }
    /**
     * undocumented function
     *  
     * @return the userdetails
     * @author sandip
     **/
    public function userView($id = null)
    {
        if($id == null){
            $id = Auth::guard('org')->user()->id;
        }else{
            $model = org_user::where('id',$id)->first()->toArray();
            $id = $model['id'];
        }


        $userDetails = org_user::select(['id','name','email'])->with(['metas','applicant_rel','client_rel','user_role_rel'])->find($id);
        $userMeta = get_user_meta($id,null,true);

        $role_data = $userDetails->metas->where('key','role')->first();
        $userDetails['role'] = $role_data->value;
        $additionalForm = OrganizationSetting::where(['key'=>'user_profile_form'])->first();
        if($additionalForm != null){

            $additionalForm = Forms::with(['section'])->find($additionalForm->value);
            if($additionalForm != null){
                $form_slug = $additionalForm->form_slug;
                    $form_id = $additionalForm['id'];
                    $section_id = $additionalForm->section[0]['id'];
                $fields = FormBuilder::where(['form_id' => $form_id,'section_id'=> $section_id])->get();

                foreach ($fields as $key => $field) {
                    $field_key = $field->field_slug;
                    $user_meta = get_user_meta($id,$field_key,true);
                    $field_title = $field->field_title;
                    $field_type = $field->field_type;

                    if(!empty($user_meta)){
                        if($field_type == 'radio'){
                            $field_options = field_options($field->field_slug , $id= null);
                            $userDetails[$field_title] = $field_options[$user_meta];
                        } else {
                            $userDetails[$field_title] = $user_meta;
                        }
                        
                    }
                }
            }else{
                $userDetails = $userDetails; 
            }

        }



        return view('organization.user.view',['model' => $userDetails]);
        // return view('organization.user.preview',['model' => $userDetails , 'user_log' => $user_log]);
    }


    /**
     * undocumented function
     *
     * @return user data to edit
     * @author sandip
     **/
      public function userDetails($id = null){   
	        if($id == null){
	          $id = get_user_id();
	        }
	        $form_slug = null;
	       	$additionalForm = OrganizationSetting::where(['key'=>'user_profile_form'])->first();
	        if($additionalForm != null){
	            $additionalForm = Forms::find($additionalForm->value);
	            if($additionalForm != null){
	                $form_slug = $additionalForm->form_slug;
	            }
	        }
          $model = org_user::with(['user_role_rel','metas'])->find($id);
          return view('organization.user.edit',['model' => $model,'form_slug'=>$form_slug]);
      }



      /**
       * undocumented function
       *
       * @return view of the change password
       * @author sandip
       **/
      public function changePass()
      {
          return view('organization.user.change-password');
      }


      /**
       * undocumented function
       *
       * @return update password
       * @author sandip
       **/
        public function changePassword(Request $request)
        { 
            $model = org_user::where('id',$request->user_id)->first();
            $check = Hash::check( Hash::make($request->password) , $model->password);

            $validate = [
                            'new_password'      => 'required|min:6',
                            'confirm_password'  => 'required|same:new_password|min:6'
                        ];
          $this->validate($request , $validate);

          $model = org_user::where('id',$request->user_id)->update(['password' => Hash::make($request->new_password) , 'app_password' => $request->new_password]);
          if($model){
              echo "<script type='text/javascript'>Materialize.toast('password Change Successfully', 4000)</script>";
              return back();
          }
        }
        /**
         * undocumented function
         *
         * @return void
         * @author sandip
         **/
          public function updateUserDetails(Request $request, $id)
          {
              $model = org_user::find($id);
              $model->name = $request->name;
              $model->email = $request->email;
              // $model->user_type = 'employee';
              $model->save();
              $notToDeleteIds = [];
              $currentStoredRoles = UserRoleMapping::where(['user_id'=>$id])->pluck('role_id')->toArray();
                if($request->has('role_id') != ''){
                    $newSelectedRoles = array_map('intval',$request->role_id);
                    $this->deleteFromRelatedTables($currentStoredRoles, $newSelectedRoles, $id);
                    
                    foreach($request->role_id as $key => $role){
                        $model = UsersRole::find($role);

                        if($model->slug == 'employee'){
                            $usersMeta = new GroupUserMeta;
                            $usersMeta->key = 'joining_date';
                            $usersMeta->value = date('Y-m-d');
                            $usersMeta->save();
                        }
                        if($model->slug == 'client'){
                            $this->createClient($id, $request->name);
                        }
                            $mappingModel = UserRoleMapping::firstOrNew(['user_id'=>$id,'role_id'=>$role]);
                            $mappingModel->user_id = $id;
                            $mappingModel->role_id = $role;
                            $mappingModel->status = 1;
                            $mappingModel->save();
                            $notToDeleteIds[] = $mappingModel->id;
                      }
                      foreach($request->role_id as $key => $role){
                          $model = UsersRole::find($role);

                          if($model->slug == 'employee'){
                              $usersMeta = new GroupUserMeta;
                              $usersMeta->key = 'joining_date';
                              $usersMeta->value = date('Y-m-d');
                              $usersMeta->save();
                          }
                          if($model->slug == 'client'){
                            $this->createClient($id, $request->name);
                          }
                          $mappingModel = UserRoleMapping::firstOrNew(['user_id'=>$id,'role_id'=>$role]);
                          $mappingModel->user_id = $id;
                          $mappingModel->role_id = $role;
                          $mappingModel->status = 1;
                          $mappingModel->save();
                          $notToDeleteIds[] = $mappingModel->id;
                      }
                }

              $meta_data = $request->except('name','email','password','confirm-password','_token','confirm_password','role_id');
              if(!empty($meta_data) && !empty($id)){
                update_user_metas($meta_data, $id, true);
              }
              UserRoleMapping::whereNotIn('id',$notToDeleteIds)->where('user_id',$id)->delete();
              return back();
          }
        protected function deleteFromRelatedTables($currentStoredRoles, $newSelectedRoles, $userId){
            $roleToRemove = array_diff($currentStoredRoles, $newSelectedRoles);
            if(!empty($roleToRemove)){
                foreach($roleToRemove as $key => $role){
                    $role = UsersRole::find($role);
                    if($role->slug == 'employee'){
                        Employee::where(['user_id'=>$userId])->delete();
                    }
                    if($role->slug == 'client'){
                        Client::where(['user_id'=>$userId])->delete();
                    }
                }
            }
        }


        /**
         * undocumented function
         *
         * @return void
         * @author 
         **/
        protected function createClient($userid, $name){
            $client = Client::where(['user_id'=>$userid])->first();
            if($client == null){
                $client = new Client;
                $client->name = $name;
                $client->user_id = $userid;
                $client->save();
            }
        }
        /**
         * undocumented function
         *
         * @return void
         * @author 
         **/
        public function changeStatus($id)
        {
            $model = org_user::where('id',$id)->first();
                if($model['status'] == 0){
                    org_user::where('id',$id)->update(['status' => 1]);
                }else{
                    org_user::where('id',$id)->update(['status' => 0]);
                }
            return back();
        }

        /**
         * undocumented function
         *
         * @return void
         * @author 
         **/
        public function deleteUser ($id)
        {
            $model = org_user::where('id',$id)->delete();
            return back();
        }



}
