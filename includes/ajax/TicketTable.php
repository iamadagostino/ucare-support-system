<?php

namespace SmartcatSupport\ajax;


class TicketTable extends AjaxComponent {

//    public function list_tickets() {
//        $html = $this->render( $this->plugin->template_dir . '/ticket_table.php',
//            array(
//                'headers' => $this->table_headers(),
//                'data' => $this->get_tickets()
//            )
//        );
//
//        wp_send_json_success( $html );
//    }

    public function list_tickets() {
        $html = $this->render( $this->plugin->template_dir . '/ticket_list.php',
            array(
                'query' => $this->get_tickets()
            )
        );

        wp_send_json_success( $html );
    }

    public function table_data( $col, $ticket ) {
        switch( $col ) {
            case 'id':
                echo $ticket->ID;
                break;

            case 'subject':
                echo $ticket->post_title;
                break;

            case 'email':
                echo \SmartcatSupport\util\ticket\author_email( $ticket );
                break;

            case 'status':
                $statuses = \SmartcatSupport\util\ticket\statuses();
                $status = get_post_meta( $ticket->ID, 'status', true );

                if( array_key_exists( $status, $statuses ) ) {
                    echo '<span class="status-wrapper">'
                            . '<span class="ticket-status ' . $status . '"></span>'
                            . '<span class="status-tooltip">' . $statuses[ $status ] . '</span>'
                            . '</span>';
                }

                break;

            case 'priority':
                $priorities = \SmartcatSupport\util\ticket\priorities();
                $priority = get_post_meta( $ticket->ID, 'priority', true );

                echo array_key_exists( $priority, $priorities ) ? $priorities[ $priority ] : '—';

                break;

            case 'date':
                echo get_the_date( 'M j Y g:i A', $ticket->ID );
                break;

            case 'agent':
                $agents = \SmartcatSupport\util\user\list_agents();
                $agent = get_post_meta( $ticket->ID, 'agent', true );

                echo array_key_exists( $agent, $agents ) ? $agents[ $agent ] : __( 'Unassigned', \SmartcatSupport\PLUGIN_ID );

                break;

            case 'product':
                $products = \SmartcatSupport\util\ticket\products();
                $product = get_post_meta( $ticket->ID, 'product', true );

                if( array_key_exists( $product, $products ) ) {
                    echo $products[ $product ];
                }

                break;

            case 'actions':
                echo '<div class="actions">' .
                        '<button type="button" class="trigger icon-bubbles open-ticket"' .
                         'data-id="' . $ticket->ID . '"></button></div>';
                break;
        }
    }

    public function filter_tickets( $args ) {
        $form = include $this->plugin->config_dir . '/ticket_filter.php';

        if( $form->is_valid() ) {
            foreach( $form->data as $name => $value ) {
                if( !empty( $value ) ) {
                    $args['meta_query'][] = array( 'key' => $name, 'value' => $value );
                }
            }
        }

        return $args;
    }

    public function subscribed_hooks() {
        return parent::subscribed_hooks( array(
            'wp_ajax_support_list_tickets' => array( 'list_tickets' ),
            'support_tickets_table_column_data' => array( 'table_data', 10, 2 ),
            'support_ticket_table_query_vars' => array( 'filter_tickets' )
        ) );
    }

    private function get_tickets() {
        $args = array(
            'post_type' => 'support_ticket',
            'post_status' => 'publish',
            'posts_per_page' => 5,
            'paged' => isset ( $_REQUEST['page'] ) ? $_REQUEST['page'] : 1
        );

        if( !current_user_can( 'edit_others_tickets' ) ) {
            $args['author'] = wp_get_current_user()->ID;
        }

//        $query = new \WP_Query( apply_filters( 'support_ticket_table_query_vars', $args ) );

//        return $query->posts;

        return new \WP_Query( apply_filters( 'support_ticket_table_query_vars', $args ) );
    }

    private function table_headers() {
        $headers = array(
            'id'        => __( 'Case #', \SmartcatSupport\PLUGIN_ID ),
            'status'    => __( 'Status', \SmartcatSupport\PLUGIN_ID ),
            'subject'   => __( 'Subject', \SmartcatSupport\PLUGIN_ID ),
            'email'     => __( 'Email', \SmartcatSupport\PLUGIN_ID ),
            'date'      => __( 'Date', \SmartcatSupport\PLUGIN_ID )
        );

        if( \SmartcatSupport\util\ticket\ecommerce_enabled() ) {
            $headers['product'] = __( 'Product', \SmartcatSupport\PLUGIN_ID );
        }

        if( current_user_can( 'manage_support_tickets' ) ) {
            $headers['priority'] = __( 'Priority', \SmartcatSupport\PLUGIN_ID );
            $headers['agent'] = __( 'Assigned', \SmartcatSupport\PLUGIN_ID );
        }

        $headers['actions'] = __( 'Actions', \SmartcatSupport\PLUGIN_ID );

        return apply_filters( 'support_ticket_table_headers', $headers );
    }
}
