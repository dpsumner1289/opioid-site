<?php // Template name: Submit Content Page
get_header();

if(1==1):
?>
<section class="submit-content btw">
    <div class="container container-sm flex row afc jfc">
        <?php
        if(have_posts()): while(have_posts()): the_post();
        $postID = get_the_ID();
        $gform = trim(get_post_meta($postID, 'form_shortcode', true));
        echo '<h1 class="page-title">'.get_the_title().'</h1>';
        echo '<div class="slides-outer flex row">';
        echo '<div class="slides flex row afs jfs">';
        echo '<div class="slide slide-1 flex col afc jfc">';
        echo !empty(get_the_content()) ? '<div class="content">' . apply_filters( 'the_content', $post->post_content ) . '</div>':  '';
        echo '<a href="#" class="button gtb hidden-arrow" id="submit-content-button">SUBMIT CONTENT<i class="fal fa-arrow-right"></i></a>';
        echo '</div>';
        echo '<div class="slide slide-2">';
        if(!empty($gform)) {
            echo do_shortcode($gform);
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
        endwhile; endif;
        ?>
    </div>
</section>
<script>
jQuery(document).ready(function($) {
    var slideNum = 1;
    function slideInit() {
        var contWidth = $('section.submit-content > div.container').width();
        var slidesOuter = $('div.slides-outer');
        var slides = $('div.slides');
        var slide1 = $('div.slide-1');
        var slide1height = slide1.height();
        var slide2 = $('div.slide-2');
        var slide2Height = slide2.height();

        slides.width(contWidth*2);
        slidesOuter.height(slide1height);
    }
    function slide() {
        var contWidth = $('section.submit-content > div.container').width();
        var slidesOuter = $('div.slides-outer');
        var slides = $('div.slides');
        var slide2 = $('div.slide-2');
        var slide2Height = slide2.height();
        slides.animate({
            'left' : "-="+contWidth,
        });
        setTimeout(() => {
            slidesOuter.animate({
                'height' : slide2Height,
            });   
        }, 300);
        slideNum++;
    }
    function nextSlide() {
        var slidesOuter = $('div.slides-outer');
        var thisSlide = $('div#gform_page_1_'+slideNum);
        var thisSlideHeight = thisSlide.height();
        thisSlideHeight = thisSlideHeight + 100;
        console.log(thisSlide);
        setTimeout(() => {
            slidesOuter.animate({
                'height' : thisSlideHeight,
            });   
        }, 300);
        slideNum++;
    }
    
    slideInit();
    $('#submit-content-button').on('click', function(e) {
        e.preventDefault();
        slide();
    });
    $('input.gform_next_button').on('click', function(e) {
        nextSlide();
    });
});
</script>
<?php
else:
?>
<section class="submit-content need-to-login btw">
    <div class="container container-sm flex row afc jfc">
        <h1 class="page-title">You must be logged in to submit content</h1>
        <a href="#" class="pwdless-register">LOG IN</a>
    </div>
</section>
<?php
endif;
//global_cta();
get_footer();