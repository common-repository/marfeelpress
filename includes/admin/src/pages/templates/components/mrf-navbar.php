<nav class="navbar navbar-expand sticky-top">
	<a class="navbar-brand navbar-dark" href="admin.php?page=mrf-onboarding">
		<?php require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'components/mrf-logo.php' ); ?>
	</a>

	<h1 class="navbar-title font-weight-bold">
		<?php if ( $context->back ) { ?>
			<a href="admin.php?page=mrf-onboarding" class="text-white text-decoration-none">
				<?php require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'components/mrf-back-icon.php' ); ?>
			</a>
		<?php } ?>

		<?php echo $context->title; ?>
	</h1>

</nav>
