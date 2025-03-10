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

        // Add bulk upload page
        add_action('admin_menu', array($this, 'add_bulk_upload_page'));

        // Handle bulk upload
        add_action('wp_ajax_tagg_create_logos', array($this, 'handle_bulk_logo_creation'));
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

    /**
     * Add bulk upload page
     */
    public function add_bulk_upload_page() {
        add_submenu_page(
            'edit.php?post_type=tagg_logo',
            __('Bulk Add Logos', 'tagg'),
            __('Bulk Add', 'tagg'),
            'upload_files',
            'tagg-bulk-add',
            array($this, 'render_bulk_add_page')
        );
    }

    /**
     * Render bulk add page
     */
    public function render_bulk_add_page() {
        // Enqueue media scripts
        wp_enqueue_media();
        
        // Add nonce for security
        wp_nonce_field('tagg_bulk_add', 'tagg_bulk_nonce');
        ?>
        <div class="wrap">
            <h1><?php _e('Bulk Add Logos', 'tagg'); ?></h1>
            
            <div class="tagg-bulk-add-container">
                <p class="description">
                    <?php _e('Select multiple images from the Media Library to create logo entries.', 'tagg'); ?>
                </p>

                <div class="tagg-bulk-settings">
                    <?php
                    // Get all logo categories
                    $categories = get_terms(array(
                        'taxonomy' => 'tagg_logo_category',
                        'hide_empty' => false,
                    ));
                    
                    if (!empty($categories) && !is_wp_error($categories)) {
                        ?>
                        <p>
                            <label for="tagg-bulk-category"><?php _e('Assign to Category:', 'tagg'); ?></label>
                            <select id="tagg-bulk-category" name="category">
                                <option value=""><?php _e('None', 'tagg'); ?></option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?php echo esc_attr($category->slug); ?>">
                                        <?php echo esc_html($category->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                        <?php
                    }
                    ?>

                    <p>
                        <button type="button" class="button button-primary" id="tagg-select-images">
                            <?php _e('Select Images from Media Library', 'tagg'); ?>
                        </button>
                    </p>

                    <div id="tagg-selected-images" class="tagg-selected-images"></div>

                    <p>
                        <button type="button" class="button button-primary hidden" id="tagg-create-logos">
                            <?php _e('Create Logo Entries', 'tagg'); ?>
                        </button>
                    </p>

                    <div id="tagg-creation-results" class="tagg-creation-results hidden">
                        <h3><?php _e('Results', 'tagg'); ?></h3>
                        <div class="tagg-results-content"></div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var mediaFrame;
            var selectedImages = [];

            $('#tagg-select-images').on('click', function(e) {
                e.preventDefault();

                if (mediaFrame) {
                    mediaFrame.open();
                    return;
                }

                mediaFrame = wp.media({
                    title: '<?php _e('Select Logo Images', 'tagg'); ?>',
                    button: {
                        text: '<?php _e('Use Selected Images', 'tagg'); ?>'
                    },
                    multiple: true,
                    library: {
                        type: 'image'
                    }
                });

                mediaFrame.on('select', function() {
                    var selection = mediaFrame.state().get('selection');
                    selectedImages = [];
                    
                    $('#tagg-selected-images').empty();
                    
                    selection.each(function(attachment) {
                        selectedImages.push(attachment.id);
                        
                        var img = $('<div class="tagg-selected-image">' +
                            '<img src="' + attachment.attributes.url + '" alt="">' +
                            '<span class="title">' + attachment.attributes.title + '</span>' +
                            '</div>');
                            
                        $('#tagg-selected-images').append(img);
                    });

                    if (selectedImages.length > 0) {
                        $('#tagg-create-logos').removeClass('hidden');
                    } else {
                        $('#tagg-create-logos').addClass('hidden');
                    }
                });

                mediaFrame.open();
            });

            $('#tagg-create-logos').on('click', function() {
                var $button = $(this);
                var $results = $('#tagg-creation-results');
                var $content = $results.find('.tagg-results-content');

                $button.prop('disabled', true);
                $results.removeClass('hidden');
                $content.empty().append('<p><?php _e('Creating logo entries...', 'tagg'); ?></p>');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'tagg_create_logos',
                        nonce: $('#tagg_bulk_nonce').val(),
                        images: selectedImages,
                        category: $('#tagg-bulk-category').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            $content.html('<p class="success">' + 
                                response.data.message + '</p>' +
                                '<p><a href="edit.php?post_type=tagg_logo" class="button">' +
                                '<?php _e('View All Logos', 'tagg'); ?></a></p>'
                            );
                            
                            // Clear selection
                            selectedImages = [];
                            $('#tagg-selected-images').empty();
                            $('#tagg-create-logos').addClass('hidden');
                        } else {
                            $content.html('<p class="error">' + response.data + '</p>');
                        }
                    },
                    error: function() {
                        $content.html('<p class="error"><?php _e('An error occurred while creating the logos.', 'tagg'); ?></p>');
                    },
                    complete: function() {
                        $button.prop('disabled', false);
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Handle bulk logo creation
     */
    public function handle_bulk_logo_creation() {
        // Check nonce and permissions
        if (!check_ajax_referer('tagg_bulk_add', 'nonce', false) || !current_user_can('upload_files')) {
            wp_send_json_error(__('Unauthorized access.', 'tagg'));
        }

        $images = isset($_POST['images']) ? array_map('intval', $_POST['images']) : array();
        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';

        if (empty($images)) {
            wp_send_json_error(__('No images selected.', 'tagg'));
        }

        $created = 0;
        foreach ($images as $image_id) {
            $attachment = get_post($image_id);
            if (!$attachment) continue;

            // Create logo post
            $logo_data = array(
                'post_title' => $attachment->post_title,
                'post_type' => 'tagg_logo',
                'post_status' => 'publish'
            );

            $logo_id = wp_insert_post($logo_data);

            if (!is_wp_error($logo_id)) {
                // Set featured image
                set_post_thumbnail($logo_id, $image_id);

                // Assign category if specified
                if (!empty($category)) {
                    wp_set_object_terms($logo_id, $category, 'tagg_logo_category');
                }

                $created++;
            }
        }

        wp_send_json_success(array(
            'message' => sprintf(
                _n(
                    '%d logo entry has been created.',
                    '%d logo entries have been created.',
                    $created,
                    'tagg'
                ),
                $created
            )
        ));
    }
} 