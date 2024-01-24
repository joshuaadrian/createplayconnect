<!doctype html>
<!--[if lt IE 7 ]> <html class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html> <!--<![endif]-->
<head>

  <!-- META DATA -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

  <!-- TITLE -->
  <title><?php wp_title('|', true, 'right'); ?></title>

  <!-- STYLES AND SCRIPTS -->
  <?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>

  <?php //get_template_part('partials/nav','header'); ?>