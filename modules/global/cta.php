<?php
if(!function_exists('global_cta')) {
    function global_cta() {
        $bg_image = gpm('cta_background_image');
        $bg_image = wp_get_attachment_url($bg_image, 'full');
        $cta_heading = gpm('cta_heading', true);
        $cta_content = gpm('cta_content', true);
        $cta_button_label = gpm('cta_button_label');
        $cta_button_link = gpm('cta_button_link');
        ?>
        <section class="global_cta" <?php if(!empty($bg_image)){echo 'style="background-image:url('.$bg_image.')"';} ?>>
            <div class="container container-xlg xtra-padding flex row afc jfe">
                <div class="item_1_2"></div>
                <div class="item_1_2">
                    <div class="content flex col afs jfc">
                        <?php if(!empty($cta_heading)){echo '<h2 class="big">'.$cta_heading.'</h2>';} ?>
                        <?php if(!empty($cta_content)){echo '<div class="cta-content">'.$cta_content.'</div>';} ?>
                        <?php if(!empty($cta_button_label) && !empty($cta_button_link)){echo '<a href="'.$cta_button_link.'" class="button gtb hidden-arrow">'.$cta_button_label.'<i class="fal fa-arrow-right"></i></a>';} ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}