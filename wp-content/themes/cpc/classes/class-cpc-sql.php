<?php

if ( !class_exists('WP_CPC_SQL') ) {

    class WP_CPC_SQL {

        public $is_prod;
        private $global_wpdb;

        public function __construct() {

            global $wpdb;
            $this->global_wpdb = $wpdb;

            // DEFAULT ENV VARIABLES
            $this->is_prod = strpos( $_SERVER['SERVER_NAME'], "cpcintersect.com" ) !== false ? true : false;

        }

        public function get_attachment_id_from_src( $src ) {
            global $wpdb;
            $reg = "/-[0-9]+x[0-9]+?.(jpg|jpeg|png|gif|svg)$/i";
            $src1 = preg_replace($reg,'',$src);

            if ($src1 != $src){
                $ext = pathinfo($src, PATHINFO_EXTENSION);
                $src = $src1 . '.' .$ext;
            }

            $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$src'";
            $id = $wpdb->get_var($query);
            return $id;
        }

        public function get_meta_by_key( $meta_key = false, $compare = '=' ) {

            if ( !$meta_key ) { return false; }

            $value = '';

            if ( is_array( $meta_key ) ) {

                $compare = 'LIKE';
                $mk_counter = 0;

                foreach ( $meta_key as $mk ) {
                    
                    $prepending = $mk_counter ? " OR meta_key " : "( meta_key ";
                    $value .= $prepending . $compare . " '" . $mk . "'";

                    $mk_counter++;

                }

                $value .= ')';

            } else {

                $value = 'meta_key ' . $compare . ' ' . $meta_key;

            }

            $table_name = $this->global_wpdb->prefix . "postmeta";

            $custom_query = $this->global_wpdb->get_results(
                "
                SELECT post_id, meta_value
                FROM   $table_name
                WHERE   $value AND meta_value != ''
                "
            );

            if ( !empty( $custom_query ) ) {
                return $custom_query;
            }

        }

        public function get_image_id( $attachment_url = '' ) {

          $attachment_id = false;
         
          // If there is no url, return.
          if ( '' == $attachment_url )
            return;
         
          // Get the upload directory paths
          $upload_dir_paths = wp_upload_dir();
         
          // Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
          if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
         
            // If this is the URL of an auto-generated thumbnail, get the URL of the original image
            $attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
         
            // Remove the upload path base directory from the attachment URL
            $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );
         
            // Finally, run a custom database query to get the attachment ID from the modified attachment URL
            $attachment_id = $this->global_wpdb->get_var( $this->global_wpdb->prepare( "SELECT wposts.ID FROM $this->global_wpdb->posts wposts, $this->global_wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
         
          }
         
          return $attachment_id;

        }

        public function get_meta_post_ids( $meta_key = false, $meta_value = false ) {

            if ( !$meta_key || !$meta_value ) { return false; }

            $table_name = $this->global_wpdb->prefix . "postmeta";

            $custom_query = $this->global_wpdb->get_results( $wpdb->prepare(
                "
                SELECT DISTINCT post_id
                FROM            $table_name
                WHERE           meta_key = '%s'
                AND             meta_value = '%s'
                ",
                $meta_key,
                $meta_value
            ) );

            if ( !empty( $custom_query ) ) {
                return $custom_query;
            }

        }

    }

}