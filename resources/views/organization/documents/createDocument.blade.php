@extends('layouts.main')
@section('content')
	@php
		$page_title_data = array(
			'show_page_title' => 'yes',
			'show_add_new_button' => 'yes',
			'show_navigation' => 'yes',
			'page_title' => 'Create Document',
			'add_new' => 'All Documents',
			'route' => 'documents'
		);
	@endphp	
@include('common.pageheader',$page_title_data) 
@include('common.pagecontentstart')
@include('common.page_content_primary_start')
	<style type="text/css">
		.select2-selection__rendered{
			color: black;
		}
	</style>
	@if(Session::has('success'))
		<script type="text/javascript">
			$(document).ready(function(){
				 Materialize.toast('Document Created Successfully' ,4000 );
			});
		</script>
	@endif
	<div class="row main-dashboard">
		<div class="col l9 pl-7" style="width: 70%;float: left">
			@if(@$document != null)
				{!! Form::model($document,['route' => 'update.documents','method' => 'POST']) !!}
				{!! Form::hidden('id',null) !!}
			@else
				{!! Form::open(['route' => 'save.documents','method' => 'POST']) !!}
			@endif
				{!! FormGenerator::GenerateSection('document',['type'=>'inset']) !!}
					<button type="submit">Create Document</button>
				{!! Form::close() !!}
		</div>

		@if(request()->route()->parameters() != null)
			<div class="col l3 pl-7" style="width: 30%;float: left">

				{!! Form::open(['method' => 'post' , 'route' => 'document.send']) !!}
					<div class="card">
						<div class="content" >
							
							<div class="field">
								<div id="field_send_to" class="field-wrapper field-wrapper-send_to field-wrapper-type-select ">
									<div id="field_label_select_status" class="field-label">
										<label for="input_select_status">
											<h4 class="field-title" id="select_fiellds">Select Fields</h4>
										</label>
									</div>
									<div id="field_send_to" class="field field-type-multi_select">
										{!!Form::select('send_to[]',['all'=>'All','designation'=>'By Designation','department'=>'By Department','shift'=>'By Shifts','roles'=>'By Roles','users'=>'By Users'],null,['class'=>'filter','style' => 'color:black'])!!}

									</div><!-- field -->
								</div>

					            @php
					                if(@$model != null){
					                    $boxClass = (@in_array('designation',@array_keys(@$model->selected_users)))?'':'box';
					                }else{
					                    $boxClass = 'box';
					                }
					            @endphp
							</div>


				            <div class="applyed-filters hidden designation">
				            	<div id="field_send_to" class="field-wrapper field-wrapper-send_to field-wrapper-type-select ">
									<div id="field_label_select_status" class="field-label">
										<label for="input_select_status">
											<h4 class="field-title" id="select_fiellds">Select designation</h4>
										</label>
									</div>
									<div id="field_send_to" class="field field-type-multi_select">
					            	{!!Form::select('users[designation][]',$params['designations'],@$model->selected_users['designation'],['class'=>'designation '.$boxClass,'multiple'=>'multiple'])!!}
							        </div>
							    </div>
					            @php
					                if(@$model != null){
					                    $boxClass = (@in_array(@$params['department'],@array_keys(@$model->selected_users)))?'':'box';
					                }else{
					                    $boxClass = 'box';
					                }
					            @endphp
				            </div>

							<div class="applyed-filters hidden department">
								
					            <div id="field_send_to" class="field-wrapper field-wrapper-send_to field-wrapper-type-select ">
									<div id="field_label_select_status" class="field-label">
										<label for="input_select_status">
											<h4 class="field-title" id="select_fiellds">Select department</h4>
										</label>
									</div>
									<div id="field_send_to" class="field field-type-multi_select">
					            	{!!Form::select('users[department][]',$params['departments'], @$model->selected_users['department'], ['class'=>'department '.$boxClass,'multiple'=>'multiple'])!!}
							        </div>
							    </div>

					            @php
					                if(@$model != null){
					                    $boxClass = (@in_array('shifts',@array_keys(@$model->selected_users)))?'':'box';
					                }else{
					                    $boxClass = 'box';
					                }
					            @endphp
					        </div>


							<div class="applyed-filters hidden shift">
					            <div id="field_send_to" class="field-wrapper field-wrapper-send_to field-wrapper-type-select ">
									<div id="field_label_select_status" class="field-label">
										<label for="input_select_status">
											<h4 class="field-title" id="select_fiellds">Select shifts</h4>
										</label>
									</div>
									<div id="field_send_to" class="field field-type-multi_select">
					            	{!!Form::select('users[shift][]',$params['shifts'],@$model->selected_users['shifts'],['class'=>'shifts '.$boxClass,'multiple'=>'multiple'])!!}
							        </div>
							    </div>
					            @php
					                if(@$model != null){
					                    $boxClass = (@in_array('roles',@array_keys(@$model->selected_users)))?'':'box';
					                }else{
					                    $boxClass = 'box';
					                }
					            @endphp
					        </div>

							<div class="applyed-filters hidden roles">
								
					            <div id="field_send_to" class="field-wrapper field-wrapper-send_to field-wrapper-type-select ">
									<div id="field_label_select_status" class="field-label">
										<label for="input_select_status">
											<h4 class="field-title" id="select_fiellds">Select roles</h4>
										</label>
									</div>
									<div id="field_send_to" class="field field-type-multi_select">
					            	{!!Form::select('users[roles][]',$params['roles'],@$model->selected_users['roles'],['class'=>'roles '.$boxClass,'multiple'=>'multiple'])!!}
							        </div>
							    </div>
					            @php
					                if(@$model != null){
					                    $boxClass = (@in_array('users',@array_keys(@$model->selected_users)))?'':'box';
					                }else{
					                    $boxClass = 'box';
					                }
					            @endphp
					        </div>

				            <div class="applyed-filters hidden users">
				            	
					            <div id="field_send_to" class="field-wrapper field-wrapper-send_to field-wrapper-type-select ">
									<div id="field_label_select_status" class="field-label">
										<label for="input_select_status">
											<h4 class="field-title" id="select_fiellds">Select users</h4>
										</label>
									</div>
									<div id="field_send_to" class="field field-type-multi_select">
					            	{!!Form::select('users[users][]',$params['users'],@$model->selected_users['users'],['class'=>'users '.$boxClass,'multiple'=>'multiple'])!!}
							        </div>
							    </div>

							</div>

							<input type="hidden" name="document_id" value="{{ request()->route()->parameters()['id'] }}">
						</div>
						<button type="submit">Send</button>
					</div>
				{!! Form::close() !!}

			</div> 
		@endif
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.hidden').hide();
			$(document).on('change','.filter',function(e){
				e.preventDefault();
				e.stopPropagation();

				var value = $(this).val().toString();
				var arrayValue = value.split(',');
				$(arrayValue).each(function(k,v){
					console.log(v);
					if(v != null || v != ''){
						
						$('.content').find('.'+v).parents('.card').find('.applyed-filters').hide();
						$('.content').find('.'+v).removeClass('hidden').show();
					}
					if(v == 'all'){
						$('.applyed-filters').hide();
					}
				});
			});


			$(document).on('click','.select2-selection__choice__remove',function(e){
				e.preventDefault();
				e.stopPropagation();

				var value = $('.filter').val().toString();
				var arrayValue = value.split(',');
				$(arrayValue).each(function(k,v){
					
					$('.content').find('.'+v).addClass('hidden').hide();

				});
			});
		});
	</script>
@endsection
