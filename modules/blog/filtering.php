<?php
if(!function_exists('sidebar_filters')) {
    function sidebar_filters() {
        // tax terms
        $topics = get_terms(array('taxonomy' => array('topic'), 'hide_empty' => false, 'parent' => 0));
        $types = get_terms(array('taxonomy' => array('resource_type'), 'hide_empty' => false, 'parent' => 0));

        // parts
        $open_section = '<aside class="filtering flex col">';
        $mobile_header = '<div class="mobile_filter_header"><a href="#">FILTERS <i class="far fa-chevron-down"></i></a></div>';
        $open_tab = '<div class="filter_tab"><i class="far fa-bars"></i> FILTERS</div>';
        $heading = '<div class="flex row afc jfe filter_heading">
                        <a class="reset nolink">Clear Search</a>
                        <a class="hide"><i class="far fa-times"></i>&nbsp;COLLAPSE</a>
                        <div class="dark-blue flex row full nowrap"><div class="info-wrap">
                        <i class="fal fa-info-circle"></i>&nbsp; <i>Select one or more categories below to filter your search results.</i>
                        </div></div>
                    </div>';
        $cat_heading = '<div class="filter-links flex col"><h3 class="cat_heading">TOPICS</h3>';
        $close_section = '</div></div></aside><!-- /.filtering -->';
        $output = '';

        $output .= $open_section;
        $output .= $mobile_header;
        $output .= $open_tab;
        $output .= $heading;
        $output .= $cat_heading;
        foreach( $topics as $topic ) {
            $topic_link = sprintf( 
                '<div class="term-wrap"><i class="fas fa-check"></i><a class="tax_term" data-tax="topic" data-term="%1$s" data-name="%3$s" alt="%2$s">%3$s</a></div>',
                $topic->term_id,
                esc_attr( sprintf( __( 'View all posts in %s', 'textdomain' ), $topic->name ) ),
                esc_html( $topic->name )
            );
            $output.= sprintf( esc_html__( '%s'), $topic_link );
        }
        $cat_heading = '<div class="flex col"><h3 class="cat_heading border-top">RESOURCE TYPES</h3>';
        $output .= $cat_heading;
        foreach( $types as $type ) {
            $type_link = sprintf( 
                '<div class="term-wrap"><i class="fas fa-check"></i><a class="tax_term" data-tax="resource_type" data-term="%1$s" data-name="%3$s" alt="%2$s">%3$s</a></div>',
                $type->term_id,
                esc_attr( sprintf( __( 'View all posts in %s', 'textdomain' ), $type->name ) ),
                esc_html( $type->name )
            );
            $output.= sprintf( esc_html__( '%s'), $type_link );
            $child_types = get_terms(array('taxonomy' => array('resource_type'), 'hide_empty' => false, 'parent' => $type->term_id));
            foreach($child_types as $child) {
                $child_link = sprintf( 
                    '<div class="term-wrap"><i class="fas fa-check"></i><a class="tax_term child_term" data-tax="resource_type" data-term="%1$s" data-name="%3$s" alt="%2$s">%3$s</a></div>',
                    $child->term_id,
                    esc_attr( sprintf( __( 'View all posts in %s', 'textdomain' ), $child->name ) ),
                    esc_html( $child->name )
                );
                $output.= sprintf( esc_html__( '%s'), $child_link );
            } 
        }
        $output .= $close_section;
        $output .= "<script>
                    jQuery(document).ready(function($){
                        var filterHeight = $('aside.filtering').height();
                        var filterWidth = $('aside.filtering').outerWidth();
                        filterNewWidth = filterWidth - 10;
                        $('aside.filtering').css({
                            'left': '-'+filterNewWidth+'px',
                        });
                        $('main.site-main').css('min-height', filterHeight+'px');
                        var initWwidth = $(window).width();
                        function show_filter(el) {
                            var windowWidth = $(window).width();
                            $(el).addClass('active');
                            $(el).addClass('slideleft');
                            $('.mobile_filter_header a').addClass('open');
                            $('.site-main').css('overflow','hidden');
                            $('.filter_tab').css('opacity','0');
                            $('.filter_heading a').addClass('active');
                            if(windowWidth > 1024) {
                                $('aside.filtering').animate({
                                    left:'+='+filterNewWidth+'px'
                                }, 400);
                                $('.resource_feed_html').animate({
                                    marginLeft:'+='+filterNewWidth+'px',
                                    marginRight:'-='+filterNewWidth+'px'
                                }, 400);
                            }else{
                                $('aside.filtering').animate({
                                    height: '100%'
                                  }, 400 );
                            }
                        }
                        function hideFilter() {
                            var windowWidth = $(window).width();
                            $('.filter_tab').removeClass('active');
                            $('.filter_tab').removeClass('slideleft');
                            $('.mobile_filter_header a').removeClass('open');
                            $('.filter_tab').css('opacity','1');
                            $('#page').css('overflow','initial');
                            $('.filter_heading a').addClass('active');
                            if(windowWidth > 1024) {
                                $('aside.filtering').animate({
                                    left:'-='+filterNewWidth+'px'
                                }, 400);
                                $('.resource_feed_html').animate({
                                    marginLeft:'-='+filterNewWidth+'px',
                                    marginRight:'+='+filterNewWidth+'px'
                                }, 400);
                            } else {
                                $('aside.filtering').animate({
                                    height: '45px'
                                  }, 400 );
                            }
                        }
                        function resetFilters(){
                            $('input[type=text], input[type=date]').val('');
                            $('select').prop('selectedIndex',0);
                            $('input[type=\"radio\"]').prop('checked', false);
                        }
                        $(document).on('click', 'a.reset', function(){resetFilters()});
                        $(document).on('click', '.filter_tab:not(\".active\"), .mobile_filter_header:not(\".open\") a', function(){show_filter(this)});
                        $(document).on('click', '.filter_heading a.hide.active, .filter_tab.active, a.cat_filter', function(){
                            hideFilter();
                        });
                        $(document).on('click', '.menu-bars, section.resource_feed', function(e){
                            if($('.filter_tab').hasClass('active')){
                                hideFilter();
                            }
                        });
                    });
                    </script>";

        return $output;
    }
}