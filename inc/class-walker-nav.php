<?php
/**
 * Custom Walker Class for Navigation Mobile Menus
 *
 * This class extends the Walker_Nav_Menu class to customize the output of navigation menus.
 *
 * @package OpenGovAsia
 */
 class Mobile_Nav_Walker extends Walker_Nav_Menu {
 
     // Start Level (Sub-menu)
     function start_lvl(&$output, $depth = 0, $args = null) {
         $output .= '<ul class="uc-nav-sub" data-uc-nav>';
     }
 
     // End Level (Sub-menu)
     function end_lvl(&$output, $depth = 0, $args = null) {
         $output .= '</ul>';
     }
 
     // Start Element (Menu Item)
     function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
         $has_children = !empty($args->walker->has_children);
         $classes = !empty($item->classes) ? implode(' ', array_filter($item->classes)) : '';
         $classes .= $has_children ? ' uc-parent' : '';
         $classes = trim($classes);
 
         $output .= '<li' . (!empty($classes) ? ' class="' . esc_attr($classes) . '"' : '') . '>';
         $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
     }
 
     // End Element (Menu Item)
     function end_el(&$output, $item, $depth = 0, $args = null) {
         $output .= '</li>';
     }
 }
 