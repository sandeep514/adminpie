<?php $__env->startSection('content'); ?>

	<?php echo $__env->make('organization.settings._tabs', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

		<?php echo FormGenerator::GenerateSection('attsetsec1'); ?>

	
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>