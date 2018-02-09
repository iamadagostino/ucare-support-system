<?php
/**
 *
 * @since 1.6.1
 * @package ucare
 */
namespace ucare;


/**
 * Add-ons menu page.
 *
 * @since 1.6.1
 * @package ucare
 */
class AddonsPage extends MenuPage {

    /**
     * Base slug
     *
     * @var string
     */
    protected $slug = 'add-ons';

    /**
     * Make a call to add_menu_page()
     *
     * @since 1.6.1
     * @return string
     */
    public function add_menu_page() {
        return add_submenu_page( 'ucare_support', __( 'Add-ons', 'ucare' ), __( 'Add-ons', 'ucare' ), 'manage_options', 'ucare-add-ons', array( $this, 'render' ) );
    }

    /**
     * Do enqueues and setup data for the page.
     *
     * @since 1.6.1
     * @return void
     */
    public function on_load() {
        parent::on_load();

        $this->enqueue_scripts();
    }

    /**
     * Enqueue menu page scripts.
     *
     * @since 1.6.1
     * @return void
     */
    private function enqueue_scripts() {
        $bundle = ucare_dev_var( 'bundle.production.min.js', 'bundle.dev.js' );
        $deps = array(
            'react',
            'redux',
            'react-redux',
            'react-dom'
        );

        $localize = array(
            'vars' => array(
                'products' => $this->fetch_products()
            )
        );

        wp_register_script( 'ucare-add-ons', strcat( $this->assets_url, 'build/', $bundle ), $deps, PLUGIN_VERSION, true );
        wp_localize_script( 'ucare-add-ons', 'ucare_addons_l10n', $localize );

        wp_enqueue_script( 'ucare-add-ons' );
    }

    /**
     * Output the menu page.
     *
     * @since 1.6.1
     */
    public function render() {
        echo '<h1>', __( 'Add-ons', 'ucare' ), '</h1><div id="ucare-add-ons"></div>';
    }

    /**
     * Fetch products from the cache. If cache has expired, re-cache from our server.
     *
     * @since 1.6.1
     * @return array
     */
    private function fetch_products() {
        $cached = get_transient( 'ucare_addons_cache' );

        if ( is_array( $cached ) ) {
            return $cached;
        }

        $response = wp_remote_get( 'https://ucaresupport.com/wp-json/smartcat/v1/downloads' );

        if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
            return array();
        }

        $products = json_decode( wp_remote_retrieve_body( $response ), true );
        set_transient( 'ucare_addons_cache', $products, 60 * 60 * 24 );

        return $products;
    }

}
