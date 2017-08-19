@extends('admin.layouts.main')
@section('content')
@php
$page_title_data = array(
  'show_page_title' => 'yes',
  'show_add_new_button' => 'no',
  'show_navigation' => 'yes',
  'page_title' => 'Form: Organization User Register Form',
  'add_new' => '+ Apply leave'
); 
@endphp
@include('common.pageheader',$page_title_data)
@include('common.pagecontentstart')
@include('common.page_content_primary_start')
    <div class="module-wrapper">
        <div class="list-container">
            <nav id="aione_nav" class="aione-nav aione-nav-vertical">
                <div class="aione-nav-background"></div>
                <ul id="sortable" class="aione-menu">
                     <li class="aione-nav-item level0 has-children  ">
                        <a href="javascript:;">
                            <span class="nav-item-icon " style="background:rgba(0, 0, 0, 0)"><i class="fa fa-list">
                                </i></span>
                            <span class="nav-item-text">
                                List sections
                            </span>
                            <span class="nav-item-arrow"></span>
                        </a>
                        <ul id="sortable_submenu" class="side-bar-submenu">
                          
                        </ul>
                        <i class="material-icons dp48 del">clear</i>    
                    </li>
                    <li class="aione-nav-item level0 has-children  ">
                        <a href="javascript:;">
                            <span class="nav-item-icon " style="background:rgba(0, 0, 0, 0)"><i class="fa fa-amazon">
                                </i></span>
                            <span class="nav-item-text">
                                Section 1
                            </span>
                            <span class="nav-item-arrow"></span>
                        </a>
                        <ul id="sortable_submenu" class="side-bar-submenu">
                            <li class="aione-nav-item level1 ">
                                <a href="http://master.adminpie.com/account/profile">
                                    <span class="nav-item-icon">P</span>
                                    <span class="nav-item-text">Text</span>
                                </a>
                            </li>
                            <li class="aione-nav-item level1 ">
                                <a href="http://master.adminpie.com/account/activities">
                                    <span class="nav-item-icon">A</span>
                                    <span class="nav-item-text">Radio</span>
                                </a>
                            </li>
                            <li class="aione-nav-item level1 ">
                                <a href="http://master.adminpie.com/account/attendance">
                                    <span class="nav-item-icon">A</span>
                                    <span class="nav-item-text">Checkbox</span>
                                </a>
                            </li>
                            <li class="aione-nav-item level1 ">
                                <a href="http://master.adminpie.com/account/performance">
                                    <span class="nav-item-icon">P</span>
                                    <span class="nav-item-text">Textarea</span>
                                </a>
                            </li>
                           
                        </ul>
                        <i class="material-icons dp48 del">clear</i>    
                    </li>
                    <li class="aione-nav-item level0 has-children  ">
                        <a href="javascript:;">
                            <span class="nav-item-icon " style="background:rgba(0, 0, 0, 0)"><i class="fa fa-amazon">
                                </i></span>
                            <span class="nav-item-text">
                                Section 2
                            </span>
                            <span class="nav-item-arrow"></span>
                        </a>
                        <ul id="sortable_submenu" class="side-bar-submenu">
                            <li class="aione-nav-item level1 ">
                                <a href="http://master.adminpie.com/account/profile">
                                    <span class="nav-item-icon">P</span>
                                    <span class="nav-item-text">Text</span>
                                </a>
                            </li>
                            <li class="aione-nav-item level1 ">
                                <a href="http://master.adminpie.com/account/activities">
                                    <span class="nav-item-icon">A</span>
                                    <span class="nav-item-text">Radio</span>
                                </a>
                            </li>
                            <li class="aione-nav-item level1 ">
                                <a href="http://master.adminpie.com/account/attendance">
                                    <span class="nav-item-icon">A</span>
                                    <span class="nav-item-text">Checkbox</span>
                                </a>
                            </li>
                            <li class="aione-nav-item level1 ">
                                <a href="http://master.adminpie.com/account/performance">
                                    <span class="nav-item-icon">P</span>
                                    <span class="nav-item-text">Textarea</span>
                                </a>
                            </li>
                           
                        </ul>
                        <i class="material-icons dp48 del">clear</i>    
                    </li>
                </ul>
            </nav>
        </div>
        <div class="Detail-container">
          
                
                    <div class="row no-margin-bottom">
                        <div class="input-field col l12">
                            <input placeholder="Enter section name" name="section_name" id="user_name" type="text" >
                            <label for="user_name">Section Name</label>
             
                        </div>

                        <div class="input-field col l12">
                            <input placeholder="Enter slug" name="section_slug" id="emailId" type="text" >
                            <label for="emailId">Slug</label>
                            
                        </div>

                        <div class="input-field col l12">
                            <input placeholder="Enter description" name="section_description" id="roleId" type="text" >
                            <label for="roleId">Description</label>
                        </div>
                        
                        <div class="input-field col l12">
                            <select class="no-margin-bottom aione-field" name="section_type">
                                <option selected="selected" disabled="disabled" hidden="hidden" value="">Select type</option>
                                <option value="Single">Single</option>
                                <option value="Repeater">Repeater</option>
                            </select>
                        </div>
                       {{--  
                        @if(@$errors->has())
                            @foreach($errors->all() as $kay => $err)
                                <div style="color: red">{{$err}}</div>
                            @endforeach
                        @endif --}}

                        <div class="col s12 m12 l12 aione-field-wrapper center-align">
                          <button class="save_user btn waves-effect waves-light light-blue-text text-darken-2 white darken-2" type="submit">Save
                            <i class="material-icons right">save</i>
                          </button>
                        </div>

                    </div>
                
            <div>
                Fileds List
            </div>
            <ul class="collection">
                <li class="collection-item">Name (Text)<i class="material-icons dp48 del">clear</i>    </li>
                <li class="collection-item">Gender (Radio)<i class="material-icons dp48 del">clear</i>    </li>
                <li class="collection-item">Skills (checkbox)<i class="material-icons dp48 del">clear</i>    </li>
                <li class="collection-item">Description (textarea)<i class="material-icons dp48 del">clear</i>    </li>
                
            </ul>
            <div>
                <button class="btn blue add-new-field">Add Field</button>
            </div>
           {{--  <div class="row">
                <div class="col s12 m12 l12 aione-field-wrapper">
                    <label>Name</label>
                    <input type="text" name="name" value="" class="no-margin-bottom aione-field" >
                </div>
                <div class="col s12 m12 l12 aione-field-wrapper">
                    <label>Description</label>
                    <textarea></textarea> 
                </div>

            </div> --}}
        </div>
        <div style="clear: both;padding:20px">
            
        </div>
        
        
    </div>
    <style type="text/css">
        .module-wrapper > .list-container{
            float: left;
            width: 25%;
            border: 1px solid #e8e8e8;
            height: 100%;
            padding: 10px;
        }
        .module-wrapper > .Detail-container{
            float: right;
            width: 74%;
            border: 1px solid #e8e8e8;
            padding: 10px;
           
        }
        .list-modules > li > div,.list-sub-modules > li{
            border: 1px solid #e8e8e8;
            padding:10px 5px;
            margin-bottom: 5px;
            box-shadow: 1px 1px 1px 1px #F2F1F1;
            background-color: white;
        }
        .list-modules > li > div > .del,.list-sub-modules > li > .del{
            float: right;
            color: #757575;
            font-size: 18px;
            cursor: pointer;
        }
        .list-modules > li > div > .arrow{
            float: left;
            color: #757575;
            font-size: 18px;
            transform: rotate(270deg);
            cursor: pointer;
        }
        .list-sub-modules > li{
            margin-left: 10px;
             transition: opacity 1s ease-out;
        }
        .list-active .list-sub-modules{
            display: block;
            
        }
        .list-sub-modules{
            display: none;
        }
       .module-wrapper .editor{
            height: 200px;margin: 5px 10px
        }
        .module-wrapper .sp-preview{
            height: 40px;
            width: 40px;
        }
        .module-wrapper .sp-dd{
            padding: 2px 6px;
            height: 40px;
            line-height: 40px;
        }
        .module-wrapper .btn.blue{
            float: right;
           
            margin: 10px;
        }
        .aione-nav-item .material-icons{
            position: absolute;
            top: -10px;
            right: -10px;
            border: 3px solid white;
            line-height: 14px;
            height: 20px;
            background-color: red;
            font-size: 14px;
            border-radius: 50%;
            cursor: pointer;
            display: none;
        }
        .aione-nav-item:hover .material-icons{
            display: block
        }
        /*******************************/
        .Detail-container .collection{
            border: none;
        }
        .Detail-container .collection .collection-item{
            border: 1px solid #e8e8e8;
            margin-bottom: 5px;
        }
         .Detail-container .collection .collection-item > .material-icons{
            float: right;
            font-size: 18px;
            color: #757575;
         }
    </style>
    <script type="text/javascript">
        $("#custom").spectrum({
            color: '#000',
            showAlpha: true,    
        });
        $(document).ready(function(){
            $(document).on('click','.add-new-field',function(){
                var html = '<li class="collection-item">Untitled<i class="material-icons dp48 del">clear</i></li>';
                $('.collection').append(html);
            });

            $(document).on('click','.list-modules .arrow',function(){ 
                if($(this).parents('li').hasClass('list-active')){
                    $(this).parents('li').removeClass('list-active');
                }else{
                    $(this).parents('li').addClass('list-active');
                    $(this).parents('li').siblings().removeClass('list-active');    
                }
                
            });

            $('.input1').iconpicker(".input1");

            $('#custom').change(function(){
                $('.color_picker').val($("#custom").spectrum('get').toRgbString());             
            });
            $('.font-awesome').change(function(){
                $('.font-awesome-text').val($(this).val());
            });
            if($('input[name=icon]').val() != ""){
                $('.geticonval > i').each(function(){
                    if($(this).attr('class') == 'fa '+$('input[name=icon]').val()){
                        $(this).parent().addClass('geticonval selectedicon');
                        $('.font-awesome').val($('input[name=icon]').val());
                    }else{
                        console.log("not in class");
                    }
                });
            }
            if($('input[name=color]').val() != ""){
                $('.sp-preview-inner').css({'background-color': $('input[name=color]').val()});
            }
        });
        var editorJs = ace.edit("editor-js");
        editorJs.setTheme("ace/theme/monokai");
        editorJs.getSession().setMode("ace/mode/javascript");
        var editorCss = ace.edit("editor-css");
        editorCss.setTheme("ace/theme/monokai");
        editorCss.getSession().setMode("ace/mode/css");
        $("#custom").spectrum({
            color: '#000',
            showAlpha: true,
        });


        $( function() {
                $( "#sortable" ).sortable({
                    axis: "y"
                });
                $( "#sortable" ).disableSelection();
              } );
        $( function() {
                $( "#sortable_submenu" ).sortable({
                    axis: "y"
                });
                $( "#sortable_submenu" ).disableSelection();
              } );


        
    </script>
@endsection