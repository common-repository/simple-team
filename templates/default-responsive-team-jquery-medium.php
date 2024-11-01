<section class="team">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="col-lg-12">
                <div class="border-title">
                    <h2><?php echo $this->title; ?></h2>
                    <div class='ico-border'> <i class='ico-bg flower'></i> </div>
                    <span class="tag-line"><?php echo $this->subtitle; ?></span>
                </div>                        
                <div class="row pt-md" id="hover-cap-3col">

                    <?php
//valid meta fields:
// short_bio, email, designation, web_url, telephone, location, social, profile_title, profile_linkedin, _thumbnail_id
                    foreach (get_posts($args) as $post) {
                        $meta = get_post_custom($post->ID);
                        ?>
                        <div class="thumbnails col-lg-3 col-md-3 col-sm-col-xs-12">
                            <div class="mthumbnail">
                                <div class="mcaption img-box">
                                    <ul class="text-center">
                                        <li><a href="<?php echo $meta['profile_facebook'][0]; ?>"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="<?php echo $meta['profile_twitter'][0]; ?>"><i class="fa fa-twitter"></i></a></li>
                                        <li><a href="<?php echo $meta['profile_linkedin'][0]; ?>"><i class="fa fa-linkedin"></i></a></li>
                                    </ul>
                                </div>
                                <?php echo get_the_post_thumbnail($post->ID, "medium", array('class' => 'img-responsive img-rounded')); ?>
                            </div>
                            <h4><?php echo $post->post_title; ?></h4>
                            <?php echo $post->post_content; ?>

                        </div>
                        <?php
                    }
                    ?>


                </div>
            </div>
        </div>
    </div>
</section>
