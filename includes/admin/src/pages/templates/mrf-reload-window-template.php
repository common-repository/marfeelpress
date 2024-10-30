<script>
	if (!!window.opener) {
		window.opener.location.href = window.location.href.replace('closePopup=1', '');
		window.close();
	}
</script>