@extends('layouts.main')
@section('content')
<style type="text/css">
.active
{
	background-color: #005A8B;
	-webkit-background-color: #005A8B;
}
td
{
	line-height:25px;
	border-radius:0px;
	border:1px solid #fff;
}
td:nth-child(1)
{
	border:none;
}
td:nth-child(2)
{
	border:none;
}
.design-bg
{
	padding:0px;
	background-color: #fff;
}
.aione-navigation
{
	padding:0px 10px;
}
.fa-calendar
{
	font-size: 25px;

}
.main-container
{
	border-top:1px solid #e8e8e8;
	margin-top:15px;
}
.design-style
{
	font-size: 18px;
	line-height:50px;
}
.fa
{
	font-size:18px;
}
a{
	color:black;
}
.nav:hover
{
	color:#2196F3;

}
.sunday
{
	background-color:#fc2065;
}
</style>
<script type="text/javascript">
	
</script>
	<div id="att_data" class="card" style="margin-top: 0px;padding:10px">
	<input id="token" type="hidden" name="_token" value="{{csrf_token()}}" >
		<div id="add_new_wrapper" class="add-new-wrapper light-blue darken-2 create-fields" >
		{!! Form::open(['route'=>'upload.attendance', "files"=>true , 'class'=> 'form-horizontal','method' => 'post'])!!}

				<div class="row no-margin-bottom ">
					<div class="col s12 m2 l3  input-field">
						{!!Form::text('title',null,['class' => 'validate','placeholder'=>'Enter Title','id'=>'attendence-title','style'=>'color:#fff'])!!}
						<label for="attendence-title">Enter title</label>

					</div>
					<div class="col s12 m2 l5 aione-field-wrapper file-field input-field">
						<div class="btn" style="margin-top: 0px">
					        <span>Choose File</span>
					        <input type="file" name="attendance_file" >
					    </div>
					    <div class="file-path-wrapper">
							{!!Form::text('file',null,['class' => 'file-path validate'])!!}
						</div>
					</div>
					
					<div class="col s12 m3 l4 aione-field-wrapper right-align">
						
							<button class="btn waves-effect waves-light light-blue-text text-darken-2 white darken-2" type="submit" name="action" style="margin-top: 10px;">Upload Attendance
							<i class="material-icons right">save</i>
							</button>
					</div>
				</div>
			{!!Form::close()!!}
		</div>
		<div id="projects" class="projects list-view">
			<div class="row ">
				<div class="col s12 m12 l6 " >
					<ul class="class-list" style="margin: 0px;margin-top: 4px">
						<li style="display: inline-block;"><a style="margin-top: 0px" onclick="showHide('month')"  class="btn monthly">Monthly</a></li>
						
						<li style="display: inline-block;"><a style="margin-top: 0px"  class="btn weekly">Weekly</a></li>

						<li style="display: inline-block;"><a style="margin-top: 0px" class="btn daily" >Daily</a></li>
					</ul>
				</div>

				<div class="col s12 m12 l6 right-align">
					
					<a id="add_new" href="#" class="btn add-new" style="width: 50%;margin-top: 4px;background-color: #0288D1">
						Import Attendence
					</a>
				</div>
			</div>
		</div>
	
		<div id="main" class="main-container">
		
		</div>
	</div>
	<script type="text/javascript">
	$(document).ready(function(){
		attendance_list();

		$(".monthly").click(function(){
			$(this).addClass("active");
			$(".weekly").removeClass("active");
			$(".daily").removeClass("active");
		});
			$(".weekly").click(function(){
			$(this).addClass("active");
			$(".monthly").removeClass("active");
			$(".daily").removeClass("active");
		});
			$(".daily").click(function(){
			$(this).addClass("active");
			$(".monthly").removeClass("active");
			$(".weekly").removeClass("active");
		});
	});
	function showHide(show)
	{
		$("#"+show).show();
	}

	function attendance_list()
	{
		$.ajax({
				url:route()+'/attendance/list',
				type:'Get',
				success: function(res){
					$("#main").html(res);
					console.log('data sent successfull');
					$("#month , #week ,#days").hide();

				}
			});
	}

	function attendance_filter(date, week, mo, yr)
	{
		
		var postData = {};
		postData['date'] = date;
		postData['week'] = week;
		postData['month'] = mo;
		postData['years'] = yr;
		postData['_token'] = $("#token").val();
		$.ajax({
				url:route()+'/attendance/list',
				type:'POST',
				data:postData,
				success: function(res){
					$("#main").html(res);
					$("#month , #week ,#days").hide();

					if(date)
					{
						$("#days").show();
					}else if(week){
						$("#week").show();
					}else{
						$("#month").show();
					}
					
				
					console.log('data sent successfull');
				}
			});
		}


	</script>
	
@endsection()