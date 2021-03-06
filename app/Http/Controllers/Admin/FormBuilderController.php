<?php

namespace App\Http\Controllers\Admin;

use Artisan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Admin\FormBuilder;
use App\Model\Admin\forms as forms;
use App\Model\Admin\FormsMeta;
use App\Model\Admin\section as sec;
use App\Model\Admin\SectionMeta as SM;
use App\Model\Admin\FieldMeta as FM;
use App\Model\Organization\Collaborator;
use Session;
use Auth;

class FormBuilderController extends Controller
{
    protected $valid_fields = [
                            'form_title' => 'required',
                            'form_slug' => 'required|regex:/^[a-z0-9-_]+$/|min:4|max:300'
                        ];
    protected $valid_sections = [
                            'section_name' => 'required',
                            'section_slug' => 'required|unique:global_form_sections|regex:/^[a-z0-9-_]+$/|min:4|max:300'
                        ];
    // protected $valid_form_fields = [
    //                         'field_title' => 'required|min:4|max:300',
    //                         'field_type' => 'required',
    //                         'field_description' => 'required',
    //                         'order' => 'required',
    //                         'field_slug' => 'bail|required|unique:global_form_fields|regex:/^[a-z0-9]+$/|min:4|max:300'
    //                     ];

    public function index(){

        $plugins = ['js' => ['custom'=>['builder']]];
        return view('admin.formbuilder.create',['plugins' => $plugins,'type'=>'form' ]);
    }

    public function createForm(Request $request)
    {
        // dd($request->type);
        $created_by = get_user_id();
        $request['created_by'] = $created_by; 

        $modelName = $this->assignModel('forms');
        $this->validate($request, $this->valid_fields);
        $model = new $modelName;
        $model->fill($request->all());
        $model->save();
        // return redirect()->route('list.forms');
        Session::flash('success','Form created successfully');
        if(Auth::guard('admin')->check()){
            return redirect()->route('list.forms');
        }else{
            if($request['type'] == 'survey'){
                Session::flash('success','Survey created successfully');
                return redirect()->route('list.survey');
            }else{
                return redirect()->route('org.list.forms');
            }
        }
    }

    public function listForm(Request $request)
    {
        //pre filled settings
        /*$settingsArray = [
                'settingsec1f6' => 'Login',
                'show_reset_button' => 'no',
                'show_title' => 'yes',
                'show_description' => 'no',
                'field_variable' => 'slug',
                'show_tooltip' => 'no',
                'show_placeholder' => 'yes',
                'label_position' => 'top',
                'form_theme' => 'light'
        ];
        foreach(forms::get() as $key => $value){
            foreach($settingsArray as $k => $v){
                $formMeta = FormsMeta::firstOrNew(['form_id'=>$value->id,'key'=>$k]);
                $formMeta->form_id = $value->id;
                $formMeta->key = $k;
                $formMeta->value = $v;
                $formMeta->save();
            }
        }*/
        $modelName = $this->assignModel('forms');
        $sortedBy = @$request->orderby;
        if($request->has('items')){
          $perPage = $request->items;
          if($perPage == 'all'){
            $perPage = 999999999999999;
          }
        }else{
          $perPage = 10; //get_items_per_page();
        }
        if($request->has('search')){
            if($sortedBy != ''){
                $model = $modelName::where('type','form')->where('form_title','like','%'.$request->search.'%')->orderBy($sortedBy,$request->order)->with(['section'])->paginate($perPage);
            }else{
                $model = $modelName::where('type','form')->where('form_title','like','%'.$request->search.'%')->with(['section'])->paginate($perPage);
            }
        }else{
            if($sortedBy != ''){
                $model = $modelName::where('type','form')->orderBy($sortedBy,$request->order)->with(['section'])->paginate($perPage);
            }else{
                 $model = $modelName::where('type','form')->paginate($perPage);
            }
        }
        if(Auth::guard('admin')->check()){
            $previewRoute = 'form.preview';
            $deleteRoute = 'delete.form';
            $sectionRoute = 'list.sections';
            $settingsRoute = 'form.settings';
            $cloneRoute = 'form.clone';
        }else{
            $previewRoute = 'org.form.preview';
            $deleteRoute = 'org.delete.form';
            $sectionRoute = 'org.list.sections';
            $settingsRoute = 'org.form.settings';
            $cloneRoute = 'org.form.clone';
        }
        $datalist =  [
                        'datalist'=>$model,
                        'showColumns' => ['form_title'=>'Form Title','form_slug'=>'ID','created_at'=>'Created'],
                        'actions' => [
                                'view'=>['title'=>'View','route'=>$previewRoute],
                                'section'=>['title'=>'Edit','route'=>['route'=>$sectionRoute]],
                                'settings'=>['title'=>'Settings','route'=>$settingsRoute],
                                'clone'=>['title'=>'Clone','route'=>$cloneRoute],
                                'delete'=>['title'=>'Delete','class'=>'red','route'=>$deleteRoute],
                                ]
                    ];

        // $model = forms::with(['section'])->get();
        return view('admin.formbuilder.list',$datalist);
    }
    
    public function deleteForm($id)
    {
        $modelName = $this->assignModel('forms');
        $model = $modelName::where('id',$id)->delete();
        if(Auth::guard('admin')->check()){
            return redirect()->route('list.forms');
        }else{
            return redirect()->route('org.list.forms');
        }
    }

    public function updateFormDetails(Request $request)
    {
        // dd($request->all());

        $modelName = $this->assignModel('forms');
        $model = $modelName::where('id',$request->id)->update($request->except('_token','id'));
        return back();
    }
    // end form 

    protected function validateAddModule($request){
        $rules = [
            'section_name' => 'required'
        ];

        $this->validate($request,$rules);
    }

    //start section
    public function createSection(Request $request , $id){
        $this->validateAddModule($request);
        
        $modelName = $this->assignModel('section');
        $lastOrder = $modelName::where(['form_id' => $id])->orderBy('order','DESC')->first();
        if($lastOrder == null){
            $newOrder = 1;
        }else{
            $newOrder = $lastOrder->order+1 ;
        }
        $model = new $modelName;
        $model->form_id = $id;
        $model->section_name = $request->section_name;
        $model->order = $newOrder;
        $model->section_slug = $request->section_slug;
        $model->save();
        $new_sec_id = $model->id;
        
        $modelName = $this->assignModel('SectionMeta');
        $section = new $modelName;
        $section->section_id = $new_sec_id;
        $section->key = 'modelName';
        $section->value = $request->section_type;
        return back();
    } 

    protected function validateUpdateSection($request){
        $rules = [
            'section_id' => 'required',
            'section_name' => 'required',
            'section_slug' => 'required',
            'section_description' => 'required',
            'section_type' => 'required'
        ];

        $this->validate($request, $rules);
    }

    public function updateSection(Request $request, $form_id){

        $this->validateUpdateSection($request);
        $sectionId = $request->section_id;
        $modelName = $this->assignModel('section');
        $model = $modelName::firstOrNew(['form_id'=>$form_id,'id'=>$sectionId]);
        $model->form_id = $form_id;
        $model->section_name = $request->section_name;
        $model->section_slug = $request->section_slug;
        $model->section_description = $request->section_description;
        $model->save();
        $modelName = $this->assignModel('SectionMeta');
        $model = $modelName::firstOrNew(['section_id'=>$sectionId,'key'=>'section_type']);
        $model->section_id = $sectionId;
        $model->key = 'section_type';
        $model->value = $request->section_type;
        $model->save();
        return back();
    }


    protected function validateCreateField($request){
        $rules = [
            'field_title' => 'required'       
        ];

        $this->validate($request,$rules);
    }

    public function createField(Request $request,$form_id, $section_id){
        $this->validateCreateField($request);
        $modelName = $this->assignModel('FormBuilder');
        $lastOrder = $modelName::where(['form_id' => $form_id , 'section_id' => $section_id])->orderBy('order','DESC')->first();
        if($lastOrder == NULL){
            $newOrder = 1;
        }else{
            $newOrder = $lastOrder->order+1;
        }
        $model = new $modelName;
        $model->form_id = $form_id;
        $model->section_id = $section_id;
        $model->field_title = $request->field_title;
        $model->field_slug = $request->field_slug;
        $model->field_type = $request->field_type;
        $model->order = $newOrder;
        $model->save();
        if($request->type == 'survey'){
            $formMeta = $this->assignModel('FieldMeta');
            $metaModel = new $formMeta;
            $metaModel->key = 'question_id';
            $metaModel->value = 'SID'.$form_id.'_GID'.$section_id.'_QID'.$model->id;
            $metaModel->form_id = $form_id;
            $metaModel->section_id = $section_id;
            $metaModel->field_id = $model->id;
            $metaModel->save();
        }
        return back();
    }

    /*
    *   To show all sections list  on the behalf 
    *   of form slug
    *
    *   @Last Update: Rahul Sharma 08 June 2017
    */
    public function sectionsList($form_id){
        $permission = true;
        $plugins = [
                        'js' => ['custom'=>['builder']],
                   ];
        $modelName = $this->assignModel('section');
        $formModel = $this->assignModel('forms');
        $form = $formModel::find($form_id);
        $model = $modelName::orderBy('order','ASC')->where('form_id',$form_id)->with(['fields'=>function($query){
            $query->with('fieldMeta')->orderBy('order','ASC');
        },'sectionMeta','form'])->get();
        // dd($model);
        return view('admin.formbuilder.sections')->with([ 'sections' => $model,'plugins'=> $plugins,'form'=>$form,'permission'=>$permission]);
    }

    public function deleteSection($id)
    {
        $modelName = $this->assignModel('section');
        $model = $modelName::with(['fields'])->where('id',$id)->delete();
        return back();
    }


    public function addRow(Request $request){
        $rowCount = $request->rowCount;
        return view('admin.formbuilder._rowAppend' , ['rowCount'=>$rowCount])->render();
    }


    public function listFields(Request $request , $form_id, $section_id)
    {  
        $modelName = $this->assignModel('FormBuilder');
        $model = $modelName::where(['section_id' => $section_id,'form_id'=>$form_id])->with([
                'fieldMeta'=>function($query) use ($form_id, $section_id){
                    $query->where(['form_id'=>$form_id,'section_id'=>$section_id]);
                }])->get();
        $plugins = [
                        'js' => ['custom'=>['builder']] 
                   ];
            //check if the existing field data has same slug

        $slug_data=[];
        $existSlug = '';
        Session::put('sameSlugmessage', "");
        $checkExstingSlug = $modelName::select('field_slug')->where(['form_id'=> $form_id,'section_id' => $section_id])->get();
            foreach($checkExstingSlug  as $key => $array){
                $slug_data[] = $array->field_slug;
            }
        $result_slug = count($slug_data) === count(array_flip($slug_data));
        // dd($slug_data);
        if($result_slug == false){
            $existSlug = "dublicate slug recognized , please verify and rename the slug";
        }else{
            $existSlug = "";
        }
        Session::put('sameSlugmessage', $existSlug);        
        return view('admin.formbuilder.formbuilder', $plugins)->with(['model'=>$model,'plugins'=>$plugins]);
    }

    public function getFieldDataById($form_id,$section_id,$field_id)
    {
        dump($form_id);
        dump($section_id);
        dump($field_id);
        $modelName = $this->assignModel('FormBuilder');
        $model = $modelName::where(['section_id' => $section_id,'form_id'=>$form_id])->with([
                'fieldMeta'=>function($query) use ($form_id, $section_id){
                    $query->where(['form_id'=>$form_id,'section_id'=>$section_id]);
                }])->get();
        $plugins = [
                        'js' => ['custom'=>['builder']] 
                   ];
        return view('admin.formbuilder._row',$model)->render();
    }

    public function fieldMeta(Request $request)
    {
        $modelName = $this->assignModel('FieldMeta');
        $meta = $modelName::select('key','value')->where('field_id',$request->id)->get();
        return view('admin.formbuilder._row')->with(['model'=> $meta]);
    }
    public function fieldList(Request $request , $id)
    {

        $plugins = [
                        'js' => ['custom'=>['builder']],
                   ];
        $modelName = $this->assignModel('section');
        $sections = $modelName::where('id',$id)->first();
        return view('admin.formbuilder.formbuilder')->with(['plugins'=> $plugins,'section' => $sections]);
    }

//previous code

    public function store(Request $request){
        dd($request->all());
        $modelName = $this->assignModel('FormBuilder');
        $model = new $modelName;   
        $model->fill($request->all());
        $model->save(); 
        $modelName = $this->assignModel('formbuilder');
        $field_id = $modelName::where(['field_slug' => $request->field_slug])->first();
            unset($request['field_slug'],$request['field_title'],$request['field_type'],$request['field_description'],$request['_token']); 
            if($model){
                foreach ($request->all() as $key => $value) {
                    $modelName = $this->assignModel('FieldMeta');
                    $meta = new $modelName;
                    $meta->form_id = $request->form_id;
                    $meta->section_id = $request->section_id;
                    $meta->field_id = $field_id->id;
                    $meta->key = $key;
                        if($value == ""){
                           $meta->value = ""; 
                        }elseif($value){
                            $meta->value = $value;
                        }
                    $meta->save();

                }
            }
            return back();
    }

    public function formFields(){

        return view('admin.formbuilder._fields')->render();
    }
    public function formsList(){
        $modelName = $this->assignModel('FormBuilder');
        $model = $modelName::get();
        dd($model);
        return view('admin.formbuilder.list',['model'=>$model]);
    }
    public function deleteField(Request $request){
        $id = $request->id;
        $modelName = $this->assignModel('FormBuilder');
        $modelName::where('id',$id)->delete();
        $modelName = $this->assignModel('FieldMeta');
        $modelName::where('field_id',$id)->delete();
        return back();  
    }

    protected function validateUpdateFields($request){
        $rules = [
            'field_type' => 'required',
            'field_slug' => 'required',
        ];
        $this->validate($request, $rules);
    }

    public function updateField(Request $request, $form_id, $section_id, $field_id){
        if($request->has('field_options')){
            $new_field_options = [];
            foreach($request['field_options'] as $k => $v){
                if(is_array($v)){
                    foreach ($v as $key => $value) {
                        if($value != null){
                            $new_field_options[$k][$key] = $value;
                        }
                    }
                }
            }
            $request['field_options'] = $new_field_options;

        }
        if($request->has('field_conditions')){
            $new_field_options = [];
            foreach($request['field_conditions'] as $k => $v){
                if(is_array($v)){
                    foreach ($v as $key => $value) {
                        if($value != null){
                            $new_field_options[$k][$key] = $value;
                        }
                    }
                }
            }
            $request['field_conditions'] = $new_field_options;

        }
        $this->validateUpdateFields($request);
        $modelName = $this->assignModel('FormBuilder');
        $model = $modelName::firstOrNew(['form_id'=>$form_id,'section_id'=>$section_id,'id'=>$field_id]);
        $model->field_slug = $request->field_slug;
        $model->field_title = $request->field_title;
        $model->field_type = $request->field_type;
        $model->field_description = $request->field_description;
        //$model->order = $request->field_order;
        $model->save();
        $modelName = $this->assignModel('FieldMeta');
        $exceptedKeys = $request->except(['_token','field_slug','field_title','field_type','field_description','field_order']);
        foreach($exceptedKeys as $k => $value){
            $model = $modelName::firstOrNew(['form_id'=>$form_id,'section_id'=>$section_id,'field_id'=>$field_id,'key'=>$k]);
            $model->form_id = $form_id;
            $model->section_id = $section_id;
            $model->field_id = $field_id;
            $model->key = $k;
            if(is_array($value)){
                $value = json_encode($value);
            }
            $model->value = ($value == null)?'':$value;
            $model->save();
        }

        return back();
    }

    protected function assignModel($model){
        if(Auth::guard('admin')->check()){
            return 'App\\Model\\Admin\\'.$model;
        }else{
            return 'App\\Model\\Organization\\'.$model;
        }
    }

    public function formSettings($id){ 
        $modelName = $this->assignModel('FormsMeta');
        $formModel = $this->assignModel('forms');
        $form = $formModel::find($id);

        $model = $modelName::with(['forms'])->where('form_id',$id)->get();
        $modelData = [];
        foreach ($model as $key => $value) {
            $modelData[$value->key] = $value->value;
        }
        $modelData['id'] = $id;
        return view('admin.formbuilder.form-settings',['model'=>$modelData,'form'=>$form]);
    }

    public function storeSettings(Request $request, $id){
        // dd($request->all());
        $modelName = $this->assignModel('FormsMeta');
        // dd($modelName);
        foreach($request->except(['_token']) as $key => $value){
            $model = $modelName::firstOrNew(['key'=>$key,'form_id'=>$id]);
            $model->key = $key;
            $model->value = ($value == null)?'':$value;
            $model->form_id = $id;
            $model->save();
        }
        return back();
    }

    public function uploadMedia(Request $request){

    }
    public function sectionSort(Request $request)
    {
        $Associate = $this->assignModel('section');
        $sort = [];
        $index = 1;
        foreach ($request->id as $key => $value) {
            $sort[$value] = $index;
            $index++;
        }
        foreach ($sort as $key => $value) {
            $model = $Associate::where('id',$key)->update(['order' => $value]);
        }
    }
    public function fieldSortDown( $id)
    {
    	$index= 1;
        $field_id = $id;
        $Associate = $this->assignModel('FormBuilder');
        
        $section_id = $Associate::select('section_id')->where('id',$field_id)->first()->section_id;
        $form_id = $Associate::select('form_id')->where('id',$field_id)->first()->form_id;
        $order = $Associate::select('order')->where('id',$field_id)->first()->order;
        
        $getFieldsList = $Associate::where(['section_id' => $section_id , 'form_id' => $form_id])->orderBy('order','ASC')->get()->toArray();
        foreach($getFieldsList as $key => $value){
        	$value['order'] = $index;
            $model = $Associate::where(['section_id' => $section_id , 'form_id' => $form_id])->where('id',$value['id'])->update($value);
        	$index++;
        }
        $getFieldsList = $Associate::where(['section_id' => $section_id , 'form_id' => $form_id])->orderBy('order','ASC')->get()->toArray();

        foreach ($getFieldsList as $key => $value) {
            if($value['order'] != null){
                if($order < $value['order']){

                    $next_order = $Associate::select('order')->where(['order' => $order+1,'section_id' => $section_id , 'form_id' => $form_id])->first()->order;
                    $nextFieldId = $Associate::select('id')->where(['order' => $order+1,'section_id' => $section_id , 'form_id' => $form_id])->first()->id;
                    $updateOrderPre = $Associate::where(['id' => $nextFieldId , 'section_id' => $section_id , 'form_id' => $form_id])->update(['order' => $order]);

                    $updateOrderNext = $Associate::where(['id' => $field_id ,'section_id' => $section_id , 'form_id' => $form_id])->update(['order' => $next_order]);
                }else{
                    Session::flash('null_order' , "NULL order in list field");
                }
            }
        }
        return back();
    }
    public function fieldSortUp( $id)
    {   
        $Associate = $this->assignModel('FormBuilder');
        $index = 1;
        $field_id = $id;
        $section_id = $Associate::select('section_id')->where('id',$field_id)->first()->section_id;
        $form_id = $Associate::select('form_id')->where('id',$field_id)->first()->form_id;
        $order = $Associate::select('order')->where('id',$field_id)->first()->order;

        $getFieldsList = $Associate::where(['section_id' => $section_id , 'form_id' => $form_id])->orderBy('order','ASC')->get()->toArray();
        foreach($getFieldsList as $key => $value){
        	$value['order'] = $index;
            $model = $Associate::where(['section_id' => $section_id , 'form_id' => $form_id])->where('id',$value['id'])->update($value);
        	$index++;
        }
		$getFieldsList = $Associate::where(['section_id' => $section_id , 'form_id' => $form_id])->orderBy('order','ASC')->get()->toArray();

        foreach ($getFieldsList as $key => $value) {
            $count_fields = $Associate::where(['section_id' => $section_id , 'form_id' => $form_id])->get()->count();
            if($value['order'] != null){
                $next_order = $Associate::select('order')->where(['order' => $order-1,'section_id' => $section_id , 'form_id' => $form_id])->first()->order;
                $prevField = $Associate::select('id')->where(['order' => $order-1,'section_id' => $section_id , 'form_id' => $form_id])->first()->id;
                $updateOrderPre = $Associate::where(['id' => $prevField , 'section_id' => $section_id , 'form_id' => $form_id])->update(['order' => $order]);

                $updateOrderNext = $Associate::where(['id' => $field_id ,'section_id' => $section_id , 'form_id' => $form_id])->update(['order' => $next_order]);
            }
        }
        return back();
    }
    public function getMetaById($model , $id)
    {
        $Associate = $this->assignModel($model);
        $meta = $Associate::where('id',$key)->get();
        return $meta;
    }

    public function getfieldMeta($form_id , $section_id , $field_id)
    {
        $data = [];
        $Associate = $this->assignModel('FieldMeta');
        $meta = $Associate::where(['form_id'=>$form_id,'section_id' => $section_id , 'field_id'=>$field_id])->get()->toArray();
        foreach ($meta as $key => $value) {
            foreach ($value as $k => $v) {
                if($k == 'section_id'){
                    unset($k);
                    $data[$key]['section_id'] = $new_section_id;
                }else{
                    $data[$key][$k] = $v;
                }
            }
        }
        return $data;
    }
    public function fieldClone($id)
    {
        $data = [];
        $Associate = $this->assignModel('FormBuilder');
        $model = $Associate::where('id',$id)->first()->toArray();

        $field_id = $id;
        $form_id = $model['form_id'];
        $section_id = $model['section_id'];
        $lastOrderId = $Associate::where(['form_id'=>$form_id,'section_id' => $section_id])->orderBy('order','DESC')->first();
        $model['order'] = $lastOrderId->order+1;

        if($model != null){
            $create = new $Associate;
            $create->fill($model);
            $create->save();
            $new_field_id = $create->id;

            $Associate = $this->assignModel('FieldMeta');
            $meta = $Associate::where(['form_id'=>$form_id,'section_id' => $section_id , 'field_id'=>$field_id])->get()->toArray();
            foreach ($meta as $key => $value) {
                foreach ($value as $k => $v) {
                    if($k == 'field_id'){
                        unset($k);
                        $data[$key]['field_id'] = $new_field_id;
                    }else{
                        $data[$key][$k] = $v;
                    }
                }
            }
            
            foreach($data as $key => $value){
                $meta = new $Associate;
                $meta->fill($data);
                $meta->save();
            }
        }
        return back();
    }
    public function sectionClone($id)
    {
        $Associate = $this->assignModel('section');
        $model = $Associate::where('id',$id)->first()->toArray();
        $lastOrderId = $Associate::orderBy('order','DESC')->first();

        $form_id = $model['form_id'];
        $section_id = $model['id'];
        $model['order'] = $lastOrderId->order+1;
        $field_id = '';
        
        if($model != null){

            $create = new $Associate;
            $create->fill($model);
            $create->save();
            $new_section_id = $create->id;
                $data = [];
            $Associate = $this->assignModel('FormBuilder');
            $model = $Associate::where(['form_id'=>$form_id,'section_id' => $section_id])->get()->toArray();

            foreach($model as $key => $value){

                foreach ($value as $k => $v) {
                    if($k == 'section_id' || $k == 'id'){
                        unset($k);
                        $data[$key]['section_id'] = $new_section_id;
                    }else{

                        $data[$key][$k] = $v;
                    }
                }
            }
            foreach ($data as $key => $value) {
                $create = new $Associate;
                $create->fill($value);
                $create->save();
            }
        }
        return back();
    }
    public function formClone($id)
    {
        $formModel = $this->assignModel('forms');
        $formData = $formModel::find($id)->toArray();
        $old_slug = $formData['form_slug'];
        unset($formData['form_slug']);
        $formData['form_slug'] = $old_slug.'_copy';

        if(@$formData != null){
            $data = [];
            $create = new $formModel;
            $create->fill($formData);
            $create->save();
            $new_form_id = $create->id;

            $sectionModel = $this->assignModel('section');
            $sectionData = $sectionModel::where('form_id',$id)->get()->toArray();   
            $sec_id = [];
            if(count($sectionData)> 0){
                foreach ($sectionData as $key => $value) {
                    $sec_id[] = $value['id'];
                }
                $sectionData = [];
                foreach ($sec_id as $key => $value) {
                    $tempVar = $sectionModel::with(['fields' => function($query) use($value , $id){
                        $query->where(['section_id'=>$value , 'form_id' => $id])->with(['fieldMeta'=>function($query) use($id , $value){
                            $query->where(['form_id'=>$id , 'section_id' => $value]);
                        }]);
                        }])->where('form_id',$id)->get()->toArray();
                    if(!empty($tempVar)){
                        $sectionData[] = $tempVar[$key];
                    }
                }
                foreach($sectionData as $key => $value){
                    foreach ($value as $k => $v) {
                        if($k == 'form_id' || $k == 'id'){
                            unset($k);
                            $data[$key]['form_id'] = $new_form_id;
                        }else{
                            $data[$key][$k] = $v;
                        }
                    }
                }
                foreach ($data as $key => $value) {
                    $create = new $sectionModel;
                    $create->fill($value);
                    $create->save();
                    $new_section_id = $create->id;

                    $fielsModel = $this->assignModel('FormBuilder');

                    // $fieldData = $fielsModel::with(['fieldMeta'=>function($query) use($id , $sec_id){
                    //     $query->where(['form_id'=>$id , 'section_id' => $sec_id]);
                    // }])->where(['section_id'=>$sec_id , 'form_id' => $id])->get()->toArray();
                    // dd($fieldData);
                    foreach($value['fields'] as $k => $v){
                        unset($v['id']);
                        $v['section_id'] = $new_section_id;
                        $v['form_id'] = $new_form_id;

                        $field = new $fielsModel;
                        $field->fill($v);
                        $field->save();
                        $field_id = $field->id;
                        
                        if(array_key_exists('field_meta', $v)){

                            if($v['field_meta'] != null){
                                $Associate = $this->assignModel('fieldMeta');
                                foreach ($v['field_meta'] as $key => $value) {
                                    $value['field_id'] = $field_id;
                                    $value['section_id'] = $new_section_id;
                                    $value['form_id'] = $new_form_id;

                                    $fieldMeta = new $Associate;
                                    $fieldMeta->fill($value);
                                    $fieldMeta->save();
                                }
                            }
                        }

                    }
                }
            }
            
            
        }
        return back();

    }
    public function customForm($id)
    {
        $modelName = $this->assignModel('FormsMeta');
        $formModel = $this->assignModel('forms');
        $form = $formModel::find($id);

        $model = $modelName::where(['form_id'=>$id])->whereIn('key', [ 'js','css'])->get()->toArray();
        $data = [];
        foreach($model as $key => $value){
                if($value['key'] == 'js'|| $value['key'] == 'css'){
                    $data[$value['key']] = $value['value']; 
                }
        }
        return view('admin.formbuilder.custom-code',compact('data','form'));
    }
    public function updateCustomForm(Request $request , $id)
    {
        return $this->saveCustomForm($request , $id);
    }
    public function saveCustomForm(Request $request , $id)
    {
        $modelName = $this->assignModel('FormsMeta');
            foreach($request->except('_token') as $key => $value){
                $meta = FormsMeta::firstOrNew(['form_id'=>$id, 'key'=>$key]);
                $meta->form_id = $id;
                $meta->key = $key;
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $meta->value = $value;
                $meta->save();
            }

        return redirect()->route('form.custom',$id);
    }
    public function previewForm($id)
    {
        $modelName = $this->assignModel('forms');

        $form = $modelName::find($id);
        $slug = $modelName::select('form_slug')->where('id',$id)->first()->form_slug;
        return view('admin.formbuilder.preview',compact('slug','form'));
    }
    public function sectionMove(Request $request)
    {
        // dump($request->all());
        // dd($id);
        $modelName = $this->assignModel('section');

        if($request->has('want_to')){
            if($request['want_to'] == 'move'){
                $form_id = $modelName::where('id',$request['sectionId'])->first()->form_id;
                $model = $modelName::where('id',$request['sectionId'])->update(['form_id' => $request['move_to']]);
                $modelName = $this->assignModel('FormBuilder');
                $sectionMeta = $modelName::where(['section_id'=>$request['sectionId'] , 'form_id' => $form_id])->update(['form_id' => $request['move_to']]);
                return back();
            }
            if($request['want_to'] == 'copy'){

                $form_id = $modelName::where('id',$request['sectionId'])->first()->form_id;

                $section = $modelName::where('id',$request['sectionId'])->first();
                unset($section->form_id);
                $section->form_id = $request['move_to'];
                $saveSection = new $modelName;
                $saveSection->fill($section->toArray());
                $saveSection->save();
                $new_sec_id = $saveSection->id;

                $sectionId = $request['sectionId'];
                $modelName = $this->assignModel('FormBuilder');
                $sectionMeta = $modelName::with(['fieldMeta'=>function($query) use( $sectionId , $form_id, $request){
                    $query->where(['section_id'=>$request['sectionId'] , 'form_id' => $form_id]);
                }])->where(['section_id'=>$request['sectionId'] , 'form_id' => $form_id])->get()->toArray();



                foreach($sectionMeta as $k => $v){
                    unset($v['id']);
                    $v['section_id'] = $new_sec_id;
                    $v['form_id'] = $request['move_to'];

                    $field = new $modelName;
                    $field->fill($v);
                    $field->save();
                    $field_id = $field->id;
                    
                    if(array_key_exists('field_meta', $v)){
                        if($v['field_meta'] != ''){
                            $Associate = $this->assignModel('fieldMeta');
                            foreach ($v['field_meta'] as $key => $value) {
                                $value['field_id'] = $field_id;
                                $value['section_id'] = $new_sec_id;
                                $value['form_id'] = $request['move_to'];

                                $fieldMeta = new $Associate;
                                $fieldMeta->fill($value);
                                $fieldMeta->save();
                            }
                        }
                    }
                }
            }
            return back();
        }
    }
    function listSections(Request $request )
    {
        $modelName = $this->assignModel('section');
        $model = $modelName::where('form_id',$request['formId'])->pluck('section_name','id');
        return $model;
    }
    public function fieldMove(Request $request , $field_id = null)
    {
        $modelName = $this->assignModel('FormBuilder');
        
        if($request->has('want_to')){
            if($request['want_to'] == 'move'){
                $move = $modelName::where('id',$request['field_id'])->update(['form_id' => $request['move_to_form'] , 'section_id' => $request['move_to_section']]);
                $fieldMetaModel = $this->assignModel('FieldMeta');
                $move = $fieldMetaModel::where('field_id',$request['field_id'])->update(['form_id' => $request['move_to_form'] , 'section_id' => $request['move_to_section']]);
            }
            if($request['want_to'] == 'copy'){
                $getField = $modelName::where('id',$request['field_id'])->first()->toArray();
                $getField['form_id'] = $request['move_to_form'];
                $getField['section_id'] = $request['move_to_section'];

                $copy = new $modelName;
                $copy->fill($getField);
                $copy->save();
                $new_form_id = $copy->id;
                

                $fieldMetaModel = $this->assignModel('FieldMeta');
                $meta = $fieldMetaModel::where(['field_id' => $request['field_id']] )->get()->toArray();

                foreach ($meta as $key => $value) {
                    $copy = new $fieldMetaModel;
                    $copy['field_id'] = $new_form_id;
                    $copy['section_id'] = $request['move_to_section'];
                    $copy['form_id'] = $request['move_to_form'];

                    $copy->fill($value);
                    $copy->save();
                }
            }
            return back();   
        }
    }
}
