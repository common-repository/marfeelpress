<div class="form-group row">
	<div class="col-6 offset-4">
		<button class="btn btn-warning" name="reset-version">Update Leroy Version</button>
	</div>
</div>
<div class="form-group row">
	<label for="tenant-home" class="col-4 col-form-label text-right">
		Tenant name
	</label>
	<div class="col-6">
		<input type="text" name="tenant-home" class="form-control" value="<?php echo $context->tenant_home; ?>" />
	</div>
</div>
<div class="form-group row">
	<label for="tenant-uri" class="col-4 col-form-label text-right">
		Tenant uri
	</label>
	<div class="col-6">
		<input type="text" name="tenant-uri" class="form-control" value="<?php echo $context->tenant_uri; ?>" />
	</div>
</div>
<div class="form-group row">
	<label for="tenant-type" class="col-4 col-form-label text-right">
		Tenant type
	</label>
	<div class="col-6">
		<input type="text" name="tenant-type" class="form-control" value="<?php echo $context->tenant_type; ?>" />
	</div>
</div>
<div class="form-group row">
	<label for="media-group" class="col-4 col-form-label text-right">
		Media Group
	</label>
	<div class="col-6">
		<input type="text" name="media-group" class="form-control" value="<?php echo $context->media_group; ?>" />
	</div>
</div>
<div class="form-group row">
	<label for="api-token" class="col-4 col-form-label text-right">
		API Token
	</label>
	<div class="col-6">
		<input type="text" name="api-token" class="form-control" value="<?php echo $context->api_token; ?>" />
	</div>
</div>
<?php if ( ! empty( $context->permalink_structure ) ) { ?>
	<div class="form-group row">
		<div class="col-6 offset-4">
			<div class="custom-control custom-checkbox custom-control-inline">
				<input type="checkbox" class="custom-control-input" name="avoid-query" id="avoid_query_params"
					<?php echo $context->avoid_query_params ? 'checked' : ''; ?> value="1">
				<label class="custom-control-label" for="avoid_query_params">Avoid query params usage</label>
			</div>
		</div>
	</div>
<?php } ?>
<div class="form-group row">
	<div class="col-6 offset-4">
		<div class="custom-control custom-checkbox custom-control-inline">
			<input type="checkbox" class="custom-control-input" name="disable-multipage" id="disable_multipage"
				<?php echo $context->disable_multipage ? 'checked' : ''; ?> value="1">
			<label class="custom-control-label" for="disable_multipage">Disable multipage</label>
		</div>
	</div>
</div>
<div class="row mb-5">
	<label class="control-label text-right col-4">
		factory reset
	</label>
	<div class="col-6">
		<button type="submit" name="reset" value="1" class="btn btn-danger">Reset</button>
	</div>
</div>

<?php if ( $context->is_longtail ) { ?>
	<div class="form-group row">
		<div class="col-6 offset-4">
			<div class="custom-control custom-checkbox custom-control-inline">
				<input type="checkbox" class="custom-control-input" name="move-to-enterprise" id="move_to_enterprise" value="1">
				<label class="custom-control-label" for="move_to_enterprise">Move this tenant to ENTERPRISE</label>
			</div>
		</div>
	</div>
<?php } ?>
