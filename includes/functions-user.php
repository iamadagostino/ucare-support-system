<?php
/**
 * Functions for managing WordPress users.
 *
 * @since 1.4.2
 * @package ucare
 */
namespace ucare;


// TODO add caps for support agent & user to edit own media



// Create a draft ticket for a user
add_action( 'template_redirect', 'ucare\create_user_draft_ticket' );

// Add user roles
add_action( 'init', 'ucare\add_user_roles' );

// Assign caps to user roles
add_action( 'init', 'ucare\add_role_capabilities' );


/**
 * Add customer user roles for the support system.
 *
 * @since 1.5.1
 * @return void
 */
function add_user_roles() {

    $roles = array(
        'support_admin' => __( 'Support Admin', 'ucare' ),
        'support_agent' => __( 'Support Agent', 'ucare' ),
        'support_user'  => __( 'Support User',  'ucare' )
    );

    foreach ( $roles as $role => $name ) {

        if ( is_null( get_role( $role ) ) ) {
            add_role( $role, $name );
        }

    }

}


/**
 * Add capabilities to user roles.
 *
 * @since 1.5.1
 * @return void
 */
function add_role_capabilities() {

    // Add capabilities to administrator
    $role = get_role( 'administrator' );

    if ( $role ) {

        /**
         *
         * System wide access control caps
         */
        $role->add_cap( 'use_support' );
        $role->add_cap( 'manage_support' );
        $role->add_cap( 'manage_support_tickets' );

        /**
         *
         * support_ticket specific caps
         */
        $role->add_cap( 'publish_support_tickets' );

        $role->add_cap( 'edit_support_ticket' );
        $role->add_cap( 'edit_support_tickets' );
        $role->add_cap( 'edit_others_ticket_notes' );
        $role->add_cap( 'edit_private_ticket_notes' );
        $role->add_cap( 'edit_published_ticket_notes' );

        $role->add_cap( 'delete_support_ticket' );
        $role->add_cap( 'delete_support_tickets' );
        $role->add_cap( 'delete_others_support_tickets' );
        $role->add_cap( 'delete_private_support_tickets' );
        $role->add_cap( 'delete_published_support_tickets' );

        $role->add_cap( 'read_support_ticket' );
        $role->add_cap( 'read_private_support_tickets' );

        /**
         *
         * Administrator already has full control over media and comments
         */
    }


    // Add capabilities to support admins
    $role = get_role( 'support_admin' );

    if ( $role ) {

        /**
         *
         * System wide access control caps
         */
        $role->add_cap( 'use_support' );
        $role->add_cap( 'manage_support' );
        $role->add_cap( 'manage_support_tickets' );

        /**
         *
         * support_ticket specific caps
         */
        $role->add_cap( 'publish_support_tickets' );

        $role->add_cap( 'edit_support_ticket' );
        $role->add_cap( 'edit_support_tickets' );
        $role->add_cap( 'edit_others_ticket_notes' );
        $role->add_cap( 'edit_private_ticket_notes' );
        $role->add_cap( 'edit_published_ticket_notes' );

        $role->add_cap( 'delete_support_ticket' );
        $role->add_cap( 'delete_support_tickets' );
        $role->add_cap( 'delete_others_support_tickets' );
        $role->add_cap( 'delete_private_support_tickets' );
        $role->add_cap( 'delete_published_support_tickets' );

        $role->add_cap( 'read_support_ticket' );
        $role->add_cap( 'read_private_support_tickets' );


    }


    // Add capabilities to support agents
    $role = get_role( 'support_agent' );

    if ( $role ) {

        /**
         *
         * System wide access control caps
         */
        $role->add_cap( 'use_support' );
        $role->add_cap( 'manage_support_tickets' );

        /**
         *
         * support_ticket specific caps. Agents can only create, edit non-published and read others tickets.
         */
        $role->add_cap( 'publish_support_tickets' );

        $role->add_cap( 'edit_support_ticket' );
        $role->add_cap( 'edit_support_tickets' );

        $role->add_cap( 'read_support_ticket' );

    }


    // Add capabilities to support users
    $role = get_role( 'support_user' );

    if ( $role ) {

        /**
         *
         * System wide access control caps
         */
        $role->add_cap( 'use_support' );

        /**
         *
         * support_ticket specific caps. Users can only create, edit non-published and read tickets.
         */
        $role->add_cap( 'publish_support_tickets' );

        $role->add_cap( 'edit_support_ticket' );
        $role->add_cap( 'edit_support_tickets' );

        $role->add_cap( 'read_support_ticket' );

    }


    // If EDD is active
    add_subscriber_caps();

    // If Woo is active
    add_customer_caps();

}

/**
 * Add support user capabilities to the subscriber role.
 *
 * @param bool $force Skip check to see if EDD is active.
 *
 * @since 1.5.1
 * @return void
 */
function add_subscriber_caps( $force = false ) {

    if ( $force || UCARE_ECOMMERCE_MODE === 'edd' ) {

        $role = get_role( 'subscriber' );

        if ( $role ) {
            $role->add_cap( 'user_support' );
        }

    }

}

/**
 * Add support user capabilities to the customer role.
 *
 * @param bool $force Skip check to see if WooCommerce is active.
 *
 * @since 1.5.1
 * @return void
 */
function add_customer_caps( $force = false ) {

    if ( $force || UCARE_ECOMMERCE_MODE === 'woo' ) {

        $role = get_role( 'customer' );

        if ( $role ) {
            $role->add_cap( 'use_support' );
        }

    }

}


/**
 * Remove capabilities from user roles.
 *
 * @since 1.5.1
 * @return void
 */
function remove_capabilities() {

}


/**
 * Remove support user capabilities from the subscriber role.
 *
 * @since 1.5.1
 * @return void
 */
function remove_subscriber_caps() {

}

/**
 * Remove support user capabilities from the customer role.
 *
 * @since 1.5.1
 * @return void
 */
function remove_customer_caps() {

}


/**
 * @param string $cap
 * @param array $args
 *
 * @since 1.4.2
 * @return array
 */
function get_users_with_cap( $cap = 'use_support', $args = array() ) {

    $users = get_users( $args );

    return array_filter( $users, function ( $user ) use ( $cap ) {

        return $user->has_cap( $cap );

    } );

}


/**
 * Get a WordPress user, defaults to the current logged in user.
 *
 * @param mixed  $user
 * @param string $by
 *
 * @since 1.5.1
 * @return mixed
 */
function get_user_by_field( $user = null, $by = 'id' ) {

    if ( !$user ) {
        $user = wp_get_current_user();
    } else {
        $user = get_user_by( $by, $user );
    }

    return $user;

}


/**
 * Check to see if a user has a capability.
 *
 * @param string $cap
 * @param int|null  $user_id
 *
 * @since 1.5.1
 * @return boolean
 */
function user_has_cap( $cap, $user_id = null ) {

    $user = get_user_by_field( $user_id );

    if ( $user ) {
        return $user->has_cap( $cap );
    }

    return false;

}


/**
 * Get the value of a single field from a \WP_User object
 *
 * @param string $field The field to get
 * @param mixed  $user
 *
 * @since 1.4.2
 * @return mixed
 */
function get_user_field( $field, $user = null ) {

    $user = get_user( $user );

    if ( $user ) {
        return get_the_author_meta( $field, $user->ID );
    }

    return false;

}


/**
 * Gets a \WP_user object. If no user is passed, will default to the currently logged in user.
 * Returns false if no user can be found.
 *
 * @param mixed $user The user to get.
 *
 * @since 1.0.0
 * @return false|\WP_User
 */
function get_user( $user = null ) {

    if ( is_null( $user ) ) {
        $user = wp_get_current_user();
    } else if ( is_numeric( $user ) ) {
        $user = get_userdata( absint( $user ) );
    }

    // Make sure we have a valid support user
    return $user->has_cap( 'use_support' ) ? $user : false;

}


/**
 * Create a post draft for the user when they navigate to the create ticket page.
 *
 * @action template_redirect
 *
 * @since 1.5.1
 * @return void
 */
function create_user_draft_ticket() {

    if ( !get_user_draft_ticket() ) {

        $user = get_current_user_id();

        $data = array(
            'post_author' => $user,
            'post_type'   => 'support_ticket',
            'post_status' => 'draft'
        );

        $id = wp_insert_post( $data );

        if ( is_numeric( $id ) ) {
            update_user_meta( $user, 'draft_ticket', $id );
        }

    }

}


/**
 * Get the draft ticket for the current user.
 *
 * @since 1.5.1
 * @return \WP_Post|false
 */
function get_user_draft_ticket() {

    $draft = get_post( get_user_meta( get_current_user_id(), 'draft_ticket', true ) );

    if ( $draft && $draft->post_status == 'draft' ) {
        return $draft;
    }

    return false;

}

//print_r( get_option( 'wp_user_roles'));die;
