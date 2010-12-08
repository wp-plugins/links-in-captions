<?php
/*
Plugin Name: Links in Captions
Plugin URI: http://www.seodenver.com/lottery/
Description: Easily add links to image captions in the WordPress editor.
Author: Katz Web Services, Inc.
Version: 1.0
Author URI: http://www.katzwebservices.com
*/

/*
Copyright 2010 Katz Web Services, Inc.  (email: info@katzwebservices.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


add_filter('img_caption_shortcode', 'add_link_to_caption_shortcode', true, 3);
// This function is taken from wp-includes/media.php
function add_link_to_caption_shortcode($empty, $attr, $content) {
	extract(shortcode_atts(array(
		'id'	=> '',
		'align'	=> 'alignnone',
		'width'	=> '',
		'caption' => ''
	), $attr));

	# BEGIN Added for this plugin
		// replaces {link rel="nofollow" url="http://www.example.com"}Text{/link}
		$caption = preg_replace('/\{link(.*?)\}(.*?)\{\/link\}/ism', '[add_caption_link$1]$2[/add_caption_link]', $caption);
		
		// Added for this plugin it replaces {link rel="nofollow" url="http://www.example.com" text="Text" /}
		$caption = preg_replace('/\{link(.*?)\/\}/ism', '[add_caption_link $1 /]', $caption);
		
		$caption = str_replace('&quot;', '"', $caption);
	# END Added for this plugin

	if ( 1 > (int) $width || empty($caption) )
		return $content;

	if ( $id ) $id = 'id="' . esc_attr($id) . '" ';
	
	// Added do_shortcode() to the $caption for this plugin
	return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '" style="width: ' . (10 + (int) $width) . 'px">'
	. do_shortcode( $content ) . '<p class="wp-caption-text">' . do_shortcode($caption) . '</p></div>';
}

add_shortcode('add_caption_link', 'add_link_to_caption_shortcode_shortcode');
function add_link_to_caption_shortcode_shortcode($attr, $content = null) {
	extract(shortcode_atts(array(
		'url'		=> '',
		'href'		=> '',
		'target'	=> '',
		'rel'		=> '',
		'text'		=> ''
	), $attr));
	
	$url = empty($url) ? '' :  ' href="'.$url.'"';
	$href = empty($url) ? ' href="'.$url.'"' : $url; // We'll also allow $href to be used if $url is empty.
	$rel = empty($rel) ? '' :  ' rel="'.$rel.'"';
	$target = empty($target) ? '' :  ' target="'.$target.'"';
	
	if(empty($url) || (empty($content) && empty($text))) { return $content; }
	else if(empty($content)) { $content = $text; }
	
	return "<a{$href}{$target}{$rel}>$content</a>";
}