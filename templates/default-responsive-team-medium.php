<section class="team">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="col-lg-12">
                <div class="border-title">
                    <h2><?php echo $this->title;?></h2>
                    <div class='ico-border'> <i class='ico-bg flower'></i> </div>
                    <span class="tag-line"><?php echo $this->subtitle; ?></span>
                </div>                        
                <div class="row pt-md">
                    <?php
//valid meta fields:
// short_bio, email, designation, web_url, telephone, location, social, profile_title, profile_linkedin, _thumbnail_id
                    foreach (get_posts($args) as $post) {
                        $meta = get_post_custom($post->ID);
                        ?>
                        <div class="col-lg-3 col-md-3 col-sm-col-xs-12 profile">
                            <div class="img-box"> 
                                <?php echo get_the_post_thumbnail($post->ID, "medium", array('class' => 'img-responsive')); ?>
                                <ul class="text-center">
                                    <li><a href="<?php echo $meta['profile_facebook'][0]; ?>"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="<?php echo $meta['profile_twitter'][0]; ?>"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="<?php echo $meta['profile_linkedin'][0]; ?>"><i class="fa fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                            <h1><?php echo $post->post_title; ?></h1>
                            <h2><?php echo $meta['profile_title'][0]; ?></h2>
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
