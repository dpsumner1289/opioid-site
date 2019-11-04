<?php
if(!function_exists('resource_search')) {
    function resource_search() {
        // tax terms
        $topics = get_terms(array('taxonomy' => array('topic'), 'hide_empty' => false));
        $types = get_terms(array('taxonomy' => array('resource_type'), 'hide_empty' => false, 'parent' => 0));

        // parts
        $form = '<form class="hero-search">
                    <div class="search-field">
                        <i class="fas fa-search"></i>
                        <input class="keywords" type="text" placeholder="SEARCH RESOURCES" />
                        <button class="post-search" type="submit"><i class="fas fa-arrow-right"></i></button>
                    </div>
                </form>';
        echo $form;
    }
}

if(!function_exists('header_search')) {
    function header_search() {
        // parts
        $form = '<form class="header-search" action="" method="post" onsubmit="window.location.href=\'/resources\';">
                    <div class="search-field">
                        <i class="fas fa-search"></i>
                        <input class="keywords" name="otf_resource_search" type="text" placeholder="SEARCH RESOURCES" />
                        <button class="post-search" type="submit"><i class="fas fa-arrow-right"></i></button>
                    </div>
                </form>';
        return $form;
    }
}