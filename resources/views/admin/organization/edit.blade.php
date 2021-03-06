@extends('admin.layouts.main')
@section('content')
@php
$page_title_data = array(
    'show_page_title' => 'yes',
    'show_add_new_button' => 'no',
    'show_navigation' => 'yes',
    'page_title' => 'Edit Organization',
    'add_new' => '+ Add Designation'
);
@endphp
@include('common.pageheader',$page_title_data) 
@include('common.pagecontentstart')
    @include('common.page_content_primary_start')
   
        @php
            $model = 'App\\Model\\Admin\\GlobalModule';
            $array = json_decode($org_data['modules']);
                $selected = $model::whereIn('id',$array)->pluck('id');
    		unset($org_data['modules']);
    		$org_data['modules'] = $selected;
        @endphp
	{!!Form::model($org_data, ['route' => ['edit.organization', $org_data->id]])!!}
        {{-- @include('admin.organization._form')      --}}
         {!! FormGenerator::GenerateForm('edit_organization_form') !!}           
        <div class="row right-align pv-10">
            {{-- <button type="submit" class="btn btn-primary blue">Update Organization<i class="icon-arrow-right14 position-right"></i>
            </button>   --}}
        </div>    
    {!! Form::close() !!}        
    
    @include('common.page_content_primary_end')
    @include('common.page_content_secondry_start')

    @include('common.page_content_secondry_end')
@include('common.pagecontentend')
@endsection
