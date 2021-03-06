@extends('layouts.main')
@section('content')
@php
$page_title_data = array(
	'show_page_title' => 'yes',
	'show_add_new_button' => 'yes',
	'show_navigation' => 'yes',
	'page_title' => 'Salary',
); 
	$id = "";
  
	@endphp	

  @if(@$errors->has())
    <script type="text/javascript">
      $(window).load(function(){
        $('.modal').modal('open');
      });
    </script>
  @endif
  @if(@$data)
    @foreach(@$data as $key => $value)
      @php
        $model = ['name' => $value['name'],'id' => $value['id']];
      @endphp
    @endforeach

    <script type="text/javascript">
      $(window).load(function(){
        alert("hello");
        document.getElementById('modal-edit').click();
      });
    </script>
  @endif
@include('common.pageheader',$page_title_data) 
@include('common.pagecontentstart')
@include('common.page_content_primary_start')
<div>


      {!! Form::open(['route'=>'hrm.salary'])!!}
        {!! Form::selectMonth('month') !!}
        {!! Form::selectRange('year',2016,2030) !!}

      {!! Form::submit()!!}
      
      
      <input type="submit" name='generate' value='generate'>
      
        

      {!! Form::close()!!}  
</div>
	@include('common.list.datalist')
	
@include('common.page_content_primary_end')
@include('common.page_content_secondry_start')
	@if(@$newData == 'undefined' || @$newData == '' || @$newData == null)
		{!! Form::open(['route'=>'store.designation' , 'class'=> 'form-horizontal','method' => 'post']) !!}

	@endif
	@include('common.modal-onclick',['data'=>['modal_id'=>'add_new_model','heading'=>'Add designation','button_title'=>'Save Designation','section'=>'titlesection']])
	 {!!Form::close()!!}
  @if(@$model)
    {!! Form::model(@$model,['route'=>'edit.designation' , 'class'=> 'form-horizontal','method' => 'post']) !!}
      <input type="hidden" name="id" value="{{$data["data"]->id}}">
      <a href="#modal_edit" style="display: none" id="modal-edit"></a>
      @include('common.modal-onclick',['data'=>['modal_id'=>'modal_edit','heading'=>'Edit designation','button_title'=>'update Designation','section'=>'titlesection']])
    {!!Form::close()!!}
  @endif
@include('common.page_content_secondry_end')
@include('common.pagecontentend')

@if(Session::has('error'))
    <h1>{{ Session::get('error') }}helloooo</h1>
@endif
@if(Session::has('error'))
    <script type="text/javascript">Materialize.toast('{{Session::get('error')}}' , 4000)</script>
  @endif
  <style type="text/css">
  	.modal-footer a{
  		font-size: 13px;margin: 8px;display: inline-block;
  	}
  	.modal-footer .save{
  		color: white;background-color: #2196f3;border-color: #2196f3;    padding: 8px 12px;    border-radius: 3px;    cursor: pointer;    font-weight: 400;    text-align: center;vertical-align: middle;
  	}
  	.modal{
  		    overflow-y: hidden;
  		    border-radius: 4px;
  	}
  	.modal-header i{
  		color: #a9a9a9;
  		cursor: pointer;
  	}
  	.modal-header i:hover{
  		color:#676767;
  	}
  

	#style-2::-webkit-scrollbar-thumb
	{
		border-radius: 5px;
		-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
		background-color: #dcdcdc;
	}

  </style>
@endsection