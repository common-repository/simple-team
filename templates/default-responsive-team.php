<section class="team">
    <div class="row<?php if ($this->extra_classes != "") echo " " . $this->extra_classes; ?>">
        <div class="col-md-10 col-md-offset-1">
            <div class="col-lg-12">
                <?php
                if ($this->title != "" || $this->subtitle != "") {
                    ?>
                    <div class="border-title">
                        <?php
                        if ($title != "") {
                            echo '<h2>' . esc_html($this->title) . '</h2>';
                        }
                        ?>
                        <div class='ico-border'> <i class='ico-bg flower'></i> </div>
                        <span class="tag-line">
                            <?php
                            if ($subtitle != "") {
                                echo esc_html($this->subtitle);
                            }
                            ?>
                        </span>
                    </div>   
                <?php } ?>
                <div class="row pt-md">
                    <?php
//valid meta fields:
// short_bio, email, designation, web_url, telephone, location, social, profile_title, profile_linkedin, _thumbnail_id
                    foreach (get_posts($args) as $post) {
                        //     echo "<pre>" . print_r($post, true) . "</pre>";
                        $meta = get_post_custom($post->ID);
                        ?>
                        <div class="col-lg-2 col-md-2 col-sm-col-xs-12 profile">
                            <div class="img-box">
                                <?php echo get_the_post_thumbnail($post->ID, "medium", array('class' => 'img-responsive')); ?>
                                <ul class="text-center">
                                    <li><a href="<?php echo $meta['profile_facebook'][0]; ?>"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="<?php echo $meta['profile_twitter'][0]; ?>"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="<?php echo $meta['profile_linkedin'][0]; ?>"><i class="fa fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                            <h1><?php echo trim($post->post_title); ?></h1>
                            <h2><?php echo trim($meta['profile_title'][0]); ?></h2>
                            <div><?php echo trim($post->post_content); ?></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>