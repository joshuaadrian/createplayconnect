<?php

// global $cpc, $post, $page_meta;

$classes                         = array();
// $page_meta                       = get_post_meta( $post->ID );
// $page_headline_size_class        = !empty( $page_meta['_featured_copy_headline_size'][0] ) ? 'page-header-content-headline-' . $page_meta['_featured_copy_headline_size'][0] : 'page-header-content-headline-medium';
// $page_headline                   = !empty( $page_meta['_featured_copy_headline'][0] ) ? '<h3 class="' . $page_headline_size_class . '">' . $page_meta['_featured_copy_headline'][0] . '</h3>' : '';
// $page_subheadline                = !empty( $page_meta['_featured_copy_subheadline'][0] ) ? '<h5 class="page-header-content-subheadline">' . $page_meta['_featured_copy_subheadline'][0] . '</h5>' : '';
// $page_headline_content_alignment = !empty( $page_meta['_featured_copy_content_alignment'][0] ) ? $classes[] = 'page-header-content-alignment-' . $page_meta['_featured_copy_content_alignment'][0] : '';
// $page_headline_copy_alignment    = !empty( $page_meta['_featured_copy_alignment'][0] ) ? $classes[] = 'page-header-copy-alignment-' . $page_meta['_featured_copy_alignment'][0] : '';
// $page_headline_cta_text          = !empty( $page_meta['_featured_copy_cta_text'][0] ) ? $page_meta['_featured_copy_cta_text'][0] : '';
// $page_headline_cta_url           = !empty( $page_meta['_featured_copy_cta_url'][0] ) ? $page_meta['_featured_copy_cta_url'][0] : '';
// $page_headline_cta               = $page_headline_cta_text && $page_headline_cta_url ? '<p><a href="' . $page_headline_cta_url . '" class="button">' . $page_headline_cta_text . '</a></p>' : '';
// $page_headline_copy              = !empty( $page_meta['_featured_copy_line'][0] ) ? $page_meta['_featured_copy_line'][0] : '';
// $thumbnail_array                 = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'landscape-xl' );
// $thumbnail_array                 = empty( $thumbnail_array[0] ) ? get_bloginfo('url') . '/wp-content/themes/cpc/assets/images/featured-default.jpg' : $thumbnail_array[0];

?>

<div id="home" class="page-header">

  <div class="page-header-content <?php echo implode(' ', $classes ); ?>">

    <div class="page-header-content-inner">

      <h1>CPC Intersect</h1>

      <h2>Experiences Where Brands and People Connect</h2>

    </div>

  </div>

</div>