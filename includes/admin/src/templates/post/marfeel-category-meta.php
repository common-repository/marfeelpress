<?php
	use Admin\Marfeel_Press_Admin_Translator;
?>

<table class="form-table">
	<tbody>
		<tr class="form-field form-required term-name-wrap">
			<th scope="row">
				<label for="name">
					<?php echo Marfeel_Press_Admin_Translator::trans( 'category.marfeelizable' ); ?>
				</label>
			</th>
			<td>
				<input type="checkbox" name="no_marfeelize" id="name" size="40" aria-required="true" <?php echo $no_marfeelize ? ' checked' : ''; ?>>
				<p class="description">
					<?php echo Marfeel_Press_Admin_Translator::trans( 'category.marfeelizable.description' ); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>
