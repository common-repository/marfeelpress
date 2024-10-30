<input type="hidden" name="is-dev" value="<?php echo $context->is_dev ? 1 : 0; ?>">
<input type="hidden" name="is-advanced" value="<?php echo $context->is_advanced ? 1 : 0; ?>">
<input type="hidden" name="ok" value="1">
<div class="form-group row">
	<label class="col-4 col-form-label text-right">
		<?php echo trans( 'plugin.availability' ); ?>
	</label>
	<div class="col-6">
		<?php
			foreach ( $context->options as $option ) {
				$checked = $context->selected_availability == $option['mode'] ? 'checked' : '';
				echo '<div class="custom-control custom-radio">';
					echo '<input type="radio" id="activation-mode-' . $option['mode'] . '" name="availability" class="custom-control-input" ' . $checked . ' value="' . $option['mode'] . '">';
					echo '<label class="custom-control-label" for="activation-mode-' . $option['mode'] . '">' . $option['mode'] . '. <span class="text-muted">' . $option['description'] . '</span></label>';
				echo '</div>';
			}
		?>
	</div>
</div>

<div class="form-group row">
	<div class="col-6 offset-4">
		<div class="custom-control custom-checkbox custom-control-inline">
			<input type="hidden" name="amp" value="0">
			<input type="checkbox" class="custom-control-input" name="amp" id="amp"
				<?php echo $context->amp ? 'checked' : ''; ?> value="1"
				onchange="jQuery(this).is(':checked') ? jQuery('#amp-url').show() : jQuery('#amp-url').hide();">
			<label class="custom-control-label" for="amp"><?php echo trans( 'activate.amp' ); ?>
				<div class="mrf-customTooltip top">
					<i class="mt-n1 align-top icon icon-info--blue"></i>
					<div class="mrf-tiptext">
						<?php echo trans( 'amp.url.tooltip' ); ?>
					</div>
				</div>
			</label>
		</div>

		<div id="amp-url" class="<?php echo $context->amp ? '' : 'hidden'; ?>">
			<label><?php echo trans( 'amp.url' ); ?></label>
			<div>
				<div class="custom-control custom-radio">
					<input type="radio" id="amp-url-query" name="amp-url" class="custom-control-input" value="?amp=1" <?php echo $context->amp_url == '?amp=1' ? 'checked' : ''; ?>>
					<label class="custom-control-label" for="amp-url-query"><?php echo trans( 'amp.url.params' ); ?></label>
					<span class="text-muted"><?php echo $context->permalink_structure; ?>?amp=1</span>
				</div>
				<div class="custom-control custom-radio">
					<input type="radio" id="amp-url-query-nv" name="amp-url" class="custom-control-input" value="?amp" <?php echo $context->amp_url == '?amp' ? 'checked' : ''; ?>>
					<label class="custom-control-label" for="amp-url-query-nv"><?php echo trans( 'amp.url.paramsnv' ); ?></label>
					<span class="text-muted"><?php echo $context->permalink_structure; ?>?amp</span>
				</div>
				<div class="custom-control custom-radio">
					<input type="radio" id="amp-url-params" name="amp-url" class="custom-control-input" value="/amp/" <?php echo $context->amp_url == '/amp/' ? 'checked' : ''; ?>>
					<label class="custom-control-label" for="amp-url-params"><?php echo trans( 'amp.url.segments' ); ?></label>
					<span class="text-muted"><?php echo rtrim( $context->permalink_structure, '/' ); ?>/amp/</span>
				</div>
			</div>
		</div>
	</div>
</div>
