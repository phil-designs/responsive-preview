<?php

/**
* Plugin Name: Responsive Preview
* Plugin URI: https://www.phildesigns.com
* Description: This plugin wraps your Wordpress site within an interface to allow you to preview the mobile responsiveness of your theme.
* Version: 3.0.0
* Author: phil.designs | Phillip De Vita
* Author URI: http://www.phildesigns.com
* License: GPL2
*/

/**
 * Return an array of default device definitions used by the plugin.
 *
 * Each device has a unique key and an array of properties that include
 * name, width, height, icon (FontAwesome classes) and an optional
 * orientation label (Portrait/Landscape) used in the UI.
 *
 * @return array
 */
function rp_get_default_devices() {
    // default devices (portrait mode); rotation toggle will swap width/height if rotatable
    return array(
        'desktop' => array(
            'name'      => 'Desktop',
            'width'     => '100%',
            'height'    => '100%',
            'icon'      => 'fa fa-desktop',
            'rotatable' => false,
        ),
        'laptop' => array(
            'name'      => 'Laptop (1024px)',
            'width'     => 1024,
            'height'    => '100%',
            'icon'      => 'fa fa-laptop',
            'rotatable' => false,
        ),
        'laptop_wide' => array(
            'name'      => 'Laptop (1440px)',
            'width'     => 1440,
            'height'    => '100%',
            'icon'      => 'fa fa-laptop',
            'rotatable' => false,
        ),
        'tablet' => array(
            'name'      => 'Tablet (768px)',
            'width'     => 768,
            'height'    => '100%',
            'icon'      => 'fa fa-tablet',
            'rotatable' => false,
        ),
        'ipad' => array(
            'name'      => 'iPad (834px)',
            'width'     => 834,
            'height'    => 1112,
            'icon'      => 'fa fa-tablet',
            'rotatable' => true,
        ),
        // phones
        'iphone_se' => array(
            'name'      => 'iPhone SE',
            'width'     => 320,
            'height'    => 568,
            'icon'      => 'fa fa-mobile',
            'rotatable' => true,
        ),
        'iphone_xr' => array(
            'name'      => 'iPhone XR / 11 / 12 / 13 / 14',
            'width'     => 414,
            'height'    => 896,
            'icon'      => 'fa fa-mobile',
            'rotatable' => true,
        ),
        'iphone_pro' => array(
            'name'      => 'iPhone 14 Pro',
            'width'     => 393,
            'height'    => 852,
            'icon'      => 'fa fa-mobile',
            'rotatable' => true,
        ),
        'pixel7' => array(
            'name'      => 'Google Pixel 7',
            'width'     => 393,
            'height'    => 851,
            'icon'      => 'fa fa-mobile',
            'rotatable' => true,
        ),
        // legacy
        'android' => array(
            'name'      => 'Android Nexus 4',
            'width'     => 384,
            'height'    => 600,
            'icon'      => 'fa fa-mobile',
            'rotatable' => true,
        ),
    );
}

// add a link to the WP Toolbar
function custom_toolbar_link($wp_admin_bar) {
    $args = array(
        'id' => 'responsive_preview',
        'title' => 'Responsive Preview', 
        'href' => plugin_dir_url( __FILE__ ) . 'preview-page.php', 
        'meta' => array(
            'class' => 'responsive_preview_btn',
            'target' => '_blank', 
            'title' => 'Responsive Preview'
            )
    );
    $wp_admin_bar->add_node($args);
}
add_action('admin_bar_menu', 'custom_toolbar_link', 999);
/* The add_action # is the menu position:
10 = Before the WP Logo
15 = Between the logo and My Sites
25 = After the My Sites menu
100 = End of menu
*/

// add a "Settings" link under the plugin name on the Plugins page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'rp_settings_link');
function rp_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=responsive-preview">Settings</a>';
    array_unshift( $links, $settings_link );
    return $links;
}

// ---------- SETTINGS PAGE --------------------------------------------------
add_action('admin_menu', 'rp_add_admin_menu');
add_action('admin_init', 'rp_settings_init');

/**
 * Register the settings and add fields.
 */
function rp_settings_init() {
    register_setting('rp_settings', 'rp_devices', 'rp_sanitize_devices');
    register_setting('rp_settings', 'rp_custom_devices', 'rp_sanitize_custom_devices');

    // existing devices checkbox section
    add_settings_section(
        'rp_section',
        'Available Device Viewports',
        'rp_section_callback',
        'responsive-preview'
    );

    $devices = rp_get_default_devices();
    foreach ( $devices as $key => $device ) {
        add_settings_field(
            'rp_field_' . $key,
            $device['name'],
            'rp_field_render',
            'responsive-preview',
            'rp_section',
            array( 'key' => $key, 'device' => $device )
        );
    }

    // custom device management section
    add_settings_section(
        'rp_custom_section',
        'Custom Devices',
        'rp_custom_section_callback',
        'responsive-preview'
    );
    add_settings_field(
        'rp_field_custom',
        'Add / Remove devices',
        'rp_custom_field_render',
        'responsive-preview',
        'rp_custom_section'
    );
}

/**
 * Sanitize the devices option. Only allow keys that exist in the defaults.
 */
function rp_sanitize_devices( $input ) {
    // we allow enabling of any key, not just defaults, so custom devices can be toggled
    $sanitized = array();
    if ( is_array( $input ) ) {
        foreach ( $input as $key => $val ) {
            if ( $val ) {
                $sanitized[ sanitize_key( $key ) ] = 1;
            }
        }
    }
    // desktop (100%) should always be available
    $sanitized['desktop'] = 1;
    return $sanitized;
}

function rp_section_callback() {
    echo '<p>Check the viewport presets you want to appear in the preview dropdown. The desktop option (100% width) cannot be removed.</p>';
}

function rp_custom_section_callback() {
    echo '<p>You can define additional device presets. Give each a name and dimensions; portrait is assumed and orientation may be toggled on the front end.</p>';
}

function rp_custom_field_render() {
    ?>
    <table id="rp-custom-table" class="widefat">
        <thead>
            <tr>
                <th style="padding-left:10px;">Name</th>
                <th>Width</th>
                <th>Height</th>
                <th>Rotatable?</th>
                <th>Enabled</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <p><button id="rp-custom-add" class="button">Add Device</button></p>
    <?php
}

/**
 * Merge default and custom devices into a single array.
 * Custom devices will override defaults if the same key is used.
 *
 * @return array
 */
function rp_get_all_devices() {
    $defaults = rp_get_default_devices();
    $custom = get_option('rp_custom_devices', array());
    if ( is_array( $custom ) ) {
        foreach ( $custom as $key => $data ) {
            // ensure rotatable flag exists
            if ( ! isset( $data['rotatable'] ) ) {
                $data['rotatable'] = false;
            }
            $defaults[ $key ] = $data;
        }
    }
    return $defaults;
}

/**
 * Sanitize custom devices array. Keys may be provided or generated.
 */
function rp_sanitize_custom_devices( $input ) {
    $sanitized = array();
    if ( ! is_array( $input ) ) {
        return $sanitized;
    }
    foreach ( $input as $key => $device ) {
        if ( ! is_array( $device ) ) {
            continue;
        }
        $name = sanitize_text_field( $device['name'] ?? '' );
        $width = sanitize_text_field( $device['width'] ?? '' );
        $height = sanitize_text_field( $device['height'] ?? '' );
        $rot = isset( $device['rotatable'] ) ? 1 : 0;
        if ( $name === '' || $width === '' || $height === '' ) {
            continue; // skip incomplete
        }
        // ensure key is unique and valid
        if ( empty( $key ) || 'new_' === substr( $key, 0, 4 ) ) {
            $key = 'custom_' . uniqid();
        }
        $sanitized[ $key ] = array(
            'name'      => $name,
            'width'     => $width,
            'height'    => $height,
            'icon'      => 'fa fa-mobile',
            'rotatable' => $rot,
        );
    }
    // remove any devices that were present previously but are no longer in sanitized custom list
    $old = get_option( 'rp_custom_devices', array() );
    if ( is_array( $old ) ) {
        $devices = get_option( 'rp_devices', array() );
        foreach ( array_keys( $old ) as $oldkey ) {
            if ( ! isset( $sanitized[ $oldkey ] ) ) {
                if ( isset( $devices[ $oldkey ] ) ) {
                    unset( $devices[ $oldkey ] );
                }
            }
        }
        update_option( 'rp_devices', $devices );
    }
    return $sanitized;
}


/**
 * Render a checkbox for each device.
 */
function rp_field_render( $args ) {
    $key     = $args['key'];
    $device  = $args['device'];
    $options = get_option('rp_devices', array());
    ?>
    <input type="checkbox" name="rp_devices[<?php echo esc_attr($key); ?>]" value="1" <?php checked( isset( $options[ $key ] ) && $options[ $key ] ); ?> <?php disabled( 'desktop' === $key ); ?> />
    <?php
}

/**
 * Output the settings page markup.
 */
function rp_options_page() {
    ?>
    <div class="wrap">
        <h1>Responsive Preview Settings</h1>
        <form action="options.php" method="post" id="rp-settings-form">
            <?php
                settings_fields('rp_settings');
                do_settings_sections('responsive-preview');
                submit_button();
            ?>
        </form>
    </div>
    <script>
    jQuery(document).ready(function($){
        var customSection = $('#rp_field_custom').closest('tr');
        // add row button handler
        function addRow(data) {
            var idx = data && data.key ? data.key : 'new_' + Date.now();
            var name = data && data.name ? data.name : '';
            var width = data && data.width ? data.width : '';
            var height = data && data.height ? data.height : '';
            var rot = data && data.rotatable ? 'checked' : '';
            var enabled = (data ? (data.enabled ? 'checked' : '') : 'checked');
            var row = '<tr class="rp-custom-row">' +
                    '<td><input type="hidden" name="rp_custom_devices['+idx+'][key]" value="'+idx+'" />' +
                    '<input type="text" name="rp_custom_devices['+idx+'][name]" value="'+name+'" placeholder="Name" /></td>' +
                    '<td><input type="text" name="rp_custom_devices['+idx+'][width]" value="'+width+'" placeholder="width" /></td>' +
                    '<td><input type="text" name="rp_custom_devices['+idx+'][height]" value="'+height+'" placeholder="height" /></td>' +
                    '<td><input type="checkbox" name="rp_custom_devices['+idx+'][rotatable]" value="1" '+rot+' /></td>' +
                    '<td><input type="checkbox" name="rp_devices['+idx+']" value="1" '+enabled+' /></td>' +
                    '<td><button class="button rp-remove-row">Remove</button></td>' +
                '</tr>';
            $('#rp-custom-table tbody').append(row);
        }
        $('#rp-custom-add').click(function(e){
            e.preventDefault();
            addRow();
        });
        $(document).on('click', '.rp-remove-row', function(e){
            e.preventDefault();
            $(this).closest('tr').remove();
        });
        // populate existing
        var existing = <?php echo json_encode(get_option('rp_custom_devices', array())); ?>;
        var selected = <?php echo json_encode(get_option('rp_devices', array())); ?>;
        for(var k in existing){ if(existing.hasOwnProperty(k)){
            existing[k].key = k;
            existing[k].enabled = selected[k] ? true : false;
            addRow(existing[k]);
        }}
    });
    </script>
    <?php
}

function rp_add_admin_menu() {
    add_options_page(
        'Responsive Preview',
        'Responsive Preview',
        'manage_options',
        'responsive-preview',
        'rp_options_page'
    );
}

?>