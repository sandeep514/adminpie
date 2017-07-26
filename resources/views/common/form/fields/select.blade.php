@if(isset($options['type']))

	@if($options['type'] == 'inset')
		@php
			$model = FormGenerator::GetMetaValue($collection->fieldMeta,'choice_model');
			if($model != false && $model != '' && $model != null){
				$exploded = explode('@',$model);
				$result = new $exploded[0];
				$exploded[1] = str_replace('()', '', $exploded[1]);
			}
			//field_value
			$selectedArray = null;
			if(FormGenerator::GetMetaValue($collection->fieldMeta,'field_value') == 'id'){
				if(!empty(request()->route()->parameters)){
					$selectedArray[] = request()->route()->parameters['id'];
				}
			}
			/*foreach($collection->fieldMeta as $key => $field_class){
				if($field_class->key == 'field_class'){
					dump($field_class);
				}
			}*/
		@endphp
		@if($model != false && $model != '' && $model != null)
			<div class="col s12 m2 l12 aione-field-wrapper">
				{!! Form::select(str_replace(' ','_',strtolower($collection->field_title)),$result->$exploded[1](),$selectedArray,["class"=>"no-margin-bottom aione-field " , 'placeholder'=>FormGenerator::GetMetaValue($collection->fieldMeta,'field_placeholder')])!!}
			</div>
		<div class="error-red">	
			@if(@$errors->has())
				{{$errors->first(str_replace(' ','_',strtolower($collection->field_title)))}}
			@endif
		</div>

		@else
			@php
				$optionValues = json_decode(FormGenerator::GetMetaValue($collection->fieldMeta,'field_options'), true);
				$arrayOptions = array_combine($optionValues['key'], $optionValues['value']);
			@endphp
			<div class="col s12 m2 l12 aione-field-wrapper">
				{!! Form::select(str_replace(' ','_',strtolower($collection->field_title)),$arrayOptions,null,["class"=>"no-margin-bottom aione-field" , 'placeholder'=>FormGenerator::GetMetaValue($collection->fieldMeta,'field_placeholder')])!!}
			</div>
		<div class="error-red">	
			@if(@$errors->has())
				{{$errors->first(str_replace(' ','_',strtolower($collection->field_title)))}}
			@endif
		</div>

		@endif

	@elseif($options['type'] == 'inline')
		<div class="row" style="margin-bottom: 0px;padding: 14px">
            <div class="col l6" style="line-height: 48px;">
                {{ucfirst($collection->field_title)}}
            </div>
            <div class="col l6 right-align aione-field-wrapper">
               	@php
					$optionValues = json_decode(FormGenerator::GetMetaValue($collection->fieldMeta,'field_options'), true);
					$arrayOptions = array_combine($optionValues['key'], $optionValues['value']);
				@endphp
               {!! Form::select(str_replace(' ','_',strtolower($collection->field_title)),$arrayOptions,null,["class"=>"no-margin-bottom aione-field select_2 browser-default  " , 'placeholder'=>FormGenerator::GetMetaValue($collection->fieldMeta,'field_placeholder')])!!}
               
            </div>
        </div>
	@else
		@php
			$selectedArray = null;
			if(FormGenerator::GetMetaValue($collection->fieldMeta,'field_value') == 'id'){
				if(!empty(request()->route()->parameters)){
					$selectedArray[] = request()->route()->parameters['id'];
				}
			}
		@endphp
		<div class="row">
			<div class="col l3" style="line-height: 30px">
				{{$collection->field_title}}
			</div>
			<div class="col l9" >
				@php
					$optionValues = json_decode(FormGenerator::GetMetaValue($collection->fieldMeta,'field_options'), true);
					$arrayOptions = array_combine($optionValues['key'], $optionValues['value']);
				@endphp
				{!! Form::select(str_replace(' ','_',strtolower($collection->field_title)),$arrayOptions,$selectedArray,['placeholder'=>FormGenerator::GetMetaValue($collection->fieldMeta,'field_placeholder')])!!}
			</div>	
		</div>
		
		<div class="error-red">	
			@if(@$errors->has())
				{{$errors->first(str_replace(' ','_',strtolower($collection->field_title)))}}
			@endif
		</div>

	@endif
@else

	@php
		$model = FormGenerator::GetMetaValue($collection->fieldMeta,'choice_model');
		if($model != false && $model != '' && $model != null){
			$exploded = explode('@',$model);
			$result = new $exploded[0];
			$exploded[1] = str_replace('()', '', $exploded[1]);
		}
		$selectedArray = [];
		if(FormGenerator::GetMetaValue($collection->fieldMeta,'field_value') == 'id'){
			if(!empty(request()->route()->parameters)){
				$selectedArray[] = request()->route()->parameters['id'];
			}
		}
	@endphp
	@if($model != false && $model != '' && $model != null)
		<div class="row">
			<div class="col l3" style="line-height: 30px">
				{{$collection->field_title}}
			</div>
			<div class="col l9" >
				{!! Form::select(str_replace(' ','_',strtolower($collection->field_title)),$result->$exploded[1](),$selectedArray,['placeholder'=>FormGenerator::GetMetaValue($collection->fieldMeta,'field_placeholder')])!!}
			</div>	
		</div>
		
		<div class="error-red">	
			@if(@$errors->has())
				{{$errors->first(str_replace(' ','_',strtolower($collection->field_title)))}}
			@endif
		</div>
	@else
		@php
			$selectedArray = null;
			if(FormGenerator::GetMetaValue($collection->fieldMeta,'field_value') == 'id'){
				$selectedArray[] = request()->route()->parameters['id'];
			}
		@endphp
		<div class="row">
			<div class="col l3" style="line-height: 30px">
				{{$collection->field_title}}
			</div>
			<div class="col l9" >
				@php
					$optionValues = json_decode(FormGenerator::GetMetaValue($collection->fieldMeta,'field_options'), true);
					$arrayOptions = array_combine($optionValues['key'], $optionValues['value']);
				@endphp
				{!! Form::select(str_replace(' ','_',strtolower($collection->field_title)),$arrayOptions,$selectedArray,['placeholder'=>FormGenerator::GetMetaValue($collection->fieldMeta,'field_placeholder')])!!}
			</div>	
		</div>
		
		<div class="error-red">	
			@if(@$errors->has())
				{{$errors->first(str_replace(' ','_',strtolower($collection->field_title)))}}
			@endif
		</div>
	@endif
@endif