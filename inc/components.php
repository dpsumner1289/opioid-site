<?php
// global modules
include (trailingslashit(get_template_directory()).'modules/global/cta.php');

// home page modules
include (trailingslashit(get_template_directory()).'modules/home/hero.php');
include (trailingslashit(get_template_directory()).'modules/home/topic-grid.php');
include (trailingslashit(get_template_directory()).'modules/home/physician_stories.php');

// blog modules
include (trailingslashit(get_template_directory()).'modules/blog/filtering.php');
include (trailingslashit(get_template_directory()).'modules/blog/searching.php');
include (trailingslashit(get_template_directory()).'modules/blog/related_posts.php');