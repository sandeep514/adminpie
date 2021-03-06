@extends('layouts.main')
@section('content')
@php
	$page_title_data = array(
	'show_page_title' => 'yes',
	'show_add_new_button' => 'no',
	'show_navigation' => 'yes',
	'page_title' => 'Edit Profile',
	'add_new' => ''
); 
@endphp
@include('common.pageheader',$page_title_data)	
@include('common.pagecontentstart')
	@include('common.page_content_primary_start')
		@include('organization.user._tabs')
		{{-- @php
			$data = json_decode($model->user_type);
			$model['user_type'] = array_map('intval',$data);
		@endphp --}}
		@php
			$processData = $model;
			$data = [];
		@endphp
		@foreach($processData->metas as $key => $meta)
			@php
				$model[$meta->key] = $meta->value;
			@endphp
		@endforeach
		@php
			$roleIds = $model->user_role_rel->pluck('role_id');
			$model['role_id'] = $roleIds;
		@endphp

	{!! Form::model($model,['route'=>['update.userDetails',$model['id']]], ['class'=> 'form-horizontal','method' => 'post','id'=>'save-user-details'])!!}
				{{-- @if(@$model->user_role_rel != null)
					@php
					$model['role_id'] = $model->user_role_rel->pluck('role_id');
					@endphp 	
				@endif --}}
			{{-- @include ('organization.user._form_employee') --}}
			{{-- {!! FormGenerator::GenerateForm('edit_user_form') !!} --}}


			{!! FormGenerator::GenerateForm('organization_edit_user_form') !!}
			@if($form_slug != null)
                {!! FormGenerator::GenerateForm($form_slug,[],null,'org') !!}
       		@endif
			<button type="submit">save</button>
	{!!Form::close()!!}
	@include('common.page_content_primary_end')
	@include('common.page_content_secondry_start')
	
	@include('common.page_content_secondry_end')
@include('common.pagecontentend')
	

@endsection()