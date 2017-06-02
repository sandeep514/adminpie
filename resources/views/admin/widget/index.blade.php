@extends('admin.layouts.main')
@section('content')
	
<div class="fade-background">
</div>
<div id="projects" class="projects list-view">
	<div class="row">
		<div class="col s12 m9 l9 pr-7" >
			<div class="row no-margin-bottom">
				<div class="col s12 m12 l6  pr-7 tab-mt-10" >
					<!-- <input class="search aione-field" placeholder="Search" /> -->
					<nav>
					    <div class="nav-wrapper">
					      	<form>
						        <div class="input-field">
						          	<input id="search" type="search" required style="background-color: #ffffff">
						          	<label class="label-icon" for="search" style=""><i class="material-icons icon-search" >search</i></label>
						          	<i class="material-icons icon-close">close</i>
						        </div>
					      	</form>
					    </div>
					</nav>
				</div>
				<div class="col s6 m6 l3  aione-field-wrapper pl-7 tab-mt-10">
					<div class="row aione-sort" style="">
						<select class="col  browser-default aione-field" >
							<option value="" disabled selected>Sort By</option>
							<option value="1">Name</option>
							<option value="2">Date</option>
						</select>
						<div class="col alpha-sort" style="width: 25%;padding-left:7px;">
							<a href="javascript:;" class="sort" ><i class="fa fa-sort-alpha-asc arrow_sort white" ></i></a>
						</div>
					</div>
				</div>

				<div class="col s6 m6 l3 pl-7 right-float tab-mt-10 tab-pl-10">
					<div class="row aione-switch-view">
						<ul class="right  views m-0" >
							<li class="inline-block" sty><a href="#list-view" class=" view" data-view="list-view"><i class="material-icons" >view_list</i></a></li>
							
							

							<li class="inline-block" ><a href="#detail-view" class=" view" data-view="detail-view"><i class="material-icons" >view_stream</i></a></li>


							<li class="inline-block" ><a href="#grid-view" class=" view" data-view="grid-view"><i class="material-icons" >view_module</i></a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="list" id="list">

			@if(!empty($data))
				@foreach($data as $key => $val)

				<div class="card-panel shadow white z-depth-1 hoverable project"  >

					<div class="row valign-wrapper no-margin-bottom">
						<div class="col l1 s2 center-align project-image-wrapper">
							<a href="{{-- {{route('add_project_info.project', ['id' => $projVal->id])}} --}}" data-toggle="popover" title=" {{$val->title}}" data-content="TEST">
							{{-- <img src="{{ asset('assets/images/sgs_sandhu.jpg') }}" alt="" class="project-image circle responsive-img">  --}}
							<div class="defualt-logo">
								{{ucwords(substr($val->title, 0, 1))}} 
							</div>
							
							</a>
						</div>
						
						<div class="col l11 s10 editable " >
							<div class="row m-0 valign-wrapper">
								<div class="col s8 m8 l8">
									<input type="hidden" value="{{$val->id}}" class="shift_id" >
									<input type="hidden" name="_token" value="{{csrf_token()}}" class="shift_token" >
									
									<a href="#" data-toggle="popover" title="Popover title" data-content="TEST" >
										<h5 class="project-title black-text flow-text truncate line-height-35">
											<span class="project-name shift_name font-size-14" contenteditable="true" > {{$val->title}}</span>
										</h5>
									</a>
									<a href="{{route('delete.widget',['id'=>$val->id])}}">delete</a>
								</div>
								
								<div class="col s4 m4 l4 right-align">
									
								</div>
							</div>
						</div>
					</div>
						
				</div>
				@endforeach
			@endif
			</div>
		</div>

		<div class="col s12 m3 l3 pl-7" >
			<a id="add_new" href="#" class="btn add-new display-form-button" >
				Add Widget
			</a>
			<div id="add_new_wrapper" class="add-new-wrapper add-form ">
			 {!! Form::open(['route' => 'create.widget' ,'class'=> 'form-horizontal','method' => 'post']) !!}
				

					<div class="row no-margin-bottom">
						<div class="col s12 m2 l12 " style="padding: 10px 0px">
							Title
						</div>
						<div class="col s12 m2 l12 " style="padding: 10px 0px">
							{!! Form::text('title',null,['class' => 'aione-setting-field' , 'style' => 'border:1px solid #a8a8a8;margin-bottom: 0px;height: 30px ;']) !!}
						</div>

						<div class="col s12 m2 l12 " style="padding: 10px 0px">
							Slug
						</div>
						<div class="col s12 m2 l12 " style="padding: 10px 0px">
							{!! Form::text('slug',null,['class' => 'aione-setting-field' , 'style' => 'border:1px solid #a8a8a8;margin-bottom: 0px;height: 30px ;']) !!}
						</div>
						<div class="col s12 m2 l12 " style="padding: 10px 0px">
							Module
						</div>
						<div class="col s12 m2 l12 " style="padding: 10px 0px">
							{!! Form::text('module_id',null,['class' => 'aione-setting-field' , 'style' => 'border:1px solid #a8a8a8;margin-bottom: 0px;height: 30px ;']) !!}
						</div>
						<div class="col s12 m2 l12 " style="padding: 10px 0px">
							Model
						</div>
						<div class="col s12 m2 l12 " style="padding: 10px 0px">
							{!! Form::text('model',null,['class' => 'aione-setting-field' , 'style' => 'border:1px solid #a8a8a8;margin-bottom: 0px;height: 30px ;']) !!}
						</div>
						<div class="col s12 m2 l12 " style="padding: 10px 0px">
							Description
						</div>
						<div class="col s12 m2 l12 " style="padding: 10px 0px">
							{!! Form::textarea('description',null,['rows' => '10' ,'class' => 'materialize-textarea', 'style' => 'border:1px solid #a8a8a8;margin-bottom: 0px;']) !!}
						</div>
						

						<div class="col s12 m6 l12 aione-field-wrapper center-align">
							<button class="btn blue" type="submit">Save Widget
								
							</button>
						</div>
					</div>
				{!!Form::close()!!}

			</div>
			
		</div>
	</div>
</div>
<script type="text/javascript">
	$('.add-new').off().click(function(e){
			e.preventDefault();
			$('.add-new-wrapper').toggleClass('active');
			$('.fade-background').fadeToggle(300);
		});
		
		$('.fade-background').click(function(){
			$('.fade-background').fadeToggle(300);
			$('.add-new-wrapper').toggleClass('active');
		});
</script>
@endsection