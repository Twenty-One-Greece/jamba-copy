<?php
/*
Plugin Name: Img Title Removal
Plugin URI: http://www.glauserconsulting.com/2008/11/nu-agency/
Description: Filter plugin that removes all title tags from images in posts.
Author: Ivan Glauser
Version: 1.1
Author URI: http://www.glauserconsulting.com
License: GPL3
*/


/*  Copyright 2011  Ivan Glauser  (email : ivan.glauser@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_filter('the_content', 'remove_img_titles', 1000);
add_filter('post_thumbnail_html', 'remove_img_titles', 1000);
add_filter('wp_get_attachment_image', 'remove_img_titles', 1000);

function remove_img_titles($text) {
    
    // Get all title="..." tags from the html.
    $result = array();
    preg_match_all('|title="[^"]*"|U', $text, $result);
    
    // Replace all occurances with an empty string.
    foreach($result[0] as $img_tag)
    {
        $text = str_replace($img_tag, '', $text);
    }
    
    return $text;
}

?>