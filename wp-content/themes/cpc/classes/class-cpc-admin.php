<?php

if ( !class_exists('WP_CPC_Admin') ) {

  class WP_CPC_Admin {

    public $is_prod;
    public $sql;

    public function __construct() {

      // DEFAULT ENV VARIABLES
      $this->is_prod = strpos( $_SERVER['SERVER_NAME'], "cpcintersect.com" ) !== false ? true : false;
      $this->sql     = new WP_cpc_SQL();

      // ADD ADMIN MENU
      add_action( 'admin_menu', array( $this, 'setup_theme_admin_menus' ) );

      // INIT CUSTOM METABOXES
      add_action( 'init', array( $this, 'initialize_cmb_meta_boxes' ), 9999 );

      // ADD CUSTOM METABOXES
      add_filter( 'cmb_meta_boxes', array( $this, 'custom_metaboxes' ) );

      // ADD CUSTOM POST TYPES
      add_action( 'init', array( $this, 'register_custom_post_types' ), 0 );

      // ADD CUSTOM TAXONOMIES
      add_action( 'init', array( $this, 'register_taxonomies' ), 0 );

      // ADD CUSTOM POST COLUMNS
      add_filter( 'manage_posts_columns', array( $this, 'columns_head_posts' ), 10 );  
      add_action( 'manage_posts_custom_column', array( $this, 'columns_content_posts' ), 10, 2 );

      // REMOVE PAGE EDITOR
      add_action('admin_init', array( $this, 'remove_editor' ) );

      // REGISTER SIDEBARS
      add_action( 'widgets_init', array( $this, 'register_sidebars' ) );

      // ADD THEME SUPPORT
      add_action( 'init', array( $this, 'theme_support' ) );

      // REGISTER MENUS
      add_action( 'init', array( $this, 'create_menus' ) );

      // ENQUEUE ADMIN SCRIPTS/STYLES
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_styles' ) );

      // CUSTOM CONTENT FOR REVISION HISTORY
      //add_action( 'wp_restore_post_revision', 'restore_revision', 10, 2 );
      add_filter( '_wp_post_revision_fields', array( $this, 'revision_fields' ) );
      //add_filter( '_wp_post_revision_field__content_block_width', 'revision_field', 10, 2 );
      //
      add_filter( 'image_size_names_choose', array( $this, 'media_image_sizes' ) );

      add_action( 'admin_init', array( $this, 'editor_styles' ) );

      add_action( 'cmb_render_cta_internal_url', array( $this, 'cta_internal_url' ), 10, 2 );

      add_filter( 'cmb_validate_cta_internal_url', array( $this, 'validate_cta_internal_url' ) );

      add_action( 'cmb_render_add_cta', array( $this,'cmb_render_cta_internal_url'), 10, 2 );

      add_filter( 'cmb_validate_add_cta', array( $this,'cmb_validate_cta_internal_url') );
      
      add_filter( 'upload_mimes', array( $this, 'media_uploader_mime_types' ) );

      add_action( 'admin_menu', array( $this, 'remove_menus' ) );

    }

    public function remove_menus() {
      remove_menu_page( 'index.php' );
      remove_menu_page( 'edit.php' );
      remove_menu_page( 'edit-comments.php' );
    }

    public function media_uploader_mime_types( $mimes ) {
      $mimes['svg'] = 'image/svg+xml';
      return $mimes;
    }

    public function cmb_render_cta_internal_url( $field, $meta ) {

      echo '<p class="cmb_metabox_description">Select a internal page/post to link to:</p>';

      $page_options = '';
      $post_options = '';

      $options = get_posts(

        array(
          'post_type'      => array( 'page', 'post' ),
          'posts_per_page' => -1,
          'orderby'        => 'post_title',
          'order'          => 'ASC'
        )

      );

      foreach ( $options as $option ) {
        
        if ( $option->post_type == 'page' ) {
          $page_options .= '<option value="'. get_permalink( $option->ID ) .'"'. selected( get_permalink( $option->ID ), $meta, false ) .'>'. $option->post_title .'</option>';
        } else {
          $post_options .= '<option value="'. get_permalink( $option->ID ) .'"'. selected( get_permalink( $option->ID ), $meta, false  ) .'>'. $option->post_title .'</option>';
        }

      }

      echo '<select name="',$field['id'],'" id="',$field['id'],'"><option value="">&#8212; Select Page or Post &#8212;</option><optgroup label="Pages">',$page_options ,'</optgroup><optgroup label="Posts">' . $post_options .'</optgroup></select>';

      if ( ! empty( $field['desc'] ) ) {
          echo '<p class="cmb_metabox_description">',$field['desc'],'</p>';
      }

    }

    function cmb_validate_cta_internal_url( $new ) {

      if ( empty ( $new ) ) return; 

      return $new;

    }

    function editor_styles() {
      add_editor_style( '/assets/build/css/editor.css' );
    }

    public function restore_revision( $post_id, $revision_id ) {

      // $post          = get_post( $post_id );
      // $revision      = get_post( $revision_id );
      // $meta_cta_text = get_metadata( 'post', $revision->ID, '_content_block_cta_text', true );

      // if ( false !== $meta_cta_text )
      //   update_post_meta( $post_id, '_content_block_cta_text', $meta_cta_text );
      // else
      //   delete_post_meta( $post_id, '_content_block_cta_text' );

    }

    public function revision_fields( $fields ) {

      global $post;

      if ( $post && !empty( $post->post_type ) && $post->post_type == 'content_blocks' ) {
        $fields['_content_block_width'] = 'Content Block Width';
      }

      return $fields;

    }

    public function revision_field( $value, $field ) {

    //   global $revision;
    //   return get_metadata( 'post', $revision->ID, $field, true );

    }

    public function admin_scripts_styles() {

      global $post;

      $ss_url  = get_bloginfo('stylesheet_directory');
      $post_id = !empty( $post->ID ) ? $post->ID : 0;

      wp_register_style( 'cpc-fonts', 'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' ); wp_enqueue_style( 'cpc-fonts');

      wp_register_script( 'app-admin', "$ss_url/assets/build/js/app-admin.min.js", array('jquery') ); wp_enqueue_script( 'app-admin' );
      wp_localize_script( 'app-admin', 'r_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'r_post_id' => $post_id ) );

      // STYLESHEET
      wp_register_style( 'admin', get_template_directory_uri() . '/assets/build/css/admin.css', array(), '1.0.0', 'all' );
      wp_enqueue_style( 'admin' );

    }

    public function cmb_get_term_options( $taxonomy = 'category', $args = array() ) {

        $args['taxonomy'] = $taxonomy;
        // $defaults = array( 'taxonomy' => 'category' );
        $args = wp_parse_args( $args, array( 'taxonomy' => 'category' ) );

        $taxonomy = $args['taxonomy'];

        $terms = (array) get_terms( $taxonomy, $args );

        // Initate an empty array
        $term_options = array();

        if ( ! empty( $terms ) ) {

          $term_options[]      = '&mdash; Choose Program &mdash;';
          $term_options['all'] = 'All Programs';

          foreach ( $terms as $term ) {
            $term_options[ $term->slug ] = $term->name;
          }
          
        }

        return $term_options;

    }

    function media_image_sizes( $sizes ) {

      $add_sizes = array(
        "cpc-medium" => __( "cpc Medium")
      );

      $new_sizes = array_merge( $sizes, $add_sizes );

      return $new_sizes;

    }

    public function theme_support() {

      add_theme_support( 'menus' );
      add_theme_support( 'post-thumbnails' );
      add_theme_support( 'category-thumbnails' ); 
      add_image_size( 'landscape-xl', 1600, 800, true );
      add_image_size( 'landscape-l', 1200, 600, true );
      add_image_size( 'landscape-m', 800, 400, true );
      add_image_size( 'cpc-medium', 440, 330, true );

    }

    public function create_menus() {

      $menus = array(

        'Main Menu' => array(

          array(
            'menu-item-title'      =>  __('About'),
            'menu-item-classes'    => 'nav-about',
            'menu-item-url'        => '#about', 
            'menu-item-target'     => '_self',
            'menu-item-attr-title' => 'About cpc',
            'menu-item-status'     => 'publish'
          ),
          array(
            'menu-item-title'      =>  __('Clients'),
            'menu-item-classes'    => 'nav-clients',
            'menu-item-url'        => '#clients', 
            'menu-item-target'     => '_self',
            'menu-item-attr-title' => 'Clients',
            'menu-item-status'     => 'publish'
          ),
          array(
            'menu-item-title'      =>  __('Contact'),
            'menu-item-classes'    => 'nav-contact',
            'menu-item-url'        => '#contact', 
            'menu-item-target'     => '_self',
            'menu-item-attr-title' => 'Contact',
            'menu-item-status'     => 'publish'
          )

        )

      );

      foreach( $menus as $key => $menu ) {

        $menu_name   = $key;
        $menu_exists = wp_get_nav_menu_object( $menu_name );

        if ( !$menu_exists ) {
          
          $menu_id = wp_create_nav_menu( $menu_name );

          foreach ( $menu as $key => $value ) {

            wp_update_nav_menu_item( $menu_id, 0, array(
              'menu-item-title'      => $value['menu-item-title'],
              'menu-item-classes'    => $value['menu-item-classes'],
              'menu-item-url'        => $value['menu-item-url'],
              'menu-item-target'     => $value['menu-item-target'],
              'menu-item-attr-title' => $value['menu-item-attr-title'],
              'menu-item-status'     => $value['menu-item-status']
            ) );

          }

        }

      }

    }

    public function register_sidebars() {
      register_sidebar( array(
        'name' => 'Footer Widgets',
        'id' => 'footer-widgets',
        'description' => 'Widgets in this area will be shown in the footer of all posts and pages.',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
      ) );
    }

    public function float2rat($n, $tolerance = 1.e-6) {
      $h1=1; $h2=0;
      $k1=0; $k2=1;
      $b = 1/$n;
      do {
          $b = 1/$b;
          $a = floor($b);
          $aux = $h1; $h1 = $a*$h1+$h2; $h2 = $aux;
          $aux = $k1; $k1 = $a*$k1+$k2; $k2 = $aux;
          $b = $b-$a;
      } while (abs($n-$h1/$k1) > $n*$tolerance);

      return "$h1/$k1";
    }

    public function remove_editor() {
      //remove_post_type_support('page', 'editor');
    }

    public function setup_theme_admin_menus() {
      add_menu_page( 'Theme settings', 'CPC', 'manage_categories', 'cpc_theme_settings', array( $this, 'theme_settings_page' ) );
    }

    public function theme_settings_page() {
      require_once( TEMPLATEPATH . '/admin/admin_ui.php' );
    }

    public function cmb_get_post_options( $query_args ) {

      global $post;

      $args = wp_parse_args( $query_args, array(
        'post_type'      => 'post',
        'posts_per_page' => 10,
        'orderby'        => 'post_title',
        'order'          => 'ASC'
      ) );

      $posts = get_posts( $args );

      $post_options = array( array('name' => '&mdash; Select &mdash;', 'value' => '' ) );

      if ( $posts ) {
        foreach ( $posts as $post ) {
          $post_options[] = array(
            'name'  => $post->post_title,
            'value' => $post->ID
          );
        }
      }

      return $post_options;

    }

    public function cmb_get_content_block_options( $query_args ) {

      $args = wp_parse_args( $query_args, array(
        'post_type'      => 'content_blocks',
        'posts_per_page' => 10,
        'orderby'        => 'post_title',
        'order'          => 'ASC',
        'meta_value'     => 'basic',
        'meta_key'       => '_content_block_template',
        'meta_compare'   => '='
      ) );

      $posts = get_posts( $args );

      $post_options = array();

      if ( $posts ) {

        $post_options[] = array(
          'name'  => '&mdash; Select ' . ucwords( $args['meta_value'] ) . ' Block &mdash;',
          'value' => ''
        );

        foreach ( $posts as $post ) {
          $post_options[] = array(
            'name'  => $post->post_title,
            'value' => $post->ID
          );
        }
      } else {
        $post_options[] = array(
          'name'  => '&mdash; No ' . ucwords( $args['meta_value'] ) . ' Blocks Created &mdash;',
          'value' => ''
        );
      }

      return $post_options;

    }

    public function custom_metaboxes( array $meta_boxes ) {

      // Start with an underscore to hide fields from custom fields list
      $prefix = '_';

      $meta_boxes['division_metabox'] = array(
        'id'         => 'division_metabox',
        'title'      => 'Page Division',
        'pages'      => array( 'page' ), // Post type
        'context'    => 'side',
        'priority'   => 'low',
        'show_names' => true, // Show field names on the left
        // 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
        'fields'     => array(
          array(
            'name'    => 'Division',
            'desc'    => '',
            'id'      => $prefix . 'page_division',
            'type'    => 'radio',
            'options' => array(
              ''          => 'None',
              'business'  => 'Business',
              'sports'    => 'Sports',
              'faith'     => 'Faith',
              'groups'    => 'Groups & Events',
              'preschool' => 'Preschool',
              'school'    => 'School',
              'senior'    => 'Senior',
              'yearbooks'  => 'Yearbooks'
            ),
          ),
        ),
      );

      $meta_boxes['page_featured_copy_metabox'] = array(
        'id'         => 'page_featured_copy_metabox',
        'title'      => 'Featured Copy',
        'pages'      => array( 'page', ), // Post type
        'context'    => 'side',
        'priority'   => 'low',
        'show_names' => true, // Show field names on the left
        // 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
        'fields'     => array(
          array(
            'name' => 'Headline',
            'desc' => '',
            'id'   => $prefix . 'featured_copy_headline',
            'type' => 'text'
          ),
          array(
            'name' => 'Headline Size',
            'desc' => '',
            'id'   => $prefix . 'featured_copy_headline_size',
            'type' => 'select',
            'options' => array(
              'large'  => 'Large',
              'medium' => 'Medium',
              'small'  => 'Small'
            )
          ),
          array(
            'name' => 'Subheadline',
            'desc' => '',
            'id'   => $prefix . 'featured_copy_subheadline',
            'type' => 'text'
          ),
          array(
            'name' => 'Call To Action Text',
            'desc' => '',
            'id'   => $prefix . 'featured_copy_cta_text',
            'type' => 'text'
          ),
          array(
            'name' => 'Call To Action Url',
            'desc' => '',
            'id'   => $prefix . 'featured_copy_cta_url',
            'type' => 'text'
          ),
          array(
            'name' => 'Copy',
            'desc' => '',
            'id'   => $prefix . 'featured_copy_line',
            'type' => 'textarea_small'
          ),
          array(
            'name' => 'Content Box Alignment',
            'desc' => '',
            'id'   => $prefix . 'featured_copy_content_alignment',
            'type'    => 'select',
            'options' => array(
              'left'   => 'Left',
              'right'  => 'Right'
            ),
          ),
          array(
            'name' => 'Copy Alignment',
            'desc' => '',
            'id'   => $prefix . 'featured_copy_alignment',
            'type'    => 'select',
            'options' => array(
              'left'   => 'Left',
              'center' => 'Center',
              'right'  => 'Right'
            ),
          ),
        ),
      );

      $meta_boxes['page_featured_copy_dropdown'] = array(
        'id'         => 'page_featured_copy_dropdown',
        'title'      => 'Featured Copy Dropdown',
        'pages'      => array( 'page', ),
        'context'    => 'side',
        'priority'   => 'low',
        'fields'     => array(
          array(
            'id'          => $prefix . 'featured_copy_dropdown',
            'type'        => 'group',
            'description' => '',
            'options'     => array(
              'group_title'   => __( 'Link {#}', 'cmb' ), // {#} gets replaced by row number
              'add_button'    => __( 'Add Another Link', 'cmb' ),
              'remove_button' => __( 'Remove Link', 'cmb' ),
              'sortable'      => true, // beta
            ),
            'fields'      => array(
              array(
                'name' => 'Dropdown Text',
                'desc' => '',
                'id'   => $prefix . 'featured_copy_dropdown_text',
                'type' => 'text'
              ),
              array(
                'name' => 'Dropdown Url',
                'desc' => '',
                'id'   => $prefix . 'featured_copy_dropdown_url',
                'type' => 'text'
              ),
            ),
          ),
        ),
      );

      $meta_boxes['post_gallery'] = array(
        'id'         => 'post_gallery',
        'title'      => __( 'Post Photo Gallery', 'cmb' ),
        'pages'      => array( 'post', ),
        'context'    => 'normal',
        'priority'   => 'high',
        'fields'     => array(
          array(
            'id'          => $prefix . 'post_gallery',
            'type'        => 'group',
            'description' => '',
            'options'     => array(
              'group_title'   => __( 'Photo {#}', 'cmb' ), // {#} gets replaced by row number
              'add_button'    => __( 'Add Another Entry', 'cmb' ),
              'remove_button' => __( 'Remove Entry', 'cmb' ),
              'sortable'      => true, // beta
            ),
            // Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
            'fields'      => array(
              array(
                'name' => 'Image',
                'id'   => 'image',
                'type' => 'file'
              ),
              array(
                'name' => 'Use Image Caption',
                'id'   => 'image_caption',
                'type' => 'checkbox'
              ),
            ),
          ),
        ),
      );

      $meta_boxes['content_block_featured_copy_metabox'] = array(
        'id'         => 'content_block_featured_copy_metabox',
        'title'      => 'Featured Content Block Copy',
        'pages'      => array( 'content_blocks', ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
        'fields'     => array(
          array(
            'name' => 'Headline',
            'desc' => '',
            'id'   => $prefix . 'featured_content_block_headline',
            'type' => 'text'
          ),
          array(
            'name' => 'Copy',
            'desc' => '',
            'id'   => $prefix . 'featured_content_block_copy',
            'type' => 'textarea_small'
          ),
        ),
      );

      $meta_boxes['page_quick_links_metabox'] = array(
        'id'         => 'page_quick_links_metabox',
        'title'      => 'Quick Links',
        'pages'      => array( 'page', ), // Post type
        'context'    => 'side',
        'priority'   => 'low',
        'show_names' => true, // Show field names on the left
        'fields'     => array(
          array(
            'name' => 'Quick Link Copy #1',
            'desc' => '',
            'id'   => $prefix . 'quick_link_copy_1',
            'type' => 'text'
          ),
          array(
            'name' => 'Quick Link URL #1',
            'desc' => '',
            'id'   => $prefix . 'quick_link_url_1',
            'type' => 'text'
          ),
          array(
            'name' => 'Quick Link Copy #2',
            'desc' => '',
            'id'   => $prefix . 'quick_link_copy_2',
            'type' => 'text'
          ),
          array(
            'name' => 'Quick Link URL #2',
            'desc' => '',
            'id'   => $prefix . 'quick_link_url_2',
            'type' => 'text'
          ),
          array(
            'name' => 'Quick Link Copy #3',
            'desc' => '',
            'id'   => $prefix . 'quick_link_copy_3',
            'type' => 'text'
          ),
          array(
            'name' => 'Quick Link URL #3',
            'desc' => '',
            'id'   => $prefix . 'quick_link_url_3',
            'type' => 'text'
          ),
          array(
            'name' => 'Quick Link Copy #4',
            'desc' => '',
            'id'   => $prefix . 'quick_link_copy_4',
            'type' => 'text'
          ),
          array(
            'name' => 'Quick Link URL #4',
            'desc' => '',
            'id'   => $prefix . 'quick_link_url_4',
            'type' => 'text'
          ),
          array(
            'name' => 'Quick Link Copy #5',
            'desc' => '',
            'id'   => $prefix . 'quick_link_copy_5',
            'type' => 'text'
          ),
          array(
            'name' => 'Quick Link URL #5',
            'desc' => '',
            'id'   => $prefix . 'quick_link_url_5',
            'type' => 'text'
          ),
        )
      );

      $meta_boxes['content_block_templates_metabox'] = array(
        'id'         => 'content_block_templates_metabox',
        'title'      => 'Template Options',
        'pages'      => array( 'content_blocks' ), // Post type
        'context'    => 'side',
        'priority'   => 'low',
        'show_names' => true, // Show field names on the left
        // 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
        'fields'     => array(
          array(
            'name'    => 'Template',
            'desc'    => '',
            'id'      => $prefix . 'content_block_template',
            'type'    => 'select',
            'options' => array(
              'basic'    => 'Basic',
              'featured' => 'Featured',
              'news'     => 'News',
              'endeavor' => 'Endeavor'
            ),
          ),
          array(
            'name'    => 'Division',
            'desc'    => '',
            'id'      => $prefix . 'content_block_division',
            'type'    => 'radio',
            'options' => array(
              ''          => 'None',
              'business'  => 'Business',
              'sports'    => 'Sports',
              'faith'     => 'Faith',
              'groups'    => 'Groups & Events',
              'preschool' => 'Preschool',
              'school'    => 'School',
              'senior'    => 'Senior',
              'yearbooks'  => 'Yearbooks'
            ),
          ),
        ),
      );

      $meta_boxes['content_block_type_options_metabox'] = array(
        'id'         => 'content_block_copy_options_metabox',
        'title'      => 'Copy Options',
        'pages'      => array( 'content_blocks' ), // Post type
        'context'    => 'side',
        'priority'   => 'low',
        'show_names' => true, // Show field names on the left
        // 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
        'fields'     => array(
          array(
            'name' => 'Centered',
            'desc' => '',
            'id'   => $prefix . 'content_block_copy_centered',
            'type' => 'checkbox'
          )
        )
      );

      $meta_boxes['content_block_cta_metabox'] = array(
        'id'         => 'content_block_cta_metabox',
        'title'      => 'Call To Action',
        'pages'      => array( 'content_blocks' ), // Post type
        'context'    => 'side',
        'priority'   => 'low',
        'show_names' => true, // Show field names on the left
        // 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
        'fields'     => array(
          array(
            'name' => 'Text',
            'desc' => '',
            'id'   => $prefix . 'content_block_cta_text',
            'type' => 'text'
          ),
          array(
            'name' => 'Internal URL',
            'desc' => 'Select an internal page or post to link to.',
            'id'   => $prefix . 'content_block_cta_internal_url',
            'type' => 'add_cta'
          ),
          array(
            'name' => 'External URL',
            'desc' => 'Enter an external url to link to.',
            'id'   => $prefix . 'content_block_cta_external_url',
            'type' => 'text_url'
          ),
          
          array(
            'name' => 'Size',
            'desc' => '',
            'id'   => $prefix . 'content_block_cta_size',
            'type' => 'select',
            'options' => array(
              'small' => 'Small',
              'large' => 'Large'
            )
          )
        )
      );

      /**
       * Metabox to be displayed on a single page ID
       */
      $meta_boxes['content_block_tabs_metabox'] = array(
        'id'         => 'about_page_metabox',
        'title'      => __( 'About Page Metabox', 'cmb' ),
        'pages'      => array( 'page', ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
        'show_on'    => array( 'key' => 'id', 'value' => array( 2 ), ), // Specific post IDs to display this metabox
        'fields'     => array(
          array(
            'name' => __( 'Test Text', 'cmb' ),
            'desc' => __( 'field description (optional)', 'cmb' ),
            'id'   => $prefix . '_about_test_text',
            'type' => 'text',
          ),
        )
      );

      /**
       * Repeatable Field Groups
       */
      $meta_boxes['content_block_endeavor_metabox'] = array(
        'id'         => 'content_block_endeavor_metabox',
        'title'      => 'Endeavor',
        'pages'      => array( 'content_blocks' ),
        'fields'     => array(
          array(
            'id'          => $prefix . 'content_block_endeavor_blocks',
            'type'        => 'group',
            'description' => '',
            'options'     => array(
              'group_title'   => __( 'Block {#}', 'cmb' ), // {#} gets replaced by row number
              'add_button'    => __( 'Add Another Block', 'cmb' ),
              'remove_button' => __( 'Remove Block', 'cmb' ),
              'sortable'      => true, // beta
            ),
            // Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
            'fields'      => array(
              array(
                'name' => 'Title',
                'id'   => $prefix . 'content_block_endeavor_title',
                'type' => 'text',
                // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
              ),
              array(
                'name' => 'Copy',
                'id'   => $prefix . 'content_block_endeavor_copy',
                'type'    => 'textarea_small',
                'options' => array( 'textarea_rows' => 5, )
              ),
              array(
                'name' => 'Call To Action Text',
                'desc' => '',
                'id'   => $prefix . 'content_block_endeavor_cta_text',
                'type' => 'text'
              ),
              array(
                'name' => 'Call To Action URL',
                'desc' => '',
                'id'   => $prefix . 'content_block_endeavor_cta_url',
                'type' => 'text_url'
              ),
              array(
                'name' => 'Image',
                'desc' => '',
                'id'   => $prefix . 'content_block_endeavor_image',
                'type' => 'file',
              ),
            ),
          ),
        ),
      );

      $meta_boxes['content_block_news_metabox'] = array(
        'id'         => 'content_block_news_metabox',
        'title'      => 'News',
        'pages'      => array( 'content_blocks' ),
        'fields'     => array(
          array(
            'id'          => $prefix . 'content_block_news_slides',
            'type'        => 'group',
            'description' => '',
            'options'     => array(
              'group_title'   => __( 'News Slide {#}', 'cmb' ), // {#} gets replaced by row number
              'add_button'    => __( 'Add Another Slide', 'cmb' ),
              'remove_button' => __( 'Remove Slide', 'cmb' ),
              'sortable'      => true, // beta
            ),
            // Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
            'fields'      => array(
              array(
                'name'    => 'Post',
                'desc'    => 'Select a post to add to the News Content Block',
                'id'      => $prefix . 'content_block_news_slide',
                'type'    => 'select',
                'options' => $this->cmb_get_post_options( array( 'posts_per_page' => -1 ) ),
              ),
            ),
          ),
        ),
      );

      $meta_boxes['hero_one_content_blocks_metabox'] = array(
        'id'         => 'hero_one_content_blocks_metabox',
        'title'      => 'Hero One Content Blocks',
        'pages'      => array( 'page', ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
        'fields'     => array(
          array(
            'name'    => 'Featured Content Block One',
            'desc'    => '',
            'id'      => $prefix . 'hero_one_featured_content_block_one',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'featured' ) ),
          ),
          array(
            'name'    => 'Featured Content Block Two',
            'desc'    => '',
            'id'      => $prefix . 'hero_one_featured_content_block_two',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'featured' ) ),
          ),
          array(
            'name'    => 'News Content Block',
            'desc'    => '',
            'id'      => $prefix . 'hero_one_news_content_block_one',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'news' ) ),
          ),
          array(
            'name'    => 'Featured Content Block Three',
            'desc'    => '',
            'id'      => $prefix . 'hero_one_featured_content_block_three',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'featured' ) ),
          ),
          array(
            'name'    => 'Featured Content Block Four',
            'desc'    => '',
            'id'      => $prefix . 'hero_one_featured_content_block_four',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'featured' ) ),
          ),
          array(
            'name'    => 'Endeavor Content Block',
            'desc'    => '',
            'id'      => $prefix . 'hero_one_endeavor_content_block_one',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'endeavor' ) ),
          ),
          array(
            'name'    => 'Basic Content Block',
            'desc'    => '',
            'id'      => $prefix . 'hero_one_basic_content_block_one',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'basic' ) ),
          ),
        ),
      );

      $meta_boxes['related_posts_metabox'] = array(
        'id'         => 'related_posts_metabox',
        'title'      => 'Related Posts',
        'pages'      => array( 'post' ), // Post type
        'context'    => 'side',
        'priority'   => 'low',
        'show_names' => true, // Show field names on the left
        'fields'     => array(
          array(
            'name'    => 'Related Post One',
            'desc'    => '',
            'id'      => $prefix . 'related_post_one',
            'type'    => 'select',
            'options' => $this->cmb_get_post_options( array( 'posts_per_page' => -1 ) ),
          ),
          array(
            'name'    => 'Related Post Two',
            'desc'    => 'If none selected posts in the same category will be automatically inserted.',
            'id'      => $prefix . 'related_post_two',
            'type'    => 'select',
            'options' => $this->cmb_get_post_options( array( 'posts_per_page' => -1 ) ),
          ),
        ),
      );

      $meta_boxes['hero_two_content_blocks_metabox'] = array(
        'id'         => 'hero_two_content_blocks_metabox',
        'title'      => 'Hero Two Content Blocks',
        'pages'      => array( 'page', ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
        'fields'     => array(
          array(
            'name'    => 'Featured Content Block One',
            'desc'    => '',
            'id'      => $prefix . 'hero_two_featured_content_block_one',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'featured' ) ),
          ),
          array(
            'name'    => 'Featured Content Block Two',
            'desc'    => '',
            'id'      => $prefix . 'hero_two_featured_content_block_two',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'featured' ) ),
          ),
          array(
            'name'    => 'Featured Content Block Three',
            'desc'    => '',
            'id'      => $prefix . 'hero_two_featured_content_block_three',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'featured' ) ),
          ),
          array(
            'name'    => 'Featured Content Block Four',
            'desc'    => '',
            'id'      => $prefix . 'hero_two_featured_content_block_four',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'featured' ) ),
          ),
          array(
            'name'    => 'Endeavor Content Block',
            'desc'    => '',
            'id'      => $prefix . 'hero_two_endeavor_content_block_one',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'endeavor' ) ),
          ),
          array(
            'name'    => 'Basic Content Block',
            'desc'    => '',
            'id'      => $prefix . 'hero_two_basic_content_block_one',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'basic' ) ),
          ),
        ),
      );

      $meta_boxes['content_one_content_blocks_metabox'] = array(
        'id'         => 'content_one_content_blocks_metabox',
        'title'      => 'Content One Content Blocks',
        'pages'      => array( 'page', ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
        'fields'     => array(
          array(
            'name'    => 'Basic Content Block One',
            'desc'    => '',
            'id'      => $prefix . 'content_one_basic_content_block_one',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'basic' ) ),
          ),
          array(
            'name'    => 'Endeavor Content Block',
            'desc'    => '',
            'id'      => $prefix . 'content_one_endeavor_content_block_one',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'endeavor' ) ),
          ),
          array(
            'name'    => 'Basic Content Block Two',
            'desc'    => '',
            'id'      => $prefix . 'content_one_basic_content_block_two',
            'type'    => 'select',
            'options' => $this->cmb_get_content_block_options( array( 'posts_per_page' => -1 , 'meta_value' => 'basic' ) ),
          ),
        ),
      );

      // $meta_boxes['page_content_blocks_metabox'] = array(
      //   'id'         => 'page_content_blocks_metabox',
      //   'title'      => 'Content Blocks',
      //   'pages'      => array( 'page' ),
      //   'fields'     => array(
      //     array(
      //       'id'          => $prefix . 'page_content_blocks',
      //       'type'        => 'group',
      //       'description' => '',
      //       'options'     => array(
      //         'group_title'   => __( 'Content Block {#}', 'cmb' ), // {#} gets replaced by row number
      //         'add_button'    => __( 'Add Another Content Block', 'cmb' ),
      //         'remove_button' => __( 'Remove Content Block', 'cmb' ),
      //         'sortable'      => true, // beta
      //       ),
      //       // Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
      //       'fields'      => array(
      //         array(
      //           'name' => 'Title',
      //           'id'   => $prefix . 'page_content_block_id',
      //           'type' => 'select',
      //           'options' => $this->cmb_get_post_options( array( 'post_type' => 'content_blocks', 'posts_per_page' => -1 ) )
      //           // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
      //         )
      //       ),
      //     ),
      //   ),
      // );

      $meta_boxes['faculty_info_metabox'] = array(
        'id'         => 'faculty_info_metabox',
        'title'      => 'Faculty & Staff Info',
        'pages'      => array( 'faculty_and_staff' ),
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
        'fields'     => array(
          array(
            'name' => 'Full Name',
            'desc' => '',
            'id'   => $prefix . 'faculty_name',
            'type' => 'text',
            // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
          ),
          array(
            'name' => 'Position',
            'desc' => '',
            'id'   => $prefix . 'faculty_position',
            'type' => 'text',
            // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
          )
        )
      );

      /**
       * Metabox for the user profile screen
       */
      $meta_boxes['user_edit'] = array(
        'id'         => 'user_edit',
        'title'      => __( 'User Profile Metabox', 'cmb' ),
        'pages'      => array( 'user' ), // Tells CMB to use user_meta vs post_meta
        'show_names' => true,
        'cmb_styles' => false, // Show cmb bundled styles.. not needed on user profile page
        'fields'     => array(
          array(
            'name'     => __( 'Extra Info', 'cmb' ),
            'desc'     => __( 'field description (optional)', 'cmb' ),
            'id'       => $prefix . 'exta_info',
            'type'     => 'title',
            'on_front' => false,
          ),
          array(
            'name'    => __( 'Avatar', 'cmb' ),
            'desc'    => __( 'field description (optional)', 'cmb' ),
            'id'      => $prefix . 'avatar',
            'type'    => 'file',
            'save_id' => true,
          ),
          array(
            'name' => __( 'Facebook URL', 'cmb' ),
            'desc' => __( 'field description (optional)', 'cmb' ),
            'id'   => $prefix . 'facebookurl',
            'type' => 'text_url',
          ),
          array(
            'name' => __( 'Twitter URL', 'cmb' ),
            'desc' => __( 'field description (optional)', 'cmb' ),
            'id'   => $prefix . 'twitterurl',
            'type' => 'text_url',
          ),
          array(
            'name' => __( 'Google+ URL', 'cmb' ),
            'desc' => __( 'field description (optional)', 'cmb' ),
            'id'   => $prefix . 'googleplusurl',
            'type' => 'text_url',
          ),
          array(
            'name' => __( 'Linkedin URL', 'cmb' ),
            'desc' => __( 'field description (optional)', 'cmb' ),
            'id'   => $prefix . 'linkedinurl',
            'type' => 'text_url',
          ),
          array(
            'name' => __( 'User Field', 'cmb' ),
            'desc' => __( 'field description (optional)', 'cmb' ),
            'id'   => $prefix . 'user_text_field',
            'type' => 'text',
          ),
        )
      );

      return $meta_boxes;
    }

    public function initialize_cmb_meta_boxes() {

      if ( !class_exists( 'cmb_Meta_Box' ) ) {
        require_once( get_template_directory() . '/classes/lib/metabox/init.php' );
      }

    }

    public function register_custom_post_types() {

      register_post_type('content_blocks', array(
        'label'           => 'Content Blocks',
        'description'     => '',
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_icon'       => 'dashicons-id-alt',
        'capability_type' => 'page',
        'map_meta_cap'    => true,
        'hierarchical'    => false,
        'has_archive'     => false,
        'rewrite'         => array('slug' => '', 'with_front' => false),
        'query_var'       => true,
        'supports'        => array( 'title', 'editor', 'thumbnail', 'revisions' ),
        'labels'          => array (
          'name'               => 'Content Blocks',
          'singular_name'      => 'Content Block',
          'menu_name'          => 'Content Blocks',
          'add_new'            => 'Add Content Block',
          'add_new_item'       => 'Add New Content Block',
          'edit'               => 'Edit',
          'edit_item'          => 'Edit Content Block',
          'new_item'           => 'New Content Block',
          'view'               => 'View Content Blocks',
          'view_item'          => 'View Content Block',
          'search_items'       => 'Search Content Blocks',
          'not_found'          => 'No Content Block Found',
          'not_found_in_trash' => 'No Content Blocks Found in Trash',
          'parent'             => 'Parent Content Block',
        ))
      );

    }

    public function register_taxonomies() {

      // register_taxonomy(
      //   'programs', 
      //   array('faculty_and_staff'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
      //   array(
      //     'hierarchical' => true,     /* if this is true, it acts like categories */             
      //     'labels'       => array(
      //       'name'              => __( 'Programs' ), /* name of the custom taxonomy */
      //       'singular_name'     => __( 'Program Category' ), /* single taxonomy name */
      //       'search_items'      =>  __( 'Search Program Categories' ), /* search title for taxomony */
      //       'all_items'         => __( 'All Program Categories' ), /* all title for taxonomies */
      //       'parent_item'       => __( 'Parent Program Category' ), /* parent title for taxonomy */
      //       'parent_item_colon' => __( 'Parent Program Category:' ), /* parent taxonomy title */
      //       'edit_item'         => __( 'Edit Program Category' ), /* edit Program taxonomy title */
      //       'update_item'       => __( 'Update Program Category' ), /* update title for taxonomy */
      //       'add_new_item'      => __( 'Add New Program Category' ), /* add new title for taxonomy */
      //       'new_item_name'     => __( 'New Program Category Name' ) /* name title for taxonomy */
      //     ),
      //     'show_admin_column' => true, 
      //     'show_ui'           => true,
      //     'query_var'         => true,
      //     'rewrite'           => array( 'slug' => 'colleague-note-categories' ),
      //   )
      // );

    }

    public function columns_head_posts( $defaults ) {
        
      global $post;

      if ( $post->post_type == 'content_blocks' ) {
        //$new_defaults = array_slice($defaults, 0, 1, true) + array('post_photo' => 'post photo') + array_slice($defaults, 1, NULL, true);
        $defaults = array_slice( $defaults, 0, 2, true ) + array('used_on_pages' => 'Used On Pages', 'content_block_division' => 'Division', 'content_block_template' => 'Template', 'content_block_edited_by' => 'Last Edited By') + array_slice( $defaults, 2, -1, true );
      }

      return $defaults;

    }

    public function columns_content_posts( $column_name, $post_ID ) {

      if ( $column_name == 'used_on_pages' ) {

        $used_on_pages = $this->sql->get_meta_by_key( array( '_content_one_%', '_hero_one_%', '_hero_two_%' ) );
        $page_titles   = array();
        
        if ( $used_on_pages ) {

          foreach ( $used_on_pages as $used_on_page ) {

            $parent_page_id            = $used_on_page->post_id;
            $parent_page_content_block = $used_on_page->meta_value;
                
            if ( $parent_page_content_block && $parent_page_content_block == $post_ID ) {

              $title = get_the_title( $parent_page_id );

              $page_titles[ $title ] = array(
                'id'         => $parent_page_id,
                'title'      => $title
              );

            }

          }

          ksort( $page_titles );

        }

        if ( empty( $page_titles ) ) {

          echo '&mdash;';

        } else {

          $output = '';

          foreach ( $page_titles as $page_title ) {
            $output .= '<a href="' . get_bloginfo('url') . '/wp-admin/post.php?post=' . $page_title['id'] . '&action=edit">' . $page_title['title'] . '</a>, ';
          }

          echo substr( $output, 0, -2 );

        }

      }

      if ( $column_name == 'content_block_division' ) {

        echo ( get_post_meta( $post_ID, '_content_block_division', true ) ? ucwords( str_replace( '_', ' ', get_post_meta( $post_ID, '_content_block_division', true ) ) ) : '&mdash;' );

      }

      if ( $column_name == 'content_block_template' ) {

        echo ( get_post_meta( $post_ID, '_content_block_template', true ) ? ucwords( str_replace( '_', ' ', get_post_meta( $post_ID, '_content_block_template', true ) ) ) : 'Basic' );

      }

      if ( $column_name == 'content_block_size' ) {

        $width  = get_post_meta( $post_ID, '_content_block_width', true ) ? get_post_meta( $post_ID, '_content_block_width', true ) : '&mdash;';
        $offset = get_post_meta( $post_ID, '_content_block_offset', true ) ? get_post_meta( $post_ID, '_content_block_offset', true ) : '&mdash;';

        if ( is_numeric( $width ) ) {

          $width_dec = $width / 16;
          $width_fraction = '';

          if ( $width == 16 ) {
            $width_fraction = 'Full';
          } else if ( $width == 8 ) {
            $width_fraction = 'Half';
          } else if ( $width == 6 ) {
            $width_fraction = 'Third';
          } else if ( $width == 4 ) {
            $width_fraction = 'Quarter';
          } else {
            $width_fraction = $this->float2rat( $width_dec );
          }

          $width = $width_fraction . ' or ' . $width . ' columns';

        }

        if ( is_numeric( $offset ) ) {

          $offset_dec = $offset / 16;
          $offset_fraction = '';

          if ( $offset == 16 ) {
            $offset_fraction = 'Full';
          } else if ( $offset == 8 ) {
            $offset_fraction = 'Half';
          } else if ( $offset == 6 ) {
            $offset_fraction = 'Third';
          } else if ( $offset == 4 ) {
            $offset_fraction = 'Quarter';
          } else {
            $offset_fraction = $this->float2rat( $offset_dec );
          }

          $offset = $offset_fraction . ' or ' . $offset . ' columns';

        }

        echo 'Width: ' . $width;
        echo '<br />Offset: ' . $offset;

      }

      if ( $column_name == 'content_block_edited_by' ) {
        $author_link = get_post_meta($post_ID, '_edit_last', true) ? '<a href="' . get_bloginfo('url') . '/wp-admin/user-edit.php?user_id='. get_post_meta($post_ID, '_edit_last', true)  .'&wp_http_referer=%2Fwp-admin%2Fusers.php">' . get_the_modified_author() . '</a>' : '&mdash;';
        echo ( get_the_modified_author() ? 'Author: ' . $author_link . '<br />Datetime: ' . get_the_modified_date('F j, Y') . ' at ' .  get_the_modified_date('g:i a') : '&mdash;' );

      }

    }


  }

  $cpc_admin = new WP_CPC_Admin();

}