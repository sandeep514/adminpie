@php 
  $link=$_SERVER['REQUEST_URI'];
@endphp

<div class="col l12"  >
    <ul class="aione-tabs">
        <li class="tab col aione-active {{strpos($link, 'edit')?'aione-active':''}}"><a href="{{Route('profile.edit')}}">General Info</a></li>
        <li class="tab col {{strpos($link, 'personal-detail')?'aione-active':''}}""><a href="javascript:;">Personal Details</a></li>
        <li class="tab col {{strpos($link, 'bank-detail')?'aione-active':''}}""><a href="javascript:;">Banking Details</a></li>
        <li class="tab col {{strpos($link, 'changepassword')?'aione-active':''}}""><a href="{{ route('profile.changepassword') }}">Change Password</a></li>
    </ul>
</div>
<style type="text/css">

   .aione-tabs{
      border-bottom: 1px solid #e8e8e8;
      padding-bottom: 4px;
      padding: 0px;
      margin: 0px;
   }
   .aione-tabs > .tab{
     

   }
   .aione-tabs > .tab:hover{
      background-color: #e8e8e8;
          border-bottom: 1px solid #EEE;
   }
   .aione-tabs > .tab > a{
    padding: 0px 20px; 
    line-height: 40px;
    display: inline-block; 
    color: #0073aa;
   }
   .aione-active{
      border: 1px solid #e8e8e8;
      border-bottom: 1px solid #fff;
      margin-bottom: -1px;
   }
   .aione-active a{
      color: black !important;
      font-weight: 500
   }
   .aione-progress-bg {
    background: #f2f2f2;
    min-height: 4px;
  }
    .percent{
        display: none;
    }
   .progress-bar-wrapper{
        width: 80%;background-color: #e8e8e8;margin-top: 10px;overflow: hidden;border-radius:8px ;position: absolute;
   }
   .progress-bar-wrapper > .accomplished{
        background-color: #2196F3;line-height: 5px;font-size:10px;width: 10%;color: white;text-align: right;padding-right: 10px
   }
   
   .progress-bar-wrapper:hover .percent{
        display: flex;
        padding: 8px 0px 2px 0px;
   }
  



.aione-progress-inside {
    width: 80%;
    height: 4px;
    background: #22adba;
    background: linear-gradient( to right, rgba(255, 255, 255, 0),rgba(255, 255, 255, 0.05) 99%,#eee 100% ),linear-gradient(90deg,#2196F3,#2196F3,#2196F3);
    background-size: 10% 100%, 100% 100%;
    cursor: pointer;
}
.aione-progress-text{
    font-size: 10px;
    color: white;
    padding:5px;
}

</style>
<script type="text/javascript">
  $(document).ready(function(){
    $(document).on('click','.aione-tabs .tab',function(){
      $(this).addClass('aione-active');
      $(this).siblings().removeClass('aione-active');

    });
    $(document).on('click','.one-tabs .tab',function(){
      $(this).addClass('aione-active');
      $(this).siblings().removeClass('aione-active');

    });
    $(document).on('click','.aione-progress-inside',function(e){
      $('.aione-progress-inside').css({'height':'14px'});
      $('.aione-progress-text').css({'display':'block'});
        e.stopPropagation();
    });   
    $(document).on('click','body',function(){
      $('.aione-progress-inside').css({'height':'4px'});
      $('.aione-progress-text').css({'display':'none'});
        
    }); 

  });
</script>
