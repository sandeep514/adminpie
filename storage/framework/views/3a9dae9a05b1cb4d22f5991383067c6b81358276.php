<?php 
	$link=$_SERVER['REQUEST_URI'];
 ?>

<nav id="aione_account_tabs" class="aione-account-tabs aione-nav aione-nav-horizontal">
	<?php 
	$index = 0;
	$permisson = drawSidebar::checkPermisson();
	 ?>
	
	<?php $__currentLoopData = drawSidebar::drawSidebar(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $sidebar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<?php if($sidebar->name == 'Account'): ?>
			<?php 
				$routes = [];
			 ?>
			<?php $__currentLoopData = $sidebar->subModule; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ke => $subModule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php 
					$routes[] = str_replace('/{id?}','',$subModule->sub_module_route);
				 ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<?php if(isset($permisson['module'][$sidebar['id']]['permisson']) && $permisson['module'][$sidebar['id']]['permisson']=='on'): ?>
				<ul class="aione-tabs">
					<?php $__currentLoopData = $sidebar->subModule; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ke => $subModule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php if(isset($permisson['submodule'][$subModule['id']]['permisson']) && $permisson['submodule'][$subModule['id']]['permisson']=='on'): ?>
							<li class="aione-tab  <?php echo e(Request::is(str_replace('/{id?}','',$subModule->sub_module_route))?'nav-item-current':''); ?>">
								<a href="<?php echo e(url(str_replace('/{id?}','',$subModule->sub_module_route))); ?>">
									<span class="nav-item-text"><?php echo e(@$subModule['name']); ?></span>
								</a>
							</li>
						<?php endif; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</ul>
				<?php 
					$index++;
				 ?>
			<?php endif; ?>
		<?php endif; ?>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
	<div class="clear"></div>
</nav>