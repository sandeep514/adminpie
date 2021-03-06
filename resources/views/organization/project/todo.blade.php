@extends('layouts.main')
@section('content')
@php
    $page_title_data = array(
    'show_page_title' => 'yes',
    'show_add_new_button' => 'no',
    'show_navigation' => 'yes',
    'page_title' => 'To Do',
    'add_new' => '+ Add To Do'
); 
@endphp
@include('common.pageheader',$page_title_data) 
@include('common.pagecontentstart')
	@include('common.page_content_primary_start')
	<div>
		@include('organization.project._tabs')
		@include('common.todos')
	</div>
	@include('common.page_content_primary_end')
@include('common.page_content_secondry_start')

@include('common.page_content_secondry_end')
@include('common.pagecontentend')
	<style type="text/css">
		.defualt-logo-todo{
			    width: 36px;
			    line-height: 36px;
			    border: 1px dashed #0288D1;
			    color: white;
			    font-size: 18px;
			    border-radius: 50%;
		}
		.fa-close{
			display: none
		
		
		.todo_list:hover .fa-close{
			display: inline-block;
		}
		.select-dropdown{
			margin: 0px 0px 0px 0px !important;
		}
		.ph-20{
			padding: 0px 20px 0px 20px !important
		}
		.active{
			background-color: none !important;
		}
		.ph-10{
			padding-left: 10px;
			padding-right: 10px;
		}
		.pv-2{
			padding-top:2px;
			padding-bottom: 2px;
		}

		.priority-error:before{
			 content: '';
		    position: relative;
		    display: block;
		   top: -22px;
		 
		    width: 0;
		    height: 0;
		    border-left: 10px solid transparent;
		    border-right: 10px solid transparent;
		    border-bottom: 12px solid #e8e8e8;

		}
	</style>

@endsection