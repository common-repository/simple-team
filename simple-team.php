<?php
/**
 * Plugin Name: Simple Team
 * Description: A simple Team-Plugin to extend wordpress with a Team Portfolio.
 * Plugin URI: http://www.seiboldsoft.de
 * Author: Emanuel Seibold
 * Author URI: http://www.seiboldsoft.de
 * Version: 1.0
 * Text Domain: simple-team
 * License: GPL2

  Copyright 2016 Emanuel Seibold (email : wordpress AT seiboldsoft DOT de)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
/**
 * Define versions and pathes
 * 
 */
define('SST_VERSION', '1.0');
define('SST_PATH', dirname(__FILE__));
define('SST_PATH_INCLUDES', dirname(__FILE__) . '/inc');
define('SST_FOLDER', basename(SST_PATH));
define('SST_URL', plugins_url() . '/' . SST_FOLDER);
define('SST_URL_INCLUDES', SST_URL . '/inc');
define('SST_TEMPLATES', SST_URL . '/templates');

require plugin_dir_path(__FILE__) . 'inc/class-team-helper.php';

/**
 * 
 * The plugin base class - the root of all WP goods!
 * 
 * @author Emanuel Seibold
 *
 */
class SST_Plugin_Base {

    public $post_type = 'team';
    public $taxonomies = array('team-category');

    /**
     * 
     * Assign everything as a call from within the constructor
     */
    public function __construct() {

        add_theme_support('post-thumbnails', array($this->post_type));


        add_action('wp_enqueue_scripts', array($this, 'sst_add_CSS'));
        add_action('admin_enqueue_scripts', array($this, 'sst_add_admin_JS'));
        add_action('plugins_loaded', array($this, 'sst_add_textdomain'));
        add_action('admin_head', array($this, 'sst_custom_admin_head'));
        add_action('admin_init', array($this, 'sst_register_settings'), 5);
        add_action('init', array($this, 'register'));
        add_action('add_meta_boxes', array($this, 'team_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'), 10, 2);

        // Register activation and deactivation hooks
        register_activation_hook(__FILE__, 'sst_on_activate_callback');
        register_deactivation_hook(__FILE__, 'sst_on_deactivate_callback');


        add_filter('manage_edit-' . $this->post_type . '_columns', array($this, 'add_image_column'), 10, 1);
        add_action('manage_' . $this->post_type . '_posts_custom_column', array($this, 'display_image'), 10, 1);
        add_action('restrict_manage_posts', array($this, 'add_taxonomy_filters'));
        add_action('right_now_content_table_end', array($this, 'add_rightnow_counts'));
        add_action('dashboard_glance_items', array($this, 'add_glance_counts'));
    }

    public function register() {

        $this->register_post_type();
        $this->register_taxonomy_category();
        add_shortcode('simple-team', array($this, 'sst_team_shortcode_body'));
    }

    public function sst_custom_admin_head() {
        global $sst_helper;

        $team_categories = $sst_helper->list_categories();
        $available_team_templates = $sst_helper->show_templates("", false);

        echo '<script>';

        echo 'var shortcode_team_feeds = [ ';
        foreach ($team_categories as $category) {
            echo "{text: '" . $category->name . "', value: '" . $category->cat_ID . "'},";
        }
        echo '];';

        echo 'var shortcode_team_templates= [ ';
        foreach ($available_team_templates as $available_template) {
            echo "{text: '" . $available_template . "', value: '" . $available_template . "'},";
        }
        echo '];';

        echo '</script>';
    }

    /**
     *
     * Adding JavaScript scripts for the admin pages only
     *
     * Loading existing scripts from wp-includes or adding custom ones
     *
     */
    public function sst_add_admin_JS($hook) {

        wp_enqueue_script('jquery');
        wp_register_script('simple-team-admin', plugins_url('/js/simple-team-shortcode.js', __FILE__), array('jquery'), '1.0', true);
        wp_enqueue_script('simple-team-admin');
    }

    /**
     * 
     * Add CSS styles
     * 
     */
    public function sst_add_CSS() {
        wp_register_style('simple-team-style', plugins_url('/css/simple-team.css', __FILE__), array(), '1.0', 'screen');
        wp_enqueue_style('simple-team-style');
    }

    /**
     * Initialize the Settings class
     * 
     * Register a settings section with a field for a secure WordPress admin option creation.
     * 
     */
    public function sst_register_settings() {

        if (is_admin()) {
            // Loads for users viewing the WordPress dashboard
            if (!class_exists('SST_Dashboard_Glancer')) {
                require plugin_dir_path(__FILE__) . 'inc/class-dashboard-glancer.php';  // WP 3.8
            }
        }
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
            return;
        add_filter("mce_external_plugins", array($this, 'sst_team_register_tinymce_plugin'));
        add_filter('mce_buttons', array($this, 'sst_team_add_tinymce_button'));
    }

    /**
     * Register the custom post type.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_post_type
     */
    protected function register_post_type() {
        $labels = array(
            'name' => __('Team', 'simple-team'),
            'singular_name' => __('Team Member', 'simple-team'),
            'add_new' => __('Add Profile', 'simple-team'),
            'add_new_item' => __('Add Profile', 'simple-team'),
            'edit_item' => __('Edit Profile', 'simple-team'),
            'new_item' => __('New Team Member', 'simple-team'),
            'view_item' => __('View Profile', 'simple-team'),
            'search_items' => __('Search Team', 'simple-team'),
            'not_found' => __('No profiles found', 'simple-team'),
            'not_found_in_trash' => __('No profiles in the trash', 'simple-team'),
        );

        $supports = array(
            'title',
            'editor',
            'thumbnail',
            'custom-fields',
            'revisions',
        );

        $args = array(
            'labels' => $labels,
            'supports' => $supports,
            'public' => true,
            'capability_type' => 'post',
            'rewrite' => array('slug' => 'team',), // Permalinks format
            'menu_position' => 30,
            'menu_icon' => 'dashicons-id',
        );

        $args = apply_filters('team_post_type_args', $args);

        register_post_type($this->post_type, $args);
    }

    /**
     * Register a taxonomy for Team Categories.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
     */
    protected function register_taxonomy_category() {
        $labels = array(
            'name' => __('Team Categories', 'simple-team'),
            'singular_name' => __('Team Category', 'simple-team'),
            'menu_name' => __('Team Categories', 'simple-team'),
            'edit_item' => __('Edit Team Category', 'simple-team'),
            'update_item' => __('Update Team Category', 'simple-team'),
            'add_new_item' => __('Add New Team Category', 'simple-team'),
            'new_item_name' => __('New Team Category Name', 'simple-team'),
            'parent_item' => __('Parent Team Category', 'simple-team'),
            'parent_item_colon' => __('Parent Team Category:', 'simple-team'),
            'all_items' => __('All Team Categories', 'simple-team'),
            'search_items' => __('Search Team Categories', 'simple-team'),
            'popular_items' => __('Popular Team Categories', 'simple-team'),
            'separate_items_with_commas' => __('Separate team categories with commas', 'simple-team'),
            'add_or_remove_items' => __('Add or remove team categories', 'simple-team'),
            'choose_from_most_used' => __('Choose from the most used team categories', 'simple-team'),
            'not_found' => __('No team categories found.', 'simple-team'),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_tagcloud' => true,
            'hierarchical' => true,
            'rewrite' => array('slug' => 'team-category'),
            'show_admin_column' => true,
            'query_var' => true,
        );

        $args = apply_filters('team_post_type_category_args', $args);

        register_taxonomy($this->taxonomies[0], $this->post_type, $args);
    }

    function sst_team_register_tinymce_plugin($plugin_array) {
        $plugin_array['simple_team_button'] = plugins_url('/js/simple-team-shortcode.js', __FILE__);
        return $plugin_array;
    }

    function sst_team_add_tinymce_button($buttons) {
        $buttons[] = "simple_team_button";
        return $buttons;
    }

    /**
     * Returns the content of the simple-amazon
     * @param array $attr arguments passed to array
     * @param string $content optional, could be used for a content to be wrapped
     */
    public function sst_team_shortcode_body($attr, $content = null) {

        global $sst_helper;
        $output = '';

        $pull_atts = shortcode_atts(array(
            'template' => 'template',
            'id' => 'id',
            'limit' => 'limit',
            'title' => 'Our Team',
            'subtitle' => 'We have a perfect Team',
            'extra_class' => ''
                ), $attr);



        if (isset($pull_atts['id']) && intval($pull_atts['id'])) {



            $sst_helper->setTitle($pull_atts['title']);
            $sst_helper->setSubtitle($pull_atts['subtitle']);
            $sst_helper->setExtra_classes($pull_atts['extra_class']);

            if (isset($pull_atts['template'])) {
                $sst_helper->setTemplate_Name($pull_atts['template']);
            } else {
                $sst_helper->setTemplate_Name("default-responsive-team-bio");
            }
            if (isset($pull_atts['limit']) && intval($pull_atts['limit'])) {
                $sst_helper->setMax_items($pull_atts['limit']);
            } else {
                $sst_helper->setMax_items(4);
            }
            $sst_helper->setCategory($pull_atts['id']);
            $output .= $sst_helper->generate_output();
        }


        return $output;
    }

    /**
     * Add textdomain for plugin
     */
    public function sst_add_textdomain() {
        $lang_dir = basename(dirname(__FILE__)) . '/lang/';
        load_plugin_textdomain('simple-team', false, $lang_dir);
    }

    /**
     * Register the metaboxes to be used for the team post type
     *
     * @since 0.1.0
     */
    public function team_meta_boxes() {
        add_meta_box(
                'profile_fields', 'Profile Fields', array($this, 'render_meta_boxes'), 'team', 'normal', 'high'
        );
    }

    /**
     * The HTML for the fields
     *
     * @since 0.1.0
     */
    function render_meta_boxes($post) {

        $meta = get_post_custom($post->ID);
        $title = !isset($meta['profile_title'][0]) ? '' : $meta['profile_title'][0];
        $twitter = !isset($meta['profile_twitter'][0]) ? '' : $meta['profile_twitter'][0];
        $linkedin = !isset($meta['profile_linkedin'][0]) ? '' : $meta['profile_linkedin'][0];
        $facebook = !isset($meta['profile_facebook'][0]) ? '' : $meta['profile_facebook'][0];

        wp_nonce_field(basename(__FILE__), 'profile_fields');
        ?>

        <table class="form-table">

            <tr>
                <td class="team_meta_box_td" colspan="2">
                    <label for="profile_title"><?php _e('Title', 'simple-team'); ?>
                    </label>
                </td>
                <td colspan="4">
                    <input type="text" name="profile_title" class="regular-text" value="<?php echo $title; ?>">
                    <p class="description"><?php _e('E.g. CEO, Sales Lead, Designer', 'simple-team'); ?></p>
                </td>
            </tr>

            <tr>
                <td class="team_meta_box_td" colspan="2">
                    <label for="profile_linkedin"><?php _e('LinkedIn URL', 'simple-team'); ?>
                    </label>
                </td>
                <td colspan="4">
                    <input type="text" name="profile_linkedin" class="regular-text" value="<?php echo $linkedin; ?>">
                </td>
            </tr>

            <tr>
                <td class="team_meta_box_td" colspan="2">
                    <label for="profile_twitter"><?php _e('Twitter URL', 'simple-team'); ?>
                    </label>
                </td>
                <td colspan="4">
                    <input type="text" name="profile_twitter" class="regular-text" value="<?php echo $twitter; ?>">
                </td>
            </tr>

            <tr>
                <td class="team_meta_box_td" colspan="2">
                    <label for="profile_facebook"><?php _e('Facebook URL', 'simple-team'); ?>
                    </label>
                </td>
                <td colspan="4">
                    <input type="text" name="profile_facebook" class="regular-text" value="<?php echo $facebook; ?>">
                </td>
            </tr>

        </table>

        <?php
    }

    /**
     * Save metaboxes
     *
     * @since 0.1.0
     */
    function save_meta_boxes($post_id) {

        global $post;

        // Verify nonce
        if (!isset($_POST['profile_fields']) || !wp_verify_nonce($_POST['profile_fields'], basename(__FILE__))) {
            return $post_id;
        }

        // Check Autosave
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || ( defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit'])) {
            return $post_id;
        }

        // Don't save if only a revision
        if (isset($post->post_type) && $post->post_type == 'revision') {
            return $post_id;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post->ID)) {
            return $post_id;
        }

        $meta['profile_title'] = ( isset($_POST['profile_title']) ? esc_textarea($_POST['profile_title']) : '' );

        $meta['profile_linkedin'] = ( isset($_POST['profile_linkedin']) ? esc_url($_POST['profile_linkedin']) : '' );

        $meta['profile_twitter'] = ( isset($_POST['profile_twitter']) ? esc_url($_POST['profile_twitter']) : '' );

        $meta['profile_facebook'] = ( isset($_POST['profile_facebook']) ? esc_url($_POST['profile_facebook']) : '' );

        foreach ($meta as $key => $value) {
            update_post_meta($post->ID, $key, $value);
        }
    }

    /**
     * Custom column callback
     *
     * @global stdClass $post Post object.
     *
     * @param string $column Column ID.
     */
    public function display_image($column) {

        // global $post;
        switch ($column) {
            case 'thumbnail':
                // echo get_the_post_thumbnail( $post->ID, array(35, 35) );
                echo get_the_post_thumbnail(get_the_ID(), array(35, 35));
                break;
        }
    }

    /**
     * Add taxonomy filters to the post type list page.
     *
     * Code artfully lifted from http://pippinsplugins.com/
     *
     * @global string $typenow
     */
    public function add_taxonomy_filters() {
        global $typenow;

        // Must set this to the post type you want the filter(s) displayed on
        if ($this->post_type !== $typenow) {
            return;
        }

        foreach ($this->taxonomies as $tax_slug) {
            echo $this->build_taxonomy_filter($tax_slug);
        }
    }

    /**
     * Build an individual dropdown filter.
     *
     * @param  string $tax_slug Taxonomy slug to build filter for.
     *
     * @return string Markup, or empty string if taxonomy has no terms.
     */
    protected function build_taxonomy_filter($tax_slug) {
        $terms = get_terms($tax_slug);
        if (0 == count($terms)) {
            return '';
        }

        $tax_name = $this->get_taxonomy_name_from_slug($tax_slug);
        $current_tax_slug = isset($_GET[$tax_slug]) ? $_GET[$tax_slug] : false;

        $filter = '<select name="' . esc_attr($tax_slug) . '" id="' . esc_attr($tax_slug) . '" class="postform">';
        $filter .= '<option value="0">' . esc_html($tax_name) . '</option>';
        $filter .= $this->build_term_options($terms, $current_tax_slug);
        $filter .= '</select>';

        return $filter;
    }

    /**
     * Get the friendly taxonomy name, if given a taxonomy slug.
     *
     * @param  string $tax_slug Taxonomy slug.
     *
     * @return string Friendly name of taxonomy, or empty string if not a valid taxonomy.
     */
    protected function get_taxonomy_name_from_slug($tax_slug) {
        $tax_obj = get_taxonomy($tax_slug);
        if (!$tax_obj)
            return '';
        return $tax_obj->labels->name;
    }

    /**
     * Build a series of option elements from an array.
     *
     * Also checks to see if one of the options is selected.
     *
     * @param  array  $terms            Array of term objects.
     * @param  string $current_tax_slug Slug of currently selected term.
     *
     * @return string Markup.
     */
    protected function build_term_options($terms, $current_tax_slug) {
        $options = '';
        foreach ($terms as $term) {
            $options .= sprintf(
                    '<option value="%s"%s />%s</option>', esc_attr($term->slug), selected($current_tax_slug, $term->slug), esc_html($term->name . '(' . $term->count . ')')
            );
        }
        return $options;
    }

    /**
     * Add columns to post type list screen.
     *
     * @link http://wptheming.com/2010/07/column-edit-pages/
     *
     * @param array $columns Existing columns.
     *
     * @return array Amended columns.
     */
    public function add_image_column($columns) {
        $column_thumbnail = array('thumbnail' => __('Image', 'simple-team'));
        return array_slice($columns, 0, 2, true) + $column_thumbnail + array_slice($columns, 1, null, true);
    }

    /**
     * Add counts to "At a Glance" dashboard widget in WP 3.8+
     *
     * @since 0.1.0
     */
    public function add_glance_counts() {
        $glancer = new SST_Dashboard_Glancer;
        $glancer->add($this->post_type, array('publish', 'pending'));
    }

}

/**
 * Register activation hook
 *
 */
function sst_on_activate_callback() {

    flush_rewrite_rules();
}

/**
 * Register deactivation hook
 *
 */
function sst_on_deactivate_callback() {
    flush_rewrite_rules();
}

// Initialize everything

$sst_helper = new SST_Helper();
$sst_plugin_base = new SST_Plugin_Base();
