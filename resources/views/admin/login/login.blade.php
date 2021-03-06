@extends('layouts.login')
@section('content')
@include('common.pagecontentstart')
@include('common.page_content_primary_start')

<div> 
	<div class="aione-row" >
		<div class="site-logo" >
		</div>
		<div class="site-title" >
			Admin<span>Pie</span>
		</div>
		<div class="site-tagline" >
		</div>
	@if(Session::has('login_fails'))
	<div class="row error">
		<span><i class="fa fa-ban"></i></span>
		{{Session::get('login_fails')}}<a href="">recover your password</a> 
	</div>
	@endif
	{!! Form::open(['method' => 'POST','class' => 'modal-body','route' => 'org.login.post']) !!}
	{!! FormGenerator::GenerateForm('organization_user_login_form',['type'=>'inset'])!!}
	@if(session()->has('csrf_error'))
		<div style="text-align: center; color: red;">{{session('csrf_error')}}</div>
	@endif
	{!!Form::close()!!}

	{{-- @include('components._footer') --}}

	</div>
	</div> 
</div> 
@include('common.page_content_primary_end')
@endsection