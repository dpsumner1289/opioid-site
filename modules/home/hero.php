<?php
if(!function_exists('home_hero')) {
    function home_hero() {
        $image = gpm('hero_image');
        $image = wp_get_attachment_image_url($image, 'full');
        $heading = gpm('hero_heading', true);
        $subheading = gpm('hero_subheading', true);
        ?>
        <section class="home_hero btw">
            <div class="container container-md-lg flex row afs">
                <?php if($image){echo '<img src="'.$image.'" class="item_3_10"/>';} ?>
                <div class="item_7_10">
                    <div class="content">
                        <?php if($heading){echo '<h1>'.$heading.'</h1>';} ?>
                        <?php if($subheading){echo '<h2>'.$subheading.'</h2>';} ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}