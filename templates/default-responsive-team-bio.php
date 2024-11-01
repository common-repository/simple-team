<section class="team">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="col-lg-12">
                <div class="border-title">
                    <h2><?php echo $this->title; ?></h2>
                    <div class='ico-border'> <i class='ico-bg flower'></i> </div>
                    <span class="tag-line"><?php echo $this->subtitle; ?></span>
                </div>      
                <?php
                //valid meta fields:
// short_bio, email, designation, web_url, telephone, location, social, profile_title, profile_linkedin, _thumbnail_id
                $curPost = 0;
                foreach (get_posts($args) as $post) {
                    if ($curPost == 0)
                        echo '<div class="row">';
                    $meta = get_post_custom($post->ID);
                    ?>
                    <div class="col-lg-3 col-md-3 col-sm-col-xs-12 profile" >
                        <div class="img-box">
                            <?php
                            echo get_the_post_thumbnail($post->ID, "medium", array('class' => 'img-responsive img-rounded'));
                            ?>

                        </div>
                        <h1><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute('echo=0'); ?>"><?php echo $post->post_title; ?></a></h1>
                        <h2><?php echo $meta['profile_title'][0]; ?></h2>
                    </div>
                    <?php
                    if ($curPost == 3) {
                        echo '</div>';
                        $curPost = 0;
                    } else {
                        $curPost++;
                    }
                }
                ?>
            </div>

        </div>
    </div>
</div>
</section>
