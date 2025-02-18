<?php
/**
 * Plugin Name: Project Metadata Manager
 * Description: Adds a custom post type for projects with metadata fields and a shortcode to display project info.
 * Version: 1.6
 * Author: Jeremy Malcolm
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Register Custom Post Type: Projects
function pmm_register_project_cpt() {
    $args = array(
        'labels' => array(
            'name' => 'Projects',
            'singular_name' => 'Project'
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail'),
    );
    register_post_type('project', $args);
}
add_action('init', 'pmm_register_project_cpt');

// Enqueue the plugin stylesheet
function pmm_enqueue_styles() {
    wp_enqueue_style('pmm-style', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'pmm_enqueue_styles');

// Register Custom Taxonomies: Project Type and Priority Area
function pmm_register_project_taxonomies() {
    $taxonomies = [
        'project_type' => 'Project Type',
        'priority_area' => 'Priority Area'
    ];
    
    foreach ($taxonomies as $slug => $name) {
        register_taxonomy($slug, 'project', array(
            'labels' => array('name' => $name . 's', 'singular_name' => $name),
            'public' => true,
            'hierarchical' => false,
            'show_in_rest' => true,
        ));
    }
}
add_action('init', 'pmm_register_project_taxonomies');

// Add Custom Meta Fields
function pmm_add_meta_boxes() {
    add_meta_box('pmm_project_details', 'Project Details', 'pmm_project_meta_callback', 'project', 'side');
}
add_action('add_meta_boxes', 'pmm_add_meta_boxes');

function pmm_project_meta_callback($post) {
    $fields = [
        'pmm_from_date' => 'From Date',
        'pmm_to_date' => 'To Date',
        'pmm_contact_name' => 'Contact Name',
        'pmm_contact_details' => 'Contact Details',
        'pmm_project_url' => 'Project URL'
    ];
    
    foreach ($fields as $field => $label) {
        $value = get_post_meta($post->ID, $field, true);
        echo "<p><label>{$label}:</label><input type='text' name='{$field}' value='" . esc_attr($value) . "'></p>";
    }
}

function pmm_save_project_meta($post_id) {
    if (get_post_type($post_id) !== 'project') {
        return;
    }
    $fields = ['pmm_from_date', 'pmm_to_date', 'pmm_contact_name', 'pmm_contact_details', 'pmm_project_url'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'pmm_save_project_meta');

// Shortcode to Display Infobox
function pmm_project_infobox_shortcode($atts) {
    // Set default attributes
    $atts = shortcode_atts(
        array(
            'id' => 0, // Default to 0, which means no project selected
        ), 
        $atts, 
        'project_infobox'
    );
    
    // If no project ID is provided, return empty
    if ($atts['id'] == 0) return '';
    
    // Get project details
    $project_id = $atts['id'];
    $fields = [
        'pmm_from_date' => 'Dates',
        'pmm_contact_name' => 'Contact',
        'pmm_contact_details' => 'Contact Details',
        'pmm_project_url' => 'Website'
    ];
    $data = '';
    
    // Handle date formatting and show the correct Dates string
    $from_date = get_post_meta($project_id, 'pmm_from_date', true);
    $to_date = get_post_meta($project_id, 'pmm_to_date', true);
    
    // If from date is present, format it as month/year
    if ($from_date) {
        $from_date_obj = new DateTime($from_date);
        $from_date_formatted = $from_date_obj->format('F Y');
    }
    
    // If to date is present, format it as month/year, otherwise use "Current"
    if ($to_date) {
        $to_date_obj = new DateTime($to_date);
        $to_date_formatted = $to_date_obj->format('F Y');
    } else {
        $to_date_formatted = 'Current';
    }
    
    // Combine dates into a single string
    $dates_string = $from_date_formatted . ' - ' . $to_date_formatted;

    // Add the Dates field to the data
    $data .= "<p><strong>Dates:</strong> " . esc_html($dates_string) . "</p>";
    
    // Loop through other fields
    foreach ($fields as $key => $label) {
        $value = get_post_meta($project_id, $key, true);
        if ($value && $key !== 'pmm_from_date') { // Skip the from_date field since it's handled above
            if ($key === 'pmm_project_url' && filter_var($value, FILTER_VALIDATE_URL)) {
                $data .= "<p><strong>{$label}:</strong> <a href='" . esc_url($value) . "' target='_blank'>" . esc_html($value) . "</a></p>";
            } elseif ($key === 'pmm_contact_details' && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $data .= "<p><strong>{$label}:</strong> <a href='mailto:" . esc_html($value) . "'>" . esc_html($value) . "</a></p>";
            } else {
                $data .= "<p><strong>{$label}:</strong> " . esc_html($value) . "</p>";
            }
        }
    }

    // Additional project details
    $project_name = get_the_title($project_id);
    $project_types = wp_get_post_terms($project_id, 'project_type', ['fields' => 'all']);
    $priority_areas = wp_get_post_terms($project_id, 'priority_area', ['fields' => 'all']);
    $project_image = get_the_post_thumbnail($project_id, 'thumbnail', ['class' => 'pmm-logo']);
    
    ob_start();
    ?>
    <div class="pmm-project-info-box">
        <?php if ($project_image) : ?>
            <div class="pmm-logo-container"> <?php echo $project_image; ?> </div>
        <?php endif; ?>
        <div class="pmm-info-container">
            <?php if ($project_name) : ?><p><strong>Name:</strong> <?php echo esc_html($project_name); ?></p><?php endif; ?>
            <?php if (!empty($project_types)) : ?>
                <p><strong>Type:</strong> 
                <?php 
                    foreach ($project_types as $type) {
                        $tooltip = $type->description ? "title='" . esc_attr($type->description) . "'" : '';
                        echo "<span class='custom-tooltip' {$tooltip}>" . esc_html($type->name) . "</span> ";
                    }
                ?>
                </p>
            <?php endif; ?>
            <?php if (!empty($priority_areas)) : ?>
                <p><strong>Priority Area:</strong> 
                <?php 
                    foreach ($priority_areas as $area) {
                        $tooltip = $area->description ? "title='" . esc_attr($area->description) . "'" : '';
                        echo "<span class='custom-tooltip' {$tooltip}>" . esc_html($area->name) . "</span> ";
                    }
                ?>
                </p>
            <?php endif; ?>
            <?php echo $data; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('project_infobox', 'pmm_project_infobox_shortcode');

// Default Single Post Template
function pmm_default_project_template($template) {
    if (is_singular('project')) {
        return plugin_dir_path(__FILE__) . 'templates/single-project.php';
    }
    return $template;
}
add_filter('template_include', 'pmm_default_project_template');

// Custom Archive Template
function pmm_default_project_archive_template($template) {
    if (is_post_type_archive('project')) {
        return plugin_dir_path(__FILE__) . 'templates/archive-project.php';
    }
    return $template;
}
add_filter('template_include', 'pmm_default_project_archive_template');
?>
