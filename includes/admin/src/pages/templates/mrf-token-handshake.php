<meta name="referrer" content="never" />

<form 	name="frm1"
		action="<?php echo $context->insight_url; ?>/onboarding/token-handshake"
		method="POST">
	<input type="hidden" name="tenantUri" value="<?php echo $_SERVER['HTTP_HOST']; ?>"/>
	<input type="hidden" name="userEmail" value="<?php echo $context->tracking["user"]["email"]; ?>"/>
	<input type="hidden" name="token" value="<?php echo $context->secret_press_key; ?>"/>
	<input type="hidden" name="redirectionSuccessUrl" value="<?php echo $context->host_with_protocol; ?>/wp-admin/?page=mrf-signup"/>
	<input type="hidden" name="redirectionErrorUrl" value="<?php echo $context->host_with_protocol; ?>/wp-admin/?page=mrf-signup"/>
	<?php echo $context->draft_definition; ?>
</form>

<script>
   document.frm1.submit();
</script>
