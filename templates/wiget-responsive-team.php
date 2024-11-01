<section class="team">
    <ul class="widget-team">
        <?php
//valid meta fields:
// short_bio, email, designation, web_url, telephone, location, social, profile_title, profile_linkedin, _thumbnail_id
        foreach (get_posts($args) as $post) {
            $meta = get_post_custom($post->ID);
            ?>
            <li>
                <h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute('echo=0'); ?>"><?php echo $post->post_title; ?></a></h3>
                <?php echo get_the_post_thumbnail($post->ID, "medium", array('class' => 'img-responsive img-rounded')); ?>
                <h4><?php echo $meta['profile_title'][0]; ?></h4>
            </li>
            <?php
        }
        ?>

    </ul>
</section>
