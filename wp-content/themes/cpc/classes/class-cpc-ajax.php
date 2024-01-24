<?php

if ( !class_exists('WP_CPC_Ajax') ) {

    class WP_CPC_Ajax {

        public function __construct() {

            add_action( 'wp_ajax_nopriv_my_ajax_callback', array( $this, 'my_ajax_callback' ) );
            add_action( 'wp_ajax_my_ajax_callback', array( $this, 'my_ajax_callback' ) );
              
        }

        public function my_ajax_callback() {
            
        }

    }

}