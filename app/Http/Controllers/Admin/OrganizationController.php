<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Artisan;
use App\Model\Admin\GlobalOrganization as ORG;
use Session;
use Hash;
use App\Model\Organization\User;
use App\Model\Organization\UsersType;
use DB;
// use App\Repositories\Organization\OrganizationRepositoryContract;


class OrganizationController extends Controller
{

	public function listOrg()
	{
		$org_list = ORG::select(['id','name'])->get();
        $plugins = [
                'js' => ['custom'=>['list']]
        ];
		return View::make('admin.organization.list', ['org_list'=>$org_list,'plugins'=>$plugins]);
	}
	public function index()
	{

      
	}
	public function delete($id)
	{
		try{
            DB::beginTransaction();
      		$model = ORG::findOrFail($id);
			$model->delete();

         $data =   DB::select("select CONCAT('DROP TABLE `',t.table_schema,'`.`',t.table_name,'`;') AS dropTable
          FROM information_schema.tables t
          WHERE t.table_schema = '".env('DB_DATABASE', 'forge')."'
          AND t.table_name LIKE 'ocrm_".$id."%' 
          ORDER BY t.table_name");
        foreach ($data as $key => $value) {
             DB::select($value->dropTable);
          }
                      DB::commit();

        Session::flash('success','Successfully deleted!');
          return redirect()->route('list.organizations');
        }catch(\Exception $e){
          // throw $e;
            DB::rollback();
        	return ['status'=>'error', 'message'=>'Somthing goes wrong Try again.'];
        }

	}

	public function create(){
         
         // Artisan::call('make:migration:schema',[
         //                        '--model'=>false,
         //                        'name'=>'create_global_widgets',
         //                        '--schema'=>'title:string, description:text:nullable, module_id:integer:nullable, model:string:nullable, slug:string, status:integer:default(1)'
         //                    ]);


        // Artisan::call('make:migration:schema',[
        //                         '--model'=>false,
        //                         'name'=>'create_global_forms',
        //                         '--schema'=>'form_title:string, form_slug:string, form_description:text:nullable'
        //                     ]);

        // Artisan::call('make:migration:schema',[
        //                         '--model'=>false,
        //                         'name'=>'create_global_form_meta',
        //                         '--schema'=>'form_id:integer, key:string, value:text'
        //                     ]);

        // Artisan::call('make:migration:schema',[
        //                         '--model'=>false,
        //                         'name'=>'create_global_form_sections',
        //                         '--schema'=>'form_id:integer, section_name:string, section_slug:string, section_description:text:nullable'
        //                     ]);

        // Artisan::call('make:migration:schema',[
        //                         '--model'=>false,
        //                         'name'=>'create_global_form_section_meta',
        //                         '--schema'=>'section_id:integer, key:string, value:text'
        //                     ]);

        // Artisan::call('make:migration:schema',[
        //                         '--model'=>false,
        //                         'name'=>'create_global_form_fields',
        //                         '--schema'=>'field_slug:string, form_id:integer, section_id:integer, field_title:string, type:string, field_description:text:nullable'
        //                     ]);

        // Artisan::call('make:migration:schema',[
        //                         '--model'=>false,
        //                         'name'=>'create_global_form_field_  meta',
        //                         '--schema'=>'field_id:integer, key:string, value:text'
        //                     ]);
          // Artisan::call('migrate');
	
		return view('admin.organization.create');
	}
    public function edit(){
        return view('admin.organization.edit');
    }
	public function save(Request $request)
	{
		$org_count = ORG::where('name',$request->name)->count();
		if($org_count>0){
			 Session::flash('error','Organization name already Exist!');
			 return redirect()->route('create.organization');
		}
		$org = new ORG();
		$org->fill($request->except('description'));
		$org->save();
		$org->id;
		Session::put('organization_id',$org->id);
        
        //Widget Permisson
        Artisan::call('make:migration:schema',[
                                '--model'=>false,
                                'name'=>'create_'.$org->id.'_widget_permissons',
                                '--schema'=>'role_id:integer, widget_id:integer:nullable, permisson:string:nullable'
                            ]);
	// USERS
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_users',
                                '--schema'=>'name:string, email:string:unique, password:string, api_token:char(60), remember_token:string, user_type:string, role_id:integer:nullable, status:integer:default(0)'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_users_types',
                                '--schema'=>'type:string, status:integer:default(0)'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_users_roles',
                                '--schema'=>'name:string, description:text:nullable, status:integer:default(0)'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_role_permissons',
                                '--schema'=>'role_id:integer, module_id:integer, read:string:nullable, write:string:nullable, delete:string:nullable, other:string:nullable, status:integer:default(1)'
                            ]);

		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_users_metas',
                                '--schema'=>'user_id:integer , key:string, value:text, type:string:nullable'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_users_notes',
                                '--schema'=>'user_id:integer, title:string, description:text, priority:string:default("low")'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_users_todos',
                                '--schema'=>'user_id:integer, title:string, description:text, start:dateTime:nullable, end:dateTime:nullable, priority:string:default("low"), status:integer:default(0)'
                            ]);
	Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_categories',
                                '--schema'=>'name:text, description:text:nullable, type:string, status:integer:default(1)'
                            ]);

	Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_leaves',
                                '--schema'=>'name:text, employee_id:integer, leave_category_id:integer, from:date , to:date,  description:text:nullable, total_days:integer:nullable, status:integer'
                            ]);

		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_designations',
                                '--schema'=>'name:string, status:integer:default(1)'
                            ]);
//Shifts
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_shifts',
                                '--schema'=>'name:string, from:string, to:string: status:integer:default(1)'
                            ]);

	
	//Pages 
			Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_pages',
                                '--schema'=>'title:string:nullable, content:text:nullable, tags:text:nullable, categories:string:nullable, post_type:string:nullable, attachments:string:nullable, version:string:nullable, revision:string:nullable, created_by:string:nullable, post_status:string:nullable, status:integer:default(1)'
                            ]);
				Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_page_metas',
                                '--schema'=>'page_id:integer, key:string, value:text'
                            ]);
	//EMPLOYEE
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_employees',
                                '--schema'=>'user_id:integer, employee_id:integer, designation:text:nullable, department:string:nullable, marital_status:string:nullable, experience:string:nullable, blood_group:string:nullable, joining_date:dateTime:nullable, disability_percentage:string:nullable, status:integer:default(0)'
                            ]);
	//Department
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_departments',
                                '--schema'=>'name:string, description:text:nullable, status:integer:default(1)'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_employee_meta',
                                '--schema'=>'employee_id:integer , key:string, value:text'
                            ]);
	//STUDENT
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_students',
                                '--schema'=>'user_id:integer, student_id:integer:nullable, dob:string:nullable,  qualification:string:nullable, college_university:string:nullable, joining_date:dateTime:nullable, status:integer:default(0)'
                            ]);

		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_student_meta',
                                '--schema'=>'student_id:integer, key:string, value:text'
                            ]);

	//TEAM MIGRATION
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_teams',
                                '--schema'=>'title:string, description:text:nullable, member_ids:text:nullable'
                            ]);

		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_support_tickets',
                                '--schema'=>'user_id:integer , title:string, description:text, type:string:nullable, assign_to:string:nullable, end:dateTime:nullable, priority:string:default("low"), status:integer:default(0)'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_support_ticket_metas',
                                '--schema'=>'user_id:integer ,key:string, value:text'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_contacts',
                                '--schema'=>'name:string , email:string:unique'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_contact_metas',
                                '--schema'=>'contact_id:integer, key:string , value:text'
         
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_clients',
                                '--schema'=>'name:string, company_name:string:nullable, address:string:nullable, country:string:nullable, state:string:nullable, city:string:nullable, email:string:nullable, phone:string:nullable, additional_info:text:nullable'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_client_metas',
                                '--schema'=>'client_id:integer, key:string , value:text, type:string'
                            ]);

		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_attendances',
                                '--schema'=>'employee_id:string, user_id:integer:nullable, date:string, month:string,year:string, day:string:nullable, month_week_no:integer:nullable, in_time:string:nullable, out_time:string:nullable, total_hour:string:nullable, actual_hour:string:nullable, over_time:string:nullable, due_time:string:nullable, import_data:string:nullable, ip_address:string:nullable, attendance_status:string:nullable, submited_by:string:nullable, check_in:string:nullable, check_out:string:nullable,check_for_checkin_checkout:string:null,  deleted_at:timestamp:nullable'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_holidays',
                                '--schema'=>'title:string:nullable, description:text:nullable , date_of_holiday:date, status:integer:default(1)'
                            ]);

		
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_employee_leaves',
                                '--schema'=>'employee_id:string, reason_of_leave:string:nullable, description:text:nullable, total_day_of_leave:integer:nullable, from:date, to:date, approved_status:integer:default(0), approved_by:string:nullable'
                            ]);

		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_employee_short_leave',
                                '--schema'=>'employee_id:string, reason_of_leave:string:nullable, description:text:nullable, from:string, to:string,  approved_status:integer:default(0), approved_by:string:nullable'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_projects',
                                '--schema'=>'name:text, description:text:nullable, tags:text:nullable, category:string:nullable'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_project_categories',
                                '--schema'=>'name:text, description:text:nullable, status:integer:default(1)'
                            ]);
//_project_metas
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_project_metas',
                                '--schema'=>'project_id:integer, key:string , value:text, type:string'
                            ]);
//_project_tasks
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_project_tasks',
                                '--schema'=>'project_id:integer, title:string, description:text:nullable, assign_to:string:nullable, priority:string:default("low"), end_date:dateTime:nullable, status:integer:default(0)'
                            ]);
//ORGANIZATION TODOS
        Artisan::call('make:migration:schema',[
                                '--model'=>false,
                                'name'=>'create_'.$org->id.'_project_todos',
                                '--schema'=>'project_id:integer, title:string, description:text:nullable, start:dateTime:nullable, end:dateTime:nullable, priority:string:default("low"), status:integer:default(1)'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_project_notes',
                                '--schema'=>'project_id:integer, title:string, description:text:nullable, status:integer:default(1)'
                            ]);
//ORGANIZATION METAS
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_organization_settings',
                                '--schema'=>'key:string , value:text'
                            ]);
//ORGANIZATION METAS
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_organization_departments',
                                '--schema'=>'name:string , description:text:nullable'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_services',
                                '--schema'=>'name:string, description:text:nullable,  cost:string:nullable, status:integer:default(0)'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_service_metas',
                                '--schema'=>'service_id:integer , key:string, value:text'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_orders',
                                '--schema'=>'name:string , description:text:nullable, cost:string:nullable, quantity:string:nullable, status:integer:default(0)'
                            ]);
		Artisan::call('make:migration:schema',[
								'--model'=>false,
                                'name'=>'create_'.$org->id.'_order_metas',
                                '--schema'=>'order_id:integer , key:string, value:text'
                            ]);
		Artisan::call('migrate');

		$org_usr = new User();
		$org_usr->fill($request->all()); 
		$org_usr->user_type = json_encode([1]);
		$org_usr->password = Hash::make($request->password);
		$org_usr->save();  

		$userTypes = [
        	['type'=>'Admin','status'=>1],
        	['type'=>'Employee','status'=>1],
        	['type'=>'Customer','status'=>1],
        	['type'=>'Student','status'=>1],
        ];

        UsersType::insert($userTypes);

		echo 'Organization create successfully';
	}

}

