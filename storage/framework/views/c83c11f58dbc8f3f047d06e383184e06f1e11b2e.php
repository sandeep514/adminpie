<?php $__env->startSection('content'); ?>
<?php if(Session::has('login_fails')): ?>
	<div class="login-error">
		<?php echo e(Session::get('login_fails')); ?>

	</div>
<?php endif; ?>
	
	<?php echo Form::open(['method' => 'POST','class' => 'modal-body','route' => 'org.login.post']); ?>

		<div class="text-center">
			
			<h5 class="content-group" style="font-size: 26px;font-weight: 900;color: grey;margin-top: 0px">Admin<span style="color: #03A9F4">Pie</span></h5>
		</div>
		
		<?php if(Session::has('password-changed')): ?>
		<span style="background-color: #CDF3CD;display: inline-block;width: 100%;padding: 10px;">
			<?php echo e(Session::get('password-changed')); ?>

			</span>
		<?php endif; ?>
		
		<div class="form-group has-feedback has-feedback-left">
			
			<?php echo Form::email('email',null,['class' => 'form-control' , 'placeholder' => 'Username']); ?>

			<div class="form-control-feedback">
				<i class="icon-user text-muted"></i>
			</div>
		</div>
		<div class="form-group has-feedback<?php echo e($errors->has('email') ? ' has-error' : ''); ?> has-feedback-left">
			
			<?php echo Form::password('password',['class' => 'form-control' , 'placeholder' => 'Password']); ?>

			<?php if($errors->has('email')): ?>
	            <span class="help-block">
	                <strong><?php echo e($errors->first('email')); ?></strong>
		        </span>
	        <?php endif; ?>
	        
			<div class="form-control-feedback">
				<i class="icon-lock2 text-muted"></i>
			</div>
		</div>

		<div class="form-group login-options">
			<div class="row">
				<div class="col-sm-6">
					<label class="checkbox-inline">
						<?php echo Form::checkbox('remember',null,['class' => 'styled' ,  'checked' => 'checked']); ?>

						Remember
					</label>
				</div>

				<div class="col-sm-6 text-right">
					<a href="<?php echo e(route('forgot.password')); ?>">Forgot password?</a>
				</div>
			</div>
		</div>

		<div class="form-group">
			
			<?php echo Form::button('Login<i class="icon-arrow-right14 position-right"></i>',['type'=> 'submit','class' => 'btn bg-blue btn-block']); ?>

		</div>

		
	<?php echo Form::close(); ?>

	<div class="footer">
			© 2017, All Right Reserved. <a href="http://oxosolutions.com/" target="_blank"  style="color: white"><span>OXO solutions</span></a>
	</div>
	<style type="text/css">
		.login-cover{
			    background: url('<?php echo e(asset('assets/images/cool-bg.jpg')); ?>') no-repeat;
    background-size: cover;
		}
		.panel-body{
			position: absolute;
			left: 50%;
			top: 50%;
			margin-left: -160px !important;
			    margin-top: -195px !important;
		}
		.footer{
			    position: fixed;
    bottom: 20px;
    text-align: center;
    color: white;
        background-color: hsla(0,0%,0%,0.3);
    padding: 10px;
		}
		.login-error{
		    background-color: #F7D4D4;
			padding: 10px;
			border-left: 3px solid #E26262;
		}
	</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>