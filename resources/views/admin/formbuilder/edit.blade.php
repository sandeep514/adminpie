@if(Auth::guard('admin')->check() == true)
  @php
    $layout = 'admin.layouts.main';
  @endphp
@else
  @php
    $layout = 'layouts.main';
  @endphp
@endif
@extends($layout)
@section('content')

<style type="text/css">
  .add-field-icon{
    color: #7A9BBE;
      padding: 6px;
      margin-left: 10px;
  }
  .add-field-icon i{
        transform: rotate(272deg);
  }
  .add-field-desc{ 
    font-family: Comic Sans MS, sans-serif !important;
    color: #7A9BBE;
    font-size: 12px;
    height: 13px;
    line-height: 1em;
    text-shadow: 0 1px 0 #FFFFFF;
  }
  .add-field-content button{
    float: right;
      margin-right: 13px;
  }
  .field-title {
    font-weight: 700;
  }
  .field-description {
    font-size: 12px;
    color: #696969;
}
input{
  margin-bottom: 0px !important;

}
</style>
{!!Form::model($mdoel,['route'=>'form.store'])!!}
  <!-- main-content-->
  <div class="card" style="margin-top: 0px">
    <div class="row" style="background-color: #24425C;color: white;padding: 15px 10px;font-weight: bold;">
      Form details
    </div>
    <div class="row  valign-wrapper" style="padding: 10px;">
      <div class="col l3 center-align">
        <h6>Form name</h6>
      </div>
      <div class="col l9">
        <input type="text" name="form_name" value="" />
      </div>
    </div>
    <div class="row">
      <div class="col l3">
        <h6>Enter slug name</h6>
      </div>
      <div class="col l9">
        <input type="text" name="form_slug" value="" />
      </div>
    </div>
  </div>
  <div class="card" style="margin-top: 0px;">
  	<div class="content-wrapper">
  		<section class="section-header">
  			
  			<div>
              <table class="bordered centered" style="background-color: transparent;">
                  <thead>
                    <tr class=" " style="background-color: #24425C;color: white">
                      <th style="width: 100px;">Field Order</th>
                      <th class="left-align">Field Label</th>
                      <th>Field Name </th>
                      <th>Field Type</th>
                    </tr>
                  </thead>
                  <tbody class="form-rows">
                    
                  </tbody>
              </table>
  			</div>
  			<div>
  				<p>No fields. Click the + Add Field button to create your first field. </p>
  			</div>
  			<div class="row" style="background-color: #EAF2FA;padding: 9px;">
  				<div class="col l10">
  					<span class="add-field-icon"><i class="fa fa-share" aria-hidden="true"></i></span>
  					<span class="add-field-desc">Drag and drop to reorder</span>
  				</div>
  				<div class="col l2 add-field-content">
  					<button class="btn add-row" type="button">Add Field</button>
  				</div>
  			</div>
        <div class="row">
          <div class="col l12" style="margin: 15px">
            <button class=" btn" type="submit">Save survey</button>  
          </div>
          
        </div>
        
  		</section>
  	</div>
  </div>
{!!Form::close()!!}
<style type="text/css">
  .options{
    display: none;
   position: absolute;
   margin: 0 auto;
   margin-top: 20px;
   left: 20%;
  }
  .option-trigger:hover .options{
    display: block;
  }
</style>
@endsection
