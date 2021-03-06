@extends('layouts.main')
@section('content')
@php
    $page_title_data = array(
    'show_page_title' => 'yes',
    'show_add_new_button' => 'yes',
    'show_navigation' => 'yes',
    'page_title' => 'Credentials',
    'add_new' => '+ Add Credentials'
); 
@endphp
@include('common.pageheader',$page_title_data) 
@include('common.pagecontentstart')
@include('common.page_content_primary_start')
<div>
    @include('organization.project._tabs')
	
	@include('common.list.datalist')

	{!! Form::open(['route' => 'credientals.save' , 'type' => 'POST']) !!}
	<div id="add_new_model" class="modal modal-fixed-footer" style="overflow-y: hidden">
		
		<div class="modal-header white-text  blue darken-1" ">
			<div class="row" style="padding:15px 10px;margin-bottom: 0px">
				<div class="col l7 left-align">
					<h5 style="margin:0px">Create Credential</h5>	
				</div>
				<div class="col l5 right-align">
					<a href="javascript:;" name="closeModel" onclick="close()" id="closemodal" class="closeDialog close-model-button" style="color: white"><i class="fa fa-close"></i></a>
				</div>
					
			</div>
			
		</div>
		<div class="modal-content">
		{!!FormGenerator::GenerateSection('cresec1',['type'=>'inset',@$model])!!}
		{{-- {!!FormGenerator::GenerateSection('cresec2',['type'=>'inset'],@$model)!!} --}}
			<div class="row" style="margin-bottom: 20px">
				<div id="repeat" class="col l12" >
					<div class="row" style="border: 1px solid #e8e8e8;padding: 10px">
						<div>
							<i class="fa fa-close delete-row" style="float: right"></i>
						</div>
						<div class="col s12 m2 l12 aione-field-wrapper">
							 <input class="no-margin-bottom aione-field" placeholder="Title" name="title[]" type="text">
						</div>
						


						<div class="col s12 m2 l12 aione-field-wrapper">
							 <input class="no-margin-bottom aione-field" placeholder="Username or Email" name="email[]" type="text">
						</div>
			


						<div class="col s12 m2 l12 aione-field-wrapper">
							 <input class="no-margin-bottom aione-field" placeholder="Password" name="password[]" type="password" value="">
						</div>

						
					</div>
				</div>
				<div class="col l12">
					<a href="javascript:;" class="btn blue add-row" style="margin-bottom: 30px">Add Row</a>
				</div>
				
			</div>
		</div>
		
		<div class="modal-footer">
			@if(request()->route()->parameters()['id'])
				<input type="hidden" name="project_id" value="{{request()->route()->parameters()['id']}}">
			@endif
			<input type="submit" class="btn blue" name="submit" value="submit">

		</div>
	</div>
	
	{!! Form::close() !!}
		
</div>
@include('common.page_content_primary_end')
@include('common.page_content_secondry_start')

@include('common.page_content_secondry_end')
@include('common.pagecontentend')
<style type="text/css">
	   
    .projects-logo{
        
        background-color: #000;margin: 10%;

    }
    .p-15{
        padding: 15px !important;
    }
    .pv-5{
        padding: 5px 0px !important; 
    }
    .project-logo{
        color: white;width: 70px;margin: 0 auto; line-height: 70px;font-size: 24px;border-radius: 50%
    }
</style>
<script type="text/javascript">
		$(document).ready(function(){
			$('#add_new_model').modal({
				 dismissible: true
			});
			$('.close-model-button').click(function(){
			$("#add_new_model").modal('close');
		});
		})
	
</script>
<script type="text/javascript">
	  $(".add-row").click(function(){
	  		var html = $("#repeat .row").html();
	        $("#repeat").append('<div class="row" style="border: 1px solid #e8e8e8;padding: 10px">'+html+'</div>');
	        $('.delete-row').show();
	    });
	    $("#repeat").on('click','.delete-row',function(){
	        $(this).parent().parent().remove();
	        countAppendedRows();
	    });
	    function countAppendedRows() {
	    	if($('#repeat').find('.row').length == 1 ){
		    	$('.delete-row').remove();
		    }
	    }
	    countAppendedRows();
</script>
@endsection	