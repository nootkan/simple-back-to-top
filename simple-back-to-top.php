<?php
/**
 * Plugin Name: Simple Back to Top
 * Plugin URI: https://yourwebsite.com
 * Description: A simple back to top button with smooth scrolling for ClassicPress sites.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: simple-back-to-top
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class SimpleBackToTop {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('wp_footer', array($this, 'add_back_to_top_button'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Enqueue frontend CSS and JavaScript files
     */
    public function enqueue_frontend_scripts() {
        wp_enqueue_script(
            'simple-back-to-top-js',
            plugin_dir_url(__FILE__) . 'assets/back-to-top.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'simple-back-to-top-css',
            plugin_dir_url(__FILE__) . 'assets/back-to-top.css',
            array(),
            '1.0.0'
        );
        
        // Add inline CSS for custom colors
        $this->add_custom_colors();
    }
    
    /**
     * Enqueue admin CSS and JavaScript files
     */
    public function enqueue_admin_scripts() {
        // Only enqueue admin styles and scripts on our settings page
        $screen = get_current_screen();
        if ($screen && $screen->id === 'settings_page_simple-back-to-top') {
            wp_enqueue_style(
                'simple-back-to-top-admin-css',
                plugin_dir_url(__FILE__) . 'assets/admin-styles.css',
                array(),
                '1.0.0'
            );
            
            wp_enqueue_script(
                'simple-back-to-top-admin-js',
                plugin_dir_url(__FILE__) . 'assets/admin-scripts.js',
                array('jquery'),
                '1.0.0',
                true
            );
        }
    }
    
    /**
     * Add custom colors based on admin settings
     */
    public function add_custom_colors() {
        $button_color = get_option('sbt_button_color', '#333333');
        $hover_color = get_option('sbt_hover_color', '#555555');
        $text_color = get_option('sbt_text_color', '#ffffff');
        $button_shape = get_option('sbt_button_shape', 'circle');
        $button_size = get_option('sbt_button_size', '50');
        $border_radius = get_option('sbt_border_radius', '50');
        
        // Shadow settings
        $enable_shadow = get_option('sbt_enable_shadow', '1');
        $shadow_color = get_option('sbt_shadow_color', '#000000');
        $shadow_intensity = get_option('sbt_shadow_intensity', '30');
        
        // Border settings
        $enable_border = get_option('sbt_enable_border', '0');
        $border_color = get_option('sbt_border_color', '#ffffff');
        $border_width = get_option('sbt_border_width', '2');
        
        // Calculate border radius based on shape and custom setting
        $radius_value = $border_radius;
        if ($button_shape === 'circle') {
            $radius_value = '50'; // Always 50% for perfect circle
        } elseif ($button_shape === 'square') {
            $radius_value = '0'; // No radius for perfect square
        }
        // For 'custom' shape, use the user's border_radius setting
        
        // Build shadow CSS
        $shadow_css = 'none';
        if ($enable_shadow === '1') {
            $shadow_opacity = $shadow_intensity / 100;
            $shadow_css = "0 2px 10px rgba(" . hexdec(substr($shadow_color, 1, 2)) . ", " . hexdec(substr($shadow_color, 3, 2)) . ", " . hexdec(substr($shadow_color, 5, 2)) . ", {$shadow_opacity})";
            $hover_shadow_css = "0 4px 15px rgba(" . hexdec(substr($shadow_color, 1, 2)) . ", " . hexdec(substr($shadow_color, 3, 2)) . ", " . hexdec(substr($shadow_color, 5, 2)) . ", " . ($shadow_opacity + 0.1) . ")";
        } else {
            $hover_shadow_css = 'none';
        }
        
        // Build border CSS
        $border_css = 'none';
        if ($enable_border === '1') {
            $border_css = "{$border_width}px solid {$border_color}";
        }
        
        $custom_css = "
        <style type='text/css'>
            .back-to-top {
                background-color: {$button_color} !important;
                color: {$text_color} !important;
                width: {$button_size}px !important;
                height: {$button_size}px !important;
                border-radius: {$radius_value}% !important;
                box-shadow: {$shadow_css} !important;
                border: {$border_css} !important;
            }
            .back-to-top:hover {
                background-color: {$hover_color} !important;
                box-shadow: {$hover_shadow_css} !important;
            }
            @media (max-width: 768px) {
                .back-to-top {
                    width: " . ($button_size - 5) . "px !important;
                    height: " . ($button_size - 5) . "px !important;
                }
            }
        </style>";
        
        echo $custom_css;
    }
    
    /**
     * Add the back to top button to the footer
     */
    public function add_back_to_top_button() {
        $button_text = get_option('sbt_button_text', '↑');
        $button_position = get_option('sbt_button_position', 'bottom-right');
        
        echo '<div id="back-to-top" class="back-to-top ' . esc_attr($button_position) . '" title="Back to Top">';
        echo '<span>' . esc_html($button_text) . '</span>';
        echo '</div>';
    }
    
    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_options_page(
            'Back to Top Settings',
            'Back to Top',
            'manage_options',
            'simple-back-to-top',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting('simple_back_to_top_settings', 'sbt_button_text');
        register_setting('simple_back_to_top_settings', 'sbt_button_position');
        register_setting('simple_back_to_top_settings', 'sbt_scroll_offset');
        register_setting('simple_back_to_top_settings', 'sbt_button_color');
        register_setting('simple_back_to_top_settings', 'sbt_hover_color');
        register_setting('simple_back_to_top_settings', 'sbt_text_color');
        register_setting('simple_back_to_top_settings', 'sbt_button_shape');
        register_setting('simple_back_to_top_settings', 'sbt_button_size');
        register_setting('simple_back_to_top_settings', 'sbt_border_radius');
        register_setting('simple_back_to_top_settings', 'sbt_enable_shadow');
        register_setting('simple_back_to_top_settings', 'sbt_shadow_color');
        register_setting('simple_back_to_top_settings', 'sbt_shadow_intensity');
        register_setting('simple_back_to_top_settings', 'sbt_enable_border');
        register_setting('simple_back_to_top_settings', 'sbt_border_color');
        register_setting('simple_back_to_top_settings', 'sbt_border_width');
    }
    
    /**
     * Admin settings page
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>Back to Top Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('simple_back_to_top_settings'); ?>
                <?php do_settings_sections('simple_back_to_top_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Button Text/Symbol</th>
                        <td>
                            <input type="text" name="sbt_button_text" value="<?php echo esc_attr(get_option('sbt_button_text', '↑')); ?>" />
                            <p class="description">Text or symbol to display in the button. Popular options: ↑, ⬆, ▲, TOP, UP</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Quick Symbol Select</th>
                        <td>
                            <button type="button" class="button symbol-btn" data-symbol="↑">↑ Arrow Up</button>
                            <button type="button" class="button symbol-btn" data-symbol="⬆">⬆ Bold Arrow</button>
                            <button type="button" class="button symbol-btn" data-symbol="▲">▲ Triangle</button>
                            <button type="button" class="button symbol-btn" data-symbol="⇧">⇧ Shift Arrow</button>
                            <button type="button" class="button symbol-btn" data-symbol="TOP">TOP</button>
                            <button type="button" class="button symbol-btn" data-symbol="UP">UP</button>
                            <p class="description">Click any symbol to use it as your button text</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Button Position</th>
                        <td>
                            <select name="sbt_button_position">
                                <option value="bottom-right" <?php selected(get_option('sbt_button_position', 'bottom-right'), 'bottom-right'); ?>>Bottom Right</option>
                                <option value="bottom-left" <?php selected(get_option('sbt_button_position'), 'bottom-left'); ?>>Bottom Left</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Button Colors</th>
                        <td>
                            <div class="color-picker-section">
                                <label>Background Color:</label><br>
                                <div class="color-input-wrapper">
                                    <input type="text" name="sbt_button_color" class="color-field" value="<?php echo esc_attr(get_option('sbt_button_color', '#333333')); ?>" />
                                    <div class="color-preview" data-target="sbt_button_color" style="background-color: <?php echo esc_attr(get_option('sbt_button_color', '#333333')); ?>"></div>
                                </div>
                                <br><br>
                                
                                <label>Hover Color:</label><br>
                                <div class="color-input-wrapper">
                                    <input type="text" name="sbt_hover_color" class="color-field" value="<?php echo esc_attr(get_option('sbt_hover_color', '#555555')); ?>" />
                                    <div class="color-preview" data-target="sbt_hover_color" style="background-color: <?php echo esc_attr(get_option('sbt_hover_color', '#555555')); ?>"></div>
                                </div>
                                <br><br>
                                
                                <label>Text Color:</label><br>
                                <div class="color-input-wrapper">
                                    <input type="text" name="sbt_text_color" class="color-field" value="<?php echo esc_attr(get_option('sbt_text_color', '#ffffff')); ?>" />
                                    <div class="color-preview" data-target="sbt_text_color" style="background-color: <?php echo esc_attr(get_option('sbt_text_color', '#ffffff')); ?>"></div>
                                </div>
                            </div>
                            <p class="description">Enter hex colors (e.g., #333333) or click the color squares to choose</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Button Shape & Style</th>
                        <td>
                            <div class="shape-controls">
                                <label>Shape:</label><br>
                                <select name="sbt_button_shape" id="button-shape-select">
                                    <option value="circle" <?php selected(get_option('sbt_button_shape', 'circle'), 'circle'); ?>>Circle</option>
                                    <option value="square" <?php selected(get_option('sbt_button_shape'), 'square'); ?>>Square</option>
                                    <option value="custom" <?php selected(get_option('sbt_button_shape'), 'custom'); ?>>Custom (Rounded Corners)</option>
                                </select>
                                <br><br>
                                
                                <div id="border-radius-control" style="display: <?php echo get_option('sbt_button_shape', 'circle') === 'custom' ? 'block' : 'none'; ?>;">
                                    <label>Corner Roundness (%):</label><br>
                                    <input type="range" name="sbt_border_radius" id="border-radius-slider" min="0" max="50" value="<?php echo esc_attr(get_option('sbt_border_radius', '10')); ?>" />
                                    <span id="radius-value"><?php echo esc_attr(get_option('sbt_border_radius', '10')); ?>%</span>
                                    <p class="description">0% = Square corners, 50% = Fully rounded</p>
                                    <br>
                                </div>
                                
                                <label>Button Size (pixels):</label><br>
                                <input type="range" name="sbt_button_size" id="button-size-slider" min="30" max="80" value="<?php echo esc_attr(get_option('sbt_button_size', '50')); ?>" />
                                <span id="size-value"><?php echo esc_attr(get_option('sbt_button_size', '50')); ?>px</span>
                                <p class="description">Size of the button (30px - 80px)</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Shadow Effects</th>
                        <td>
                            <div class="shadow-controls">
                                <label>
                                    <input type="checkbox" name="sbt_enable_shadow" value="1" <?php checked(get_option('sbt_enable_shadow', '1'), '1'); ?> id="enable-shadow-checkbox" />
                                    Enable Drop Shadow
                                </label>
                                <br><br>
                                
                                <div id="shadow-options" style="display: <?php echo get_option('sbt_enable_shadow', '1') === '1' ? 'block' : 'none'; ?>;">
                                    <label>Shadow Color:</label><br>
                                    <div class="color-input-wrapper">
                                        <input type="text" name="sbt_shadow_color" class="color-field" value="<?php echo esc_attr(get_option('sbt_shadow_color', '#000000')); ?>" />
                                        <div class="color-preview" data-target="sbt_shadow_color" style="background-color: <?php echo esc_attr(get_option('sbt_shadow_color', '#000000')); ?>"></div>
                                    </div>
                                    <br><br>
                                    
                                    <label>Shadow Intensity:</label><br>
                                    <input type="range" name="sbt_shadow_intensity" id="shadow-intensity-slider" min="10" max="80" value="<?php echo esc_attr(get_option('sbt_shadow_intensity', '30')); ?>" />
                                    <span id="intensity-value"><?php echo esc_attr(get_option('sbt_shadow_intensity', '30')); ?>%</span>
                                    <p class="description">Controls shadow opacity and depth</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Border Options</th>
                        <td>
                            <div class="border-controls">
                                <label>
                                    <input type="checkbox" name="sbt_enable_border" value="1" <?php checked(get_option('sbt_enable_border', '0'), '1'); ?> id="enable-border-checkbox" />
                                    Enable Border
                                </label>
                                <br><br>
                                
                                <div id="border-options" style="display: <?php echo get_option('sbt_enable_border', '0') === '1' ? 'block' : 'none'; ?>;">
                                    <label>Border Color:</label><br>
                                    <div class="color-input-wrapper">
                                        <input type="text" name="sbt_border_color" class="color-field" value="<?php echo esc_attr(get_option('sbt_border_color', '#ffffff')); ?>" />
                                        <div class="color-preview" data-target="sbt_border_color" style="background-color: <?php echo esc_attr(get_option('sbt_border_color', '#ffffff')); ?>"></div>
                                    </div>
                                    <br><br>
                                    
                                    <label>Border Width:</label><br>
                                    <input type="range" name="sbt_border_width" id="border-width-slider" min="1" max="8" value="<?php echo esc_attr(get_option('sbt_border_width', '2')); ?>" />
                                    <span id="border-width-value"><?php echo esc_attr(get_option('sbt_border_width', '2')); ?>px</span>
                                    <p class="description">Border thickness (1px - 8px)</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Show Button After Scrolling (pixels)</th>
                        <td>
                            <input type="number" name="sbt_scroll_offset" value="<?php echo esc_attr(get_option('sbt_scroll_offset', '300')); ?>" min="100" max="1000" />
                            <p class="description">How far down the page to scroll before showing the button (default: 300px)</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <!-- Simple Color Picker Modal -->
            <div id="simple-color-picker" style="display: none;">
                <div class="color-picker-overlay">
                    <div class="color-picker-modal">
                        <h3>Choose Color</h3>
                        <div class="color-grid">
                            <!-- Common colors -->
                            <div class="color-swatch" data-color="#000000" style="background: #000000;" title="Black"></div>
                            <div class="color-swatch" data-color="#333333" style="background: #333333;" title="Dark Gray"></div>
                            <div class="color-swatch" data-color="#666666" style="background: #666666;" title="Gray"></div>
                            <div class="color-swatch" data-color="#999999" style="background: #999999;" title="Light Gray"></div>
                            <div class="color-swatch" data-color="#ffffff" style="background: #ffffff; border: 1px solid #ccc;" title="White"></div>
                            
                            <div class="color-swatch" data-color="#ff0000" style="background: #ff0000;" title="Red"></div>
                            <div class="color-swatch" data-color="#00ff00" style="background: #00ff00;" title="Green"></div>
                            <div class="color-swatch" data-color="#0000ff" style="background: #0000ff;" title="Blue"></div>
                            <div class="color-swatch" data-color="#ffff00" style="background: #ffff00;" title="Yellow"></div>
                            <div class="color-swatch" data-color="#ff00ff" style="background: #ff00ff;" title="Magenta"></div>
                            
                            <div class="color-swatch" data-color="#800000" style="background: #800000;" title="Dark Red"></div>
                            <div class="color-swatch" data-color="#008000" style="background: #008000;" title="Dark Green"></div>
                            <div class="color-swatch" data-color="#000080" style="background: #000080;" title="Dark Blue"></div>
                            <div class="color-swatch" data-color="#808000" style="background: #808000;" title="Olive"></div>
                            <div class="color-swatch" data-color="#800080" style="background: #800080;" title="Purple"></div>
                            
                            <div class="color-swatch" data-color="#ffa500" style="background: #ffa500;" title="Orange"></div>
                            <div class="color-swatch" data-color="#ffc0cb" style="background: #ffc0cb;" title="Pink"></div>
                            <div class="color-swatch" data-color="#00ffff" style="background: #00ffff;" title="Cyan"></div>
                            <div class="color-swatch" data-color="#a52a2a" style="background: #a52a2a;" title="Brown"></div>
                            <div class="color-swatch" data-color="#dda0dd" style="background: #dda0dd;" title="Plum"></div>
                        </div>
                        <div class="color-input-section">
                            <label>Or enter hex code:</label>
                            <input type="text" id="hex-input" placeholder="#333333" maxlength="7">
                        </div>
                        <div class="color-picker-buttons">
                            <button type="button" class="button button-primary" id="apply-color">Apply</button>
                            <button type="button" class="button" id="cancel-color">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

// Initialize the plugin
new SimpleBackToTop();

/**
 * Plugin activation hook
 */
register_activation_hook(__FILE__, 'simple_back_to_top_activate');

function simple_back_to_top_activate() {
    // Set default options
    add_option('sbt_button_text', '↑');
    add_option('sbt_button_position', 'bottom-right');
    add_option('sbt_scroll_offset', '300');
    add_option('sbt_button_color', '#333333');
    add_option('sbt_hover_color', '#555555');
    add_option('sbt_text_color', '#ffffff');
    add_option('sbt_button_shape', 'circle');
    add_option('sbt_button_size', '50');
    add_option('sbt_border_radius', '10');
    add_option('sbt_enable_shadow', '1');
    add_option('sbt_shadow_color', '#000000');
    add_option('sbt_shadow_intensity', '30');
    add_option('sbt_enable_border', '0');
    add_option('sbt_border_color', '#ffffff');
    add_option('sbt_border_width', '2');
}

/**
 * Plugin deactivation hook
 */
register_deactivation_hook(__FILE__, 'simple_back_to_top_deactivate');

function simple_back_to_top_deactivate() {
    // Plugin deactivated - settings remain for reactivation
}

/**
 * Plugin uninstall hook - only runs when plugin is deleted
 */
register_uninstall_hook(__FILE__, 'simple_back_to_top_uninstall');

function simple_back_to_top_uninstall() {
    // Clean up options when plugin is completely removed
    delete_option('sbt_button_text');
    delete_option('sbt_button_position');
    delete_option('sbt_scroll_offset');
    delete_option('sbt_button_color');
    delete_option('sbt_hover_color');
    delete_option('sbt_text_color');
    delete_option('sbt_button_shape');
    delete_option('sbt_button_size');
    delete_option('sbt_border_radius');
    delete_option('sbt_enable_shadow');
    delete_option('sbt_shadow_color');
    delete_option('sbt_shadow_intensity');
    delete_option('sbt_enable_border');
    delete_option('sbt_border_color');
    delete_option('sbt_border_width');
}
?>