@extends('layouts.main')
@section('content')
@php
$page_title_data = array(
	'show_page_title' => 'yes',
	'show_add_new_button' => 'no',
	'show_navigation' => 'yes',
	'page_title' => 'Import Attendance',
	'add_new' => '+ Add Designation'
); 
@endphp
@include('common.pageheader',$page_title_data)



@if(Session::has('success'))
<p class="alert">{{ Session::get('success') }}</p>
@endif


@if(Session::has('error'))
<p class="alert">{{ Session::get('error') }}</p>
@endif

{{-- <div id="add_new_wrapper" class="add-new-wrapper light-blue darken-2 create-fields active" >
{!! Form::open(['route'=>'upload.attendance', "files"=>true , 'class'=> 'form-horizontal','method' => 'post'])!!}

		<div class="row no-margin-bottom ">
			<div class="col s12 m2 l3  input-field">
				{!!Form::text('title',null,['class' => 'validate','placeholder'=>'Enter Title','id'=>'attendence-title','style'=>'color:#fff'])!!}
				<label for="attendence-title">Enter title</label>

			</div>
			<div class="col s12 m2 l5 aione-field-wrapper file-field input-field">
				<div class="btn" style="margin-top: 0px">
			        <span>Choose File</span>
			        <input type="file" name="attendance_file" >
			    </div>
			    <div class="file-path-wrapper">
					{!!Form::text('file',null,['class' => 'file-path validate'])!!}
				</div>
			</div>
			
			<div class="col s12 m3 l4 aione-field-wrapper right-align">
				
					<button class="btn waves-effect waves-light light-blue-text text-darken-2 white darken-2" type="submit" name="action" style="margin-top: 10px;">Upload Attendance
					<i class="material-icons right">save</i>
					</button>
			</div>
		</div>
	{!!Form::close()!!}
</div> --}}
@include('common.pagecontentstart')
@include('common.page_content_primary_start')
<div class="row">
	{!! Form::open(['route'=>'upload.attendance', "files"=>true , 'class'=> 'form-horizontal','method' => 'post'])!!}
	<div class="row" style="padding:10px 0px">
		
		<div class="col l12 aione-field-wrapper">
			{{-- <input type="text" name="" class="aione-setting-field" style="border:1px solid #a8a8a8;margin-bottom: 0px;height: 30px "> --}}
			{!!Form::text('title',null,['class' => 'aione-field','id'=>'attendence-title','placeholder'=>'Enter title'])!!}
		</div>
	</div>

	<div class="row pv-10" >
		{{-- <div class="col l3" style="line-height: 46px">
			Upload
		</div>
		<div class="col l9">
			<div class="file-field input-field" style="margin-top: 0px">
				<div class="btn">
					<span>Choose file</span>
					<input type="file" name="attendance_file">
				</div>
				<div class="file-path-wrapper">
					{!!Form::text('file',null,['class' => 'file-path validate'])!!}
				</div>
			</div>	
		</div> --}}
		{!!Form::file('file',null,['class'=>'no-margin-bottom aione-field file-path validate','placeholder'=>'Select File to Upload','style'=>'border:1px solid #a8a8a8;margin-bottom: 0px;height: 30px'])!!}
	</div>
	<div  class="row">
		<button class="btn blue" type="submit" name="action" style="margin-top: 10px;">Upload Attendance
		
		</button>
	</div>
	{!!Form::close()!!}
</div>
@include('common.page_content_primary_end')
@include('common.page_content_secondry_start')
@include('common.page_content_secondry_end')
@include('common.pagecontentend')
<style type="text/css">
	
		.aione-setting-field:focus{
		border-bottom: 1px solid #a8a8a8 !important;
		box-shadow: none !important;
	}
</style>

@endsection