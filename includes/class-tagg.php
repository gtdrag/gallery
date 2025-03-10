<?php
/**
 * The main TAGG class
 * 
 * @since 1.0.0
 */
class TAGG {
    /**
     * Plugin instance
     *
     * @var TAGG
     */
    private static $instance = null;

    /**
     * Initialize the plugin
     */
    public function init() {
        // Register custom post type
        add_action('init', array($this, 'register_logo_post_type'));
        
        // Register shortcode
        add_shortcode('tagg_gallery', array($this, 'gallery_shortcode'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Add meta boxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        
        // Save meta box data
        add_action('save_post_tagg_logo', array($this, 'save_meta_box_data'));
    }

    /**
     * Register the custom post type for logos
     */
    public function register_logo_post_type() {
        $labels = array(
            'name'               => _x('Logos', 'post type general name', 'tagg'),
            'singular_name'      => _x('Logo', 'post type singular name', 'tagg'),
            'menu_name'          => _x('TAGG Logos', 'admin menu', 'tagg'),
            'name_admin_bar'     => _x('Logo', 'add new on admin bar', 'tagg'),
            'add_new'            => _x('Add New', 'logo', 'tagg'),
            'add_new_item'       => __('Add New Logo', 'tagg'),
            'new_item'           => __('New Logo', 'tagg'),
            'edit_item'          => __('Edit Logo', 'tagg'),
            'view_item'          => __('View Logo', 'tagg'),
            'all_items'          => __('All Logos', 'tagg'),
            'search_items'       => __('Search Logos', 'tagg'),
            'not_found'          => __('No logos found.', 'tagg'),
            'not_found_in_trash' => __('No logos found in Trash.', 'tagg')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'tagg-logo'),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'thumbnail'),
            'menu_icon'          => 'dashicons-format-gallery'
        );

        register_post_type('tagg_logo', $args);
        
        // Register category taxonomy for logos
        $tax_labels = array(
            'name'              => _x('Logo Categories', 'taxonomy general name', 'tagg'),
            'singular_name'     => _x('Logo Category', 'taxonomy singular name', 'tagg'),
            'search_items'      => __('Search Logo Categories', 'tagg'),
            'all_items'         => __('All Logo Categories', 'tagg'),
            'parent_item'       => __('Parent Logo Category', 'tagg'),
            'parent_item_colon' => __('Parent Logo Category:', 'tagg'),
            'edit_item'         => __('Edit Logo Category', 'tagg'),
            'update_item'       => __('Update Logo Category', 'tagg'),
            'add_new_item'      => __('Add New Logo Category', 'tagg'),
            'new_item_name'     => __('New Logo Category Name', 'tagg'),
            'menu_name'         => __('Categories', 'tagg'),
        );

        $tax_args = array(
            'hierarchical'      => true,
            'labels'            => $tax_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'logo-category'),
        );

        register_taxonomy('tagg_logo_category', array('tagg_logo'), $tax_args);
    }

    /**
     * Add meta boxes to the logo post type
     */
    public function add_meta_boxes() {
        add_meta_box(
            'tagg_logo_details',
            __('Logo Details', 'tagg'),
            array($this, 'render_logo_details_meta_box'),
            'tagg_logo',
            'normal',
            'high'
        );
    }

    /**
     * Render the logo details meta box
     */
    public function render_logo_details_meta_box($post) {
        // Add a nonce field
        wp_nonce_field('tagg_logo_meta_box', 'tagg_logo_meta_box_nonce');
        
        // Get current values
        $website_url = get_post_meta($post->ID, '_tagg_logo_website_url', true);
        
        echo '<p>';
        echo '<label for="tagg_logo_website_url">' . __('Website URL', 'tagg') . '</label><br>';
        echo '<input type="url" id="tagg_logo_website_url" name="tagg_logo_website_url" value="' . esc_attr($website_url) . '" class="widefat">';
        echo '</p>';
        
        echo '<p>' . __('Don\'t forget to set a featured image for this logo!', 'tagg') . '</p>';
    }

    /**
     * Save meta box data
     */
    public function save_meta_box_data($post_id) {
        // Check if nonce is set
        if (!isset($_POST['tagg_logo_meta_box_nonce'])) {
            return;
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['tagg_logo_meta_box_nonce'], 'tagg_logo_meta_box')) {
            return;
        }
        
        // If this is an autosave, don't do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save data
        if (isset($_POST['tagg_logo_website_url'])) {
            update_post_meta($post_id, '_tagg_logo_website_url', sanitize_url($_POST['tagg_logo_website_url']));
        }
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=tagg_logo',
            __('TAGG Settings', 'tagg'),
            __('Settings', 'tagg'),
            'manage_options',
            'tagg-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Render the settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('TAGG Gallery Settings', 'tagg'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('tagg_options');
                do_settings_sections('tagg-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style('tagg-style', TAGG_PLUGIN_URL . 'css/tagg-style.css', array(), TAGG_VERSION);
        wp_enqueue_script('tagg-script', TAGG_PLUGIN_URL . 'js/tagg-script.js', array('jquery'), TAGG_VERSION, true);
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        // Only load on logo post type pages
        $screen = get_current_screen();
        if ($screen->post_type !== 'tagg_logo') {
            return;
        }
        
        wp_enqueue_style('tagg-admin-style', TAGG_PLUGIN_URL . 'css/tagg-admin.css', array(), TAGG_VERSION);
        wp_enqueue_script('tagg-admin-script', TAGG_PLUGIN_URL . 'js/tagg-admin.js', array('jquery'), TAGG_VERSION, true);
        
        // Add inline styles for the admin preview
        $admin_styles = "
            .post-type-tagg_logo #postimagediv .inside img {
                max-width: 200px;
                max-height: 150px;
                width: auto;
                height: auto;
                object-fit: contain;
            }
            .post-type-tagg_logo .wp-list-table .column-thumbnail img {
                max-width: 100px;
                max-height: 75px;
                width: auto;
                height: auto;
                object-fit: contain;
            }
        ";
        wp_add_inline_style('tagg-admin-style', $admin_styles);
    }

    /**
     * Gallery shortcode handler
     */
    public function gallery_shortcode($atts) {
        $atts = shortcode_atts(array(
            'category' => '',
            'limit' => -1,
            'columns' => 3,
            'link' => 'yes',
            'width' => '',
            'height' => '',
            'horizontal_gap' => '20',
            'vertical_gap' => '20'
        ), $atts, 'tagg_gallery');
        
        $args = array(
            'post_type' => 'tagg_logo',
            'posts_per_page' => $atts['limit'],
            'orderby' => 'title',
            'order' => 'ASC',
        );
        
        // Filter by category if specified
        if (!empty($atts['category'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'tagg_logo_category',
                    'field'    => 'slug',
                    'terms'    => $atts['category'],
                ),
            );
        }
        
        $logos_query = new WP_Query($args);
        
        ob_start();
        
        if ($logos_query->have_posts()) {
            // Generate style tag for gallery layout
            echo '<style>';
            echo '.tagg-gallery {';
            echo '    display: grid;';
            echo '    grid-template-columns: repeat(' . absint($atts['columns']) . ', minmax(0, 1fr));';
            echo '    column-gap: ' . absint($atts['horizontal_gap']) . 'px;';
            echo '    row-gap: ' . absint($atts['vertical_gap']) . 'px;';
            echo '    max-width: 100%;';
            echo '    width: 100%;';
            echo '    box-sizing: border-box;';
            echo '}';
            
            echo '@media screen and (max-width: 768px) {';
            echo '    .tagg-gallery {';
            echo '        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));';
            echo '    }';
            echo '}';
            
            echo '.tagg-gallery .tagg-logo {';
            echo '    display: flex;';
            echo '    justify-content: center;';
            echo '    align-items: center;';
            if (!empty($atts['height'])) {
                echo '    height: ' . esc_attr($atts['height']) . 'px;';
            }
            if (!empty($atts['width'])) {
                echo '    width: ' . esc_attr($atts['width']) . 'px;';
                echo '    max-width: 100%;';
            }
            echo '    margin: 0 auto;';
            echo '    box-sizing: border-box;';
            echo '}';
            
            echo '.tagg-gallery .tagg-logo a {';
            echo '    display: flex;';
            echo '    justify-content: center;';
            echo '    align-items: center;';
            echo '    width: 100%;';
            echo '    height: 100%;';
            echo '}';
            
            echo '.tagg-gallery .tagg-logo img {';
            echo '    max-width: 100%;';
            echo '    max-height: 100%;';
            echo '    width: auto;';
            echo '    height: auto;';
            echo '    object-fit: contain;';
            echo '}';
            echo '</style>';
            ?>
            <div class="tagg-gallery">
                <?php while ($logos_query->have_posts()) : $logos_query->the_post(); ?>
                    <div class="tagg-logo">
                        <?php 
                        $website_url = get_post_meta(get_the_ID(), '_tagg_logo_website_url', true);
                        
                        if ($atts['link'] === 'yes' && !empty($website_url)) {
                            echo '<a href="' . esc_url($website_url) . '" target="_blank" rel="noopener">';
                        }
                        
                        if (has_post_thumbnail()) {
                            echo get_the_post_thumbnail(get_the_ID(), 'full', array('class' => 'tagg-logo-img'));
                        }
                        
                        if ($atts['link'] === 'yes' && !empty($website_url)) {
                            echo '</a>';
                        }
                        ?>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php
        } else {
            echo '<p>' . __('No logos found.', 'tagg') . '</p>';
        }
        
        wp_reset_postdata();
        
        return ob_get_clean();
    }

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
} 