@extends('layouts.main')


@section('content')
	@if(@$errors->has())
	<script type="text/javascript">
		$(window).load(function(){
			document.getElementById('add_new').click();
		});
	</script>
	@endif
	
@php
	$page_title_data = array(
	'show_page_title' => 'yes',
	'show_add_new_button' => 'yes',
	'show_navigation' => 'yes',
	'page_title' => 'Job Openings',
	'add_new' => '+ Add Job Opening',
	'route' => 'opening.create'
); 

	 
@endphp

@if(Session::has('error'))
<div class="alert alert-info red" >{{ Session::get('error') }}</div>
@endif
@include('common.pageheader',$page_title_data) 
@include('common.pagecontentstart')
@include('common.page_content_primary_start')
@include('common.list.datalist')
@include('common.page_content_primary_end')
@include('common.page_content_secondry_start')

{{-- {!! Form::open(['route'=>'store.employee' , 'class'=> 'form-horizontal','method' => 'post'])!!}
@include('common.modal-onclick',['data'=>['modal_id'=>'add_new_model','heading'=>'Add Employee','button_title'=>'Save Employee','section'=>'addempsec1']])
{!!Form::close()!!} --}}
@include('common.page_content_secondry_end')
@include('common.pagecontentend')
@endsection