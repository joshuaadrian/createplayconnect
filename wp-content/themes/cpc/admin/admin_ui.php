<?php

  global $cpc;

    if ( false == get_option( 'cpc_options' ) ) {

      $cpc_options = array();
      add_option( 'cpc_options', $cpc_options );

    }

    $cpc_options           = get_option( 'cpc_options' );
    $cpc_site_message_copy = get_option( 'cpc_site_message_copy' );
    $cpc_site_message_link = get_option( 'cpc_site_message_link' );
    $cpc_site_message_url  = get_option( 'cpc_site_message_url' );
    $cpc_header_logo       = get_option( 'cpc_header_logo' );
    $cpc_footer_logo       = get_option( 'cpc_footer_logo' );

		if ( isset( $_POST["update_settings"] ) ) {

      $output          = 'Nothing to update.';
      $message_class   = 'warning';

      if ( !empty( $_POST["cpc_site_message_copy"] ) ) {
        $output        = 'Options updated.';
        $message_class = 'updated';
        update_option( 'cpc_site_message_copy', $_POST["cpc_site_message_copy"] );
      } else {
        update_option( 'cpc_site_message_copy', '' );
      }

      if ( !empty( $_POST["cpc_site_message_link"] ) ) {
        $output        = 'Options updated.';
        $message_class = 'updated';
        update_option( 'cpc_site_message_link', $_POST["cpc_site_message_link"] );
      } else {
        update_option( 'cpc_site_message_link', '' );
      }

      if ( !empty( $_POST["cpc_site_message_url"] ) ) {
        $output        = 'Options updated.';
        $message_class = 'updated';
        update_option( 'cpc_site_message_url', $_POST["cpc_site_message_url"] );
      } else {
        update_option( 'cpc_site_message_url', '' );
      }

      if ( !empty( $_POST["cpc_site_message_type"] ) ) {
        $output        = 'Options updated.';
        $message_class = 'updated';
        update_option( 'cpc_site_message_type', $_POST["cpc_site_message_type"] );
      } else {
        update_option( 'cpc_site_message_url', '' );
      }

      if ( !empty( $_POST["cpc_site_options"] ) ) {

        $output        = 'Options updated.';
        $message_class = 'updated';
        update_option( 'cpc_site_options', $_POST["cpc_site_options"] );

      }

      if ( !empty( $_POST['cpc_header_logo_image'] ) ) {
        
        $output        = 'Image(s) updated.';
        $message_class = 'updated';
        update_option( 'cpc_header_logo', $_POST["cpc_header_logo_image"] );

      } else {
        update_option( 'cpc_header_logo', '' );
      }

      if ( !empty( $_POST['cpc_footer_logo_image'] ) ) {
        
        $output        = 'Image(s) updated.';
        $message_class = 'updated';
        update_option( 'cpc_footer_logo', $_POST["cpc_footer_logo_image"] );

      } else {
        update_option( 'cpc_footer_logo', '' );
      }

      $cpc_options           = get_option( 'cpc_options' );
      $cpc_site_message_copy = get_option( 'cpc_site_message_copy' );
      $cpc_site_message_link = get_option( 'cpc_site_message_link' );
      $cpc_site_message_url  = get_option( 'cpc_site_message_url' );
      $cpc_header_logo       = get_option( 'cpc_header_logo' );
      $cpc_footer_logo       = get_option( 'cpc_footer_logo' );

			echo '<div id="message" class="' . $message_class . '"><p><strong>' . $output . '</strong></p></div>';

		}

		?>

    <div id="cpc-settings" class="wrap">

      <h1>cpc Options</h1>
    
      <?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_options'; ?>
        
      <h2 class="nav-tab-wrapper">
        <a href="?page=cpc_theme_settings&tab=general_options" class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>">General Settings</a> 
        <a href="?page=cpc_theme_settings&tab=site_message_options" class="nav-tab <?php echo $active_tab == 'site_message_options' ? 'nav-tab-active' : ''; ?>">Site Message</a> 
        <a href="?page=cpc_theme_settings&tab=leads_export_options" class="nav-tab <?php echo $active_tab == 'leads_export_options' ? 'nav-tab-active' : ''; ?>">Leads Export</a>
        <a href="?page=cpc_theme_settings&tab=help_options" class="nav-tab <?php echo $active_tab == 'help_options' ? 'nav-tab-active' : ''; ?>">Help Options</a>
      </h2>

        <?php if ( $active_tab == 'general_options' ) : ?>

          <form method="POST" action="">

            <input type="hidden" name="update_settings" value="Y" />

            <table class="form-table">
              <tr valign="top">  
                <th scope="row" align="left">  
                  <label for="cpc_header_logo_image">cpc Header Logo</label>   
                </th>  
                <td>

                  <?php wp_enqueue_media(); ?>
                  <input type="text" name="cpc_header_logo_image" id="cpc_header_logo_image" value="<?php echo !empty( $cpc_header_logo ) ? $cpc_header_logo : ''; ?>" />
                  <input class="file-upload button" name="cpc_header_logo_button" id="cpc_header_logo_button" value="Add Image" type="button" />

                  <?php

                  if ( !empty( $cpc_header_logo ) ) {

                    $image_id = $cpc->sql->get_attachment_id_from_src( $cpc_header_logo );
                    $img      = wp_get_attachment_image( $image_id, $size = 'full', $icon = false );

                    echo '<div class="admin-image-thumb"><a href="#" data-image="cpc_header_logo_image" class="admin-image-thumb-delete">X</a><div class="admin-image-thumb-inner">' . $img . '</div></div>';

                  }

                  ?>

                </td>  
              </tr>
              <tr valign="top">  
                <th scope="row" align="left">  
                  <label for="cpc_footer_logo_image">cpc Footer Logo</label>   
                </th>  
                <td>  
                  
                  <?php wp_enqueue_media(); ?>
                  <input type="text" name="cpc_footer_logo_image" id="cpc_footer_logo_image" value="<?php echo !empty( $cpc_footer_logo ) ? $cpc_footer_logo : ''; ?>" />
                  <input class="file-upload button" name="cpc_footer_logo_button" id="cpc_footer_logo_button" value="Add Image" type="button" />

                  <?php

                  if ( !empty( $cpc_footer_logo ) ) {

                    $image_id = $cpc->sql->get_attachment_id_from_src( $cpc_footer_logo );
                    $img      = wp_get_attachment_image( $image_id, $size = 'full', $icon = false );

                    echo '<div class="admin-image-thumb"><a href="#" data-image="cpc_footer_logo_image" class="admin-image-thumb-delete">X</a><div class="admin-image-thumb-inner">' . $img . '</div></div>';

                  }

                  ?>
  
                </td>  
              </tr>
            </table>

            <p class="form-action">
              <input type="submit" value="Update Logos" class="button-primary"/> 
            </p>

          </form>

        <?php endif; ?>

        <?php if ( $active_tab == 'site_message_options' ) : ?>

          <form method="POST" action="">

            <input type="hidden" name="update_settings" value="Y" />

            <table class="form-table">
              <tr valign="top">  
                <th scope="row" align="left">  
                  <label for="cpc_site_message_copy">Copy</label>   
                </th>  
                <td>  
                  <input type="text" id="cpc_site_message_copy" name="cpc_site_message_copy" size="50" value="<?php echo get_option( 'cpc_site_message_copy' ); ?>" /> 
                </td>  
              </tr>
              <tr valign="top">  
                <th scope="row" align="left">  
                  <label for="cpc_site_message_link">Link</label>   
                </th>  
                <td>  
                  <input type="text" id="cpc_site_message_link" name="cpc_site_message_link" size="50" value="<?php echo get_option( 'cpc_site_message_link' ); ?>" /> 
                </td>  
              </tr>
              <tr valign="top">  
                <th scope="row" align="left">  
                  <label for="cpc_site_message">URL</label>   
                </th>  
                <td>  
                  <input type="text" id="cpc_site_message" name="cpc_site_message_url" size="50" value="<?php echo get_option( 'cpc_site_message_url' ); ?>" /> 
                </td>  
              </tr>
              <tr valign="top">  
                <th scope="row" align="left">  
                  <label for="cpc_site_message_type">Type</label>   
                </th>  
                <td>

                  <?php $type = get_option( 'cpc_site_message_type' ); ?>

                  <select id="cpc_site_message_type" name="cpc_site_message_type">
                    <option value="promotional" <?php selected( $type, 'promotional' ); ?>>Promotional (Light Blue)</option>
                    <option value="promotional-alt" <?php selected( $type, 'promotional-alt' ); ?>>Promotional Alt (Dark Blue)</option>
                    <option value="deadline" <?php selected( $type, 'deadline' ); ?>>Deadline (Green)</option>
                    <option value="alert" <?php selected( $type, 'alert' ); ?>>Alert (Yellow)</option>
                  </select>

                </td>  
              </tr>
            </table>

            <p class="form-action">
              <input type="submit" value="Update Site Message" class="button-primary"/> 
            </p>

          </form>

        <?php endif; ?>

        <?php if ( $active_tab == 'leads_export_options' ) : ?>

          <form action="" method="post" name="cpc_lead_export_form">
            
            <p class="form-action">
              <input type="submit" name="submit" value="Export Lead CSV" class="button" />
            </p>

          </form>

        <?php endif; ?>

        <?php if ( $active_tab == 'help_options' ) : ?>

          <h3>cpc WordPress Guide</h3>
          <p><a href="https://docs.google.com/" target="_blank">Open Guide</a></p>

          <h3>General WordPress Usage Guide</h3>
          <p><a href="http://easywpguide.com/" target="_blank">Open Guide</a></p>

        <?php endif; ?>

      </div>

    <div class="clear"></div>