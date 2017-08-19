<div class="repeater-group">

	@if($model != null)
		@foreach($model as $key => $value)
			@php
				$defaulValues = [];
			@endphp
			<div class="row repeat-row" style="border:1px dashed #e8e8e8; margin-top: 1%;">
				<div class="row">
					<div class="col l1 offset-l11 right-align">
						<i class="fa fa-close close-delete"></i>
					</div>
				</div>
				<div class="row" style="padding:15px 10px; ">
					<div class="col l12">
						@php
							$defaulValues[] = $key;
							$defaulValues[] = $value;
						@endphp
						@foreach($collection->fields as $secKey => $field)
								@php
									$options['default_value'] = $defaulValues[$secKey];
									$options['from'] = 'repeater';
									$options['section_id'] = $collection->id;
								@endphp
								{!!FormGenerator::GenerateField($field->field_slug, $options,'', $formFrom)!!}
						@endforeach
					</div>
				</div>
			</div>
		@endforeach
	@else
		
	<div class="repeater-wrapper">
		<div class="repeater-row">
		<i class="material-icons dp48 repeater-row-delete">close</i>
		@foreach($collection->fields as $secKey => $field)
			@php
				$options['from'] = 'repeater';
				$options['section_id'] = $collection->id;
			@endphp
				
				{!!FormGenerator::GenerateField($field->field_slug, $options,'', $formFrom)!!}	
		@endforeach	
		</div>
	</div>
		
		
		
	@endif
	
		
	<button type="submit" class="btn add-new-repeater">Add New</button>
	
</div>
<style type="text/css">
	.repeater-wrapper .repeater-row{
		position: relative;
		border: 1px solid #e8e8e8;
    	padding: 20px;
    	margin-bottom: 10px;
	}
	.repeater-wrapper .repeater-row > i{
		position: absolute;
		right: 8px;
		top: 8px;
		color: #757575;
		cursor: pointer;
	}
</style>
