<?php

global $cpc;

if ( !empty( $cpc->site_message_copy ) && ( is_home() || is_front_page() ) ) {

	$link = !empty( $cpc->site_message_link ) && !empty( $cpc->site_message_url ) ? '<a href="'.$cpc->site_message_url.'">'.$cpc->site_message_link.'</a>' : '';
	$type = !empty( $cpc->site_message_type ) ? $cpc->site_message_type : '';

?>

<div class="site-message<?php echo ' site-message-' . $type; ?>">
        		
    <p>
    	<?php echo $cpc->site_message_copy; ?>
    	<?php echo $link; ?>
    </p>

</div>

<?php

}

?>