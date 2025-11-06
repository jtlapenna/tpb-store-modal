<?php
/* CSV import match fields template */
global $wpsl_settings;

if ( !defined( 'ABSPATH' ) ) exit;

if ( !current_user_can( 'wpsl_csv_manager' ) ) {
    wp_die( __( 'You do not have permission to import location data.', 'wpsl-csv' ), '', array( 'response' => 403 ) );
}

/**
 * @todo move to function in main wpsl plugin code, same as with code in class-settings
 * that can simply be called to test the server api key from add-ons and provide feedback.
 */
$address   = 'Manhattan, NY 10036, USA';
$url       = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) .'&key=' . $wpsl_settings['api_server_key'];
$response  = wp_remote_get( $url );
$valid_key = true;

if ( ! is_wp_error( $response ) ) {
    $response = json_decode( $response['body'], true );

    if ( $response['status'] !== 'OK' ) {
        $valid_key     = false;
        $wpsl_geocode  = new WPSL_Geocode();
        $error_message = $wpsl_geocode->check_geocode_error_msg( $response, true );

        echo '<div class="error notice"><p>' . sprintf( __( 'There\'s a problem with the provided %sserver key%s. This needs to be resolved before you can import the data. %s', 'wpsl' ), '<a href="https://wpstorelocator.co/document/create-google-api-keys/#server-key">', '</a>', $error_message ) . '</p></div>';
    }
}
?>

<div id="wpsl-import">
    <form id="wpsl-csv-fields" method="post" action="<?php echo admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_csv&section=import' ); ?>">
        <?php
        wp_nonce_field( 'wpsl_csv_import', 'wpsl_csv_import_nonce' );

        if ( isset( $_GET['duplicate_handling'] ) ) {
            $dup_handling = ( $_GET['duplicate_handling'] == 'skip' ) ? 'skip' : 'update';

            echo "<input type='hidden' name='wpsl_duplicate_handling' value='" . esc_attr( $dup_handling ) . "' />";
        }
        ?>
        <input type="hidden" name="wpsl_action" value="csv_import" />

        <p><?php _e( 'Map the CSV headers to the WPSL fields.', 'wpsl-csv' ); ?></p>

        <?php echo $this->import->match_fields(); ?>

        <p class="wpsl-import-btn">
            <input id="wpsl-csv-import" type="submit" value="<?php _e( 'Import Locations', 'wpsl-csv' ); ?>" class="button-primary" <?php if ( ! $valid_key ) { echo 'disabled'; } ?>>
        </p>
    </form>
</div>