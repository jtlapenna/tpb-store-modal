<?php

namespace UpsFreeVendor;

/**
 * @var string $settings_url
 */
?>
<p><?php 
echo \esc_html(\__('Beginning in June of 2024, you will no longer be able to display UPS live rates using your current credentials. UPS is updating security model to OAuth 2.0, which our plugin already supports.'));
?></p>

<p><?php 
\esc_html_e('In order to continue using UPS Live Rates:', 'flexible-shipping-ups');
?><br/>
<?php 
echo \wp_kses_post(\sprintf(\__(' - go to %1$splugin settings%2$s,', 'flexible-shipping-ups'), '<a href="' . $settings_url . '">', '</a>'));
?><br/>
<?php 
echo \wp_kses_post(\sprintf(\__(' - Change %1$sAPI Type%2$s to %1$sOAuth - REST API%2$s,', 'flexible-shipping-ups'), '<strong>', '</strong>'));
?><br/>
<?php 
\esc_html_e(' - Authorize using UPS credentials,', 'flexible-shipping-ups');
?><br/>
<?php 
\esc_html_e(' - Save the settings.', 'flexible-shipping-ups');
?>
</p>
<p><?php 
\esc_html_e('For more information on UPS OAuth 2.0 integration, please refer to the UPS documentation.', 'flexible-shipping-ups');
?></p>

<p><?php 
\esc_html_e('Thank you for choosing UPS Live Rates!', 'flexible-shipping-ups');
?></p>
<?php 
