@extends('layouts.app')
@section('content')
	{{-- <form class="modal-body"> --}}
	{!! Form::open(['method' => 'POST','class' => 'modal-body','route' => 'forgot']) !!}
		<div class="text-center">
			
			<h5 class="content-group" style="font-size: 26px;font-weight: 900;color: grey;margin-top: 0px">Admin<span style="color: #03A9F4">Pie</span></h5>
		</div>

		<div class="form-group has-feedback has-feedback-left">
			{{-- <input type="text" class="form-control" placeholder="Username"> --}}
			{!! Form::email('email',null,['class' => 'form-control' , 'placeholder' => 'Email']) !!}
			@if(Session::has('forgot-error'))
				<span style="color: red">{{Session::get('forgot-error')}}</span>
				
			@endif
			<div class="form-control-feedback">
				<i class="icon-user text-muted"></i>
			</div>
		</div>
		

		<div class="form-group login-options">
			<div class="row">
				<div class="col-sm-6">
					
				</div>

				<div class="col-sm-6 text-right">
					<a href="{{ route('org.login.post') }}">Go to Login Page</a>
				</div>
			</div>
		</div>

		<div class="form-group">
			
			{!! Form::button('Reset Password<i class="icon-arrow-right14 position-right"></i>',['type'=> 'submit','class' => 'btn bg-blue btn-block']) !!}
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
			    margin-top: -127px !important;
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