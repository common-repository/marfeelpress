<div class="form-group row">
	<div class="col-6 offset-4">
		<div id="warda_alert" class="alert alert-warning" role="alert" style="display: none">
			<?php echo trans( 'activate.mrf_router.warning-not-advisable' ); ?>
		</div>
		<div class="custom-control custom-checkbox custom-control-inline">
			<input type="checkbox" class="custom-control-input" name="mrf_router" id="mrf_router"
				<?php echo $context->mrf_router ? 'checked' : ''; ?> value="1"
				<?php echo $context->can_activate_warda ? '' : 'disabled'; ?>
				onchange="onChangeWarda()">
			<label class="custom-control-label" for="mrf_router">
				<div class="mrf-customTooltip top"
					<?php echo $context->can_activate_warda ? 'style="display: none;"' : ''; ?>
					>
					<i class="mt-n1 align-top icon icon-info--blue"></i>
					<div class="mrf-tiptext">
						<?php echo trans( 'activate.mrf_router.warning-disabled' ); ?>
					</div>
				</div>
				<?php echo trans( 'activate.mrf_router' ); ?>
			</label>
		</div>
	</div>
</div>

<div id="custom-garda" class="form-group row"<?php echo $context->mrf_router ? ' style="display: none"' : ''; ?>>
	<div class="col-6 offset-4">
		<div class="custom-control custom-checkbox custom-control-inline">
			<input type="checkbox" class="custom-control-input" name="custom_garda" id="custom_garda"
				<?php echo $context->custom_garda ? 'checked' : ''; ?> value="1">
			<label class="custom-control-label" for="custom_garda"><?php echo trans( 'activate.custom_garda' ); ?></label>
		</div>
	</div>
</div>

<div id="sticky-posts" class="form-group row">
	<div class="col-6 offset-4">
		<div class="custom-control custom-checkbox custom-control-inline">
			<input type="checkbox" class="custom-control-input" name="sticky-posts-on-top" id="sticky-posts-on-top"
				<?php echo $context->sticky_posts_on_top ? 'checked' : ''; ?> value="1">
			<label class="custom-control-label" for="sticky-posts-on-top"><?php echo trans( 'activate.sticky-posts-on-top' ); ?></label>
		</div>
	</div>
</div>

<div class="form-group row">
	<div class="col-6 offset-4">
		<div class="custom-control custom-checkbox custom-control-inline">
			<input type="checkbox" class="custom-control-input" name="cache" id="cache"
				<?php echo $context->cache ? 'checked' : ''; ?> value="1">
			<label class="custom-control-label" for="cache"><?php echo trans( 'activate.cache' ); ?></label>
		</div>
	</div>
</div>

<div class="form-group row">
	<div class="col-4 col-form-label text-right">
		<?php echo trans( 'post_type' ); ?>
	</div>
	<div class="col-6">
		<?php foreach ( $context->wp_post_types as $type ) { ?>
			<div class="custom-control custom-checkbox custom-control-inline">
				<input id="post-type-<?php echo $type; ?>" type="checkbox" class="custom-control-input" name="post-type[]" value="<?php echo $type; ?>" <?php echo in_array( $type, $context->post_type ) ? 'checked' : ''; ?> />
				<label class="custom-control-label" for="post-type-<?php echo $type; ?>"><?php echo ucfirst( $type ); ?></label>
			</div>
		<?php } ?>
	</div>
</div>

<div class="form-group row">
	<div class="col-6 offset-4">
		<div class="custom-control custom-checkbox custom-control-inline">
			<input type="checkbox" class="custom-control-input" name="multilanguage" id="multilanguage"
				<?php echo $context->multilanguage ? 'checked' : ''; ?> value="1"
				onchange="onChangeMultilanguage()">
			<label class="custom-control-label" for="multilanguage">
				<?php echo trans( 'activate.multilanguage' ); ?>
			</label>
		</div>
	</div>
</div>

<div id="multilanguage_options" class="form-group row" <?php echo $context->multilanguage ? '' : ' style="display: none"'; ?>>
	<label for="multilanguage_options" class="col-4 col-form-label text-right">
		<?php echo trans( 'activate.multilanguage.options' ); ?>
	</label>
	<div class="col-6">
		<select multiple name="multilanguage_options[]" class="form-control">
			<?php
				foreach ( $context->languages as $language ) {
					echo '<option value="' . $language . '" ' . ( in_array( $language, $context->multilanguage_options ) ? 'selected' : '' ) . '>' . $language . '</option>';
				}
			?>
		</select>
	</div>
</div>

<div class="form-group row">
	<div class="col-6 offset-4">
		<button class="insight-token-field btn btn-link" type="button" onclick="jQuery('.insight-token-field').toggle();" ><?php echo trans( 'insight_token.show' ); ?></button>
	</div>
</div>

<div class="insight-token-field hidden">
	<div class="form-group row ">
		<label for="insight-token" class="align-items-center col-4 col-form-label d-flex justify-content-end">
			<?php echo trans( 'insight_token.label' ); ?>
		</label>
		<div class="col-6">
			<input type="text" name="insight-token" class="form-control" value="<?php echo $context->insight_token; ?>" />
		</div>
		<button class="btn btn-link" type="button" onclick="jQuery('.insight-token-field').toggle();" ><?php echo trans( 'insight_token.hide' ); ?></button>
	</div>
</div>

<script type="text/javascript">
function onChangeWarda() {
	const can_activate_warda_with_plugin_change = <?php echo $context->can_activate_warda_with_plugin_change ? 'true' : 'false'; ?>;
	const has_unsupported_cache_plugin = <?php echo $context->has_unsupported_cache_plugin ? 'true' : 'false'; ?>;

	if (jQuery('#mrf_router').is(':checked')) {
		if (!!can_activate_warda_with_plugin_change) {
			jQuery('#confirm-modal-warda').show();
		} else if (!!has_unsupported_cache_plugin) {
			jQuery('#warda_alert').show();
		} else {
			jQuery('#custom-garda').hide();
		}
	} else {
		jQuery('#custom-garda').show();
		jQuery('#warda_alert').hide();
	}
}

function onChangeMultilanguage() {
	if (jQuery('#multilanguage').is(':checked')) {
		jQuery('#multilanguage_options').show();
	} else {
		jQuery('#multilanguage_options').hide();
	}
}
</script>
