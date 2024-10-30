<div>
	<div id="app">
		<?php if ( $context->is_dev ) { ?>
			<script type='text/javascript' src='http://localhost:8008/app.js'></script>
		<?php } else { ?>
			<script type='text/javascript' src='https://alexandria.marfeelcdn.com/leroy/statics/<?php echo MRFP_LEROY_BUILD_NUMBER; ?>/js/manifest.js'></script>
			<script type='text/javascript' src='https://alexandria.marfeelcdn.com/leroy/statics/<?php echo MRFP_LEROY_BUILD_NUMBER; ?>/js/vendor.js'></script>
			<script type='text/javascript' src='https://alexandria.marfeelcdn.com/leroy/statics/<?php echo MRFP_LEROY_BUILD_NUMBER; ?>/js/app.js'></script>
			<script type="text/javascript" id="mrf-varysLib" src="https://alexandria.marfeelcdn.com/varys/statics/15/dist/varys.js" async></script>
		<?php } ?>

		<script>
			Leroy.init({
				mode: 'abstract',
				api: '<?php echo $context->current_setting->base_api . '/$API/index/$RESOURCE'; ?>',
				secretKey: '<?php echo $context->current_setting->api_token_param; ?>',
				secretPressKey: '<?php echo $context->secret_press_key; ?>',
				page: '<?php echo $context->page; ?>',
				routes: {
					"adstxt_service": '<?php echo $context->current_setting->ads_txt_api . '/definitions/index/ads/ads_txt' . $context->current_setting->ads_txt_api_token_param; ?>',
					"insight":  "<?php echo $context->insight_url; ?>",
					"signin": '/wp-admin/?page=mrf-signup',
					"wp_api": '<?php echo $context->wp_api_structure . $context->current_setting->api_token_press_param; ?>',
					"ext:onboarding" : "<?php echo $context->onboarding->get_setting_url(); ?>",
					<?php
						foreach ( $context->settings as $setting ) {
							echo '"ext:' . $setting->get_setting_id() . '" : "' . $setting->get_setting_url() . '",';
						}
					?>
				},
				tracking: {
					id: '<?php echo $_SERVER['HTTP_HOST']; ?>',
					account: '<?php echo $context->tracking["account"]; ?>',
					user: {
						email: '<?php echo $context->tracking["user"]["email"]; ?>',
						lastName:  '<?php echo $context->tracking["user"]["lastName"]; ?>',
						firstName: '<?php echo $context->tracking["user"]["firstName"]; ?>'
					}
				},
				features: {
					breadcrumbNavigation: <?php echo $context->activated_once ? 'false' : 'true'; ?>,
					mpStatus: '<?php echo strtolower( $context->selected_availability ); ?>',
					hasAdstxtFile: <?php echo $context->has_adstxt_file ? 'true' : 'false'; ?>
				},
				pressVersion: 'v2',
				onError: function(message) {
					if (message.message.indexOf('fetching /settings') != -1) {
						jQuery.get('<?php echo admin_url( 'admin-ajax.php' ); ?>?action=marfeel&method=track&key=screen/error/settings');
					}
					if (message.message.indexOf('fetching /logos') != -1) {
						jQuery.get('<?php echo admin_url( 'admin-ajax.php' ); ?>?action=marfeel&method=track&key=screen/error/logo');
					}
				},
				inspect: <?php echo $context->is_local_env ? 'false' : 'true'; ?>
			});
		</script>
	</div>
</div>
