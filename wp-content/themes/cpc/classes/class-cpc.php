<?php

/**
 * cpc Theme Controller
 *
 * Class that handles all front end theme functionality.
 *
 * @class     WP_CPC
 * @version   1.0.0
 * @package   cpc/assets/classes
 * @category  Class
 * @author    GoKart Labs
 */

if ( !class_exists('WP_CPC') ) {

  class WP_CPC {

    public $is_prod;
    private $global_post;
    public $detect;
    public $content_block_closed;
    public $content_block_width;
    public $content_block_previous;
    public $content_block_counter;
    public $hero_two_featured_blocks_count;
    public $options;
    public $site_message_copy;
    public $site_message_link;
    public $site_message_url;
    public $sql;
    public $ajax;

    public function __construct() {

      // DEFAULT ENV VARIABLES
      $this->is_prod                        = strpos( $_SERVER['SERVER_NAME'], "cpc.com" ) !== false ? true : false;
      $this->content_block_closed           = false;
      $this->content_block_width            = 0;
      $this->content_block_previous         = '';
      $this->content_block_counter          = 0;
      $this->hero_two_featured_blocks_count = 0;
      $this->options                        = get_option( 'cpc_options' );
      $this->site_message_copy              = get_option( 'cpc_site_message_copy' );
      $this->site_message_link              = get_option( 'cpc_site_message_link' );
      $this->site_message_url               = get_option( 'cpc_site_message_url' );
      $this->site_message_type              = get_option( 'cpc_site_message_type' );
      $this->detect                         = new Mobile_Detect();
      $this->sql                            = new WP_CPC_SQL();
      $this->ajax                           = new WP_CPC_Ajax();

      // CLEANUP WP STUFF
      add_action( 'init', array( $this, 'head_cleanup' ) );

      // ENQUEUE SCRIPTS/STYLES
      add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ), 999 );

      // CUSTOM READ MORE LINK
      add_filter( 'the_content_more_link', array( $this, 'custom_read_more_link' ) );

      // SET GLOBAL POST VARIABLE
      add_action( 'wp', array( $this, 'set_global_post' ) );

      // MODIFY LOOP
      add_action( 'pre_get_posts', array( $this, 'modify_loop' ) );

      // ADD BODY CLASSES
      add_filter( 'body_class', array( $this, 'body_classes' ) );

      // ADD SOCIAL META
      add_action( 'wp_head', array( $this, 'social_meta' ), 5 );

      // LOAD TYPEKIT
      add_action( 'wp_head', array( $this, 'typekit_inline' ) );
      
    }

    
    public function body_classes( $classes ) {

      global $post;

      if ( is_page() ) {

        $page_division = get_post_meta( $post->ID, '_page_division', true );
        if ( $page_division ) $classes[] = 'page-division-' . $page_division;

        if ( has_post_thumbnail( $post->ID ) ) {
          $classes[] = 'page-has-featured-image';
        } else {
          $classes[] = 'page-no-featured-image';
        }
        
      }

      return $classes;

    }

    public function is_mobile() {
      return $this->detect->isMobile();
    }

    public function is_tablet() {
      return $this->detect->isTablet();
    }

    public function set_global_post() {
      global $post;
      $this->global_post = $post;
    }

    public function custom_read_more_link() {
      return '<a class="button-more button button-small button-black" href="' . get_permalink() . '">Read More</a>';
    }

    /************************************************************************/
    /* ADD SOCIAL META DATA
    /************************************************************************/

    function social_meta() {

      global $post;

      // SET VARIABLES
      $description = get_bloginfo('description');
      $image       = '';
      $title       = is_home() || is_front_page() ? 'Home' : get_the_title( $post_id );
      $title       = is_archive() ? post_type_archive_title(false, false) : $title;
      $title       = is_search() ? 'Search' : $title;
      $url         = is_search() || is_category() || is_archive() || is_404() ? "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" : get_permalink();
      $has_thumb   = has_post_thumbnail( $post->ID );

      // SET IMAGE
      if ( !$has_thumb || is_search() || is_category() || is_archive() || is_404() ) {
        $image = get_template_directory_uri() . "/assets/images/default-thumb.gif";
      } else {
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'landscape-m' );
        $image = esc_attr( $image[0] );
      }

      // FACEBOOK OPEN GRAPH META
      echo '<!-- open graph meta -->';
      echo '<meta property="og:title" content="' . $title . '"/>';  
      echo '<meta property="og:url" content="' . $url . '"/>';
      echo '<meta property="og:description" content="' . $description . '" />';
      echo '<meta property="og:type" content="website"/>';
      echo '<meta property="og:site_name" content="' . get_bloginfo('name') . '"/>';
      echo '<meta property="og:locale" content="en_US" />';
      echo '<meta property="og:image" content="' . $image . '"/>';
      echo "\n\r";

      // TWITTER CARD META
      echo '<!-- twitter card meta -->';
      echo '<meta name="twitter:card" content="summary">';
      echo '<meta name="twitter:site" content="@cpc">';
      echo '<meta name="twitter:title" content="' . $title . '">';
      echo '<meta name="twitter:description" content="' . $description . '">';
      echo '<meta name="twitter:image" content="' . $image . '">';
      echo '<meta name="twitter:domain" content=' . get_bloginfo('url') . '">';
      echo "\n\r";

    }

    public function modify_loop( $query ) {

      if ( is_category() ) {

        $query->set( 'posts_per_page', 8 );
        return;

      }

    }

    public function related_posts( $post, $count = 0, $not_in = array() ) {

      if ( empty( $post ) || !$count ) return false; 

      $tags     = wp_get_post_tags( $post->ID );
      $not_in[] = $post->ID;

      if ( $tags ) {

        $tag_ids = array();
        
        foreach ( $tags as $individual_tag ) $tag_ids[] = $individual_tag->term_id;
        
        $args = array(
          'tag__in'          => $tag_ids,
          'post__not_in'     => $not_in,
          'posts_per_page'   => $count, // Number of related posts that will be shown.
          'caller_get_posts' => 1
        );

        $related_posts = get_posts( $args );

        return $related_posts;

      }

    }

    public function substr_with_ellipsis( $string, $chars = 100 ) {
      $str_len = strlen( $string );
      if ( ( $chars + 3 ) >= $str_len ) return $string;
      preg_match( '/^.{0,' . $chars . '}(?:.*?)/iu', $string, $matches );
      $new_string = $matches[0];
      return ( $new_string === $string ) ? $string : $new_string . '&hellip;';
    }

    public function get_image_caption( $id ) {

      $image = get_post( $id );
      return $image && $image->post_type == 'attachment' ? $image->post_excerpt : false;

    }

    public function add_timestamp_to_asset_url( $path ) {
      $file = get_stylesheet_directory() . $path;
      return file_exists( $file ) ? $path . '?v=' . filemtime( get_stylesheet_directory() . $path ) : $path;
    }

    public function remove_wp_ver_css_js( $src ) {
      if ( strpos( $src, 'ver=' ) ) {
        $src = remove_query_arg( 'ver', $src );
      }
      return $src;
    }

    public function head_cleanup() {
      remove_action( 'wp_head', 'feed_links_extra', 3 );
      remove_action( 'wp_head', 'feed_links', 2 );
      remove_action( 'wp_head', 'rsd_link' );
      remove_action( 'wp_head', 'wlwmanifest_link' );
      remove_action( 'wp_head', 'index_rel_link' );
      remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
      remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
      remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
      remove_action( 'wp_head', 'wp_generator' );
      add_filter( 'style_loader_src', array( $this, 'remove_wp_ver_css_js' ), 9999 );
      add_filter( 'script_loader_src', array( $this, 'remove_wp_ver_css_js' ), 9999 );
      remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
      show_admin_bar(false);
    }

    public function typekit_inline() {

      if ( wp_script_is( 'typekit', 'done' ) ) { ?>

        <script type="text/javascript">try{Typekit.load();}catch(e){}</script>

    <?php }

    }

    public function enqueue_scripts_and_styles() {

      if ( !is_admin() ) {

        $stylesheet_url = get_bloginfo('stylesheet_directory');

        // register fonts and stylesheets
        wp_register_style( 'cpc-stylesheet', $stylesheet_url . $this->add_timestamp_to_asset_url('/assets/build/css/style.css'), array(), time(), 'all' ); wp_enqueue_style( 'cpc-stylesheet' );
      
        // register scripts
        wp_register_script( 'modernizr', $stylesheet_url . '/assets/build/js/modernizr.min.js' ); wp_enqueue_script( 'modernizr' );

        wp_deregister_script('jquery');

        if ( preg_match( '/(?i)msie 8/', $_SERVER['HTTP_USER_AGENT'] ) ) {
          wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js' ); wp_enqueue_script( 'jquery' );
          wp_register_script( 'jquery-migrate', '//code.jquery.com/jquery-migrate-1.1.0.min.js' ); wp_enqueue_script( 'jquery-migrate' );
          wp_register_script( 'respond-js', '//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js' ); wp_enqueue_script( 'respond-js' );
        } else {
          wp_register_script( 'jquery', "//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"); wp_enqueue_script( 'jquery' );
        }

        wp_enqueue_script( 'typekit', '//use.typekit.net/hce7oqv.js' );

        if ( $this->is_mobile() || $this->is_tablet() ) {
          wp_register_script( 'swipe-js', '//cdnjs.cloudflare.com/ajax/libs/swipe/2.0/swipe.min.js', array( 'jquery' ) ); wp_enqueue_script( 'swipe-js' );
        }

        wp_register_script( 'cpc-js', $stylesheet_url . $this->add_timestamp_to_asset_url('/assets/build/js/scripts.min.js'), array( 'jquery' ) ); wp_enqueue_script( 'cpc-js' );

        // LOCALIZE AJAX
        $postID = isset( $this->global_post ) ? $this->global_post->ID : 0;
        wp_localize_script( 'cpc-js', 'localized_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'salesforce-ajax-nonce' ), 'base_url' => get_bloginfo( 'url' ), 'postID' => $postID, 'is_mobile' => $this->is_mobile(), 'is_tablet' => $this->is_tablet() ) );

      }

    }

  }

  $cpc = new WP_CPC();

}