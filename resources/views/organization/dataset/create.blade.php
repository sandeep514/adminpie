@extends('layouts.main')
@section('content')

@php

	$page_title_data = array(
	'show_page_title' => 'yes',
	'show_add_new_button' => 'yes',
	'show_navigation' => 'yes',
	'page_title' => 'Add Dataset',
	'add_new' => 'All Datasets',
	'route' => 'list.dataset'
	); 
@endphp

@include('common.pageheader',$page_title_data) 
@include('common.pagecontentstart')
	@include('common.page_content_primary_start')
		{!! Form::open(['route'=>'save.dataset' , 'class'=> 'form-horizontal','method' => 'post'])!!}
			<div class="row no-margin-bottom">
									
			{{-- 	<div class="row">
					<div class="col l12" style="line-height: 30px">
						Dataset Name
					</div>
					<div class="col l12">
						<input type="text" name="dataset_name" class="aione-setting-field" style="border:1px solid #a8a8a8;margin-bottom: 0px;height: 30px ">
						@if ($errors->has('dataset_name'))
		                    <span class="help-block" style="color: red;">
		                        <strong>{{ $errors->first('dataset_name') }}</strong>
		                    </span>
		                @endif
					</div>
				</div>
				<div class="row">
					<div class="col l12" style="line-height: 30px">
						Description
					</div>
					<div class="col l12">
						 <textarea id="textarea1" name="dataset_description" class="materialize-textarea" style="border:1px solid #a8a8a8;margin-bottom: 0px;"></textarea>
						 @if ($errors->has('dataset_description'))
		                    <span class="help-block" style="color: red;">
		                        <strong>{{ $errors->first('dataset_description') }}</strong>
		                    </span>
		                @endif
					</div>
				</div>
				 --}}
				 {!! FormGenerator::GenerateForm('add_dataset_form') !!}
				{{-- <div class="col s12 m6 l12 aione-field-wrapper">
					<button class="btn blue" type="submit">Save
								</button>
				</div> --}}
			</div>
		{!!Form::close()!!}	
	@include('common.page_content_primary_end')
	@include('common.page_content_secondry_start')
	@include('common.page_content_secondry_end')
@include('common.pagecontentend')

@endsection