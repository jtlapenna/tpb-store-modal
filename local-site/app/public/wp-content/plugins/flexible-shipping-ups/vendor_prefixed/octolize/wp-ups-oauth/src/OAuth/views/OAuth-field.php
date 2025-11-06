<?php

namespace UpsFreeVendor;

/**
 * OAuth field.
 *
 * @var string $authorize_action
 * @var string $revoke_action
 * @var string $field_key
 * @var string $value
 * @var string $field_class
 * @var \Octolize\WooCommerceShipping\Ups\OAuth\TokenOption $token_option
 */
use UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
?>
<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php 
echo \esc_attr($this->field_key);
?>"><?php 
echo \wp_kses_post($this->data['title']);
?></label>
	</th>
	<td class="forminp">
		<?php 
if (empty($token_option->get_refresh_token())) {
    ?>
			<button
				data-href="<?php 
    echo \esc_url($authorize_action);
    ?>"
				id="<?php 
    echo \esc_attr($this->field_key) . '_authorize';
    ?>"
				class="button button-primary ups-oauth-button"
			><?php 
    echo \esc_html(\__('Authorize', 'flexible-shipping-ups'));
    ?></button>
			<p class="description">
				<?php 
    echo \esc_html(\__('Clicking the button will open up the UPS website. Please provide your credentials and mark the checkbox to connect our plugin with your UPS account.', 'flexible-shipping-ups'));
    ?>
			</p>
		<?php 
} else {
    ?>
			<button
				data-href="<?php 
    echo \esc_url($revoke_action);
    ?>"
			    id="<?php 
    echo \esc_attr($this->field_key) . '_revoke';
    ?>"
			    class="button ups-oauth-button"
			><?php 
    echo \esc_html(\__('Revoke', 'flexible-shipping-ups'));
    ?></button>
			<p class="description">
				<?php 
    echo \esc_html(\__('Clicking the button will disconnect your UPS account from our plugin.', 'flexible-shipping-ups'));
    ?>
				<!-- Expires at: <?php 
    echo $token_option->get_expires_at();
    ?>	-->
				<!-- Expires at: <?php 
    echo \date(\DATE_RFC2822, $token_option->get_expires_at());
    ?> -->
				<!-- Expires in: <?php 
    echo $token_option->get_expires_in();
    ?> -->
				<!-- Issued at: <?php 
    echo $token_option->get_issued_at();
    ?> -->
				<!-- <?php 
    \print_r($token_option->get());
    ?> -->
			</p>
		<?php 
}
?>
		<fieldset>
			<input class="<?php 
echo \esc_attr($field_class);
?>" type="hidden"
				   name="<?php 
echo \esc_attr($field_key);
?>" value="<?php 
echo \esc_attr($value);
?>"/>
		</fieldset>
		<script>
			jQuery(document).ready(function () {
				let changed = false;
				jQuery('select').change(function () {
					changed = true;
				});
				jQuery(document).on('click', '.ups-oauth-button', function (e) {
					if (changed) {
						alert('<?php 
echo \esc_js(\__('You have unsaved changes. Save them first.', 'flexible-shipping-ups'));
?>');
					} else {
						window.location.href = jQuery(this).data('href');
					}
					e.preventDefault();
				});
			});
		</script>
	</td>
</tr>
<?php 
