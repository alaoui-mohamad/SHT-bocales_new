
<?php
require plugin_dir_path(__DIR__) . 'vendor/autoload.php';

use Automattic\WooCommerce\Client;

$site = get_site_url();
$woocommerce = new Client(
	$site,
	'ck_ec6424efbe2ba12e0500d8b1c330537165d751cc',
	'cs_1a04a5e13c0a599e7c0effe60aafa0cebf2b58fb',
	[

		'version' => 'wc/v3',

	]
);