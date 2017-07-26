
@extends('layouts.app')
@section('content')
	{{-- <form class="modal-body"> --}}
	{!! Form::open(['method' => 'POST','class' => 'modal-body','route' => 'update.pass']) !!}
		<div class="text-center">
			
			<h5 class="content-group" style="font-size: 26px;font-weight: 900;color: grey;margin-top: 0px">Admin<span style="color: #03A9F4">Pie</span></h5>
		</div>
		

		<div class="form-group has-feedback has-feedback-left">
			{{-- <input type="text" class="form-control" placeholder="Username"> --}}
			{!! Form::password('password',['class' => 'form-control' , 'placeholder' => 'Password']) !!}
			@if ($errors->has('password'))
	            <span class="help-block">
	                <strong>{{ $errors->first('password') }}</strong>
		        </span>
	        @endif
			<div class="form-control-feedback">
				<i class="icon-lock2 text-muted"></i>
			</div>
		</div>
		<div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }} has-feedback-left">
			{{-- <input type="text" class="form-control" placeholder="Password"> --}}
			{!! Form::password('confirmPassword',['class' => 'form-control' , 'placeholder' => 'Confirm Password']) !!}
			@if ($errors->has('confirmPassword'))
	            <span class="help-block">
	                <strong>{{ $errors->first('confirmPassword') }}</strong>
		        </span>
	        @endif
	        
			<div class="form-control-feedback">
				<i class="icon-lock2 text-muted"></i>
			</div>
		</div>

		

		<div class="form-group">
			
			{!! Form::button('Proceed<i class="icon-arrow-right14 position-right"></i>',['type'=> 'submit','class' => 'btn bg-blue btn-block']) !!}
		</div>

		
	{!! Form::close() !!}
	<div class="footer">
			© 2017, All Right Reserved. <a href="http://oxosolutions.com/" target="_blank"  style="color: white"><span>OXO solutions</span></a>
	</div>
	<style type="text/css">
		.login-cover{
			    background: url('{{ asset('assets/images/cool-bg.jpg') }}') no-repeat;
    background-size: cover;
		}
		.panel-body{
			position: absolute;
			left: 50%;
			top: 50%;
			margin-left: -160px !important;
			    margin-top: -195px !important;
		}
		.footer{
			    position: fixed;
    bottom: 20px;
    text-align: center;
    color: white;
        background-color: hsla(0,0%,0%,0.3);
    padding: 10px;
		}
	</style>
@endsection