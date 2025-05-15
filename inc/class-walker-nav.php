<?php
/**
 * Custom Walker Class for Navigation Mobile Menus
 *
 * This class extends the Walker_Nav_Menu class to customize the output of navigation menus.
 *
 * @package OpenGovAsia
 */
class Mobile_Nav_Walker extends Walker_Nav_Menu
{

    // Start Level (Sub-menu)
    function start_lvl(&$output, $depth = 0, $args = null)
    {
        $output .= '<ul class="uc-nav-sub" data-uc-nav>';
    }

    // End Level (Sub-menu)
    function end_lvl(&$output, $depth = 0, $args = null)
    {
        $output .= '</ul>';
    }

    // Start Element (Menu Item)
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        $has_children = !empty($args->walker->has_children);
        $classes = !empty($item->classes) ? implode(' ', array_filter($item->classes)) : '';
        $classes .= $has_children ? ' uc-parent' : '';
        $classes = trim($classes);

        $output .= '<li' . (!empty($classes) ? ' class="' . esc_attr($classes) . '"' : '') . '>';
        $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
    }

    // End Element (Menu Item)
    function end_el(&$output, $item, $depth = 0, $args = null)
    {
        $output .= '</li>';
    }
}

/**
 * Custom Walker Class for Primary Navigation Menus
 *
 * This class extends the Walker_Nav_Menu class to customize the output of primary navigation menus.
 *
 * @package OpenGovAsia
 */

class Primary_Menu_Nav_Walker extends Walker_Nav_Menu
{

    public function start_lvl(&$output, $depth = 0, $args = null)
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<div class=\"uc-navbar-dropdown border border-gray-900 border-opacity-15 p-3 bg-white dark:bg-gray-800 shadow-xs rounded\" data-uc-drop=\" boundary: !.uc-navbar;\">\n";
        $output .= "$indent\t<div class=\"vstack gap-1 fw-medium\">\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = null)
    {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent\t</div>\n";
        $output .= "$indent</div>\n";
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $has_children = in_array('menu-item-has-children', $item->classes);

        if ($depth === 0) {
            $output .= "$indent<li class=\"fw-bold\">\n";
            $output .= "$indent\t<a href=\"" . esc_url($item->url) . "\" class=\"my-custom-class\" title=\"" . esc_attr($item->title) . "\">";
            $output .= esc_html($item->title);
            if ($has_children) {
                $output .= ' <span data-uc-navbar-parent-icon></span>';
            }
            $output .= "</a>\n";
        } else {
            $output .= "$indent\t<span>\n";
            $output .= "$indent\t\t<a href=\"" . esc_url($item->url) . "\" class=\"text-none hover:text-primary\">";
            $output .= esc_html($item->title);
            $output .= "</a>\n";
            $output .= "$indent\t</span>\n";
        }
    }

    public function end_el(&$output, $item, $depth = 0, $args = null)
    {
        if ($depth === 0) {
            $output .= "</li>\n";
        }
        
    }
}
