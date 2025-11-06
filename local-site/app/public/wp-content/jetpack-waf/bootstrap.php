<?php
define( 'DISABLE_JETPACK_WAF', false );
if ( defined( 'DISABLE_JETPACK_WAF' ) && DISABLE_JETPACK_WAF ) return;
define( 'JETPACK_WAF_MODE', 'silent' );
define( 'JETPACK_WAF_SHARE_DATA', false );
define( 'JETPACK_WAF_SHARE_DEBUG_DATA', false );
define( 'JETPACK_WAF_DIR', '/mnt/customers/customers-bh-1jlr25/0cb5220c-2923-4e3a-aee7-4db8729151e6/wp-content/wp-content/jetpack-waf' );
define( 'JETPACK_WAF_WPCONFIG', '/mnt/customers/customers-bh-1jlr25/0cb5220c-2923-4e3a-aee7-4db8729151e6/wp-content/wp-content/../wp-config.php' );
define( 'JETPACK_WAF_ENTRYPOINT', 'rules/rules.php' );
require_once '/mnt/customers/customers-bh-1jlr25/0cb5220c-2923-4e3a-aee7-4db8729151e6/wp-content/wp-content/plugins/jetpack/vendor/autoload.php';
Automattic\Jetpack\Waf\Waf_Runner::initialize();
