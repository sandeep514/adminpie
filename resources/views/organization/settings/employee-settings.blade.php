@extends('layouts.main')
@section('content')
	@include('organization.settings._header')
	 @include('common.pagecontentstart')
		@include('common.page_content_primary_start')
			@include('organization.settings._tabs')
{{-- 	{!!Form::model($model,['route'=>'save.organization.settings','method'=>'POST','files'=>true])!!} --}}
		{!!FormGenerator::GenerateSection('empsetsec1')!!}
	{{-- {!!Form::close()!!} --}}
		@include('common.page_content_primary_end')
		@include('common.page_content_secondry_start')
		@include('common.page_content_secondry_end')
	@include('common.pagecontentend')
@endsection