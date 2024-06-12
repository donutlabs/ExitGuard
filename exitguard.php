<?php
/**
 * Plugin Name: ExitGuard
 * Description: Add a customizable warning message when visitors click on external links.
 * Version: 1.2
 * Author: Christopher Spradlin
 */

// Add warning message to external links
function exitguard_add_warning($content) {
    $warning_message = get_option('exitguard_message', 'You are leaving our site. Do you want to continue?');
    $color = get_option('exitguard_color', '#ff0000');
    $text_color = get_option('exitguard_text_color', '#ffffff');

    $content .= '<script>
        jQuery(document).ready(function($) {
            $("a").filter(function() {
                return this.hostname && this.hostname !== location.hostname;
            }).click(function() {
                return confirm("' . esc_js($warning_message) . '");
            });
        });
    </script>';
    
    $content .= '<style>
        .exitguard {
            background-color: ' . esc_attr($color) . ';
            color: ' . esc_attr($text_color) . ';
            padding: 10px;
            text-align: center;
        }
    </style>';

    return $content;
}
add_filter('the_content', 'exitguard_add_warning');

// Add settings page to customize warning message
function exitguard_settings_page() {
    add_options_page('exitguard Settings', 'exitguard Settings', 'manage_options', 'exitguard-settings', 'exitguard_settings_page_callback');
}
add_action('admin_menu', 'exitguard_settings_page');

function exitguard_settings_page_callback() {
    ?>
    <div class="wrap">
        <h1>exitguard Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('exitguard_options');
            do_settings_sections('exitguard-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function exitguard_settings_init() {
    add_settings_section('exitguard_section', 'exitguard Settings', 'exitguard_section_callback', 'exitguard-settings');
    add_settings_field('exitguard_message', 'Warning Message', 'exitguard_message_callback', 'exitguard-settings', 'exitguard_section');
    add_settings_field('exitguard_color', 'Background Color', 'exitguard_color_callback', 'exitguard-settings', 'exitguard_section');
    add_settings_field('exitguard_text_color', 'Text Color', 'exitguard_text_color_callback', 'exitguard-settings', 'exitguard_section');

    register_setting('exitguard_options', 'exitguard_message');
    register_setting('exitguard_options', 'exitguard_color');
    register_setting('exitguard_options', 'exitguard_text_color');
}
add_action('admin_init', 'exitguard_settings_init');

function exitguard_section_callback() {
    echo 'Customize the warning message and styles for the exitguard plugin.';
}

function exitguard_message_callback() {
    $message = get_option('exitguard_message', 'You are leaving our site. Do you want to continue?');
    echo '<textarea id="exitguard_message" name="exitguard_message" rows="5" cols="50">' . esc_textarea($message) . '</textarea>';
}

function exitguard_color_callback() {
    $color = get_option('exitguard_color', '#ff0000');
    echo '<input type="color" id="exitguard_color" name="exitguard_color" value="' . esc_attr($color) . '">';
}

function exitguard_text_color_callback() {
    $text_color = get_option('exitguard_text_color', '#ffffff');
    echo '<input type="color" id="exitguard_text_color" name="exitguard_text_color" value="' . esc_attr($text_color) . '">';
}

