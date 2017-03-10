<?php 
/**
 * Template Name: Indeed Smart PopUps
 */
show_admin_bar(false);
?>
<html style="margin-top: 0px !important; overflow: hidden;">
<?php
wp_head();
while ( have_posts() ){
	the_post();
	the_content();
}
wp_footer();
?>
</html>