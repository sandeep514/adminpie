@extends('admin.layouts.main')
@section('content')

{!! Form::open(['route'=>'activity.template'])!!}
<input type="text" name="use_for"  value="notification">
<ul>
	<li><label for="">Language</label>{!! Form::select('language',['EN'=>'EN','FR'=>'FR'],NULL,['class'=>'','placeholder'=>'select Language'])!!}</li>
	<li><label for="">slug</label><input name="slug"  type="text"></li>
	
	<li>
		<ul>
			<li><h1>For Other </h1><br></li>
			<li><label for="">Male Content</label>
			<input name="template[male][template]" type="text">
			<input name="template[male][type]" value='other' type="hidden">
			<input name="template[male][gender]" value='male' type="hidden">
			</li>
			<li><label for="">Female Content</label>			
				<input name="template[female][template]" type="text"></li>
				<input  name="template[female][type]" value='other' type="hidden">
				<input  name="template[female][gender]" value='female' type="hidden">
		</ul>
</ul>
<input type="submit">

{!! Form::close()!!}

@endsection