<?php
// add theme options page
if( function_exists('acf_add_options_page') ) {			
    acf_add_options_sub_page(array(
        'page_title' 	=> 'Theme Options',
        'menu_title'	=> 'Theme Options',
        'parent_slug'	=> 'themes.php',
    ));
}