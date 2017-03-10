<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

global $wpdb;
$table_name = $wpdb->prefix . "popup_windows";
$sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
$table_name = $wpdb->prefix . "popup_meta";
$sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
$table_name = $wpdb->prefix . "popup_visits";
$sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
$table_name = $wpdb->prefix . "popup_country_d";
$sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
$table_name = $wpdb->prefix . "popup_form_results";
$sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
    
//Custom Post Type
$wpdb->query("
    		DELETE a,b,c,d,e FROM {$wpdb->prefix}posts a
    		LEFT JOIN {$wpdb->prefix}term_relationships b ON (a.ID=b.object_id)
    		LEFT JOIN {$wpdb->prefix}term_taxonomy c ON (c.term_taxonomy_id=b.term_taxonomy_id)
    		LEFT JOIN {$wpdb->prefix}terms d ON (c.term_id = d.term_id)
    		LEFT JOIN {$wpdb->prefix}postmeta e ON (a.ID=e.post_id)
    		WHERE a.post_type='isp_s_post_type';
    		");
?>