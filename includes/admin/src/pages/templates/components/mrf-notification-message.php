<?php
	use Admin\Marfeel_Press_Admin_Translator;
?>

<div class="mrf-notification-message toast toast-default toast-<?php echo isset( $context->toast_type ) ? $context->toast_type : ''; ?> fade-left-leave-active"
	toggle="fade-left-leave-to"
	style="display: <?php echo $context->toast_display; ?>;">
	<span class="mrf-icon <?php echo $context->variant; ?> ml-3"></span>
	<div class="toast-message px-2"><?php echo $context->message_txt; ?></div>
	<span class="icon
	<?php
		if ( $context->toast_type === 'success' ) {
			echo 'icon-close--green';
		} else {
			echo 'icon-close--red';
		}
	?>
	" onclick="hide()"></span>
	</button>
</div>

<?php if ( $context->toast_confirm ) { ?>
	<div id="confirm-modal" class="modal fade show" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="text-center">
						<p><?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.mobile_cache' ); ?></p>
					</div>
				</div>
				<footer class="modal-footer">
					<form method="post" action="">
						<button type="button" class="btn btn-link" onclick="jQuery('#confirm-modal').remove()">
							<?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.confirm.cancel' ); ?>
						</button>
						<button type="submit" class="btn btn-success" name="enable_mobile_cache" value="1">
							<?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.confirm.ok' ); ?>
						</button>
					</form>
				</footer>
			</div>
		</div>
	</div>
<?php } ?>

<div id="confirm-modal-warda" class="modal fade show" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<div class="text-center">
					<p><?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.mobile_cache-then_warda' ); ?></p>
				</div>
			</div>
			<footer class="modal-footer">
				<form method="post" action="">
					<button type="button" class="btn btn-link" onclick="jQuery('#confirm-modal-warda').hide(); jQuery('#mrf_router').prop('checked', false);">
						<?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.confirm.cancel' ); ?>
					</button>
					<button type="submit" class="btn btn-success" name="enable_mobile_cache" value="1">
						<?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.confirm.ok' ); ?>
					</button>
				</form>
			</footer>
		</div>
	</div>
</div>

<script>
function hide() {
	var el = document.querySelector('.mrf-notification-message');
	el.style.display = 'none';
}

function autoHide() {
	setTimeout(hide, 5000);
}

autoHide();

</script>
