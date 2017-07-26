<?php

namespace App\Http\Controllers\Organization\visualization;

use Illuminate\Http\Request;
use App\Model\Organization\Visualization;
use Auth;
use App\Model\Organization\Dataset as DL;
use Session;
use DB;
use Carbon\Carbon AS TM;
use Lava;
use Excel;
use App\Http\Controllers\Controller;
use App\Model\Admin\CustomMaps;
use App\Model\Organization\VisualizationCharts;
use App\Model\Organization\VisualizationChartMeta;
use App\Model\Organization\VisualizationMeta;
class VisualisationController extends Controller
{
	protected $ipAdress;
	protected $errors_list = [];
	public function __construct(Request $request)
	{ 
	  $this->ipAdress =  $request->ip();
	  DB::enableQueryLog();  
	}
	function index(){

		$plugin = [
					'css'  =>  ['datatables'],
					'js'   =>  ['datatables','custom'=>['gen-datatables']]
				   ];

		return view('visualisation.index',$plugin);
	}

	public function indexData(){

		$model = VS::orderBy('id','desc')->get();
		return Datatables::of($model)
			->addColumn('actions',function($model){
				return view('visualisation._actions',['model' => $model])->render();
			})->editColumn('dataset_id',function($model){
				try{
					return $model->dataset->dataset_name;
				}catch(\Exception $e){
					return '';
				}
			})->editColumn('created_by',function($model){
				try{
						return $model->createdBy->name;
					}catch(\Exception $e)
					{
						return '';
					}

			})->make(true);
	}

	public function create(Request $request){
		
		if($request->has('per_page')){
            $perPage = $request->per_page;
            if($perPage == 'all'){
              $perPage = 999999999999999;
            }
          }else{
            $perPage = 5;
          }
          $sortedBy = @$request->sort_by;
          if($request->has('search')){
              if($sortedBy != ''){
                  $model = Visualization::with(['dataset'])->where('name','like','%'.$request->search.'%')->orderBy($sortedBy,$request->desc_asc)->paginate($perPage);
              }else{
                  $model = Visualization::with(['dataset'])->where('name','like','%'.$request->search.'%')->paginate($perPage);
              }
          }else{
              if($sortedBy != ''){
                  $model = Visualization::with(['dataset'])->orderBy($sortedBy,$request->desc_asc)->paginate($perPage);
              }else{
                   $model = Visualization::with(['dataset'])->paginate($perPage);
              }
          }
          // dd($model);
          $datalist =  [
                          'datalist'=>$model,
                          'showColumns' => ['name'=>'Name', 'dataset.dataset_name' => 'Dataset','description'=>'Description','created_by'=>'Created By','created_at'=>'Created At'],
                          'actions' => [
                                          'edit' => ['title'=>'Edit','route'=>'edit.visualization','class' => 'edit'],
                                          'delete'=>['title'=>'Delete','route'=>'delete.visualization'],
                                          'Edit Charts'=>['title'=>'Edit Charts','route'=>'edit.visual']
                                       ]
                      ];
		return view('organization.visualization.create',$datalist);

	}

	public function store(Request $request){

		$this->modelValidate($request);
		DB::beginTransaction();
		try{

			$model = new VS();
			$model->fill($request->except(['_token']));
			$model->created_by = Auth::user()->id;
			$model->save();
			DB::commit();
			Session::flash('success','Successfully created!');
			return redirect()->route('visualisation.list');
		}catch(\Exception $e){

			DB::rollback();

			throw $e;
		}
	}


	protected function modelValidate($request){

		$rules = [
				'dataset_id'  => 'required',
				'visual_name' => 'required',
				'settings'    => 'required',
				'options'     => 'required'
		];

		$this->validate($request,$rules);
	}


	public function edit($id){
		$charts = VisualizationCharts::with(['meta'])->where('visualization_id',$id)->get();
		$model = Visualization::with(['dataset','meta'])->find($id);
		$dataset = DB::select('SELECT * FROM '.$model->dataset->dataset_table.' LIMIT 1');
		$dataset = (array)$dataset[0];
		unset($dataset['id']);
		$columns = $dataset;
		$maps = CustomMaps::get();
		$filters = $this->getMetaValue($model->meta,'filters');
		if($filters == false){
			$filters = [];
		}
		return view('organization.visualization.edit2',['columns'=>$columns,'charts'=>$charts, 'filters'=>$filters]);
	}

	public function update(Request $request, $id){

		$model = VS::findOrFail($id);

		$this->modelValidate($request);

		DB::beginTransaction();

		try{

			$model->fill($request->except(['_token']));
			$model->save();
			DB::commit();
			Session::flash('success','Successfully update!!');
			return redirect()->route('visualisation.list');
		}catch(\Exception $e){

			DB::rollback();
			throw $e;
		}
	}

	public function destroy($id){

		$model = VS::findOrFail($id);

		try{

			$model->delete();
			Session::flash('success','Successfully deleted!');
			return redirect()->route('visualisation.list');
		}catch(\Exception $e){

			throw $e;
		}
	}

	/************************************************************ Complete Visualization Work ********************************************************/

	/*
	* Called internally from craeteVisualization to validate requqest
	* @param $request
	* return JSON to API
	*/
	protected function validateRequest($request){

        if($request->has('dataset') && $request->has('visual_name')){
            return ['status'=>'true','errors'=>''];
        }else{
            return ['status'=>'false','error'=>'Fill required fields!'];
        }
    }

    public function get_visualization_by_dataset($datasetid){
    	$model = Visualisation::where('dataset_id',$datasetid)->get();
    	return ['status'=>'success','list'=>$model];
    }

    /*
    * Used in Smaart Framework Api index.api.js to create new visualization
    * @param $request (posted request)
    * return JSON to API
    */
	public function createVisualization(Request $request)
	{
        try{
            $model = new Visualization();
            $model->dataset_id = $request->select_dataset;
            $model->name = $request->visualization_name;
            $model->description = $request->description;
            $model->created_by = Auth::guard('org')->User()->id;
            $model->save();
        }catch(\Exception $e){
            if($e instanceOf \Illuminate\Database\QueryException){
                return ['status'=>'error','message'=>'No dataset found!'];
            }else{
                return ['status'=>'error','message'=>'something went wrong!'];
            }
        }
        return back();
	}

	/*
	* Used to update visualization details like chart data and meta
	* @param $request (posted request)
	* return JSON to API
	*/
	public function saveCharts(Request $request, $visualization_id){
		for($chartCount = 0; $chartCount < count($request->chart_title); $chartCount++){
			
			$visualization_chart = $this->createChart($request, $chartCount, $visualization_id);
			$this->createMeta($request, $chartCount, $visualization_chart, $visualization_id);
		}
		if($request->has('filter_columns')){
			$visualizationMeta = VisualizationMeta::where(['visualization_id'=>$visualization_id,'key'=>'filters'])->first();
			if($visualizationMeta == null){
				$visualizationMeta = new VisualizationMeta;
			}
			$visualizationMeta->visualization_id = $visualization_id;
			$visualizationMeta->key = 'filters';
			$filters = [];
			for($filterCount = 0; $filterCount < count($request->filter_columns); $filterCount++){
				$filters['filter_'.$filterCount] = ['type'=>$request->filter_type[$filterCount],'column'=>$request->filter_columns[$filterCount]];
			}
			$visualizationMeta->value = json_encode($filters);
			$visualizationMeta->save();
		}
		return back();
	}

	public function saveVisualizationSettings(Request $request, $id){
		$settings = $request->except(['_token']);
		foreach($settings as $key => $value){
			$model = VisualizationMeta::firstOrNew(['key'=>$key,'visualization_id'=>$id]);
			$model->key = $key;
			$model->value = ($value == null)?'':$value;
			$model->visualization_id = $id;
			$model->save();
		}
		return back();
	}

	protected function createMeta($request, $chartCount, $visualization_chart, $visualization_id){
		$metaData = $request->except([
										'chart_title','variable_x_axis',
										'variable_y_axis','chart_type',
										'_token','filter_columns','filter_type',
										'chart_id','area_code','tooltip_data']);
		foreach ($metaData as $chart_meta_key => $chart_meta_value) {
			if($request->has('chart_id') && @$request->chart_id['chart_'.$chartCount] != ''){
				$visualization_chart_meta = VisualizationChartMeta::where(['visualization_id'=>$visualization_id,'chart_id'=>$request->chart_id['chart_'.$chartCount],'key'=>$chart_meta_key])->first();
				if($visualization_chart_meta == null){
					$visualization_chart_meta = new VisualizationChartMeta();
				}
			}else{
				$visualization_chart_meta = new VisualizationChartMeta();
			}
			$visualization_chart_meta->visualization_id = $visualization_id;
			$visualization_chart_meta->chart_id = $visualization_chart->id;
			$visualization_chart_meta->key = $chart_meta_key;
			if(isset($chart_meta_value['chart_'.$chartCount])){
				if(is_array($chart_meta_value['chart_'.$chartCount])){
					$chart_meta_value = json_encode($chart_meta_value['chart_'.$chartCount]);
				}else{
					$chart_meta_value = $chart_meta_value['chart_'.$chartCount];
				}
				$visualization_chart_meta->value = $chart_meta_value;
			}
			
			$visualization_chart_meta->save();
		}
	}

	protected function createChart($request, $chartCount, $visualization_id){
		if($request->has('chart_id') && @$request->chart_id['chart_'.$chartCount] != ''){
			$visualization_chart = VisualizationCharts::find($request->chart_id['chart_'.$chartCount]);
		}else{
			$visualization_chart = new VisualizationCharts();
		}
		if(isset($request->chart_type['chart_'.$chartCount])){
			$chartType = $request->chart_type['chart_'.$chartCount];
		}else{
			$chartType = '';
		}
		$visualization_chart->visualization_id = $visualization_id;
		$visualization_chart->chart_title = ($request->chart_title['chart_'.$chartCount] == null)?'(no title)':$request->chart_title['chart_'.$chartCount];
		$primaryColumn = ($chartType == 'CustomMap')?@$request->area_code['chart_'.$chartCount]:@$request->variable_x_axis['chart_'.$chartCount];
		$primaryColumn = $primaryColumn;
		$visualization_chart->primary_column = ($primaryColumn != null)?$primaryColumn:'{}';
		$visualization_chart->secondary_column = ($chartType == 'CustomMap')?json_encode(@$request->tooltip_data['chart_'.$chartCount]):json_encode(@$request->variable_y_axis['chart_'.$chartCount]);
		$visualization_chart->chart_type = (@$request->chart_type['chart_'.$chartCount] != null)?@$request->chart_type['chart_'.$chartCount]:'not defined';
		$visualization_chart->status = 'true';
		$visualization_chart->save();
		return $visualization_chart;
	}

	/*
	* To display pre-filled details in edit visualization 
	* $param $id (visualization id)
	* return JSON to API
	*/
	public function visualization_details($id){
		try{
			$model = Visualisation::with(['dataset','charts','meta','chart_meta'])->find($id);
			$dataset_table = $model->dataset->dataset_table; //get dataset table name from visualization table with relation (with('dataset'))
			$dataset_model = DB::table($dataset_table)->first();
        	unset($dataset_model->id);
        	$responseArray = [];
        	$responseArray['dataset_columns'] = (array)$dataset_model;
        	$charts = [];
        	$chartIndex = 0;
        	if(!$model->charts->isEmpty()){
        		foreach($model->charts as $chart_key => $chart_value){
        			$charts[$chartIndex]['title'] = $chart_value->chart_title;
        			$charts[$chartIndex]['column_one'] = $chart_value->primary_column;
        			$charts[$chartIndex]['columns_two'] = json_decode($chart_value->secondary_column);
        			$charts[$chartIndex]['chartType'] = $chart_value->chart_type;
        			$chart_meta = VisualizationChartMeta::where('chart_id',$chart_value->id)->get();
        			if(!$chart_meta->isEmpty()){
        				foreach($chart_meta as $meta_key => $chart_meta_value){
        					json_decode($chart_meta_value->value);
        					if(json_last_error() == JSON_ERROR_NONE){
        						$charts[$chartIndex][$chart_meta_value->key] = json_decode($chart_meta_value->value);
        					}else{
        						$charts[$chartIndex][$chart_meta_value->key] = $chart_meta_value->value;
        					}
        				}
        			}
        			$chartIndex++;
        		}
        	}
        	
        	$responseArray['charts'] = $charts; // gettings all chart of this visualizaton with 'hasMany' eloquent relation
        	$responseArray['visualization_meta'] = [];
        	if(!$model->meta->isEmpty()){
        		foreach($model->meta as $key => $meta_data){
        			$responseArray['visualization_meta'][$meta_data->key] = $meta_data->value; //get all visualization_meta data from relation
        		}
        	}
        	$responseArray['maps'] = [
        								'organization_maps'=>Map::select(['id','title'])->where('status','enable')->get(),
        								'global_maps'=>GMap::select(['id','title'])->where('status','enable')->get()
        							];
        	$responseArray['chart_settings'] = GlobalSetting::where('meta_key','visual_setting')->first()->meta_value;
        	return ['status'=>'success','data'=>$responseArray];
		}catch(\Exception $e){
			return ['status'=>'error','message'=>$e->getMessage()];
		}
	}

	public function visualization_list(){

		$responseArray = [];
		$model = Visualisation::with('dataset')->get();
		foreach ($model as $key => $value) {
			$responseArray['visuals'][] = $value;
			$responseArray['dataset'][] = $value->dataset;
		}
		return ['status'=>'success','list'=>$responseArray];
	}

	public function delete_visualization($visualization_id){
		
		Visualization::where('id',$visualization_id)->delete();
		return back();
		/*$visual = Visualisation::where('id',$visualization_id)->with([
			'charts'=>function($query) use ($visualization_id){
				$query->where('visualization_id',$visualization_id)->forceDelete();
		},
			'chart_meta'=>function($query) use ($visualization_id){
				$query->where('visualization_id',$visualization_id)->delete();
		},
			'meta'=>function($query) use ($visualization_id){
				$query->where('visualization_id',$visualization_id)->delete();
		}])->forceDelete();

		return ['status'=>'success','message'=>'Visualization deleted successfully!'];*/
	}

	public function generateEmbed(Request $request){
        $user = Auth::user();
        $org_id = $user->organization_id;
        $exist = Embed::where(['user_id'=>$user->id,'visual_id'=>$request->visual_id])->first();
        if($exist == null){
            $model = new Embed;
            $embed_token = str_random(20);
            $model->visual_id = $request->visual_id;
            $model->org_id  = $org_id;
            $model->user_id = $user->id;
            $model->embed_token = $embed_token;
            $model->save();
        }else{
            $embed_token = $exist->embed_token;
        }

        return ['status'=>'success','message'=>'Successfully generated!','token'=>$embed_token];
    }

	protected function put_in_errors_list($error = '', $break = false){
		array_push($this->errors_list, $error);
		if($break == true){
			echo view('web_visualization.errors',['errors'=>$this->errors_list])->render(); // load error view
			die;
		}else{
			echo view('web_visualization.errors',['errors'=>$this->errors_list])->render(); // load error view
			return true;
		}
	}

	protected function getMetaValue($metaArray, $metaKey){
		$metaArray = collect($metaArray);
		$metaData = $metaArray->where('key',$metaKey);
		$metaValue = false;
		foreach($metaData as $key => $value){
			$metaValue = $value->value;
		}
		return $metaValue;
	}

	protected function get_meta_in_correct_format($visualMetas){
		$visualMetas = json_decode(json_encode($visualMetas),true);
		$valueColumns = array_column($visualMetas, 'value');
		$keyColumns = array_column($visualMetas, 'key');
		return array_combine($keyColumns,$valueColumns);
	}

	public function getFIlters($table, $columns, $columnNames){
        
        $columnsWithType = $columns;
        $columns = (array)$columns;
        $columns = array_column($columns, 'column');
        $resultArray = [];
        $model = DB::table($table)->select($columns)->where('id','!=',1)->get()->toArray();
        $tmpAry = [];
        $max =0;
        foreach($model as $k => $v){
            
            $tmpAry[] = (array)$v;
        }
        
        $index = 0;
        foreach($columns as $key => $value){           
            $filter = [];
            if($columnsWithType['filter_'.$index]['type'] == 'range'){
               
                $allData = array_column($tmpAry, $value);
                $min = min($allData);
                $max = max($allData);
                $filter['column_name'] = $columnNames[$value];
                $filter['column_min'] = (int)$min;
                $filter['column_max'] = (int)$max;
                $filter['column_type'] = $columnsWithType['filter_'.$index]['type'];
            }else{
                $filter['column_name'] = $columnNames[$value];
                $filter['column_data'] = array_unique(array_column($tmpAry, $value));
                $filter['column_type'] = $columnsWithType['filter_'.$index]['type'];
            }
            
            $index++;
            $data[$value] = $filter;
        }
     
        return $data;
    }

    protected function apply_filters($request, $dataset_table, $columns){
    	/* 
    	* Sample data array of filters
    	* 
    	*array:2 [▼
		*  "singledrop" => array:1 [▼
		*    0 => array:1 [▼
		*      "column_3" => array:1 [▼
		*        0 => "Designing"
		*      ]
		*    ]
		*  ]
		*  "multipledrop" => array:1 [▼
		*    0 => array:1 [▼
		*      "column_4" => array:2 [▼
		*        0 => "Senior"
		*        1 => "Junior"
		*      ]
		*    ]
		*  ]
		*]
		*/
    	$filterColumns = [];
    	$filterRanges = [];
    	$requested_filters = $request->except(['_token','applyFilter']);
    	$filterKeys = ['dropdown','mdropdown','checkbox','radio'];
    	foreach ($filterKeys as $key) { // $key contains filters key --> singledrop, multidrop etc
    		if(array_key_exists($key, $requested_filters)){ // check if the specific key exist in requested filters
    			foreach ($requested_filters[$key] as $k => $column) { // if key exist in request filter then get that all columns of that key
    				foreach($column as $columnName => $columnValue){
    					// array_filter removing all empty values from $columnValue, in case if user selected "All" in filters
    					$filterColumns[$columnName] = array_filter($columnValue); // create a single array of all selected filters columns
    				}
    			}
    		}
    	}
    	if($request->has('range')){
    		foreach ($request->range as $k => $column) { // if key exist in request filter then get that all columns of that key
				foreach($column as $columnName => $columnValue){
					$exploded_date = explode(';', $columnValue);
					$filterRanges[$columnName] = $exploded_date;
				}
			}
    	}
    	$with_whereIn = false; // with_whereId for check the status if whereIn added in query or not
    	$db = DB::table($dataset_table);
    	foreach($filterColumns as $columnName => $columnsData){
    		if(!empty($columnsData)){ // if $columnData array is empty that means user selected "All" in this filter, so we do not need to add in "whereIn" clause
    			$db->whereIn($columnName, $columnsData); // will create multiple "where in" clause in query 
    			$with_whereIn = true; // set status true if query have where in clause
    		}
    	}
    	if(!empty($filterRanges)){
    		foreach($filterRanges as $column => $values){
    			$db->whereBetween($column, $values);
    		}
    	}
    	if($with_whereIn == true){ // if there is whereIn clause then we need to get the id row also, otherwise select all data from table 
    		$db->orWhere('id',1); // for also get the columns header we need to get first record from datatable
    	}

    	// Finaly it will generate query: "select * from `126_data_table_1495705270` where `column_3` in (?) and `column_4` in (?, ?) or `id` = ?"
    	//dd($db->toSql());
    	
    	return $db->select($columns)->get()->toArray(); // return final query result in the form or array
    	
    }

   
    protected function getSVGMaps($chartMeta){
    	$map = '';
    	$mapId = $this->getMetaValue($chartMeta,'mapArea');
    	$model = CustomMaps::find($mapId);
    	return $model->map_data;
    }

    protected function apply_formula($records, $formula){
    	$records_array = [];
		foreach(json_decode(json_encode($records),true) as $record){ // convert associative array into indexd array
			$records_array[] = array_values($record);
		}
    	if($formula == 'count'){
    		
    		$collection = collect($records_array); // convert simple array to laravel collection
    		$countedArray = [];
    		$index = 0;
    		foreach($collection->groupBy(0) as $key => $value){ // getting data from collection with group by first column or primary column
    			$countedArray[$index][] = $key;
    			$countedArray[$index][] = $value->count();
    			$index++;
    		}
    		return $countedArray;
    	}
    	if($formula == 'addition'){
    		$collection = collect($records_array); // convert simple array to laravel collection
    		$recordsToSum = count(json_decode(json_encode($records[0]),true))-1;
			$preparedArray = [];
			$headers = $collection->pull(0); // get headers from collection
			$index = 0;
			foreach ($collection->groupBy(0) as $key => $value) {
				for($i = 1; $i <= $recordsToSum; $i++){ // recordsToSum contain that how much secondory columns we have selected 
					if($i == 1){
						$preparedArray[$index][] = $key;
					}
					$preparedArray[$index][] = $value->sum($i);
				}
				$index++;
			}
    		$records_array = collect($preparedArray);
    		$records_array->prepend($headers);
    		return $records_array;
    	}

    	if($formula == 'percent'){
    		$columns = count($records_array[0])-1;
    		$collection = collect($records_array);
    		$records_total_array = [];
    		for($i = 1; $i <= $columns; $i++){
    			$records_total_array[$i] = $collection->sum($i);
    		}
    		$headers = $collection->pull(0);
    		$records_array = [];
    		foreach($collection as $key => $value){
    			$tempArray = [];
    			foreach($value as $k => $v){
    				if(array_key_exists($k, $records_total_array)){
    					$tempArray[] = ($v*100)/$records_total_array[$k];
    				}else{
    					$tempArray[] = $v;
    				}
    			}
    			$records_array[] = $tempArray;
    		}
    		$records_array = collect($records_array);
    		$records_array->prepend($headers);
    		return $records_array;
    	}
    }

    protected function string_number_to_numeric($array_data){
    	if(!empty($array_data)){
    		
    		$settings_array = [];
    		foreach($array_data as $key => $value){
    			if(is_array($value)){
    				foreach($value as $k => $v){
    					if(is_numeric($v)){
		    				$settings_array[$key][$k] = (int)$v;
		    			}else{
		    				$settings_array[$key][$k] = $v;
		    			}
    				}
    			}else{
    				if(is_numeric($value)){
	    				$settings_array[$key] = (int)$value;
	    			}else{
	    				if($key == 'colors'){
	    					$explodeColor = explode(',',$value);
	    					$settings_array[$key] = $explodeColor;
	    				}elseif($key == 'legend'){
	    					$legendArray = [];
	    					$legendArray['position'] =  $value;
	    					$settings_array[$key] = $legendArray;
	    				}elseif($key == 'backgroundColor'){
	    					$settings_array[$key] = (array)$value;
	    				}elseif($key == 'isStacked'){
	    					$settings_array[$key] = ($value == 'true')?true:false;
	    				}else{
	    					$settings_array[$key] = ($value != '')?$value:0;
	    				}
	    			}
    			}
    		}
    		return $settings_array;
    		// dd($settings_array);
    	}else{
    		return [];
    	}
    }

	public function embedVisualization(Request $request){
		
		/*$embedModel = Embed::where('embed_token',$request->id)->first();
		
		if(empty($embedModel)){
			$this->put_in_errors_list('Wrong embed token!', true);
		}
		Session::put('org_id',$embedModel->org_id);*/ // putting organization id into session for get the data from models

		$visualization = Visualization::with([

		'dataset','charts'=>function($query){

				$query->with('meta');

		},'meta'])->find($request->id); //getting dataset, visualization charts and meta from eloquent relations

		if($visualization->charts->isEmpty()){ //if there is not chart exist in generated visualization

			$this->put_in_errors_list('No charts found!', true);
		}

		if(empty($visualization->dataset)){

			$this->put_in_errors_list('No dataset found', true);
		}
		$dataset_table = str_replace('ocrm_', '', $visualization->dataset->dataset_table); //getting dataset table name from visualization query
		$drawer_array = [];
		$chartTitles = [];
		$javascript = [];
		$visualization_settings = [];
		$drawer_array['visualization_name'] = $visualization->name;
		$drawer_array['visualization_id'] = $visualization->id;
		$drawer_array['visualization_meta'] = $this->get_meta_in_correct_format($visualization->meta);
		$drawer_array['visualizations'] = [];
		foreach ($visualization->charts as $key => $chart) {
			$columns = [];
			$columns[] = $chart->primary_column;
			foreach(json_decode($chart->secondary_column) as $column){
				$columns[] = $column;
			}
			if($chart->chart_type == 'CustomMap'){

				$viewData_meta = $this->getMetaValue($chart->meta,'viewData');
				$customData_meta = json_decode($this->getMetaValue($chart->meta,'customData'));
				
				$columns[] = $viewData_meta;
				if(!empty($customData_meta)){
					foreach ($customData_meta as $customColumn) {
						$columns[] = $customColumn;
					}
				}
				$columns = array_unique($columns);
			}
			try{
				/*
				*	if request has any filter
				*/
				if($request->has('applyFilter')){
					$dataset_records = $this->apply_filters($request, $dataset_table, $columns);
				}else{
					$dataset_records = DB::table($dataset_table)->select($columns)->get()->toArray(); //getting records with selected columns from dataset table
				}

				$formula = $this->getMetaValue($chart->meta,'formula');
				if($formula != 'no' && $formula != false){
					$dataset_records = $this->apply_formula($dataset_records, $formula);
				}
				$dataset_records = json_decode(json_encode($dataset_records),true); // generating pure array from colection of stdClass object
				$headers = array_shift($dataset_records);
				if($chart->chart_type != 'CustomMap'){
					$lavaschart = Lava::DataTable();
					$index = 0;
					foreach ($headers as $header) { // to add headers into lavacharts datatable
						if($index == 0){
							$lavaschart->addStringColumn($header); //for string header
						}else{
							if($chart->chart_type == 'TableChart'){
								$lavaschart->addStringColumn($header); //for string header
							}else{
								$lavaschart->addNumberColumn($header); //for all numeric headers
							}
						}
						$index++;
					}
				}

				$records_array = [];
				foreach($dataset_records as $record){ // convert associative array into indexd array
					$records_array[] = array_values($record);
				}

				if(!empty($records_array)){ // if after filter or without filter there is no data in records list
					if(!in_array($chart->chart_type, ['CustomMap','ListChart'])){
						$lavaschart->addRows($records_array); // lavachart add only indexed array of arrays (inserting multiple rows in to lavacharts datatable)
						$visualization_settings = $this->getMetaValue($chart->meta,'visual_settings');
						
						if(!empty($visualization_settings) && $visualization_settings != false){
							$visualization_settings = $this->string_number_to_numeric(json_decode($visualization_settings,true));
						}else{
							$visualization_settings = [];
						}
						lava::{$chart->chart_type}('chart_'.$key,$lavaschart)->setOptions($visualization_settings);
					}elseif($chart->chart_type == 'CustomMap'){
						$drawer_array['visualizations']['chart_'.$key]['map'] = $this->getSVGMaps($chart->meta); // get svg maps global or local
						$header_with_column = $headers;
						$headers = array_values($headers);
						$customMapDate = $this->create_map_array($dataset_records, $headers, $chart, $header_with_column);
						$javascript['chart_'.$key] = ['type'=>$chart->chart_type,'id'=>'chart_'.$key,'data'=>$records_array,'headers'=>$headers, 'arranged_data'=>$customMapDate];
					}elseif($chart->chart_type == 'ListChart'){
						$list_array = [];
						foreach($records_array as $ky => $inner_array){
							$list_array[] = array_combine($headers, $inner_array);
						}
						$drawer_array['visualizations']['chart_'.$key]['list'] = $list_array;
					}
					// dd($records_array);
					/*
					* Prepare data for draw visualization
					* on front
					*/
					$chartTitles['chart_'.$key] = $chart->chart_title; //collect all chart titles in single array
					$drawer_array['visualizations']['chart_'.$key]['chart_type'] = $chart->chart_type;
					$drawer_array['visualizations']['chart_'.$key]['title'] = $chart->chart_title;
					$drawer_array['visualizations']['chart_'.$key]['enableDisable'] = $this->getMetaValue($chart->meta,'enableDisable');
				}else{
					$drawer_array['visualizations']['chart_'.$key]['error'] = 'No records found with selected filters';
					//$this->put_in_errors_list('No records found with selected filters');
				}

			}catch(\Exception $e){
				$drawer_array['visualizations']['chart_'.$key]['error'] = $e->getMessage();
				//$this->put_in_errors_list($e->getMessage());
				//throw $e;
			}
		}
		/*
		* Prepare filters for front view
		 */
		$datasetColumns = (array)DB::table($dataset_table)->where('id',1)->first();
		$filter_columns = $this->getMetaValue($visualization->meta,'filters');
		$filters = [];
		if(!empty(json_decode($filter_columns,true))){
			$filters = $this->getFIlters($dataset_table, json_decode($filter_columns, true),$datasetColumns);
		}
		
		// adding selected values of filters in filters array
		$selectedFilters = $request->except(['_token','applyFilter']);
		foreach ($selectedFilters as $type => $array) {
			foreach($array as $indexedkey => $columnNames){
				foreach($columnNames as $colKey => $colArray){
					if($filters[$colKey]['column_type'] == $type){
						$filters[$colKey]['selected_value'] = $colArray;
					}
				}
			}
		}
		// dd($drawer_array);
		//Finaly load view
		return view('organization.visualization.visualization',
								[
									'filters'=>$filters, // contain all filters
									'titles'=>$chartTitles, // contains all titles 
									'visualizations'=>$drawer_array, // data for draw all charts from lava charts
									'javascript'=>$javascript, //data for custom map popup details
									'custom_map_data'=>[] //data for pop click event
								]
					);
	}

	public function create_map_array($records, $headers, $chart, $header_with_column){
		
		$records = collect($records);
		$getFirstPrimaryColumn = collect($records);
		$collectionData = collect($getFirstPrimaryColumn->first());
		$columnForGroup = $collectionData->keys()->first();
	

		$viewData_meta[] = $this->getMetaValue($chart->meta,'viewData');
		$tooltipData = json_decode($chart->secondary_column);
		$popupData = json_decode($this->getMetaValue($chart->meta,'customData'));

		$viewData_array = [];
		$tooltipData_array = [];
		$popupData_array = [];

		$recordsArray = $records->groupBy($columnForGroup)->toArray();
		$index = 0;
		foreach($recordsArray as $key => $record){
			
			foreach($record as $k => $value){
				foreach($viewData_meta as $ck => $column_key){
					$viewData_array[$key][str_replace(' ', '_', $k)] = $value[$column_key];
				}
				foreach ($tooltipData as $ck => $column_key) {
					// dd($value);
					$tooltipData_array[$key][str_replace(' ', '_', $k)][$header_with_column[$column_key]] = $value[$column_key];
				}
				
				foreach ($popupData as $ck => $column_key) {
					$popupData_array[$key][str_replace(' ', '_', $k)][$header_with_column[$column_key]] = $value[$column_key];
				}
				$recordsArray[$key][str_replace(' ', '_', $k)] = array_combine($headers, $value);
				$index++;
			}
		}

		//$viewData_array = collect($viewData_array);
		
		$viewData_array = array_map(function($item){
			return collect($item)->sum();
		}, $viewData_array);

		//Don't remove this, this is working code
		/*$records_array = [];
		foreach ($records as $key => $record) {
			foreach($record as $k => $value){
				if($k != 0){
					$records_array[$record[0]][$headers[$k]][] = $value;
				}
			}
		}*/
		/*dump('View Data Array');
		dump($viewData_array);
		dump('Tooltip Data Array');
		dump($tooltipData_array);
		dump('Popup Data Array');
		dump($popupData_array);
		dump('Final Data Array');
		dd($recordsArray);*/
		return ['view_data'=>$viewData_array, 'tooltip_data'=>$tooltipData_array,'popup_data'=>$popupData_array];
	}
	public function setting_visualization($id)
	{	
		$formModel = [];
		$model = VisualizationMeta::where('visualization_id',$id)->get();
		foreach($model as $key => $value){
			$formModel[$value->key] = $value->value;
		}
		return view('organization.visualization.visualization-setting',['model'=>$formModel]);
	}
	public function user_visualization()
	{
		return view('organization.visualization.users');
	}
	public function getDataByAjax($id, $length){

		$model = Visualization::with('dataset')->find($id);
		$dataset = DB::select('SELECT * FROM '.$model->dataset->dataset_table.' LIMIT 1');
		$dataset = (array)$dataset[0];
		unset($dataset['id']);
		$columns = $dataset;
		$maps = CustomMaps::get();

    	return view('organization.visualization.chart-append',['columns'=>$columns,'length'=>$length])->render();
    }
    public function getFilterByAjax($id)
    {	
    	$model = Visualization::with('dataset')->find($id);
		$dataset = DB::select('SELECT * FROM '.$model->dataset->dataset_table.' LIMIT 1');
		$dataset = (array)$dataset[0];
		unset($dataset['id']);
		$columns = $dataset;
		$maps = CustomMaps::get();
    	return view('organization.visualization.filter-append',['columns'=>$columns])->render();
    }
    public function getDataById($id)
    {
    	$model = Visualization::find($id);
    	return view('organization.visualization.edit-form',compact('model'));
    }
    public function updateVizDetails(Request $request)
    {
    	$data = [];

		$data['name'] = $request['visualization_name'];
		$data['dataset_id'] = $request['select_dataset'];
		$data['description'] = $request['description'];

		$data = Visualization::where('id',$request->id)->update($data);
		return back();
    }
}