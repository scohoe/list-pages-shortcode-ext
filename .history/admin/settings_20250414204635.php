<?php
if (!defined('ABSPATH')) exit;

function list_pages_shortcode_settings_page() {
    ?>
    <div class="wrap">
        <h1>List Pages Shortcode CSS Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('list_pages_shortcode_settings');
            do_settings_sections('list_pages_shortcode_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function list_pages_shortcode_register_settings() {
    register_setting(
        'list_pages_shortcode_settings',
        'list_pages_shortcode_custom_css',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => ''
        )
    );

    add_settings_section(
        'list_pages_shortcode_css_section',
        'Custom CSS',
        'list_pages_shortcode_css_section_callback',
        'list_pages_shortcode_settings'
    );

    add_settings_field(
        'list_pages_shortcode_custom_css',
        'Custom CSS',
        'list_pages_shortcode_custom_css_callback',
        'list_pages_shortcode_settings',
        'list_pages_shortcode_css_section'
    );
}

function list_pages_shortcode_css_section_callback() {
    echo '<p>Add custom CSS styles for the List Pages Shortcode plugin. These styles will be applied to all shortcode outputs.</p>';
}

function list_pages_shortcode_custom_css_callback() {
    $custom_css = get_option('list_pages_shortcode_custom_css', '');
    ?>
    <textarea name="list_pages_shortcode_custom_css" rows="10" cols="50" class="large-text code"><?php echo esc_textarea($custom_css); ?></textarea>
    <p class="description">Enter your custom CSS here. It will be added to the front-end of your site.</p>
    <?php
}