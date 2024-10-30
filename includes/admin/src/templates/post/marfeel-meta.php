<?php
	use Admin\Marfeel_Press_Admin_Translator;
?>

<ul>
	<li>
		<label for="mrf_no_marfeelizable">
			<input type="checkbox" id="mrf_no_marfeelizable" name="mrf_no_marfeelizable"<?php echo $no_marfeelize ? ' checked' : ''; ?> />
			<?php echo Marfeel_Press_Admin_Translator::trans( 'post.marfeelizable' ); ?>
		</label>
	</li>
	<li>
		<label for="mrf_amp_deactive">
			<input type="checkbox" id="mrf_amp_deactive" name="mrf_amp_deactive"<?php echo $amp_deactive ? ' checked' : ''; ?> />
			<?php echo Marfeel_Press_Admin_Translator::trans( 'post.amp.active' ); ?>
		</label>
	</li>
	<li>
		<label for="mrf_amp_deactive">
			<input type="checkbox" id="mrf_top_media" name="mrf_top_media"<?php echo $no_topmedia ? ' checked' : ''; ?> />
			<?php echo Marfeel_Press_Admin_Translator::trans( 'post.topmedia' ); ?>
		</label>
	</li>
</ul>


