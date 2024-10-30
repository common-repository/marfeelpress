<?php require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'mrf-settings-intercom-widget.php' ); ?>

<div class="p-5">
	<h5 class="section-title"><?php echo $context->title; ?></h5>
	<hr class="mb-5">

	<form method="post" action="">
		<?php require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'mrf-settings-basic-template.php' ); ?>

		<?php if ( ! $context->is_advanced && ! $context->is_dev ) { ?>
			<?php require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'components/mrf-advanced-link.php' ); ?>
		<?php } ?>

		<?php if ( $context->is_advanced || $context->is_dev ) { ?>
			<?php require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'mrf-settings-advanced-template.php' ); ?>
		<?php } ?>

		<?php if ( $context->is_dev ) { ?>
			<?php require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'mrf-settings-dev-template.php' ); ?>
		<?php } ?>

		<div class="row">
			<div class="col-11 text-right">
				<button id="save" type="submit" class="btn btn-success">Save changes</button>
			</div>
		</div>
	</form>
</div>

<?php require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'components/mrf-notification-message.php' ); ?>

<?php if ( ! $context->is_local_env ) { ?>
	<?php require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'components/mrf-inspectlet.php' ); ?>
<?php } ?>
