<?php
/**
 * TAGG Admin Settings Class
 * 
 * @since 1.0.0
 */
class TAGG_Admin {
    /**
     * Initialize the admin hooks
     */
    public function init() {
        // Register settings
        add_action('admin_init', array($this, 'register_settings'));
        
        // Add help tab
        add_action('load-post-new.php', array($this, 'add_help_tab'));
        add_action('load-post.php', array($this, 'add_help_tab'));
    }

    /**
     * Register settings fields
     */
    public function register_settings() {
        // Register settings
        register_setting('tagg_options', 'tagg_options');
        
        // Add settings section
        add_settings_section(
            'tagg_general_settings',
            __('General Settings', 'tagg'),
            array($this, 'render_general_settings_section'),
            'tagg-settings'
        );
        
        // Add settings fields
        add_settings_field(
            'tagg_default_columns',
            __('Default Columns', 'tagg'),
            array($this, 'render_default_columns_field'),
            'tagg-settings',
            'tagg_general_settings'
        );
        
        add_settings_field(
            'tagg_default_link',
            __('Default Link Behavior', 'tagg'),
            array($this, 'render_default_link_field'),
            'tagg-settings',
            'tagg_general_settings'
        );
        
        // Add shortcode help section
        add_settings_section(
            'tagg_shortcode_help',
            __('Shortcode Documentation', 'tagg'),
            array($this, 'render_shortcode_help_section'),
            'tagg-settings'
        );
    }

    /**
     * General settings section description
     */
    public function render_general_settings_section() {
        echo '<p>' . __('Configure the default settings for your logo galleries.', 'tagg') . '</p>';
    }

    /**
     * Render the default columns field
     */
    public function render_default_columns_field() {
        $options = get_option('tagg_options', array());
        $default_columns = isset($options['default_columns']) ? $options['default_columns'] : 3;
        
        echo '<select name="tagg_options[default_columns]" id="tagg_default_columns">';
        for ($i = 1; $i <= 6; $i++) {
            echo '<option value="' . $i . '" ' . selected($default_columns, $i, false) . '>' . $i . '</option>';
        }
        echo '</select>';
        echo '<p class="tagg-field-description">' . __('Default number of columns for logo galleries.', 'tagg') . '</p>';
    }

    /**
     * Render the default link field
     */
    public function render_default_link_field() {
        $options = get_option('tagg_options', array());
        $default_link = isset($options['default_link']) ? $options['default_link'] : 'yes';
        
        echo '<select name="tagg_options[default_link]" id="tagg_default_link">';
        echo '<option value="yes" ' . selected($default_link, 'yes', false) . '>' . __('Link to website URL', 'tagg') . '</option>';
        echo '<option value="no" ' . selected($default_link, 'no', false) . '>' . __('No link', 'tagg') . '</option>';
        echo '</select>';
        echo '<p class="tagg-field-description">' . __('Default link behavior for logos in galleries.', 'tagg') . '</p>';
    }

    /**
     * Render shortcode help section
     */
    public function render_shortcode_help_section() {
        ?>
        <div class="tagg-help">
            <h3><?php _e('Shortcode Usage', 'tagg'); ?></h3>
            <p><?php _e('You can use the following shortcode to display your logo gallery anywhere on your site:', 'tagg'); ?></p>
            
            <div class="tagg-shortcode-example">
                [tagg_gallery]
            </div>
            
            <p><?php _e('Additional shortcode attributes:', 'tagg'); ?></p>
            
            <ul>
                <li><code>category</code> - <?php _e('Show logos from a specific category (use category slug)', 'tagg'); ?></li>
                <li><code>columns</code> - <?php _e('Number of columns to display (1-6)', 'tagg'); ?></li>
                <li><code>limit</code> - <?php _e('Maximum number of logos to show', 'tagg'); ?></li>
                <li><code>link</code> - <?php _e('Whether to link logos to their website URLs (yes/no)', 'tagg'); ?></li>
            </ul>
            
            <h4><?php _e('Example with all attributes:', 'tagg'); ?></h4>
            
            <div class="tagg-shortcode-example">
                [tagg_gallery category="partners" columns="4" limit="8" link="yes"]
            </div>
        </div>
        <?php
    }

    /**
     * Add help tab to logo post type
     */
    public function add_help_tab() {
        $screen = get_current_screen();
        
        // Only add help tab on logo post type
        if (!$screen || $screen->post_type !== 'tagg_logo') {
            return;
        }
        
        $screen->add_help_tab(array(
            'id'      => 'tagg_help_tab',
            'title'   => __('TAGG Logo Help', 'tagg'),
            'content' => $this->get_help_tab_content(),
        ));
    }

    /**
     * Get help tab content
     */
    private function get_help_tab_content() {
        ob_start();
        ?>
        <h2><?php _e('Using the TAGG Logo Gallery', 'tagg'); ?></h2>
        
        <h3><?php _e('Adding Logos', 'tagg'); ?></h3>
        <p><?php _e('To add a new logo to your gallery:', 'tagg'); ?></p>
        <ol>
            <li><?php _e('Enter a title for the logo (usually the company name)', 'tagg'); ?></li>
            <li><?php _e('Set a featured image - this is the logo that will be displayed', 'tagg'); ?></li>
            <li><?php _e('Enter the website URL in the "Logo Details" box', 'tagg'); ?></li>
            <li><?php _e('Optionally assign the logo to a category', 'tagg'); ?></li>
            <li><?php _e('Click Publish', 'tagg'); ?></li>
        </ol>
        
        <h3><?php _e('Displaying the Gallery', 'tagg'); ?></h3>
        <p><?php _e('Use the shortcode <code>[tagg_gallery]</code> to display your logo gallery on any page or post.', 'tagg'); ?></p>
        <?php
        return ob_get_clean();
    }
} 