{!!Form::textarea(str_replace(' ','_',strtolower($collection->field_slug)), null,['class'=>$field_input_class,'id'=>$field_input_id,'placeholder'=>$placeholder, ' data-validation' => $field_validations])!!}
<div id="{{$field_input_id}}_editor" class="aione-code-editor medium" data-mode="{{@$field_meta->field_custom_code_language}}" data-theme="{{@$field_meta->field_custom_code_theme}}"></div>