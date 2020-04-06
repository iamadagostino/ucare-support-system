<?php
/**
 * Functions for managing scripts on the application's front-end.
 *
 * @since 1.4.2
 * @package ucare
 */
namespace ucare;


// Init scripts on load
add_action( 'ucare_loaded', 'ucare\init_scripts' );

// Fire our enqueue hook
add_action( 'ucare_init', 'ucare\enqueue_scripts' );

// Register core scripts
add_action( 'ucare_enqueue_scripts', 'ucare\register_default_scripts' );

// Load default scripts
add_action( 'ucare_enqueue_scripts', 'ucare\enqueue_default_scripts', 20 );

// Print header scripts
add_action( 'ucare_head', 'ucare\print_header_scripts' );

// Print footer scripts
add_action( 'ucare_footer', 'ucare\print_footer_scripts' );

// Register default scripts
add_action( 'ucare_default_scripts', 'ucare\default_scripts' );


/**
 * Initialize the script service.
 *
 * @param uCare $ucare The plugin instance.
 *
 * @action ucare_loaded
 *
 * @since 1.4.2
 * @return void
 */
function init_scripts( $ucare ) {
    $ucare->set( 'scripts', new Scripts() );
}


/**
 * Print enqueued header scripts.
 *
 * @action ucare_head
 *
 * @since 1.4.2
 * @return bool|array
 */
function print_header_scripts() {
    $scripts = scripts();

    if ( !$scripts || did_action( 'ucare_print_header_scripts' ) ) {
        return false;
    }

    do_action( 'ucare_print_header_scripts' );

    $scripts->do_head_items();
    $scripts->reset();

    return $scripts->done;
}


/**
 * Print enqueued footer scripts.
 *
 * @since 1.4.2
 * @return bool|array
 */
function print_footer_scripts() {
    $scripts = scripts();

    if ( !$scripts || did_action( 'ucare_print_footer_scripts' ) ) {
        return false;
    }

    do_action( 'ucare_print_footer_scripts' );

    print_underscore_templates();
    print_footer_scripts();

    $scripts->do_footer_items();
    $scripts->reset();

    return $scripts->done;
}


/**
 * Get scripts object.
 *
 * @since 1.4.2
 * @return false|\WP_Scripts
 */
function scripts() {
    return ucare()->get( 'scripts' );
}


/**
 * Fires the ucare_enqueue_scripts action at the earliest moment we know that we are on the support page.
 *
 * @action ucare_init
 *
 * @since 1.4.2
 * @return void
 */
function enqueue_scripts() {
    /**
     * Begin enqueuing scripts to be used in the front-end app
     *
     * @since 1.4.2
     */
    do_action( 'ucare_enqueue_scripts' );
}


/**
 * Register default scripts.
 *
 * @action ucare_default_scripts
 *
 * @param Scripts $scripts
 *
 * @since 1.6.0
 * @return void
 */
function default_scripts( $scripts ) {
    if ( did_action( 'ucare_register_scripts' ) ) {
        return;
    }

    $scripts->add( 'bootstrap',  resolve_url( 'assets/js/bootstrap/bootstrap.min.js'   ), array( 'jquery' ), PLUGIN_VERSION );

    $scripts->add( 'sweetalert', resolve_url( 'assets/js/sweetalert/sweetalert.min.js' ), null, PLUGIN_VERSION );
    $scripts->add( 'dropzone',   resolve_url( 'assets/js/dropzone/dropzone.min.js'     ), null, PLUGIN_VERSION );
    $scripts->add( 'redux',      resolve_url( 'assets/js/redux/redux.min.js'           ), null, PLUGIN_VERSION );

    do_action( 'ucare_register_scripts' );
}


/**
 * Register core script dependencies.
 *
 * @action ucare_enqueue_scripts
 *
 * @since 1.6.0
 * @return void
 */
function register_default_scripts() {

    $l10n = array(
        'vars' => array(
            'support_url' => support_page_url()
        ),
        'api' => array(
            'nonce'  => wp_create_nonce( 'wp_rest' ),
            'root'   => rest_url()
        ),
        'l10n' => array(
            'strings' => array(
                'delete_selection' => __( 'Delete Selection', 'ucare' ),
                'are_you_sure'     => __( 'Are you sure you want to do this?', 'ucare' ),
                'yes' => __( 'Yes', 'ucare' ),
                'no'  => __( 'No',  'ucare' )
            )
        ),
        'settings' => array(
            'max_file_size' => get_option( Options::MAX_ATTACHMENT_SIZE ),
            'strings' => array(
                'drop_files' => __( 'Drop files here to upload', 'ucare' ),
                'browser_suport' => __( 'Your browser does not support drag\'n\'drop file uploads.', 'ucare' ),
                'fallback_upload' => __( 'Please use the fallback form below to upload your files like in the olden days.', 'ucare' ),
                'file_too_big' => __( 'File is too big (%sMiB). Max filesize: %dMiB.', 'ucare' ),
                'file_type_unathorized' => __( 'You can\'t upload files of this type.', 'ucare' ),
                'server_status_code' => __( 'Server responded with %s code.' ),
                'cancel_upload' => __( 'Cancel upload', 'ucare' ),
                'cancel_this_upload' => __( 'Are you sure you want to cancel this upload?', 'ucare' ),
                'remove_file' => __( 'Remove file', 'ucare' ),
                'too_many_files' => __( 'You can not upload any more files.', 'ucare' )
            )
        )
    );

    ucare_register_script( 'ucare-extensions', resolve_url( 'assets/js/extensions.js' ), null, PLUGIN_VERSION );


    ucare_register_script( 'ucare', resolve_url( 'assets/js/ucare.js' ), array( 'jquery', 'ucare-extensions', 'redux' ), PLUGIN_VERSION );
    ucare_localize_script( 'ucare', 'ucare_l10n', $l10n );


    $deps = array(
        'jquery'
    );

    // Register jQuery plugins
    ucare_register_script( 'jquery-serializejson', resolve_url( 'assets/js/jquery-serializejson.js' ), $deps, PLUGIN_VERSION );

    $deps = array(
        'jquery',
        'jquery-serializejson'
    );
    $l10n = array(
        'rest_url'    => rest_url(),
        'rest_nonce'  => wp_create_nonce( 'wp_rest' ),
        'enforce_tos' => get_option( Options::ENFORCE_TOS )
    );
    ucare_register_script( 'login', resolve_url( 'assets/js/login.js' ), $deps, PLUGIN_VERSION, true );
    ucare_localize_script( 'login', '_ucare_login_l10n', $l10n );
}


/**
 * Enqueue all of the scripts needed for the system's front-end.
 *
 * @action ucare_enqueue_scripts
 *
 * @since 1.4.2
 * @return void
 */
function enqueue_default_scripts() {

    // Scripts
    ucare_enqueue_script( 'ucare' );
    ucare_enqueue_script( 'ucare-extensions' );
    ucare_enqueue_script( 'jquery' );
    ucare_enqueue_script( 'wp-util' );
    ucare_enqueue_script( 'sweetalert' );

    ucare_enqueue_script( 'bootstrap' );
    ucare_enqueue_script( 'dropzone' );

    ucare_enqueue_script( 'script', resolve_url( 'assets/js/script.js' ), null, PLUGIN_VERSION, true );

    // Only load these scripts in the app
    if ( is_support_page() ) {
        ucare_enqueue_script( 'scrolling-tabs',    resolve_url( 'assets/lib/scrollingTabs/scrollingTabs.min.js'  ), null, PLUGIN_VERSION, true );
        ucare_enqueue_script( 'light-gallery',     resolve_url( 'assets/lib/lightGallery/js/lightgallery.min.js' ), null, PLUGIN_VERSION, true );
        ucare_enqueue_script( 'moment',            resolve_url( 'assets/lib/moment/moment.min.js'                ), null, PLUGIN_VERSION, true );
        ucare_enqueue_script( 'lg-zoom',           resolve_url( 'assets/lib/lightGallery/plugins/lg-zoom.min.js' ), null, PLUGIN_VERSION, true );
        ucare_enqueue_script( 'textarea-autosize', resolve_url( 'assets/lib/textarea-autosize.min.js'            ), null, PLUGIN_VERSION, true );

        enqueue_app();

        ucare_enqueue_script( 'ucare-dashboard', resolve_url( 'assets/js/dashboard.js'  ), null, PLUGIN_VERSION, true );
        ucare_enqueue_script( 'ucare-plugins',   resolve_url( 'assets/js/plugins.js'    ), null, PLUGIN_VERSION, true );
        ucare_enqueue_script( 'ucare-tickets',   resolve_url( 'assets/js/ticket.js'     ), null, PLUGIN_VERSION, true );
        ucare_enqueue_script( 'ucare-comments',  resolve_url( 'assets/js/comment.js'    ), null, PLUGIN_VERSION, true );
    }

    // Load create ticket page scripts
    else if ( is_create_ticket_page() ) {
        ucare_enqueue_script( 'ucare-create-ticket', resolve_url( 'assets/js/create-ticket.js' ), array( 'ucare', 'jquery-serializejson' ), PLUGIN_VERSION, true );

    // Load edit profile page scripts
    } else if ( is_edit_profile_page() ) {
        ucare_enqueue_script( 'ucare-edit-profile', resolve_url( 'assets/js/edit-profile.js' ), array( 'ucare', 'jquery-serializejson' ), PLUGIN_VERSION, true );

    // Load login page scripts
    } else if ( is_login_page() ) {
        ucare_enqueue_script( 'login' );
    }
}


/**
 * Localizes and enqueues the core app script.
 *
 * @since 1.4.2
 * @return void
 */
function enqueue_app() {

    $i10n = array(
        'ajax_nonce'          => wp_create_nonce( 'support_ajax' ),
        'ajax_url'            => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
        'refresh_interval'    => abs( get_option( Options::REFRESH_INTERVAL, Defaults::REFRESH_INTERVAL ) ),
        'max_attachment_size' => get_option( Options::MAX_ATTACHMENT_SIZE, Defaults::MAX_ATTACHMENT_SIZE ),
        'strings' => array(
            'loading_tickets'   => __( 'Loading Tickets...', 'ucare' ),
            'loading_generic'   => __( 'Loading...', 'ucare' ),
            'delete_comment'    => __( 'Delete Comment', 'ucare' ),
            'delete_attachment' => __( 'Delete Attachment', 'ucare' ),
            'close_ticket'      => __( 'Close Ticket', 'ucare' ),
            'warning_permanent' => __( 'Are you sure you want to do this? This operation cannot be undone!', 'ucare' ),
            'yes' => __( 'Yes', 'ucare' ),
            'no'  => __( 'No', 'ucare' ),
            'drop_files' => __( 'Drop files here to upload', 'ucare' ),
            'browser_suport' => __( 'Your browser does not support drag\'n\'drop file uploads.', 'ucare' ),
            'fallback_upload' => __( 'Please use the fallback form below to upload your files like in the olden days.', 'ucare' ),
            'file_too_big' => __( 'File is too big (%sMiB). Max filesize: %dMiB.', 'ucare' ),
            'file_type_unathorized' => __( 'You can\'t upload files of this type.', 'ucare' ),
            'server_status_code' => __( 'Server responded with %s code.' ),
            'cancel_upload' => __( 'Cancel upload', 'ucare' ),
            'cancel_this_upload' => __( 'Are you sure you want to cancel this upload?', 'ucare' ),
            'remove_file' => __( 'Remove file', 'ucare' ),
            'too_many_files' => __( 'You can not upload any more files.', 'ucare' )            
        )
    );

    ucare_register_script( 'ucare-app', resolve_url( 'assets/js/app.js' ), null, PLUGIN_VERSION, true );
    ucare_localize_script( 'ucare-app', 'Globals', $i10n );

    ucare_enqueue_script( 'ucare-app' );

}

