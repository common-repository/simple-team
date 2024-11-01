<?php
defined('ABSPATH') or die();
/*
 * WordPress Team Widgets
 * author: Emanuel Seibold
 * version: 2016.01.01
 * license: MIT
 */


add_action('widgets_init', function() {
    register_widget('Team_Widget');
});

/**
 * Adds Team widget.
 */
class Team_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
                'Team_Widget', // Base ID
                __('Simple Team Widget', 'simple-team'), // Name
                array('Beschreibung' => __('Team Widget', 'simple-team'),) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {

        echo '<div class="widget">';
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }


        $tplgenerator = new SST_Helper();
        $tplgenerator->setTemplate_Name($instance["team_category_template_id"]);
        $tplgenerator->setMax_items($instance["team_category_amount"]);
        $tplgenerator->setCategory($instance["team_category"]);
        echo $tplgenerator->generate_output();
        echo '</div>';
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {

        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Team Member', 'simple-team');
        }

        if (isset($instance['team_category_amount'])) {
            $amount = $instance['team_category_amount'];
        } else {
            $amount = -1;
        }


        if (isset($instance['team_category'])) {
            $team_category = $instance['team_category'];
        } else {
            $team_category = "";
        }

        if (isset($instance['team_category_template_id'])) {
            $team_category_template_id = $instance['team_category_template_id'];
        } else {
            $team_category_template_id = "";
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title');
        ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('team_category_name'); ?>"><?php _e('Category:'); ?></label> 
            <select class="widefat" id="<?php echo $this->get_field_id('team_category'); ?>" name="<?php echo $this->get_field_name('team_category'); ?>">
                <?php
                $categories = get_categories(array('type' => 'post', 'taxonomy' => 'team-category'));

                foreach ($categories as $category) {
                    if ($category->cat_ID == $team_category) {
                        echo '<option selected value="' . $category->cat_ID . '">' . $category->name . '</option>';
                    } else {
                        echo '<option value="' . $category->cat_ID . '">' . $category->name . '</option>';
                    }
                }
                ?>
            </select> 
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('team_category_template'); ?>"><?php _e('Template:'); ?></label> 
            <select class="widefat" id="<?php echo $this->get_field_id('team_category_template_id'); ?>" name="<?php echo $this->get_field_name('team_category_template_id'); ?>">
                <?php
                foreach (scandir(get_template_directory() . "/templates/team/") as $item) {

                    if (preg_match("/.php/i", $item)) {
                        if (pathinfo($item, PATHINFO_FILENAME) == $team_category_template_id) {
                            echo '<option selected value="' . pathinfo($item, PATHINFO_FILENAME) . '">' . pathinfo($item, PATHINFO_FILENAME) . '</option>';
                        } else {
                            echo '<option value="' . pathinfo($item, PATHINFO_FILENAME) . '">' . pathinfo($item, PATHINFO_FILENAME) . '</option>';
                        }
                    }
                }
                ?>
            </select> 
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('team_category_amount_name'); ?>"><?php _e('Amount:'); ?></label> 
            <select class="widefat" id="<?php echo $this->get_field_id('team_category_amount'); ?>" name="<?php echo $this->get_field_name('team_category_amount'); ?>">
                <?php
                for ($i = -1; $i <= 21; $i++) {

                    echo '<option ';
                    echo ($i == $amount) ? "selected " : "";
                    echo 'value="' . $i . '">' . $i . '</option>';
                }
                ?>
            </select> 
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['team_category'] = (!empty($new_instance['team_category']) ) ? strip_tags($new_instance['team_category']) : '';
        $instance['team_category_name'] = (!empty($new_instance['team_category_name']) ) ? strip_tags($new_instance['team_category_name']) : '';
        $instance['team_category_template_id'] = (!empty($new_instance['team_category_template_id']) ) ? strip_tags($new_instance['team_category_template_id']) : '';
        $instance['team_category_template'] = (!empty($new_instance['team_category_template']) ) ? strip_tags($new_instance['team_category_template']) : '';
        $instance['team_category_amount_name'] = (!empty($new_instance['team_category_amount_name']) ) ? strip_tags($new_instance['team_category_amount_name']) : '';
        $instance['team_category_amount'] = (!empty($new_instance['team_category_amount']) ) ? strip_tags($new_instance['team_category_amount']) : '';


        return $instance;
    }

}
