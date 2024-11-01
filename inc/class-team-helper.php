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
class SST_Helper {

    protected $templates_folder;
    protected $templates;
    protected $category;
    protected $template_name;
    protected $max_items;
    protected $title;
    protected $subtitle;
    protected $extra_classes;

    function __construct() {
        
    }

    public function list_categories($filter = false) {
        return get_categories(array('type' => 'post', 'taxonomy' => 'team-category'));
    }

    public function show_templates($start_slug = "", $echo = true) {
        $items = array();

        foreach (scandir(SST_PATH . "/templates/") as $item) {
            if (preg_match("/.php/i", $item)) {
                if ($echo)
                    echo $item;
                $items[] = pathinfo($item, PATHINFO_FILENAME);
            }
        }
        return $items;
    }

    public function set($key, $value) {
        $this->values[$key] = $value;
    }

    public function generate_output() {
        ob_start();
        if ($this->template_name != '' && $this->category != '') {
            $args = array('post_type' => 'team', 'posts_per_page' => $this->max_items,
                'numberposts' => $this->max_items,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'team-category',
                        'field' => 'term_id',
                        'terms' => $this->category,
            )));
            ob_start();
            include(SST_PATH . "/templates/" . $this->template_name . ".php");
            $var = ob_get_contents();
            ob_get_clean();
            return preg_replace('/^\s+|\n|\r|\s+$/m', '', $var);
        }
    }

    function getTemplates() {
        return $this->templates;
    }

    function getCategory() {
        return $this->category;
    }

    function getTemplate_Name() {
        return $this->template_name;
    }

    function getMax_items() {
        return $this->max_items;
    }

    function setTemplates($templates) {
        $this->templates = $templates;
    }

    function setCategory($category) {
        $this->category = $category;
    }

    function setTemplate_Name($template_name) {
        $this->template_name = $template_name;
    }

    function setMax_items($max_items) {
        $this->max_items = $max_items;
    }

    function getTitle() {
        return $this->title;
    }

    function getSubtitle() {
        return $this->subtitle;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function setSubtitle($subtitle) {
        $this->subtitle = $subtitle;
    }

    function getExtra_classes() {
        return $this->extra_classes;
    }

    function setExtra_classes($extra_classes) {
        $this->extra_classes = $extra_classes;
    }

}
