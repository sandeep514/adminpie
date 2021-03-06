@extends('layouts.main')
@section('content')
@php
	$page_title_data = array(
	'show_page_title' => 'yes',
	'show_add_new_button' => 'yes',
	'show_navigation' => 'yes',
	'page_title' => 'Dashboards',
	'add_new' => '+ Add Dashboard'
	); 
@endphp
<style type="text/css">
.aione-widgets{
	position: relative;
	display: block;
}
.aione-widgets .aione-widget{
	border: 1px solid #e8e8e8;
	background-color: #FFFFFF;
	color: #168dc5;
}
.aione-widgets .aione-widget .aione-widget-footer{
	display:none;
}
</style>

@include('common.pageheader',$page_title_data) 
@include('common.pagecontentstart')
@include('common.page_content_primary_start')
@include('organization.dashboard._tabs')







<div class="aione-dashboard">
    <div class="aione-widgets">

    	@foreach($widgets as $widget_key => $widget)
    		@php
				$widget_id = $widget->id;
				$widget_key = $widget->slug;
				$widget_title = $widget->title;
			@endphp

	    	<div id="aione_widget_{{$widget_key}}" class="aione-widget aione-widget-{{$widget_key}} aione-widget-id-{{$widget_id}}">
	    		<div class="aione-widget-header">
	    			<div class="aione-widget-handle"></div>
	    			<div class="aione-widget-title">{{$widget_title}}</div>
	    			<div class="aione-widget-minimize"></div>
	    			<div class="aione-widget-delete"></div>
	    		</div>
	    		<div class="aione-widget-content">
	    			@if(View::exists('organization.widgets.'.$widget_key))
	    				@include('organization.widgets.'.$widget_key)
	    			@else 
	    				<div class="aione-widget-content">

	    					{{ __('messages.widget-view-misssing') }}
	    				</div>
	    			@endif
	    		</div>
	    		<div class="aione-widget-footer"></div>
	    	</div> <!-- .aione-widget -->

    	@endforeach

    </div> <!-- .aione-widgets -->
</div> <!-- .aione-dashboard -->




		<div class="row">
			@php
				$count = [];
			@endphp
			@foreach($widgets as $widgetKey => $widget)
				@php
					$count[] = $widget->id;
					// dump($widget['widgets']);

				@endphp
			@endforeach
			@php
				$isAdmin = in_array('administrator',get_user_roles());
				if(count(request()->route()->parameters()) >0 ){
					$current_dashboard_id = request()->route()->parameters()['id'];
				}
				else{
					$current_dashboard_id = "";
				}
				

				
			@endphp

			<div id="sortable_1">
			<pre>
			{{print_r($widgets->toArray())}}
			</pre>
			
			@foreach($widgets as $widgetKey => $widget)
					@php
						$file = $widget->slug;
					@endphp
					@if(View::exists('organization.widgets.'.$file))
						@php

							if($isAdmin){
								$widget['widgets'] = $widget;
							}
						@endphp
						
						<div class="ui-state-default widget-wrapper col l3 pr-14">
								@php
									$slug = request()->route()->parameters()['id'];
									if(Auth::guard('admin')->check()){
										$id = Auth::guard('admin')->user()->id;
									}else{
										$id = Auth::guard('org')->user()->id;
									}
									$widget_id = $widget['id'];
								@endphp
							@include('organization.widgets.'.$file , ['data'=>['widgets'=>$widget],'count' => count($count),'isAdmin'=>$isAdmin])
								{{-- {{route('delete.widget.dashboard',[$slug,$id,$widget_id])}} --}}
								<input type="hidden" name="slug" value="{{$slug}}">
								<input type="hidden" name="widget_id" value="{{$widget_id}}">
							<a href="javascript:;" class="delete-widget"><i class="material-icons dp48">clear</i></a>
						</div>
						
					@endif
				{{-- @endif --}}
			@endforeach
			</div>
			<div class="col l3">
				<div class="add-widget row" data-target="add-widget">
					<div class="col l12 center-align plus-sign" style="">
						+
					</div>
					<div class="col l12 center-align">
						Add New Widget
					</div>
				</div>
			</div>
			{{-- @include('common.modal-onclick',['data'=>['modal_id'=>'add-widget','heading'=>'Add Widget','button_title'=>'Save','section'=>'widsec1']]) --}}
			<div id="add-widget" class="modal modal-fixed-footer" style="overflow-y: hidden;">
				<div class="modal-header white-text  blue darken-1" ">
					<div class="row" style="padding:15px 10px;margin: 0px">
						<div class="col l7 left-align">
							<h5 style="margin:0px">Add Widget</h5>	
						</div>
						<div class="col l5 right-align">
							<a href="javascript:;" name="closeModel" onclick="close()" id="closemodal" class="closeDialog close-model-button " style="color: white"><i class="fa fa-close"></i></a>
						</div>	
					</div>
				</div>
				
				{{Form::open(['method' => 'post' , 'route' => 'update.dashboard.widget' ])}}
					<div class="modal-content" style="padding: 20px;padding-bottom: 60px">
					{!! Form::select('widget[]',@$listWidgets,null,["class"=>"no-margin-bottom aione-field " , 'placeholder'=> 'Select Widget','field_placeholder','multiple'=>true])!!}
						<input type="hidden" name="slug" value="{{@Request()->route()->parameters()['id']}}" class="slug-parameter">
						{!! csrf_field() !!}

					</div>
					<div class="modal-footer">
						<button class="btn blue " type="submit" name="action">Add</button>
					</div>	
				{{Form::close()}}
			</div>
			
		</div>
	</div> 
	<div class="row">	
		<div class="col l6 pr-7">
			<div class="card center-align chk-n-out" >
				<input id="token" type="hidden" name="_token" value="{{csrf_token()}}" >
				<input type="hidden" class="status" value="{{@$check_in_out_status}}" >
				<button href="javascript:;" status="check_in" class="checkInOut blue aione-btn" id="check_in" style="">
					<span>
						<span >
							<i class="fa fa-clock-o" style="font-size: 22px;"></i>
						</span>
						<span>
								<span style="font-size: 18px;margin-left: 5px">Check-In</span>
						</span>
					</span>
				</button>
				
				<button  status="check_out" class="checkInOut grey darken-2" id="check_out" style="display: inline-block;color: white;margin: 0 auto;padding: 8px 20px">
					<span>
						<span>
							<i class="fa fa-clock-o" style="font-size: 22px;"></i>
						</span>
						<span>
							<span style="font-size: 18px;margin-left: 5px">Check-Out</span>
						</span>
					</span>
				</button>
			</div>

		</div>
		
		
	</div>
	<div class="row">
		<div class="col l3">
			<div class="card shadow mt-0" style="border:1px solid #e1e1e1">
				<div class="center-align aione-widget-header" ><h5 class="m-0"><a href="#">Working Hours</a></h5></div>
				<div class="count">
					<span ><time id="timer">00:00:00</time> Hrs</span>
				</div>
				<div class="in-out-button">

					<a href="#" id="start">
						<i class="material-icons dp48">access_alarm</i>
						<div>
							<div class="check-in" style="font-size: 26px;color: white">
								Check In
							</div>
							<div class="check-out" style="font-size: 26px;color: white">
								Check Out
							</div>

							<div style="color: white;font-size: 14px;line-height: 7px;">
								<div class="" id="clock_1"></div>
							</div>
						</div>
					</a>
				</div>
			</div>
		</div>




@include('common.page_content_primary_end')
@include('common.page_content_secondry_start')

	@include('common.modal-onclick',['data'=>['modal_id'=>'edit-dashboard','heading'=>'Edit Dashboard','button_title'=>'Save Data','section'=>'dashboard']])

	<style type="text/css">

		.widget-wrapper {
			position: relative;
			display: block;
		}
		.widget-wrapper > a{
			position: absolute;
			top: 22px;
			right: 30px;
			display: none;
		}
		.widget-wrapper > a > i{
			font-size: 20px;
    		color: #979797;
    		
		}
		.widget-wrapper:hover  a {
			display: block;
		}
		
	</style>
	<script type="text/javascript">
	console.log($('.widget-wrapper input[name=slug]').val());
	 $( function() {
	    $( "#sortable" ).sortable({
	    	axis: "x",
	      items: "li:not(.unsortable)",
	      update : function(){
	      	var dashboard_index = [];
	      	$('.dashboard-tab').each(function(){
	      		dashboard_index.push($(this).attr('dashboard-index'));
	      	});
	      	$.ajax({
	      		url : route()+'sort/dashbaord',
	      		type : "POST",
	      		data : {
	      			data : dashboard_index,
	      			_token : $("#token").val()
	      		},
	      		success : function (res) {
	      			console.log(res);
	      		}
	      	});
	      }
	    });
	  } );
	 // $(document).on('click','.delete-dashboard',function(){
	 // 	var tabSlug = $(this).attr('tab-slug');
	 // 	// console.log(tabSlug);

		// 	$('.aione-tabs .dashboard-tab').each(function(){
		// 		if($(this).attr('dashboard-index') == tabSlug){
		// 			// console.log($(this).);
		// 		}

		// 	});

	 // 	$.ajax({
	 //      		url : route()+'delete/dashboards',
	 //      		type : "POST",
	 //      		data : {
	 //      			slug : tabSlug,
	 //      			_token : $("#token").val()
	 //      		},
	 //      		success : function (res) {
	 //      			if(res == 'true'){
	 //      			}
	 //      			console.log(res);
	 //      		}
	 //      	});
	 // });
	 $(document).on('click','.edit-dashboard',function(){
	 	
	 	var tabSlug = $('.slug-parameter').val();
	 	$.ajax({
	      		url : route()+'edit/dashboards',
	      		type : "POST",
	      		data : {
	      			slug : tabSlug,
	      			_token : $("#token").val()
	      		},
	      		success : function (res) {
	      			if(res == 'true'){
	      			}
	      			 $('#edit-dashboard').modal('open');
	      			 $('#edit-dashboard').find('input[name=title]').val(res.title);
	      			 $('#edit-dashboard').find('textarea[name=description]').val(res.description);
	      			 $('#edit-dashboard').find('input[name=slug]').val(res.slug);
	      			console.log(res);
	      		}
	      	});
	 });
	  $(document).on('click','#edit-dashboard button[type=submit]',function(){

	 	var updated_data = {title : $('#edit-dashboard input[name=title]').val(),
							 	description : $('#edit-dashboard textarea[name=description]').val(),
							 	slug : $('#edit-dashboard input[name=slug]').val(),
							 	old_slug : $('.slug-parameter').val()
							 }
	 	
	 	$.ajax({
	      		url : route()+'update/dashboards',
	      		type : "POST",
	      		data : {
	      			data : updated_data,
	      			_token : $("#token").val()
	      		},
	      		success : function (res) {
	    			// window.location.href=route()+"dashboard";
	      			
	      		}
	      	});
	 });

	 $( function() {
	    $( "#sortable_1" ).sortable();
	    $( "#sortable_1" ).disableSelection();
	  } );
		 
	$(document).ready(function() {


		
		status = $(".status").val();
		if(status=='check_in')
		{
			$("#check_out").show();
			$("#check_in").hide();
		}else if(status=='not_employ'){
				$(".chk-n-out").hide();
			$("#check_in").hide();
		}else{
			$("#check_out").hide();
			$("#check_in").show();
		}

		$('#calendar').fullCalendar({
			
		});
		
	});

	$(document).on('click','.checkInOut',function(e){

		status = $(this).attr('status');
		postdata ={}; 
		postdata['_token'] = $("#token").val();
		postdata['status'] = status;
		$.ajax({
			url:route()+'hrm/attendance/check_in_out',
			type:'POST',
			data:postdata,
			success:function(res)
			{	
				$("#check_out , #check_in").show();
				 $("#"+status).hide();
				//$("#"+status).hide();
				// if(status=='check_in'){
					
				//  }else{
				// 	$("#check_in").show();
				//  }

				
			}
		});
	});

	// function checkInOut(e)
	// {	
	// 	e.preventDefault();
	// 	 token = $("#token").val();
	// 	$.ajax({
	// 		url:route()+'attendance/check_in_out',
	// 		type:'POST',
	// 		data:{'checkInOut':'check','token':token},
	// 		success:function(res)
	// 		{
	// 			console('success');
	// 		}
	// 	});
		
		
 //    }
 //**********************stop watch********************************8
var h1 = document.getElementById('timer'),
    start = document.getElementById('start'),
    stop = document.getElementById('stop'),
    clear = document.getElementById('clear'),
    seconds = 0, minutes = 0, hours = 0,
    t;

function add() {
    seconds++;
    if (seconds >= 60) {
        seconds = 0;
        minutes++;
        if (minutes >= 60) {
            minutes = 0;
            hours++;
        }
    }
    
    h1.textContent = (hours ? (hours > 9 ? hours : "0" + hours) : "00") + ":" + (minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds);

    timer();
}
function timer() {
    t = setTimeout(add, 1000);
}
// timer();
$(document).on('click','.in-out-button > a' , function(){
	if($(this).attr('id') == 'start'){
		$(this).attr('id','stop');
		timer();
	}else{
		$(this).attr('id','start');
		clearTimeout(t);
	}
});

// /* Start button */
// start.onclick =function(){
// 	timer();
// }

//  Stop button 
// stop.onclick = function() {
//     clearTimeout(t);
// }

/* Clear button */
/*clear.onclick = function() {
    h1.textContent = "00:00:00";
    seconds = 0; minutes = 0; hours = 0;
}*/

 //***********************************************************
	</script>
<style type="text/css">
	.recent-five li{
			padding: 7px 10px;
			width: 100%
		}
		.recent-five li a{
			float: right;
		}
		.mb-10{
			margin-bottom: 10px
		}
		.pr-14{
			padding-right: 14px !important;
		}
		.fix-height{
			min-height: 230px;max-height: 230px
		}
		.back > .card > div{
			margin-bottom: 5px
		}
		.btn-unflip{
			position: absolute;
			top: 0;
			right: 0;
		}
		/*.btn-unflip-2{
			position: absolute;
			top: 0;
			right: 0;
		}
		.btn-unflip-3{
			position: absolute;
			top: 0;
			right: 0;
		}
		.btn-unflip-4{
			position: absolute;
			top: 0;
			right: 0;
		}
		.btn-unflip-5{
			position: absolute;
			top: 0;
			right: 0;
		}*/
		.count span{
			font-size: 32px;
			font-weight: 900;
			color: #8E8E8E;
			padding: 20px 0px;
			display: block;
    		text-align: center;
    		border-bottom: 1px solid #e8e8e8;
		}
		.in-out-button{
			padding: 14px;
			
		}
		.in-out-button a#start{
			display: block;
   			background-color: #00BC9B;
   			padding: 7px 30px;
		}
		.in-out-button a#start .check-out{
			display: none;
		} 
		.in-out-button a#stop .check-in{
			display: none;
		} 
		.in-out-button a#stop{
			display: block;
   			background-color: #d9534f;
   			padding: 7px 30px;
		}
		.in-out-button a i{
			color: white;
			    font-size: 50px;
		}
		.in-out-button a > div{
			display: block;
			float: right;
		}
		.aione-widget-header{
			border-bottom: 1px solid #e8e8e8;cursor: pointer;
		}
		.aione-widget-header a{
			padding: 10px;color: black;display: block
		}
		.aione-widget-content{
			border-bottom: 1px solid #e8e8e8;padding: 10px;font-size: 72px
		}
		.aione-widget-footer{
			padding: 0px 10px
		}
		.aione-widget-footer .all{
			float: left;
			width: 45%;
			font-size: 14px;
			font-weight: 600;
			padding: 10px 5px;
			border: 0px;
			border-radius: 4px;
		}
		.aione-widget-footer .recent{
			float: right;
			width: 54%;
			font-size: 14px;
			font-weight: 600;
			padding: 10px 0px;
			border: 0px;
			border-radius: 4px;
		}
		.mt-0{
			margin-top: 0px;
		}
		.m-0{
			margin: 0px;
		}
		.aione-btn{
			display: inline-block;color: white;margin: 0 auto;padding: 8px 20px;
		}
		.add-btn{
			font-size: 14px;
			font-weight: 600;
			padding: 10px 5px;
			border: 0px;
			border-radius: 4px;	
			width: 100%;

		}
		.add-widget{
			border:2px dashed #e8e8e8;
			margin-top: 10px;
			min-height: 230px;max-height: 230px;
			padding: 38px 20px;
			cursor: pointer;
		}
		.plus-sign{
			width: 100%;
			font-size: 72px;
			font-weight: 800;
			color: #676767;

		}
	</style>

@include('common.page_content_secondry_end')
@include('common.pagecontentend')
@endsection