<?php

defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

class seopress_options
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    
    /**
     * Start up
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ), 10 );
        add_action( 'admin_init', array( $this, 'set_default_values' ), 10 );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
    
    public function activate() {
        update_option($this->seopress_options, $this->data);
    }

    public function deactivate() {
        delete_option($this->seopress_options);
    }

    public function set_default_values() {

        //Enable features==========================================================================
        $seopress_toggle_options = get_option('seopress_toggle');

        $seopress_toggle_options['toggle-titles'] = '1';
        $seopress_toggle_options['toggle-xml-sitemap'] = '1';
        $seopress_toggle_options['toggle-social'] = '1';
        $seopress_toggle_options['toggle-google-analytics'] = '1';
        $seopress_toggle_options['toggle-advanced'] = '1';
        if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
            $seopress_toggle_options['toggle-woocommerce'] = '1';
        }
        $seopress_toggle_options['toggle-dublin-core'] = '1';
        $seopress_toggle_options['toggle-local-business'] = '1';
        $seopress_toggle_options['toggle-rich-snippets'] = '1';
        $seopress_toggle_options['toggle-breadcrumbs'] = '1';
        $seopress_toggle_options['toggle-robots'] = '1';
        $seopress_toggle_options['toggle-404'] = '1';
        $seopress_toggle_options['toggle-bot'] = '1';

        add_option('seopress_toggle', $seopress_toggle_options);

        //Titles & metas===========================================================================
        $seopress_titles_options = get_option('seopress_titles_option_name');
        
        //Site Title
        $seopress_titles_options['seopress_titles_home_site_title'] = '%%sitetitle%%';
        
        //Meta Description
        $seopress_titles_options['seopress_titles_home_site_desc'] = '%%tagline%%';
        
        //Post Types
        foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
            $seopress_titles_options['seopress_titles_single_titles'][$seopress_cpt_key]['title'] = '%%post_title%% - %%sitetitle%%';
            $seopress_titles_options['seopress_titles_single_titles'][$seopress_cpt_key]['description'] = '%%post_excerpt%%';
        }
        
        //Taxonomies
        foreach (seopress_get_taxonomies() as $seopress_tax_key => $seopress_tax_value) {
            //Title
            if ($seopress_tax_key =='category') {
                $seopress_titles_options['seopress_titles_tax_titles'][$seopress_tax_key]['title'] = '%%_category_title%% %%current_pagination%% - %%sitetitle%%';  
            } elseif ($seopress_tax_key =='post_tag') {
                $seopress_titles_options['seopress_titles_tax_titles'][$seopress_tax_key]['title'] = '%%tag_title%% %%current_pagination%% - %%sitetitle%%';  
            } else {
                $seopress_titles_options['seopress_titles_tax_titles'][$seopress_tax_key]['title'] = '%%term_title%% %%current_pagination%% - %%sitetitle%%';
            }

            //Desc
            if ($seopress_tax_key =='category') {
                $seopress_titles_options['seopress_titles_tax_titles'][$seopress_tax_key]['description'] = '%%_category_description%%';  
            } elseif ($seopress_tax_key =='post_tag') {
                $seopress_titles_options['seopress_titles_tax_titles'][$seopress_tax_key]['description'] = '%%tag_description%%';  
            } else {
                $seopress_titles_options['seopress_titles_tax_titles'][$seopress_tax_key]['description'] = '%%term_description%%';
            }
        }

        //Archives
        foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
            $seopress_titles_options['seopress_titles_archive_titles'][$seopress_cpt_key]['title'] = '%%cpt_plural%% %%current_pagination%% - %%sitetitle%%';
        }

        //Author
        $seopress_titles_options['seopress_titles_archives_author_title'] = '%%post_author%% - %%sitetitle%%';
        $seopress_titles_options['seopress_titles_archives_author_noindex'] = '1';

        //Date
        $seopress_titles_options['seopress_titles_archives_date_title'] = '%%archive_date%% - %%sitetitle%%';
        $seopress_titles_options['seopress_titles_archives_date_noindex'] = '1';

        //Search
        $seopress_titles_options['seopress_titles_archives_search_title'] = '%%search_keywords%% - %%sitetitle%%';

        //404
        $seopress_titles_options['seopress_titles_archives_404_title'] = __('404 - Page not found','wp-seopress').' - %%sitetitle%%';

        //Link rel prev/next
        $seopress_titles_options['seopress_titles_paged_rel'] = '1';

        add_option('seopress_titles_option_name', $seopress_titles_options);

        //XML Sitemap==============================================================================
        $seopress_xml_sitemap_options = get_option('seopress_xml_sitemap_option_name');

        $seopress_xml_sitemap_options['seopress_xml_sitemap_general_enable'] = '1';
        $seopress_xml_sitemap_options['seopress_xml_sitemap_img_enable'] = '1';
 
        global $wp_post_types;

        $args = array(
            'show_ui' => true,
        );

        $output = 'objects'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'

        $post_types = get_post_types( $args, $output, $operator ); 

        foreach ($post_types as $seopress_cpt_key => $seopress_cpt_value) {
            if ($seopress_cpt_key =='post' || $seopress_cpt_key =='page' || $seopress_cpt_key =='product') {
                $seopress_xml_sitemap_options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include'] = '1';
            }
        }
        
        $args = array(
            'show_ui' => true,
            'public' => true,
          
        ); 
        $output = 'objects'; // or objects
        $operator = 'and'; // 'and' or 'or'
        $taxonomies = get_taxonomies( $args, $output, $operator );  

        foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) {
            if ($seopress_tax_key =='category' || $seopress_tax_key =='post_tag') {
                $seopress_xml_sitemap_options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include'] = '1';
            }
        }

        add_option('seopress_xml_sitemap_option_name', $seopress_xml_sitemap_options);

        //Social===================================================================================
        $seopress_social_options = get_option('seopress_social_option_name');

        $seopress_social_options['seopress_social_facebook_og'] = '1';
        $seopress_social_options['seopress_social_twitter_card'] = '1';

        add_option('seopress_social_option_name', $seopress_social_options);

        //Advanced=================================================================================
        $seopress_advanced_option_name = get_option('seopress_advanced_option_name');

        $seopress_advanced_option_name['seopress_advanced_advanced_attachments'] = '1';
        $seopress_advanced_option_name['seopress_advanced_advanced_tax_desc_editor'] = '1';
        $seopress_advanced_option_name['seopress_advanced_appearance_title_col'] = '1';
        $seopress_advanced_option_name['seopress_advanced_appearance_meta_desc_col'] = '1';

        add_option('seopress_advanced_option_name', $seopress_advanced_option_name);
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        if (has_filter('seopress_seo_admin_menu')) {
            $sp_seo_admin_menu['icon'] = apply_filters('seopress_seo_admin_menu', $sp_seo_admin_menu['icon']);
        } else {
            $sp_seo_admin_menu['icon'] = 'dashicons-admin-seopress';
        }

        add_menu_page('SEOPress Option Page', __('SEO','wp-seopress'), 'manage_options', 'seopress-option', array( $this, 'create_admin_page' ), $sp_seo_admin_menu['icon'], 90);
        add_submenu_page('seopress-option', __('Dashboard','wp-seopress'), __('Dashboard','wp-seopress'), 'manage_options', 'seopress-option', array( $this, 'create_admin_page' ));
        $seopress_titles_help_tab = add_submenu_page('seopress-option', __('Titles & Metas','wp-seopress'), __('Titles & Metas','wp-seopress'), 'manage_options', 'seopress-titles', array( $this, 'seopress_titles_page' ));
        add_submenu_page('seopress-option', __('XML / Image / HTML Sitemap','wp-seopress'), __('XML / HTML Sitemap','wp-seopress'), 'manage_options', 'seopress-xml-sitemap', array( $this, 'seopress_xml_sitemap_page' ));
        add_submenu_page('seopress-option', __('Social Networks','wp-seopress'), __('Social Networks','wp-seopress'), 'manage_options', 'seopress-social', array( $this, 'seopress_social_page' ));
        $seopress_google_analytics_help_tab = add_submenu_page('seopress-option', __('Google Analytics','wp-seopress'), __('Google Analytics','wp-seopress'), 'manage_options', 'seopress-google-analytics', array( $this, 'seopress_google_analytics_page' ));
        add_submenu_page('seopress-option', __('Advanced','wp-seopress'), __('Advanced','wp-seopress'), 'manage_options', 'seopress-advanced', array( $this, 'seopress_advanced_page' ));
        add_submenu_page('seopress-option', __('Tools','wp-seopress'), __('Tools','wp-seopress'), 'manage_options', 'seopress-import-export', array( $this,'seopress_import_export_page'));

        function seopress_titles_help_tab() {
            $screen = get_current_screen();

            $seopress_titles_help_tab_content = '
                <ul>
                    <li><span>'.__('%%sitetitle%%','wp-seopress').'</span>'.__('Site Title','wp-seopress').'</li>
                    <li><span>'.__('%%tagline%%','wp-seopress').'</span>'.__('Tagline','wp-seopress').'</li>
                    <li><span>'.__('%%post_title%%','wp-seopress').'</span>'.__('Post Title (post, page, custom post type)','wp-seopress').'</li>
                    <li><span>'.__('%%post_excerpt%%','wp-seopress').'</span>'.__('Post excerpt','wp-seopress').'</li>
                    <li><span>'.__('%%post_date%%','wp-seopress').'</span>'.__('Post date','wp-seopress').'</li>
                    <li><span>'.__('%%post_author%%','wp-seopress').'</span>'.__('Post author','wp-seopress').'</li>
                    <li><span>'.__('%%post_category%%','wp-seopress').'</span>'.__('Post category','wp-seopress').'</li>
                    <li><span>'.__('%%post_tag%%','wp-seopress').'</span>'.__('Post tag','wp-seopress').'</li>
                    <li><span>'.__('%%_category_title%%','wp-seopress').'</span>'.__('Category title','wp-seopress').'</li>
                    <li><span>'.__('%%_category_description%%','wp-seopress').'</span>'.__('Category description','wp-seopress').'</li>
                    <li><span>'.__('%%tag_title%%','wp-seopress').'</span>'.__('Tag title','wp-seopress').'</li>
                    <li><span>'.__('%%tag_description%%','wp-seopress').'</span>'.__('Tag description','wp-seopress').'</li>
                    <li><span>'.__('%%term_title%%','wp-seopress').'</span>'.__('Term title','wp-seopress').'</li>
                    <li><span>'.__('%%term_description%%','wp-seopress').'</span>'.__('Term description','wp-seopress').'</li>
                    <li><span>'.__('%%search_keywords%%','wp-seopress').'</span>'.__('Search keywords','wp-seopress').'</li>
                    <li><span>'.__('%%current_pagination%%','wp-seopress').'</span>'.__('Current number page','wp-seopress').'</li>
                    <li><span>'.__('%%cpt_plural%%','wp-seopress').'</span>'.__('Plural Post Type Archive name','wp-seopress').'</li>
                    <li><span>'.__('%%date_archive%%','wp-seopress').'</span>'.__('Date Archive','wp-seopress').'</li>
                    <li><span>'.__('%%_cf_your_custom_field_name%%','wp-seopress').'</span>'.__('Custom fields from post, page or post type','wp-seopress').'</li>
                    <li><span>'.__('%%wc_single_cat%%','wp-seopress').'</span>'.__('Single product category','wp-seopress').'</li>
                    <li><span>'.__('%%wc_single_tag%%','wp-seopress').'</span>'.__('Single product tag','wp-seopress').'</li>
                    <li><span>'.__('%%wc_single_short_desc%%','wp-seopress').'</span>'.__('Single product short description','wp-seopress').'</li>
                </ul>
            '.wp_oembed_get('https://www.youtube.com/watch?v=HEa8m9u4mOk', array('width'=>530));

            $screen->add_help_tab( array(
                'id'    => 'seopress_titles_help_tab',
                'title' => __('Templates variables'),
                'content'   => $seopress_titles_help_tab_content,
            ));

            if (function_exists('seopress_get_locale')) {
                if (seopress_get_locale() =='fr') {
                    $screen->set_help_sidebar(
                        '<ul>
                            <li><a href="https://www.seopress.org/fr/support/guides/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">'.__("Browse our guides","wp-seopress").'</a></li>
                            <li><a href="https://www.seopress.org/fr/support/faq/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">'.__("Read our FAQ","wp-seopress").'</a></li>
                            <li><a href="https://www.seopress.org/fr/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">'.__("Check our website","wp-seopress").'</a></li>
                        </ul>'
                    );
                } else {
                    $screen->set_help_sidebar(
                        '<ul>
                            <li><a href="https://www.seopress.org/support/guides/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">'.__("Browse our guides","wp-seopress").'</a></li>
                            <li><a href="https://www.seopress.org/support/faq/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">'.__("Read our FAQ","wp-seopress").'</a></li>
                            <li><a href="https://www.seopress.org/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">'.__("Check our website","wp-seopress").'</a></li>
                        </ul>'
                    );
                }
            }
        }
        add_action('load-'.$seopress_titles_help_tab, 'seopress_titles_help_tab');

        function seopress_google_analytics_help_tab() {
            $screen = get_current_screen();

            $seopress_google_analytics_help_tab_content = '
                <p>'.__('Watch our video to learn how to connect your WordPress site with Google Analytics and get statistics right in your dashboard (PRO only).','wp-seopress').'</p>
            '.wp_oembed_get('https://www.youtube.com/watch?v=NKvjb9Z571c', array('width'=>530));

            $screen->add_help_tab( array(
                'id'    => 'seopress_google_analytics_help_tab',
                'title' => __('How-to'),
                'content'   => $seopress_google_analytics_help_tab_content,
            ));

            if (function_exists('seopress_get_locale')) {
                if (seopress_get_locale() =='fr') {
                    $screen->set_help_sidebar(
                        '<ul>
                            <li><a href="https://www.seopress.org/fr/support/guides/connectez-site-wordpress-a-google-analytics/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">'.__("Read our guide","wp-seopress").'</a></li>
                        </ul>'
                    );
                } else {
                    $screen->set_help_sidebar(
                        '<ul>
                            <li><a href="https://www.seopress.org/support/guides/connect-wordpress-site-google-analytics/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">'.__("Read our guide","wp-seopress").'</a></li>
                        </ul>'
                    );
                }
            }
        }
        add_action('load-'.$seopress_google_analytics_help_tab, 'seopress_google_analytics_help_tab');
    }
    

    function seopress_titles_page(){
        $this->options = get_option( 'seopress_titles_option_name' );
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        }
        ?>
        <form method="post" action="<?php echo admin_url('options.php'); ?>" class="seopress-option">
        <?php 
        if (get_option('blog_public') =='0') {
            echo '<div class="error notice is-dismissable">';
            echo '<p>'. __('Discourage search engines from indexing this site is <strong>ON!</strong> None of the following settings will be applied.','wp-seopress');
            echo ' <a href="'.admin_url("options-reading.php").'">'.__('Change this settings','wp-seopress').'</a></p>';
            echo '</div>';
        }
        global $wp_version, $title;
        $current_tab = '';
        $tag = version_compare( $wp_version, '4.4' ) >= 0 ? 'h1' : 'h2';
        echo '<'.$tag.'><span class="dashicons dashicons-editor-table"></span>'.$title;

        if(seopress_get_toggle_titles_option()=='1') { 
            $seopress_get_toggle_titles_option = '"1"';
        } else { 
            $seopress_get_toggle_titles_option = '"0"';
        }
        ?>
        
        <input type="checkbox" name="toggle-titles" id="toggle-titles" class="toggle" data-toggle=<?php echo $seopress_get_toggle_titles_option; ?>>
        <label for="toggle-titles"></label>
        
        <?php
        if(seopress_get_toggle_titles_option()=='1') { 
            echo '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
            echo '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
        } else { 
            echo '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
            echo '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
        }
        
        echo '<div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>';

        echo '</'.$tag.'>';

        settings_fields( 'seopress_titles_option_group' );
        ?>
        
        <div id="seopress-tabs" class="wrap">
        <?php 
            
            $plugin_settings_tabs = array(
                'tab_seopress_titles_home' => __( "Home", "wp-seopress" ), 
                'tab_seopress_titles_single' => __( "Single Post Types", "wp-seopress" ), 
                'tab_seopress_titles_archives' => __( "Archives", "wp-seopress" ), 
                'tab_seopress_titles_tax' => __( "Taxonomies", "wp-seopress" ), 
                'tab_seopress_titles_advanced' => __( "Advanced", "wp-seopress" ),
            );

            echo '<div class="nav-tab-wrapper">';
            foreach ( $plugin_settings_tabs as $tab_key => $tab_caption ) {
                echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=seopress-titles#tab=' . $tab_key . '">' . $tab_caption . '</a>'; 
            }
            echo '</div>';
        ?>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_titles_home') { echo 'active'; } ?>" id="tab_seopress_titles_home"><?php do_settings_sections( 'seopress-settings-admin-titles-home' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_titles_single') { echo 'active'; } ?>" id="tab_seopress_titles_single"><?php do_settings_sections( 'seopress-settings-admin-titles-single' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_titles_archives') { echo 'active'; } ?>" id="tab_seopress_titles_archives"><?php do_settings_sections( 'seopress-settings-admin-titles-archives' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_titles_tax') { echo 'active'; } ?>" id="tab_seopress_titles_tax"><?php do_settings_sections( 'seopress-settings-admin-titles-tax' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_titles_advanced') { echo 'active'; } ?>" id="tab_seopress_titles_advanced"><?php do_settings_sections( 'seopress-settings-admin-titles-advanced' ); ?></div>
        </div>

        <?php submit_button(); ?>
        </form>
        <?php
    }

    function seopress_xml_sitemap_page(){
        $this->options = get_option( 'seopress_xml_sitemap_option_name' );
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        }
        ?>
        <form method="post" action="<?php echo admin_url('options.php'); ?>" class="seopress-option" name="seopress-flush">
        <?php 
        global $wp_version, $title;
        $current_tab = '';
        $tag = version_compare( $wp_version, '4.4' ) >= 0 ? 'h1' : 'h2';
        echo '<'.$tag.'><span class="dashicons dashicons-media-spreadsheet"></span>'.$title;

        if(seopress_get_toggle_xml_sitemap_option()=='1') { 
            $seopress_get_toggle_xml_sitemap_option = '"1"';
        } else { 
            $seopress_get_toggle_xml_sitemap_option = '"0"';
        } ?>

        <input type="checkbox" name="toggle-xml-sitemap" id="toggle-xml-sitemap" class="toggle" data-toggle=<?php echo $seopress_get_toggle_xml_sitemap_option; ?>>
        
        <label for="toggle-xml-sitemap"></label>
    
        <?php if(seopress_get_toggle_xml_sitemap_option()=='1') { 
            echo '<span id="sitemap-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
            echo '<span id="sitemap-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
        } else { 
            echo '<span id="sitemap-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
            echo '<span id="sitemap-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
        }

        echo '<div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>';

        echo '</'.$tag.'>';

        settings_fields( 'seopress_xml_sitemap_option_group' );
        ?>
        
        <div id="seopress-tabs" class="wrap">
         <?php 
            
            $plugin_settings_tabs = array(
                'tab_seopress_xml_sitemap_general' => __( "General", "wp-seopress" ), 
                'tab_seopress_xml_sitemap_post_types' => __( "Post Types", "wp-seopress" ), 
                'tab_seopress_xml_sitemap_taxonomies' => __( "Taxonomies", "wp-seopress" ), 
                'tab_seopress_html_sitemap' => __( "HTML Sitemap", "wp-seopress" ),
            );

            echo '<div class="nav-tab-wrapper">';
            foreach ( $plugin_settings_tabs as $tab_key => $tab_caption ) {
                echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=seopress-xml-sitemap#tab=' . $tab_key . '">' . $tab_caption . '</a>'; 
            }
            echo '</div>';
        ?>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_xml_sitemap_general') { echo 'active'; } ?>" id="tab_seopress_xml_sitemap_general"><?php do_settings_sections( 'seopress-settings-admin-xml-sitemap-general' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_xml_sitemap_post_types') { echo 'active'; } ?>" id="tab_seopress_xml_sitemap_post_types"><?php do_settings_sections( 'seopress-settings-admin-xml-sitemap-post-types' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_xml_sitemap_taxonomies') { echo 'active'; } ?>" id="tab_seopress_xml_sitemap_taxonomies"><?php do_settings_sections( 'seopress-settings-admin-xml-sitemap-taxonomies' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_html_sitemap') { echo 'active'; } ?>" id="tab_seopress_html_sitemap"><?php do_settings_sections( 'seopress-settings-admin-html-sitemap' ); ?></div>
        </div>
        <?php submit_button(); ?>
        </form>
        <?php
    }

    function seopress_social_page(){
        $this->options = get_option( 'seopress_social_option_name' );
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        }
        ?>
        <form method="post" action="<?php echo admin_url('options.php'); ?>" class="seopress-option">
        <?php 
        global $wp_version, $title;
        $current_tab = '';
        $tag = version_compare( $wp_version, '4.4' ) >= 0 ? 'h1' : 'h2';
        echo '<'.$tag.'><span class="dashicons dashicons-share"></span>'.$title;
        
        if(seopress_get_toggle_social_option()=='1') { 
            $seopress_get_toggle_social_option = '"1"';
        } else { 
            $seopress_get_toggle_social_option = '"0"';
        }
        ?>
        
        <input type="checkbox" name="toggle-social" id="toggle-social" class="toggle" data-toggle=<?php echo $seopress_get_toggle_social_option; ?>>
        <label for="toggle-social"></label>
        
        <?php
        if(seopress_get_toggle_social_option()=='1') { 
            echo '<span id="social-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
            echo '<span id="social-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
        } else { 
            echo '<span id="social-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
            echo '<span id="social-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
        }

        echo '<div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>';

        echo '</'.$tag.'>';                            

        settings_fields( 'seopress_social_option_group' );
        ?>
    
         <div id="seopress-tabs" class="wrap">
         <?php 
            
            $plugin_settings_tabs = array(
                'tab_seopress_social_knowledge' => __( "Knowledge Graph", "wp-seopress" ), 
                'tab_seopress_social_accounts' => __( "Your social accounts", "wp-seopress" ), 
                'tab_seopress_social_facebook' => __( "Facebook", "wp-seopress" ), 
                'tab_seopress_social_twitter' => __( "Twitter", "wp-seopress" ), 
            );

            echo '<div class="nav-tab-wrapper">';
            foreach ( $plugin_settings_tabs as $tab_key => $tab_caption ) {
                echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=seopress-social#tab=' . $tab_key . '">' . $tab_caption . '</a>'; 
            }
            echo '</div>';
        ?>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_social_knowledge') { echo 'active'; } ?>" id="tab_seopress_social_knowledge"><?php do_settings_sections( 'seopress-settings-admin-social-knowledge' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_social_accounts') { echo 'active'; } ?>" id="tab_seopress_social_accounts"><?php do_settings_sections( 'seopress-settings-admin-social-accounts' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_social_facebook') { echo 'active'; } ?>" id="tab_seopress_social_facebook"><?php do_settings_sections( 'seopress-settings-admin-social-facebook' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_social_twitter') { echo 'active'; } ?>" id="tab_seopress_social_twitter"><?php do_settings_sections( 'seopress-settings-admin-social-twitter' ); ?></div>
        </div>

        <?php submit_button(); ?>
        </form>
        <?php
    }

    function seopress_google_analytics_page(){
        $this->options = get_option( 'seopress_google_analytics_option_name' );
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        }
        ?>
        <form method="post" action="<?php echo admin_url('options.php'); ?>" class="seopress-option">
        <?php 
        global $wp_version, $title;
        $current_tab = '';
        $tag = version_compare( $wp_version, '4.4' ) >= 0 ? 'h1' : 'h2';
        echo '<'.$tag.'><span class="dashicons dashicons-chart-area"></span>'.$title;

        if(seopress_get_toggle_google_analytics_option()=='1') { 
            $seopress_get_toggle_google_analytics_option = '"1"';
        } else { 
            $seopress_get_toggle_google_analytics_option = '"0"';
        }
        ?>
        
        <input type="checkbox" name="toggle-google-analytics" id="toggle-google-analytics" class="toggle" data-toggle=<?php echo $seopress_get_toggle_google_analytics_option; ?>>
        
        <label for="toggle-google-analytics"></label>
        
        <?php
        if(seopress_get_toggle_google_analytics_option()=='1') { 
            echo '<span id="analytics-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
            echo '<span id="analytics-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
        } else { 
            echo '<span id="analytics-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
            echo '<span id="analytics-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
        }
        
        echo '<div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>';

        echo '</'.$tag.'>';              

        settings_fields( 'seopress_google_analytics_option_group' );
        ?>
    
         <div id="seopress-tabs" class="wrap">
         <?php

            if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                $plugin_settings_tabs = array(
                    'tab_seopress_google_analytics_enable' => __( "General", "wp-seopress" ), 
                    'tab_seopress_google_analytics_features' => __( "Tracking", "wp-seopress" ),
                    'tab_seopress_google_analytics_events' => __( "Events", "wp-seopress" ),
                    'tab_seopress_google_analytics_custom_dimensions' => __( "Custom Dimensions", "wp-seopress" ),
                    'tab_seopress_google_analytics_dashboard' => __( "Stats in Dashboard", "wp-seopress" ),
                    //'tab_seopress_google_analytics_e_commerce' => __( "E-commerce", "wp-seopress" ),
                );
            } else {
                $plugin_settings_tabs = array(
                    'tab_seopress_google_analytics_enable' => __( "General", "wp-seopress" ), 
                    'tab_seopress_google_analytics_features' => __( "Tracking", "wp-seopress" ),
                    'tab_seopress_google_analytics_events' => __( "Events", "wp-seopress" ),
                    'tab_seopress_google_analytics_custom_dimensions' => __( "Custom Dimensions", "wp-seopress" ),
                );
            }

            echo '<div class="nav-tab-wrapper">';
            foreach ( $plugin_settings_tabs as $tab_key => $tab_caption ) {
                echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=seopress-google-analytics#tab=' . $tab_key . '">' . $tab_caption . '</a>'; 
            }
            echo '</div>';
        ?>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_google_analytics_enable') { echo 'active'; } ?>" id="tab_seopress_google_analytics_enable"><?php do_settings_sections( 'seopress-settings-admin-google-analytics-enable' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_google_analytics_features') { echo 'active'; } ?>" id="tab_seopress_google_analytics_features"><?php do_settings_sections( 'seopress-settings-admin-google-analytics-features' ); ?></div><div class="seopress-tab <?php if ($current_tab == 'tab_seopress_google_analytics_events') { echo 'active'; } ?>" id="tab_seopress_google_analytics_events"><?php do_settings_sections( 'seopress-settings-admin-google-analytics-events' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_google_analytics_custom_dimensions') { echo 'active'; } ?>" id="tab_seopress_google_analytics_custom_dimensions"><?php do_settings_sections( 'seopress-settings-admin-google-analytics-custom-dimensions' ); ?></div>
            <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
                <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_google_analytics_dashboard') { echo 'active'; } ?>" id="tab_seopress_google_analytics_dashboard"><?php do_settings_sections( 'seopress-settings-admin-google-analytics-dashboard' ); ?></div>
                <!-- <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_google_analytics_e_commerce') { echo 'active'; } ?>" id="tab_seopress_google_analytics_e_commerce"><?php do_settings_sections( 'seopress-settings-admin-google-analytics-e-commerce' ); ?></div>
 -->            <?php } ?>
        </div>

        <?php submit_button(); ?>
        </form>
        <?php
    }

    function seopress_advanced_page(){
        $this->options = get_option( 'seopress_advanced_option_name' );
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        }
        ?>
        <form method="post" action="<?php echo admin_url('options.php'); ?>" class="seopress-option">
        <?php 
        
        global $wp_version, $title;
        $current_tab = '';
        $tag = version_compare( $wp_version, '4.4' ) >= 0 ? 'h1' : 'h2';
        echo '<'.$tag.'><span class="dashicons dashicons-admin-tools"></span>'.$title;

        if(seopress_get_toggle_advanced_option()=='1') { 
            $seopress_get_toggle_advanced_option = '"1"';
        } else { 
            $seopress_get_toggle_advanced_option = '"0"';
        }
        ?>
        
        <input type="checkbox" name="toggle-advanced" id="toggle-advanced" class="toggle" data-toggle=<?php echo $seopress_get_toggle_advanced_option; ?>>
        <label for="toggle-advanced"></label>
        
        <?php
        if(seopress_get_toggle_advanced_option()=='1') { 
            echo '<span id="advanced-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
            echo '<span id="advanced-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
        } else { 
            echo '<span id="advanced-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
            echo '<span id="advanced-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
        }

        echo '<div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>';

        echo '</'.$tag.'>';

        settings_fields( 'seopress_advanced_option_group' );
        ?>
    
         <div id="seopress-tabs" class="wrap">
         <?php 
            
            $plugin_settings_tabs = array(
                'tab_seopress_advanced_advanced' => __( "Advanced", "wp-seopress" ), 
                'tab_seopress_advanced_appearance' => __( "Appearance", "wp-seopress" ), 
                'tab_seopress_advanced_security' => __( "Security", "wp-seopress" ), 
            );

            echo '<div class="nav-tab-wrapper">';
            foreach ( $plugin_settings_tabs as $tab_key => $tab_caption ) {
                echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=seopress-advanced#tab=' . $tab_key . '">' . $tab_caption . '</a>'; 
            }
            echo '</div>';
        ?>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_advanced_advanced') { echo 'active'; } ?>" id="tab_seopress_advanced_advanced"><?php do_settings_sections( 'seopress-settings-admin-advanced-advanced' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_advanced_appearance') { echo 'active'; } ?>" id="tab_seopress_advanced_appearance"><?php do_settings_sections( 'seopress-settings-admin-advanced-appearance' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_advanced_security') { echo 'active'; } ?>" id="tab_seopress_advanced_security"><?php do_settings_sections( 'seopress-settings-admin-advanced-security' ); ?></div>
        </div>

        <?php submit_button(); ?>
        </form>
        <?php
    }

    function seopress_import_export_page(){
        $this->options = get_option( 'seopress_import_export_option_name' );
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        }
        ?>
        <div class="seopress-option">
            <?php global $wp_version, $title;
            $tag = version_compare( $wp_version, '4.4' ) >= 0 ? 'h1' : 'h2';
            echo '<'.$tag.'><span class="dashicons dashicons-admin-settings"></span>'.$title.'</'.$tag.'>';
            ?>
            <div class="metabox-holder">
                <div class="postbox">
                    <h3><span><?php _e( 'Export Settings', 'wp-seopress' ); ?></span></h3>
                    <div class="inside">
                        <p><?php _e( 'Export the plugin settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'wp-seopress' ); ?></p>
                        <form method="post">
                            <p><input type="hidden" name="seopress_action" value="export_settings" /></p>
                            <p>
                                <?php wp_nonce_field( 'seopress_export_nonce', 'seopress_export_nonce' ); ?>
                                <?php submit_button( __( 'Export', 'wp-seopress' ), 'secondary', 'submit', false ); ?>
                            </p>
                        </form>
                    </div><!-- .inside -->
                </div><!-- .postbox -->

                <div class="postbox">
                    <h3><span><?php _e( 'Import Settings', 'wp-seopress' ); ?></span></h3>
                    <div class="inside">
                        <p><?php _e( 'Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'wp-seopress' ); ?></p>
                        <form method="post" enctype="multipart/form-data">
                            <p>
                                <input type="file" name="import_file"/>
                            </p>
                            <p>
                                <input type="hidden" name="seopress_action" value="import_settings" />
                                <?php wp_nonce_field( 'seopress_import_nonce', 'seopress_import_nonce' ); ?>
                                <?php submit_button( __( 'Import', 'wp-seopress' ), 'secondary', 'submit', false ); ?>
                            </p>
                        </form>
                    </div><!-- .inside -->
                </div><!-- .postbox -->

                <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
                    <div class="postbox">
                        <h3><span><?php _e( 'Import Redirections', 'wp-seopress' ); ?></span></h3>
                        <div class="inside">
                            <p><?php _e( 'Import your own redirections from a .csv file (separator ";"). You must have 3 columns in this order: url to match (without your domain name), url to redirect in absolute and type of redirection (301, 302 or 307).', 'wp-seopress' ); ?></p>
                            <p>
                                <a href="https://www.seopress.org/wp-content/uploads/csv/seopress-redirections-example.csv" target="_blank">
                                    <?php _e('Download a CSV example','wp-seopress'); ?>
                                </a>
                            </p>
                            <form method="post" enctype="multipart/form-data">
                                <p>
                                    <input type="file" name="import_file"/>
                                </p>
                                <p>
                                    <input type="hidden" name="seopress_action" value="import_redirections_settings" />
                                    <?php wp_nonce_field( 'seopress_import_redirections_nonce', 'seopress_import_redirections_nonce' ); ?>
                                    <?php submit_button( __( 'Import', 'wp-seopress' ), 'secondary', 'submit', false ); ?>
                                </p>
                            </form>
                        </div><!-- .inside -->
                    </div><!-- .postbox -->
                <?php } ?>

                <div id="yoast-migration-tool" class="postbox">
                    <h3><span><?php _e( 'Import posts and terms metadata from Yoast', 'wp-seopress' ); ?></span></h3>
                    <div class="inside">
                        <p><?php _e( 'By clicking Migrate, we\'ll import:', 'wp-seopress' ); ?></p>
                        <ul>
                            <li><?php _e('Title tags','wp-seopress'); ?></li>
                            <li><?php _e('Meta description','wp-seopress'); ?></li>
                            <li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)','wp-seopress'); ?></li>
                            <li><?php _e('Twitter tags (title, description and image thumbnail)','wp-seopress'); ?></li>
                            <li><?php _e('Meta Robots (noindex, nofollow...)','wp-seopress'); ?></li>
                            <li><?php _e('Canonical URL','wp-seopress'); ?></li>
                            <li><?php _e('Focus keywords','wp-seopress'); ?></li>
                        </ul>
                        <p style="color:red"><span class="dashicons dashicons-warning"></span> <?php _e( '<strong>WARNING:</strong> Migration will delete all SEOPress posts and terms metadata', 'wp-seopress' ); ?></p>
                        <button id="seopress-yoast-migrate" class="button"><?php _e('Migrate now','wp-seopress'); ?></button>
                        <span class="spinner"></span>
                        <div class="log"></div>
                    </div><!-- .inside -->
                </div><!-- .postbox -->

                <div class="postbox">
                    <h3><span><?php _e( 'Reset All Notices From Notifications Center', 'wp-seopress' ); ?></span></h3>
                    <div class="inside">
                        <p><?php _e( 'By clicking Reset Notices, you\'ll see all notices again in notifications center.', 'wp-seopress' ); ?></p>
                         <form method="post" enctype="multipart/form-data">
                            <p>
                                <input type="hidden" name="seopress_action" value="reset_notices_settings" />
                                <?php wp_nonce_field( 'seopress_reset_notices_nonce', 'seopress_reset_notices_nonce' ); ?>
                                <?php submit_button( __( 'Reset notices', 'wp-seopress' ), 'secondary', 'submit', false ); ?>
                            </p>
                        </form>
                    </div><!-- .inside -->
                </div><!-- .postbox -->

                <div class="postbox">
                    <h3><span><?php _e( 'Reset All Settings', 'wp-seopress' ); ?></span></h3>
                    <div class="inside">
                        <p style="color:red"><span class="dashicons dashicons-warning"></span> <?php _e( '<strong>WARNING:</strong> Delete all options related to SEOPress in your database AND set settings to their default values.', 'wp-seopress' ); ?></p>
                         <form method="post" enctype="multipart/form-data">
                            <p>
                                <input type="hidden" name="seopress_action" value="reset_settings" />
                                <?php wp_nonce_field( 'seopress_reset_nonce', 'seopress_reset_nonce' ); ?>
                                <?php submit_button( __( 'Reset settings', 'wp-seopress' ), 'secondary', 'submit', false ); ?>
                            </p>
                        </form>
                    </div><!-- .inside -->
                </div><!-- .postbox -->
            </div><!-- .metabox-holder -->
        </div>
    <?php
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'seopress_option_name' );
        $current_tab ='';
        echo seopress_admin_header();
        ?>
            <div id="seopress-content">
                <?php 
                    //Notifications Center
                    function seopress_advanced_appearance_notifications_option() {
                        $seopress_advanced_appearance_notifications_option = get_option("seopress_advanced_option_name");
                        if ( ! empty ( $seopress_advanced_appearance_notifications_option ) ) {
                            foreach ($seopress_advanced_appearance_notifications_option as $key => $seopress_advanced_appearance_notifications_value)
                                $options[$key] = $seopress_advanced_appearance_notifications_value;
                             if (isset($seopress_advanced_appearance_notifications_option['seopress_advanced_appearance_notifications'])) { 
                                return $seopress_advanced_appearance_notifications_option['seopress_advanced_appearance_notifications'];
                             }
                        }
                    }
                ?>     
        
                <div id="seopress-admin-tabs" class="wrap">
                    <?php 
                        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                            if (seopress_advanced_appearance_notifications_option() !='') {
                                $dashboard_settings_tabs = array(
                                    'tab_seopress_seo_tools' => __( "SEO Tools", "wp-seopress" ),
                                    'tab_seopress_links' => __( "Useful links", "wp-seopress" )
                                );
                            } else {
                                $dashboard_settings_tabs = array(
                                    'tab_seopress_notifications' => __( "Notifications Center", "wp-seopress" ),
                                    'tab_seopress_seo_tools' => __( "SEO Tools", "wp-seopress" ),
                                    'tab_seopress_links' => __( "Useful links", "wp-seopress" )
                                );
                            }
                        } else {
                            if (seopress_advanced_appearance_notifications_option() !='') {
                                $dashboard_settings_tabs = array(
                                    'tab_seopress_links' => __( "Useful links", "wp-seopress" )
                                );
                            } else {
                                $dashboard_settings_tabs = array(
                                    'tab_seopress_notifications' => __( "Notifications Center", "wp-seopress" ),
                                    'tab_seopress_links' => __( "Useful links", "wp-seopress" )
                                );
                            }
                        }
                        
                        echo '<div class="nav-tab-wrapper">';
                        foreach ( $dashboard_settings_tabs as $tab_key => $tab_caption ) {
                            echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=seopress-option#tab=' . $tab_key . '">' . $tab_caption . '</a>';
                        }
                        echo '</div>';
                    ?>

                    <div class="wrap-seopress-tab-content">
                        <?php if(seopress_advanced_appearance_notifications_option() !='1') { ?>
                            <div id="tab_seopress_notifications" class="seopress-tab <?php if ($current_tab == 'tab_seopress_notifications') { echo 'active'; } ?>">
                                <div id="seopress-notifications-center">
                                    <?php if (get_theme_support('title-tag') !='1') { ?>
                                        <div class="seopress-alert">
                                            <p>
                                                <span class="dashicons dashicons-warning"></span>
                                                <?php _e('Your theme doesn\'t use <strong>add_theme_support(\'title-tag\');</strong>','wp-seopress'); ?>
                                                <span class="impact high"><?php _e('High impact','wp-seopress'); ?></span>
                                            </p>
                                            <?php
                                            if (function_exists('seopress_get_locale')) {
                                                if (seopress_get_locale() =='fr') {
                                                    $seopress_docs_link['support']['title-tag'] = 'https://www.seopress.org/fr/support/guides/resoudre-add_theme_support-manquant-dans-votre-theme/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                                } else {
                                                    $seopress_docs_link['support']['title-tag'] = 'https://www.seopress.org/support/guides/fixing-missing-add_theme_support-in-your-theme/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                                }
                                            } ?>
                                            <?php echo '<a class="button-primary" href="'.$seopress_docs_link['support']['title-tag'].'" target="_blank">'.__('Learn more','wp-seopress').'</a>'; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (is_plugin_active('wordpress-seo/wp-seo.php')) { ?>
                                        <div class="seopress-alert">
                                            <p>
                                                <span class="dashicons dashicons-warning"></span>
                                                <?php _e('We notice that you use Yoast SEO plugin. <br>Do you want to migrate all your posts metadata to SEOPress?','wp-seopress'); ?>
                                            </p>
                                            <a class="button-primary" href="<?php echo admin_url( 'admin.php?page=seopress-import-export#yoast-migration-tool' ); ?>"><?php _e('Migrate!','wp-seopress'); ?></a>
                                        </div>
                                    <?php } ?>
                                    <?php if (!is_ssl()) { ?>
                                        <?php
                                        function seopress_get_hidden_notices_ssl_option() {
                                            $seopress_get_hidden_notices_ssl_option = get_option("seopress_notices");
                                            if ( ! empty ( $seopress_get_hidden_notices_ssl_option ) ) {
                                                foreach ($seopress_get_hidden_notices_ssl_option as $key => $seopress_get_hidden_notices_ssl_value)
                                                    $options[$key] = $seopress_get_hidden_notices_ssl_value;
                                                 if (isset($seopress_get_hidden_notices_ssl_option['notice-ssl'])) { 
                                                    return $seopress_get_hidden_notices_ssl_option['notice-ssl'];
                                                 }
                                            }
                                        }
                                        if(seopress_get_hidden_notices_ssl_option() =='1') { 
                                            //do nothing
                                        } else { ?>
                                            <div id="notice-ssl-alert" class="seopress-alert deleteable">
                                                <p>
                                                    <span class="dashicons dashicons-warning"></span>
                                                    <?php _e('Your site doesn\'t use an SSL certificate!','wp-seopress'); ?> 
                                                    <a href="https://webmasters.googleblog.com/2014/08/https-as-ranking-signal.html" target="_blank"><?php _e('Learn more','wp-seopress'); ?></a>
                                                    <span class="impact low"><?php _e('Low impact','wp-seopress'); ?></span>
                                                </p>
                                                <a class="button-primary" href="https://www.namecheap.com/?aff=105841" target="_blank"><?php _e('Buy an SSL!','wp-seopress'); ?></a>
                                                <span name="notice-ssl" id="notice-ssl" class="dashicons dashicons-trash remove-notice" data-notice="notice-ssl"></span>
                                            </div>
                                        <?php }
                                    ?>
                                    <?php } ?>
                                    <?php if (get_option('blog_public') !='1') { ?>
                                        <div class="seopress-alert">
                                            <p>
                                                <span class="dashicons dashicons-warning"></span>
                                                <?php _e('Your site is not visible to Search Engines!','wp-seopress'); ?>
                                                <span class="impact high"><?php _e('High impact','wp-seopress'); ?></span>
                                            </p>
                                            <a class="button-primary" href="<?php echo admin_url( 'options-reading.php' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                        </div>
                                    <?php } ?>
                                    <?php if (get_option('blogname') =='') { ?>
                                        <div class="seopress-alert">
                                            <p>
                                                <span class="dashicons dashicons-warning"></span>
                                                <?php _e('Your site title is empty!','wp-seopress'); ?>
                                                <span class="impact high"><?php _e('High impact','wp-seopress'); ?></span>
                                            </p>
                                            <a class="button-primary" href="<?php echo admin_url( 'options-general.php' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                        </div>
                                    <?php } ?>
                                    <?php if (get_option('permalink_structure') =='') { ?>
                                        <div class="seopress-alert">
                                            <p>
                                                <span class="dashicons dashicons-warning"></span>
                                                <?php _e('Your permalinks are not SEO Friendly! Enable pretty permalinks to fix this.','wp-seopress'); ?>
                                                <span class="impact high"><?php _e('High impact','wp-seopress'); ?></span>
                                            </p>
                                            <a class="button-primary" href="<?php echo admin_url( 'options-permalink.php' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                        </div>
                                    <?php } ?>
                                    <?php 
                                        if(get_option('rss_use_excerpt') =='0') {
                                            function seopress_get_hidden_notices_rss_use_excerpt_option() {
                                                $seopress_get_hidden_notices_rss_use_excerpt_option = get_option("seopress_notices");
                                                if ( ! empty ( $seopress_get_hidden_notices_rss_use_excerpt_option ) ) {
                                                    foreach ($seopress_get_hidden_notices_rss_use_excerpt_option as $key => $seopress_get_hidden_notices_rss_use_excerpt_value)
                                                        $options[$key] = $seopress_get_hidden_notices_rss_use_excerpt_value;
                                                        if (isset($seopress_get_hidden_notices_rss_use_excerpt_option['notice-rss-use-excerpt'])) {
                                                            return $seopress_get_hidden_notices_rss_use_excerpt_option['notice-rss-use-excerpt'];
                                                        }
                                                }
                                            }
                                            if(seopress_get_hidden_notices_rss_use_excerpt_option() =='1') {
                                                //do nothing
                                            } else { ?>
                                                <div id="notice-rss-use-excerpt-alert" class="seopress-alert deleteable">
                                                    <p>
                                                        <span class="dashicons dashicons-warning"></span>
                                                        <?php _e('You RSS feed show full text!','wp-seopress'); ?>
                                                        <span class="impact medium"><?php _e('Medium impact','wp-seopress'); ?></span>
                                                    </p>
                                                    <a class="button-primary" href="<?php echo admin_url( 'options-reading.php' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                                    <span name="notice-rss-use-excerpt" id="notice-rss-use-excerpt" class="dashicons dashicons-trash remove-notice" data-notice="notice-rss-use-excerpt"></span>
                                                </div>
                                            <?php }
                                        }
                                    ?>
                                    <?php 
                                        if(get_option('page_comments') =='1') {
                                            function seopress_get_hidden_notices_divide_comments_option() {
                                                $seopress_get_hidden_notices_divide_comments_option = get_option("seopress_notices");
                                                if ( ! empty ( $seopress_get_hidden_notices_divide_comments_option ) ) {
                                                    foreach ($seopress_get_hidden_notices_divide_comments_option as $key => $seopress_get_hidden_notices_divide_comments_value)
                                                        $options[$key] = $seopress_get_hidden_notices_divide_comments_value;
                                                        if (isset($seopress_get_hidden_notices_divide_comments_option['notice-divide-comments'])) {
                                                            return $seopress_get_hidden_notices_divide_comments_option['notice-divide-comments'];
                                                        }
                                                }
                                            }
                                            if(seopress_get_hidden_notices_divide_comments_option() =='1') {
                                                //do nothing
                                            } else { ?>
                                                <div id="notice-divide-comments-alert" class="seopress-alert deleteable">
                                                    <p>
                                                        <span class="dashicons dashicons-warning"></span>
                                                        <?php _e('Break comments into pages is ON!','wp-seopress'); ?>
                                                        <span class="impact high"><?php _e('Huge impact','wp-seopress'); ?></span>
                                                    </p>
                                                    <a class="button-primary" href="<?php echo admin_url( 'options-discussion.php' ); ?>"><?php _e('Disable this!','wp-seopress'); ?></a>
                                                    <span name="notice-divide-comments" id="notice-divide-comments" class="dashicons dashicons-trash remove-notice" data-notice="notice-divide-comments"></span>
                                                </div>
                                            <?php }
                                        }
                                    ?>
                                    <?php 
                                        if(get_option('posts_per_page') < '16') {
                                            function seopress_get_hidden_notices_posts_number_option() {
                                                $seopress_get_hidden_notices_posts_number_option = get_option("seopress_notices");
                                                if ( ! empty ( $seopress_get_hidden_notices_posts_number_option ) ) {
                                                    foreach ($seopress_get_hidden_notices_posts_number_option as $key => $seopress_get_hidden_notices_posts_number_value)
                                                        $options[$key] = $seopress_get_hidden_notices_posts_number_value;
                                                        if (isset($seopress_get_hidden_notices_posts_number_option['notice-posts-number'])) {
                                                            return $seopress_get_hidden_notices_posts_number_option['notice-posts-number'];
                                                        }
                                                }
                                            }
                                            if(seopress_get_hidden_notices_posts_number_option() =='1') {
                                                //do nothing
                                            } else { ?>
                                                <div id="notice-posts-number-alert" class="seopress-alert deleteable">
                                                    <p>
                                                        <span class="dashicons dashicons-warning"></span>
                                                        <?php _e('Display more posts per page on homepage and archives','wp-seopress'); ?>
                                                        <span class="impact medium"><?php _e('Medium impact','wp-seopress'); ?></span>
                                                    </p>
                                                    <a class="button-primary" href="<?php echo admin_url( 'options-reading.php' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                                    <span name="notice-posts-number" id="notice-posts-number" class="dashicons dashicons-trash remove-notice" data-notice="notice-posts-number"></span>
                                                </div>
                                            <?php }
                                        }
                                    ?>
                                    <?php if (seopress_xml_sitemap_general_enable_option() !='1') { ?>
                                        <div class="seopress-alert">
                                            <p>
                                                <span class="dashicons dashicons-warning"></span>
                                                <?php _e('You don\'t have an XML Sitemap!','wp-seopress'); ?>
                                                <span class="impact medium"><?php _e('Medium impact','wp-seopress'); ?></span>
                                            </p>
                                            <a class="button-primary" href="<?php echo admin_url( 'admin.php?page=seopress-xml-sitemap' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                        </div>
                                    <?php } ?>

                                    <?php
                                        function seopress_get_hidden_notices_google_business_option() {
                                            $seopress_get_hidden_notices_google_business_option = get_option("seopress_notices");
                                            if ( ! empty ( $seopress_get_hidden_notices_google_business_option ) ) {
                                                foreach ($seopress_get_hidden_notices_google_business_option as $key => $seopress_get_hidden_notices_google_business_value)
                                                    $options[$key] = $seopress_get_hidden_notices_google_business_value;
                                                 if (isset($seopress_get_hidden_notices_google_business_option['notice-google-business'])) { 
                                                    return $seopress_get_hidden_notices_google_business_option['notice-google-business'];
                                                 }
                                            }
                                        }
                                        if(seopress_get_hidden_notices_google_business_option() =='1') { 
                                            //do nothing
                                        } else { ?>
                                            <div id="notice-google-business-alert" class="seopress-alert deleteable">
                                                <p>
                                                    <span class="dashicons dashicons-warning"></span>
                                                    <?php _e('Do you have a Google My Business page? It\'s free!','wp-seopress'); ?>
                                                    <span class="impact high"><?php _e('Huge impact','wp-seopress'); ?></span>
                                                </p>
                                                <a class="button-primary" href="https://www.google.com/business/go/" target="_blank"><?php _e('Create your page now!','wp-seopress'); ?></a>
                                                <span name="notice-google-business" id="notice-google-business" class="dashicons dashicons-trash remove-notice" data-notice="notice-google-business"></span>
                                            </div>
                                        <?php }
                                    ?>

                                    <?php
                                        function seopress_get_hidden_notices_search_console_option() {
                                            $seopress_get_hidden_notices_search_console_option = get_option("seopress_notices");
                                            if ( ! empty ( $seopress_get_hidden_notices_search_console_option ) ) {
                                                foreach ($seopress_get_hidden_notices_search_console_option as $key => $seopress_get_hidden_notices_search_console_value)
                                                    $options[$key] = $seopress_get_hidden_notices_search_console_value;
                                                 if (isset($seopress_get_hidden_notices_search_console_option['notice-search-console'])) { 
                                                    return $seopress_get_hidden_notices_search_console_option['notice-search-console'];
                                                 }
                                            }
                                        }
                                        function seopress_get_google_site_verification_option() {
                                            $seopress_get_google_site_verification_option = get_option("seopress_advanced_option_name");
                                            if ( ! empty ( $seopress_get_google_site_verification_option ) ) {
                                                foreach ($seopress_get_google_site_verification_option as $key => $seopress_get_google_site_verification_value)
                                                    $options[$key] = $seopress_get_google_site_verification_value;
                                                 if (isset($seopress_get_google_site_verification_option['seopress_advanced_advanced_google'])) { 
                                                    return $seopress_get_google_site_verification_option['seopress_advanced_advanced_google'];
                                                 }
                                            }
                                        }
                                        if(seopress_get_hidden_notices_search_console_option() =='1') { 
                                            //do nothing
                                        } elseif(seopress_get_google_site_verification_option() =='') { ?>
                                            <div id="notice-search-console-alert" class="seopress-alert deleteable">
                                                <p>
                                                    <span class="dashicons dashicons-warning"></span>
                                                    <?php _e('Add your site to Google. It\'s free!','wp-seopress'); ?>
                                                    <span class="impact high"><?php _e('Huge impact','wp-seopress'); ?></span>
                                                </p>
                                                <a class="button-primary" href="https://www.google.com/webmasters/tools/home" target="_blank"><?php _e('Add your site to Search Console!','wp-seopress'); ?></a>
                                                <span name="notice-search-console" id="notice-search-console" class="dashicons dashicons-trash remove-notice" data-notice="notice-search-console"></span>
                                            </div>
                                        <?php }
                                    ?>

                                    <?php if (get_option("seopress_pro_license_key") =='' && is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
                                        <div class="seopress-alert">
                                            <p>
                                                <span class="dashicons dashicons-warning"></span>
                                                <?php _e('You have to enter your licence key to get updates and support','wp-seopress'); ?>
                                            </p>
                                            <a class="button-primary" href="<?php echo admin_url( 'admin.php?page=seopress-license' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                        </div>
                                    <?php } ?>
                                </div><!--#seopress-notifications-center-->
                                    
                            </div>
                        <?php } ?>

                        <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
                            <div id="tab_seopress_seo_tools" class="seopress-tab seopress-useful-tools <?php if ($current_tab == 'tab_seopress_seo_tools') { echo 'active'; } ?>">
                                
                                <!-- Reverse -->
                                <div class="widget widget-reverse">
                                    <h3 class="widget-title"><span class="dashicons dashicons-welcome-view-site"></span><?php _e('Check websites setup on your server','wp-seopress'); ?></h3>

                                    <p><?php echo '<strong>'.__('Host IP: ','wp-seopress-pro').'</strong>'.gethostbyname(get_home_url()); ?></p>
                                    <?php
                                        if ( get_transient( 'seopress_results_reverse' ) !='' ) { 
                                            
                                            $seopress_results_reverse = (array)json_decode(get_transient( 'seopress_results_reverse' ));

                                            echo '<p class="remote-ip"><strong>'.__('Server IP Address: ','wp-seopress').'</strong>'.$seopress_results_reverse['remoteIpAddress'].'</p>';

                                            echo '<p class="last-scrape"><strong>'.__('Last scrape: ','wp-seopress').'</strong>'.$seopress_results_reverse['lastScrape'].'</p>';
                                            echo '<p class="domain-count"><strong>'.__('Number of websites on your server: ','wp-seopress').'</strong>'.$seopress_results_reverse['domainCount'].'</p>';
                                            
                                            if ($seopress_results_reverse['domainArray'] !='') {
                                                echo '<ul>';
                                                    foreach ($seopress_results_reverse['domainArray'] as $key => $value) {
                                                        echo '<li><span class="dashicons dashicons-minus"></span><a href="'.$value[0].'" target="_blank">'.$value[0].'</a><span class="dashicons dashicons-external"></span></li>';
                                                    }
                                                echo '</ul>';
                                            }
                                        }
                                    ?>
                                    <button id="seopress-reverse-submit" class="button button-primary" name="submit">
                                        <?php _e('Get list','wp-seopress'); ?>
                                    </button>

                                    <span id="spinner-reverse" class="spinner"></span>
                                </div>

                                <!-- Whois -->
                                <div class="widget widget-whois">
                                    <h3 class="widget-title"><span class="dashicons dashicons-clock"></span><?php _e('Check your domain expiration date','wp-seopress'); ?></h3>
                                    <?php
                                        $url = get_home_url();

                                        function get_domain($url) {
                                            $urlobj = parse_url($url);
                                            $domain = $urlobj['host'];
                                            if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
                                                return $regs['domain'];
                                            }
                                            return false;
                                        }
                                        
                                        $whois = shell_exec('whois '.get_domain($url));

                                        if ($whois) {
                                            $whois_result = explode("\n",$whois);

                                            $out = array();
                                            foreach ($whois_result as $line) {
                                                //remove comments
                                                if (substr($line,0,1) == '%' || substr($line,0,1) == '#') {
                                                    continue; 
                                                }

                                                $ps = explode(':',$line);
                                                $out[trim($ps[0])] = isset($ps[1]) ? trim($ps[1]) : null;
                                            }
                                            if ($out) {
                                                echo '<ul>';
                                                    if (isset($out['Domain Name'])) {
                                                        echo '<li><span>'.__('Your domain name: ','wp-seopress').'</span> '.$out['Domain Name'].'</li>';
                                                    }
                                                    if (isset($out['Registrar'])) {
                                                        echo '<li><span>'.__('Your registrar: ','wp-seopress').'</span> '.$out['Registrar'].'</li>';
                                                    }
                                                    if (isset($out['Updated Date'])) {
                                                        echo '<li><span>'.__('Last updated date: ','wp-seopress').'</span> '.date_i18n( get_option( 'date_format' ), strtotime( $out['Updated Date'].':00:00' ) ).'</li>';
                                                    }
                                                    if (isset($out['Creation Date'])) {
                                                        echo '<li><span>'.__('Domain creation date: ','wp-seopress').'</span> '.date_i18n( get_option( 'date_format' ), strtotime( $out['Creation Date'].':00:00' ) ).'</li>';
                                                    }
                                                    if (isset($out['Registry Expiry Date'])) {
                                                        echo '<li><span>'.__('Your domain expires: ','wp-seopress').'</span> '.date_i18n( get_option( 'date_format' ), strtotime( $out['Registry Expiry Date'].':00:00' ) ).'</li>';
                                                    }
                                                echo '</ul>';
                                            }
                                        } else {
                                            echo '<p>'.__('Sorry, you domain name can\'t be verified :(','wp-seopress').'</p>';
                                        }
                                    ?>
                                    <button id="seopress-whois-alert" class="button-primary button" name="clear"><?php _e('Email me before expiration','wp-seopress'); ?></button>

                                    <span id="spinner-whois" class="spinner"></span>

                                    <span class="seopress-whois-alert log"></span>
                                </div>

                                <!-- Alexa -->
                                <div class="widget widget-alexa">
                                    <h3 class="widget-title"><span class="dashicons dashicons-chart-area"></span><?php _e('Check your Alexa Rank (Amazon index)','wp-seopress'); ?></h3>

                                    <?php
                                        if ( get_transient( 'seopress_results_alexa_rank' ) !='' ) {
                                            echo '<div class="wrap-alexa-rank">';
                                                $seopress_results_alexa_rank = get_transient( 'seopress_results_alexa_rank' );

                                                $seopress_alexa_rank_xml            = simplexml_load_string($seopress_results_alexa_rank);
                                                $seopress_alexa_rank_reach          = $seopress_alexa_rank_xml->SD->REACH['RANK'];
                                                $seopress_alexa_rank_delta          = $seopress_alexa_rank_xml->SD->RANK['DELTA'];
                                                $seopress_alexa_rank_country        = $seopress_alexa_rank_xml->SD->COUNTRY['NAME'];
                                                $seopress_alexa_rank_country_rank   = $seopress_alexa_rank_xml->SD->COUNTRY['RANK'];

                                                if ($seopress_alexa_rank_reach !='') {
                                                    echo '<span class="seopress_alexa_rank_reach">
                                                            <span class="highlight">'.__('Your Alexa Rank','wp-seopress').'</span>'
                                                            .$seopress_alexa_rank_reach.'
                                                        </span>';
                                                }
                                                if ($seopress_alexa_rank_delta !='') {
                                                    
                                                    $seopress_alexa_rank_delta_sign = substr($seopress_alexa_rank_delta, 0, 1);
                                                    $seopress_alexa_rank_delta_num = substr($seopress_alexa_rank_delta, 1, 9999);
                                                    
                                                    if ($seopress_alexa_rank_delta_sign =='+') {
                                                        $seopress_alexa_rank_delta_css = '<span class="plus"><span class="dashicons dashicons-plus"></span></span>';
                                                    } elseif ($seopress_alexa_rank_delta_sign =='-') {
                                                        $seopress_alexa_rank_delta_css = '<span class="minus"><span class="dashicons dashicons-minus"></span></span>';
                                                    }

                                                    echo '<span class="seopress_alexa_rank_delta">
                                                            '.$seopress_alexa_rank_delta_css.$seopress_alexa_rank_delta_num.'
                                                        </span>';
                                                }

                                                if ($seopress_alexa_rank_country !='') {
                                                    echo '<span class="seopress_alexa_rank_country">
                                                            <span class="highlight">'.__('Country Rank: ','wp-seopress').'</span>
                                                            '.$seopress_alexa_rank_country.'
                                                        </span>';
                                                }
                                                if ($seopress_alexa_rank_country_rank !='') {
                                                    echo '<span class="seopress_alexa_rank_country_rank"> - '.$seopress_alexa_rank_country_rank.'</span>';
                                                }
                                            echo '</div>';

                                            echo '<p><em>'._e('The lower, the best','wp-seopress').'</em></p>';
                                        }
                                    ?>

                                    <button id="seopress-request-alexa-rank" class="button-primary button" name="clear"><?php _e('Get Alexa Rank','wp-seopress'); ?></button>

                                    <span id="spinner-alexa" class="spinner"></span>
                                </div>
                            </div>
                        <?php } ?>
                        <div id="tab_seopress_links" class="seopress-tab seopress-useful-tools <?php if ($current_tab == 'tab_seopress_links') { echo 'active'; } ?>">
                            <ul>
                                <li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://www.google.com/webmasters/tools/disavow-links-main" target="_blank"><?php _e('Upload a list of links to disavow to Google','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
                                <li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://trends.google.com/trends/" target="_blank"><?php _e('Google Trends','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
                                <li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://www.dareboost.com/en/home" target="_blank"><?php _e('Dareboost: Test, analyze and optimize your website','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
                                <li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://ga-dev-tools.appspot.com/campaign-url-builder/" target="_blank"><?php _e('Google Campaign URL Builder tool','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                    
                <div class="seopress-page-list">
                    <div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>
                    <div class="seopress-feature">
                        <div class="img-tool">
                            <span class="dashicons dashicons-editor-table"></span>
                        </div>
                        <span class="inner">
                            <h3><?php _e('Titles & metas','wp-seopress'); ?></h3>
                            <p><?php _e('Manage all your titles & metas','wp-seopress'); ?></p>
                            <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-titles' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                            <?php
                                if(seopress_get_toggle_titles_option()=='1') { 
                                    $seopress_get_toggle_titles_option = '"1"';
                                } else { 
                                    $seopress_get_toggle_titles_option = '"0"';
                                }
                            ?>
                            <input type="checkbox" name="toggle-titles" id="toggle-titles" class="toggle" data-toggle=<?php echo $seopress_get_toggle_titles_option; ?>>
                            <label for="toggle-titles"></label>
                            <?php
                                if(seopress_get_toggle_titles_option()=='1') { 
                                    echo '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    echo '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                } else { 
                                    echo '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    echo '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                }
                            ?>
                            <?php
                                if (function_exists('seopress_get_locale')) {
                                    if (seopress_get_locale() =='fr') {
                                        $seopress_docs_link['support']['titles'] = 'https://www.seopress.org/fr/support/guides/gerez-vos-balises-titres-metas/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                    } else {
                                        $seopress_docs_link['support']['titles'] = 'https://www.seopress.org/support/guides/manage-titles-meta-descriptions/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                    }
                                }
                            ?>
                            <a href="<?php echo $seopress_docs_link['support']['titles']; ?>" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to manage your titles and meta descriptions - new window','wp-seopress'); ?></span></a>
                        </span>
                    </div>
                    <div class="seopress-feature">
                        <div class="img-tool">
                            <span class="dashicons dashicons-media-spreadsheet"></span>
                        </div>
                        <span class="inner">
                            <h3><?php _e('XML / Image / HTML Sitemap','wp-seopress'); ?></h3>
                            <p><?php _e('Manage your XML / Image / HTML Sitemap','wp-seopress'); ?></p>
                            <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-xml-sitemap' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                            <?php
                                if(seopress_get_toggle_xml_sitemap_option()=='1') { 
                                    $seopress_get_toggle_xml_sitemap_option = '"1"';
                                } else { 
                                    $seopress_get_toggle_xml_sitemap_option = '"0"';
                                }
                            ?>
                            <input type="checkbox" name="toggle-xml-sitemap" id="toggle-xml-sitemap" class="toggle" data-toggle=<?php echo $seopress_get_toggle_xml_sitemap_option; ?>>
                            <label for="toggle-xml-sitemap"></label>
                            <?php
                                if(seopress_get_toggle_xml_sitemap_option()=='1') { 
                                    echo '<span id="sitemap-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    echo '<span id="sitemap-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                } else { 
                                    echo '<span id="sitemap-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    echo '<span id="sitemap-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                }
                            ?>
                            <?php
                                if (function_exists('seopress_get_locale')) {
                                    if (seopress_get_locale() =='fr') {
                                        $seopress_docs_link['support']['sitemaps'] = 'https://www.seopress.org/fr/support/guides/activer-sitemap-xml/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                    } else {
                                        $seopress_docs_link['support']['sitemaps'] = 'https://www.seopress.org/support/guides/enable-xml-sitemaps/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                    }
                                }
                            ?>
                            <a href="<?php echo $seopress_docs_link['support']['sitemaps']; ?>" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to enable your XML Sitemaps - new window','wp-seopress'); ?></span></a>
                        </span>
                    </div>
                    <div class="seopress-feature">
                        <div class="img-tool">
                            <span class="dashicons dashicons-share"></span>
                        </div>
                        <span class="inner">
                            <h3><?php _e('Social Networks','wp-seopress'); ?></h3>
                            <p><?php _e('Open Graph, Twitter Card, Google Knowledge Graph and more...','wp-seopress'); ?></p>
                            <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-social' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                            <?php
                                if(seopress_get_toggle_social_option()=='1') { 
                                    $seopress_get_toggle_social_option = '"1"';
                                } else { 
                                    $seopress_get_toggle_social_option = '"0"';
                                }
                            ?>
                            <input type="checkbox" name="toggle-social" id="toggle-social" class="toggle" data-toggle=<?php echo $seopress_get_toggle_social_option; ?>>
                            <label for="toggle-social"></label>
                            <?php
                                if(seopress_get_toggle_social_option()=='1') { 
                                    echo '<span id="social-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    echo '<span id="social-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                } else { 
                                    echo '<span id="social-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    echo '<span id="social-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                }
                            ?>
                            <?php
                                if (function_exists('seopress_get_locale')) {
                                    if (seopress_get_locale() =='fr') {
                                        $seopress_docs_link['support']['knowledge'] = 'https://www.seopress.org/fr/support/guides/activer-google-knowledge-graph/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                    } else {
                                        $seopress_docs_link['support']['knowledge'] = 'https://www.seopress.org/support/guides/enable-google-knowledge-graph/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                    }
                                }
                            ?>
                            <a href="<?php echo $seopress_docs_link['support']['knowledge']; ?>" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to enable Google Knowledge Graph - new window','wp-seopress'); ?></span></a>
                        </span>
                    </div>
                    <div class="seopress-feature">
                        <div class="img-tool">
                            <span class="dashicons dashicons-chart-area"></span>
                        </div>
                        <span class="inner">
                            <h3><?php _e('Google Analytics','wp-seopress'); ?></h3>
                            <p><?php _e('Track everything about your visitors with Google Analytics','wp-seopress'); ?></p>
                            <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-google-analytics' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                            <?php
                                if(seopress_get_toggle_google_analytics_option()=='1') { 
                                    $seopress_get_toggle_google_analytics_option = '"1"';
                                } else { 
                                    $seopress_get_toggle_google_analytics_option = '"0"';
                                }
                            ?>
                            <input type="checkbox" name="toggle-google-analytics" id="toggle-google-analytics" class="toggle" data-toggle=<?php echo $seopress_get_toggle_google_analytics_option; ?>>
                            <label for="toggle-google-analytics"></label>
                            <?php
                                if(seopress_get_toggle_google_analytics_option()=='1') { 
                                    echo '<span id="analytics-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    echo '<span id="analytics-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                } else { 
                                    echo '<span id="analytics-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    echo '<span id="analytics-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                }
                            ?>
                            <?php
                                if (function_exists('seopress_get_locale')) {
                                    if (seopress_get_locale() =='fr') {
                                        $seopress_docs_link['support']['analytics'] = 'https://www.seopress.org/fr/support/guides/debutez-google-analytics/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                    } else {
                                        $seopress_docs_link['support']['analytics'] = 'https://www.seopress.org/support/guides/google-analytics/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                    }
                                }
                            ?>
                            <a href="<?php echo $seopress_docs_link['support']['analytics']; ?>" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to getting started with Google Analytics - new window','wp-seopress'); ?></span></a>
                        </span>
                    </div>
                    <div class="seopress-feature">
                        <div class="img-tool">
                            <span class="dashicons dashicons-admin-tools"></span>                                  
                        </div>
                        <span class="inner">
                            <h3><?php _e('Advanced','wp-seopress'); ?></h3>
                            <p><?php _e('Advanced SEO options for advanced users!','wp-seopress'); ?></p>
                            <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-advanced' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                            <?php
                                if(seopress_get_toggle_advanced_option()=='1') { 
                                    $seopress_get_toggle_advanced_option = '"1"';
                                } else { 
                                    $seopress_get_toggle_advanced_option = '"0"';
                                }
                            ?>
                            <input type="checkbox" name="toggle-advanced" id="toggle-advanced" class="toggle" data-toggle=<?php echo $seopress_get_toggle_advanced_option; ?>>
                            <label for="toggle-advanced"></label>
                            <?php
                                if(seopress_get_toggle_advanced_option()=='1') { 
                                    echo '<span id="advanced-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    echo '<span id="advanced-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                } else { 
                                    echo '<span id="advanced-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    echo '<span id="advanced-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                }
                            ?>
                        </span>
                    </div>
                    <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-cart"></span>                                  
                            </div>
                            <span class="inner">
                                <h3><?php _e('WooCommerce','wp-seopress'); ?></h3>
                                <p><?php _e('Improve WooCommerce SEO','wp-seopress'); ?></p>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_woocommerce$1' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                                <?php
                                    if(seopress_get_toggle_woocommerce_option()=='1') { 
                                        $seopress_get_toggle_woocommerce_option = '"1"';
                                    } else { 
                                        $seopress_get_toggle_woocommerce_option = '"0"';
                                    }
                                ?>
                                <input type="checkbox" name="toggle-woocommerce" id="toggle-woocommerce" class="toggle" data-toggle=<?php echo $seopress_get_toggle_woocommerce_option; ?>>
                                <label for="toggle-woocommerce"></label>
                                <?php
                                    if(seopress_get_toggle_woocommerce_option()=='1') { 
                                        echo '<span id="woocommerce-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                        echo '<span id="woocommerce-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    } else { 
                                        echo '<span id="woocommerce-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                        echo '<span id="woocommerce-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    }
                                ?>
                            </span>
                        </div>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-cart"></span>                                  
                            </div>
                            <span class="inner">
                                <h3><?php _e('Easy Digital Downloads','wp-seopress'); ?></h3>
                                <p><?php _e('Improve Easy Digital Downloads SEO','wp-seopress'); ?></p>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_edd$13' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                                <?php
                                    if(seopress_get_toggle_edd_option()=='1') { 
                                        $seopress_get_toggle_edd_option = '"1"';
                                    } else { 
                                        $seopress_get_toggle_edd_option = '"0"';
                                    }
                                ?>
                                <input type="checkbox" name="toggle-edd" id="toggle-edd" class="toggle" data-toggle=<?php echo $seopress_get_toggle_edd_option; ?>>
                                <label for="toggle-edd"></label>
                                <?php
                                    if(seopress_get_toggle_edd_option()=='1') { 
                                        echo '<span id="edd-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                        echo '<span id="edd-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    } else { 
                                        echo '<span id="edd-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                        echo '<span id="edd-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    }
                                ?>
                            </span>
                        </div>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-store"></span>
                            </div>
                            <span class="inner">
                                <h3><?php _e('Local Business','wp-seopress'); ?></h3>
                                <p><?php _e('Add Google Local Business data type','wp-seopress'); ?></p>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_local_business$10' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                                <?php
                                    if(seopress_get_toggle_local_business_option()=='1') { 
                                        $seopress_get_toggle_local_business_option = '"1"';
                                    } else { 
                                        $seopress_get_toggle_local_business_option = '"0"';
                                    }
                                ?>
                                <input type="checkbox" name="toggle-local-business" id="toggle-local-business" class="toggle" data-toggle=<?php echo $seopress_get_toggle_local_business_option; ?>>
                                <label for="toggle-local-business"></label>
                                <?php
                                    if(seopress_get_toggle_local_business_option()=='1') { 
                                        echo '<span id="local-business-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                        echo '<span id="local-business-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    } else { 
                                        echo '<span id="local-business-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                        echo '<span id="local-business-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    }
                                ?>
                            </span>
                        </div>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-welcome-learn-more"></span>
                            </div>
                            <span class="inner">
                                <h3><?php _e('Dublin Core','wp-seopress'); ?></h3>
                                <p><?php _e('Add Dublin Core meta tags','wp-seopress'); ?></p>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_dublin_core$8' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                                <?php
                                    if(seopress_get_toggle_dublin_core_option()=='1') { 
                                        $seopress_get_toggle_dublin_core_option = '"1"';
                                    } else { 
                                        $seopress_get_toggle_dublin_core_option = '"0"';
                                    }
                                ?>
                                <input type="checkbox" name="toggle-dublin-core" id="toggle-dublin-core" class="toggle" data-toggle=<?php echo $seopress_get_toggle_dublin_core_option; ?>>
                                <label for="toggle-dublin-core"></label>
                                <?php
                                    if(seopress_get_toggle_dublin_core_option()=='1') { 
                                        echo '<span id="dublin-core-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                        echo '<span id="dublin-core-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    } else { 
                                        echo '<span id="dublin-core-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                        echo '<span id="dublin-core-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    }
                                ?>
                            </span>
                        </div>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-media-spreadsheet"></span>
                            </div>
                            <span class="inner">
                                <h3><?php _e('Structured Data Types','wp-seopress'); ?></h3>
                                <p><?php _e('Add data types to your content: articles, courses, recipes, videos, events and products','wp-seopress'); ?></p>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_rich_snippets$9' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                                <?php
                                    if(seopress_get_toggle_rich_snippets_option()=='1') { 
                                        $seopress_get_toggle_rich_snippets_option = '"1"';
                                    } else { 
                                        $seopress_get_toggle_rich_snippets_option = '"0"';
                                    }
                                ?>
                                <input type="checkbox" name="toggle-rich-snippets" id="toggle-rich-snippets" class="toggle" data-toggle=<?php echo $seopress_get_toggle_rich_snippets_option; ?>>
                                <label for="toggle-rich-snippets"></label>
                                <?php
                                    if(seopress_get_toggle_rich_snippets_option()=='1') { 
                                        echo '<span id="rich-snippets-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                        echo '<span id="rich-snippets-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    } else { 
                                        echo '<span id="rich-snippets-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                        echo '<span id="rich-snippets-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    }
                                ?>
                            </span>
                        </div>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-feedback"></span>                              
                            </div>
                            <span class="inner">
                                <h3><?php _e('Breadcrumbs','wp-seopress'); ?></h3>
                                <p><?php _e('Enable Breadcrumbs for your theme and improve your SEO in SERPs','wp-seopress'); ?></p>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_breadcrumbs$2' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                                <?php
                                    if(seopress_get_toggle_breadcrumbs_option()=='1') { 
                                        $seopress_get_toggle_breadcrumbs_option = '"1"';
                                    } else { 
                                        $seopress_get_toggle_breadcrumbs_option = '"0"';
                                    }
                                ?>
                                <input type="checkbox" name="toggle-breadcrumbs" id="toggle-breadcrumbs" class="toggle" data-toggle=<?php echo $seopress_get_toggle_breadcrumbs_option; ?>>
                                <label for="toggle-breadcrumbs"></label>
                                <?php
                                    if(seopress_get_toggle_breadcrumbs_option()=='1') { 
                                        echo '<span id="breadcrumbs-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                        echo '<span id="breadcrumbs-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    } else { 
                                        echo '<span id="breadcrumbs-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                        echo '<span id="breadcrumbs-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    }
                                ?>
                                <?php
                                if (function_exists('seopress_get_locale')) {
                                        if (seopress_get_locale() =='fr') {
                                            $seopress_docs_link['support']['breadcrumbs'] = 'https://www.seopress.org/fr/support/guides/activer-fil-dariane/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                        } else {
                                            $seopress_docs_link['support']['breadcrumbs'] = 'https://www.seopress.org/support/guides/enable-breadcrumbs/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                        }
                                    }
                                ?>
                                <a href="<?php echo $seopress_docs_link['support']['breadcrumbs']; ?>" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to enable Breadcrumbs - new window','wp-seopress'); ?></span></a>
                            </span>
                        </div>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-performance"></span>                              
                            </div>
                            <span class="inner">
                                <h3><?php _e('Google Page Speed','wp-seopress'); ?></h3>
                                <p><?php _e('Track your website performance to improve SEO with Google Page Speed','wp-seopress'); ?></p>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_page_speed$3' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                                <?php
                                if (function_exists('seopress_get_locale')) {
                                        if (seopress_get_locale() =='fr') {
                                            $seopress_docs_link['support']['page_speed'] = 'https://www.seopress.org/fr/support/guides/analyser-site-google-page-speed/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                        } else {
                                            $seopress_docs_link['support']['page_speed'] = 'http://www.seopress.org/support/guides/analyse-site-google-page-speed/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                        }
                                    }
                                ?>
                                <a href="<?php echo $seopress_docs_link['support']['page_speed']; ?>" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to analyse your site with Google Page Speed - new window','wp-seopress'); ?></span></a>
                            </span>
                        </div>
                        <?php if (!is_multisite()) { ?>
                            <div class="seopress-feature">
                                <div class="img-tool">
                                    <span class="dashicons dashicons-media-text"></span>                              
                                </div>
                                <span class="inner">
                                    <h3><?php _e('robots.txt','wp-seopress'); ?></h3>
                                    <p><?php _e('Edit your robots.txt file','wp-seopress'); ?></p>
                                    <a href="<?php get_home_url(); ?>/robots.txt" class="button-secondary view-redirects" target="_blank"><?php _e('View your robots.txt','wp-seopress-pro'); ?></a>
                                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_robots$4' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                                    <?php
                                        if(seopress_get_toggle_robots_option()=='1') { 
                                            $seopress_get_toggle_robots_option = '"1"';
                                        } else {
                                            $seopress_get_toggle_robots_option = '"0"';
                                        }
                                    ?>
                                    
                                    <input type="checkbox" name="toggle-robots" id="toggle-robots" class="toggle" data-toggle=<?php echo $seopress_get_toggle_robots_option; ?>>
                                    <label for="toggle-robots"></label>
                                    <?php
                                        if(seopress_get_toggle_robots_option()=='1') { 
                                            echo '<span id="robots-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                            echo '<span id="robots-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                        } else { 
                                            echo '<span id="robots-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                            echo '<span id="robots-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                        }
                                    ?>
                                    <?php
                                    if (function_exists('seopress_get_locale')) {
                                            if (seopress_get_locale() =='fr') {
                                                $seopress_docs_link['support']['robots'] = 'https://www.seopress.org/fr/support/guides/editer-fichier-robots-txt/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                            } else {
                                                $seopress_docs_link['support']['robots'] = 'https://www.seopress.org/support/guides/edit-robots-txt-file/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                            }
                                        }
                                    ?>
                                    <a href="<?php echo $seopress_docs_link['support']['robots']; ?>" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to edit your robots.txt file - new window','wp-seopress'); ?></span></a>
                                </span>
                            </div>
                        <?php } ?>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-admin-post"></span>                              
                            </div>
                            <span class="inner">
                                <h3><?php _e('Google News Sitemap','wp-seopress'); ?></h3>
                                <p><?php _e('Optimize your site for Google News','wp-seopress'); ?></p>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_news$5' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                                <?php
                                    if(seopress_get_toggle_news_option()=='1') { 
                                        $seopress_get_toggle_news_option = '"1"';
                                    } else { 
                                        $seopress_get_toggle_news_option = '"0"';
                                    }
                                ?>
                                <input type="checkbox" name="toggle-news" id="toggle-news" class="toggle" data-toggle=<?php echo $seopress_get_toggle_news_option; ?>>
                                <label for="toggle-news"></label>
                                <?php
                                    if(seopress_get_toggle_news_option()=='1') { 
                                        echo '<span id="news-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                        echo '<span id="news-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    } else { 
                                        echo '<span id="news-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                        echo '<span id="news-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    }
                                ?>
                            </span>
                        </div>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-admin-links"></span>                              
                            </div>
                            <span class="inner">
                                <h3><?php _e('Redirections','wp-seopress'); ?></h3>
                                <p><?php _e('Monitor 404, create 301, 302 and 307 redirections','wp-seopress'); ?></p>
                                <a class="button-secondary view-redirects" href="<?php echo admin_url( 'edit.php?post_type=seopress_404' ); ?>"><?php _e('See redirects','wp-seopress'); ?></a>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_404$6' ); ?>"><?php _e('Manage','wp-seopress'); ?></a><br><br>
                                <?php
                                    if(seopress_get_toggle_404_option()=='1') { 
                                        $seopress_get_toggle_404_option = '"1"';
                                    } else { 
                                        $seopress_get_toggle_404_option = '"0"';
                                    }
                                ?>
                                <input type="checkbox" name="toggle-404" id="toggle-404" class="toggle" data-toggle=<?php echo $seopress_get_toggle_404_option; ?>>
                                <label for="toggle-404"></label>
                                <?php
                                    if(seopress_get_toggle_404_option()=='1') { 
                                        echo '<span id="redirections-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                        echo '<span id="redirections-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                    } else { 
                                        echo '<span id="redirections-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                        echo '<span id="redirections-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                    }
                                ?>
                                <?php
                                if (function_exists('seopress_get_locale')) {
                                        if (seopress_get_locale() =='fr') {
                                            $seopress_docs_link['support']['redirections'] = 'https://www.seopress.org/fr/support/guides/activer-redirections-301-surveillance-404/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                        } else {
                                            $seopress_docs_link['support']['redirections'] = 'http://www.seopress.org/support/guides/redirections/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                        }
                                    }
                                ?>
                                <a href="<?php echo $seopress_docs_link['support']['redirections']; ?>" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to enable 301 redirections and 404 monitoring - new window','wp-seopress'); ?></span></a>
                            </span>
                        </div>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-admin-generic"></span>
                            </div>
                            <span class="inner">
                                <h3><?php _e('SEOPress BOT - Broken links','wp-seopress'); ?></h3>
                                <p><?php _e('Scan your site to find SEO problems.','wp-seopress'); ?></p>
                                <a class="button-secondary view-redirects" href="<?php echo admin_url( 'edit.php?post_type=seopress_bot' ); ?>"><?php _e('See broken links','wp-seopress'); ?></a>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-bot-batch' ); ?>"><?php _e('Scan','wp-seopress'); ?></a>
                            </span>
                        </div>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-admin-links"></span>
                            </div>
                            <span class="inner">
                                <h3><?php _e('Backlinks','wp-seopress'); ?></h3>
                                <p><?php _e('Check your backlinks with Majestic API.','wp-seopress'); ?></p>
                                <a class="button-secondary view-redirects" href="<?php echo admin_url( 'edit.php?post_type=seopress_backlinks' ); ?>"><?php _e('See backlinks','wp-seopress'); ?></a>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_backlinks$12' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                            </span>
                        </div>
                        <?php if (!is_multisite()) { ?>
                            <div class="seopress-feature">
                                <div class="img-tool">
                                    <span class="dashicons dashicons-media-text"></span>                             
                                </div>
                                <span class="inner">
                                    <h3><?php _e('.htaccess','wp-seopress'); ?></h3>
                                    <p><?php _e('Edit your htaccess file.','wp-seopress'); ?></p>
                                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_htaccess$7' ); ?>"><?php _e('Manage','wp-seopress'); ?>
                                    </a>
                                </span>
                            </div>
                        <?php } ?>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-rss"></span>                             
                            </div>
                            <span class="inner">
                                <h3><?php _e('RSS','wp-seopress'); ?></h3>
                                <p><?php _e('Configure default WordPress RSS.','wp-seopress'); ?></p>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_rss$11' ); ?>"><?php _e('Manage','wp-seopress'); ?>
                                </a>
                            </span>
                        </div>
                    <?php } ?>
                    <div class="seopress-feature">
                        <div class="img-tool">
                            <span class="dashicons dashicons-admin-settings"></span>                                   
                        </div>
                        <span class="inner">
                            <h3><?php _e('Tools','wp-seopress'); ?></h3>
                            <p><?php _e('Import / export SEOPress settings from site to site.','wp-seopress'); ?></p>
                            <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-import-export' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                            <?php
                                if (function_exists('seopress_get_locale')) {
                                    if (seopress_get_locale() =='fr') {
                                        $seopress_docs_link['support']['export'] = 'https://www.seopress.org/fr/support/guides/exporter-importer-remise-a-niveau-parametres/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                    } else {
                                        $seopress_docs_link['support']['export'] = 'https://www.seopress.org/support/guides/export-import-reset-settings/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                    }
                                }
                            ?>
                            <a href="<?php echo $seopress_docs_link['support']['export']; ?>" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to export / import / reset settings - new window','wp-seopress'); ?></span></a>
                        </span>
                    </div>
                    <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
                        <div class="seopress-feature">
                            <div class="img-tool">
                                <span class="dashicons dashicons-admin-network"></span>                                   
                            </div>
                            <span class="inner">
                                <h3><?php _e('License','wp-seopress'); ?></h3>
                                <p><?php _e('Edit your SEOPress license key.','wp-seopress'); ?></p>
                                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-license' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                                <?php
                                    if (function_exists('seopress_get_locale')) {
                                        if (seopress_get_locale() =='fr') {
                                            $seopress_docs_link['support']['license'] = 'https://www.seopress.org/fr/support/guides/activer-licence-seopress-pro/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                        } else {
                                            $seopress_docs_link['support']['license'] = 'https://www.seopress.org/support/guides/activate-seopress-pro-license/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                        }
                                    }
                                ?>
                                <a href="<?php echo $seopress_docs_link['support']['license']; ?>" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to activate SEOPress PRO - new window','wp-seopress'); ?></span></a>
                            </span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'seopress_option_group', // Option group
            'seopress_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        register_setting(
            'seopress_titles_option_group', // Option group
            'seopress_titles_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        register_setting(
            'seopress_xml_sitemap_option_group', // Option group
            'seopress_xml_sitemap_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        register_setting(
            'seopress_social_option_group', // Option group
            'seopress_social_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );        

        register_setting(
            'seopress_google_analytics_option_group', // Option group
            'seopress_google_analytics_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        register_setting(
            'seopress_advanced_option_group', // Option group
            'seopress_advanced_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        register_setting(
            'seopress_import_export_option_group', // Option group
            'seopress_import_export_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        //Titles & metas SECTION===================================================================
        add_settings_section( 
            'seopress_setting_section_titles_home', // ID
            '',
            //__("Home","wp-seopress"), // Title
            array( $this, 'print_section_info_titles' ), // Callback
            'seopress-settings-admin-titles-home' // Page
        );  

        add_settings_field(
            'seopress_titles_home_site_title', // ID
           __("Site title","wp-seopress"), // Title
            array( $this, 'seopress_titles_home_site_title_callback' ), // Callback
            'seopress-settings-admin-titles-home', // Page
            'seopress_setting_section_titles_home' // Section                  
        );

        add_settings_field(
            'seopress_titles_home_site_desc', // ID
           __("Meta description","wp-seopress"), // Title
            array( $this, 'seopress_titles_home_site_desc_callback' ), // Callback
            'seopress-settings-admin-titles-home', // Page
            'seopress_setting_section_titles_home' // Section                  
        );

        //Single Post Types SECTION================================================================
        add_settings_section( 
            'seopress_setting_section_titles_single', // ID
            '',
            //__("Single Post Types","wp-seopress"), // Title
            array( $this, 'print_section_info_single' ), // Callback
            'seopress-settings-admin-titles-single' // Page
        );  

        add_settings_field(
            'seopress_titles_single_titles', // ID
            '',
            array( $this, 'seopress_titles_single_titles_callback' ), // Callback
            'seopress-settings-admin-titles-single', // Page
            'seopress_setting_section_titles_single' // Section                  
        );

        //Archives SECTION=========================================================================
        add_settings_section( 
            'seopress_setting_section_titles_archives', // ID
            '',
            //__("Archives","wp-seopress"), // Title
            array( $this, 'print_section_info_archives' ), // Callback
            'seopress-settings-admin-titles-archives' // Page
        );  

        add_settings_field(
            'seopress_titles_archives_titles', // ID
            '',
            array( $this, 'seopress_titles_archives_titles_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_author_title', // ID
            '',
            //__('Title template','wp-seopress'),
            array( $this, 'seopress_titles_archives_author_title_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_author_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            array( $this, 'seopress_titles_archives_author_desc_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_author_noindex', // ID
            '',
            //__("noindex","wp-seopress"), // Title
            array( $this, 'seopress_titles_archives_author_noindex_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                 
        );

        add_settings_field(
            'seopress_titles_archives_author_disable', // ID
            '',
            //__("disable","wp-seopress"), // Title
            array( $this, 'seopress_titles_archives_author_disable_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                 
        );

        add_settings_field(
            'seopress_titles_archives_date_title', // ID
            '',
            //__('Title template','wp-seopress'),
            array( $this, 'seopress_titles_archives_date_title_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_date_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            array( $this, 'seopress_titles_archives_date_desc_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_date_noindex', // ID
            '',
            //__("noindex","wp-seopress"), // Title
            array( $this, 'seopress_titles_archives_date_noindex_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                 
        );

        add_settings_field(
            'seopress_titles_archives_date_disable', // ID
            '',
            //__("disable","wp-seopress"), // Title
            array( $this, 'seopress_titles_archives_date_disable_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                 
        );

        add_settings_field(
            'seopress_titles_archives_search_title', // ID
            '',
            //__('Title template','wp-seopress'),
            array( $this, 'seopress_titles_archives_search_title_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_search_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            array( $this, 'seopress_titles_archives_search_desc_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_search_title_noindex', // ID
            '',
            //__('noindex','wp-seopress'),
            array( $this, 'seopress_titles_archives_search_title_noindex_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_404_title', // ID
            '',
            //__('Title template','wp-seopress'),
            array( $this, 'seopress_titles_archives_404_title_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_404_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            array( $this, 'seopress_titles_archives_404_desc_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        //Taxonomies SECTION=======================================================================
        add_settings_section( 
            'seopress_setting_section_titles_tax', // ID
            '',
            //__("Taxonomies","wp-seopress"), // Title
            array( $this, 'print_section_info_tax' ), // Callback
            'seopress-settings-admin-titles-tax' // Page
        );  

        add_settings_field(
            'seopress_titles_tax_titles', // ID
            '',
            array( $this, 'seopress_titles_tax_titles_callback' ), // Callback
            'seopress-settings-admin-titles-tax', // Page
            'seopress_setting_section_titles_tax' // Section                  
        );

        //Advanced SECTION=========================================================================
        add_settings_section( 
            'seopress_setting_section_titles_advanced', // ID
            '',
            //__("Advanced","wp-seopress"), // Title
            array( $this, 'print_section_info_advanced' ), // Callback
            'seopress-settings-admin-titles-advanced' // Page
        ); 

        add_settings_field(
            'seopress_titles_noindex', // ID
           __("noindex","wp-seopress"), // Title
            array( $this, 'seopress_titles_noindex_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        add_settings_field(
            'seopress_titles_nofollow', // ID
           __("nofollow","wp-seopress"), // Title
            array( $this, 'seopress_titles_nofollow_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        add_settings_field(
            'seopress_titles_noodp', // ID
           __("noodp","wp-seopress"), // Title
            array( $this, 'seopress_titles_noodp_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        add_settings_field(
            'seopress_titles_noimageindex', // ID
           __("noimageindex","wp-seopress"), // Title
            array( $this, 'seopress_titles_noimageindex_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        add_settings_field(
            'seopress_titles_noarchive', // ID
           __("noarchive","wp-seopress"), // Title
            array( $this, 'seopress_titles_noarchive_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        add_settings_field(
            'seopress_titles_nosnippet', // ID
           __("nosnippet","wp-seopress"), // Title
            array( $this, 'seopress_titles_nosnippet_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        add_settings_field(
            'seopress_titles_paged_rel', // ID
           __("Indicate paginated content to Google","wp-seopress"), // Title
            array( $this, 'seopress_titles_paged_rel_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        //XML Sitemap SECTION======================================================================
        add_settings_section( 
            'seopress_setting_section_xml_sitemap_general', // ID
            '',
            //__("General","wp-seopress"), // Title
            array( $this, 'print_section_info_xml_sitemap_general' ), // Callback
            'seopress-settings-admin-xml-sitemap-general' // Page
        ); 

        add_settings_field(
            'seopress_xml_sitemap_general_enable', // ID
           __("Enable XML Sitemap","wp-seopress"), // Title
            array( $this, 'seopress_xml_sitemap_general_enable_callback' ), // Callback
            'seopress-settings-admin-xml-sitemap-general', // Page
            'seopress_setting_section_xml_sitemap_general' // Section                  
        );

        add_settings_field(
            'seopress_xml_sitemap_img_enable', // ID
           __("Enable XML Image Sitemaps","wp-seopress"), // Title
            array( $this, 'seopress_xml_sitemap_img_enable_callback' ), // Callback
            'seopress-settings-admin-xml-sitemap-general', // Page
            'seopress_setting_section_xml_sitemap_general' // Section                  
        );
        
        add_settings_field(
            'seopress_xml_sitemap_html_enable', // ID
           __("Enable HTML Sitemap","wp-seopress"), // Title
            array( $this, 'seopress_xml_sitemap_html_enable_callback' ), // Callback
            'seopress-settings-admin-xml-sitemap-general', // Page
            'seopress_setting_section_xml_sitemap_general' // Section                  
        );

        add_settings_section( 
            'seopress_setting_section_xml_sitemap_post_types', // ID
            '',
            //__("Post Types","wp-seopress"), // Title
            array( $this, 'print_section_info_xml_sitemap_post_types' ), // Callback
            'seopress-settings-admin-xml-sitemap-post-types' // Page
        ); 

        add_settings_field(
            'seopress_xml_sitemap_post_types_list', // ID
           __("Check to INCLUDE Post Types","wp-seopress"), // Title
            array( $this, 'seopress_xml_sitemap_post_types_list_callback' ), // Callback
            'seopress-settings-admin-xml-sitemap-post-types', // Page
            'seopress_setting_section_xml_sitemap_post_types' // Section                  
        );

        add_settings_section( 
            'seopress_setting_section_xml_sitemap_taxonomies', // ID
            '',
            //__("Taxonomies","wp-seopress"), // Title
            array( $this, 'print_section_info_xml_sitemap_taxonomies' ), // Callback
            'seopress-settings-admin-xml-sitemap-taxonomies' // Page
        ); 

        add_settings_field(
            'seopress_xml_sitemap_taxonomies_list', // ID
           __("Check to INCLUDE Taxonomies","wp-seopress"), // Title
            array( $this, 'seopress_xml_sitemap_taxonomies_list_callback' ), // Callback
            'seopress-settings-admin-xml-sitemap-taxonomies', // Page
            'seopress_setting_section_xml_sitemap_taxonomies' // Section                  
        );
        
        add_settings_section( 
            'seopress_setting_section_html_sitemap', // ID
            '',
            //__("HTML Sitemap","wp-seopress"), // Title
            array( $this, 'print_section_info_html_sitemap' ), // Callback
            'seopress-settings-admin-html-sitemap' // Page
        );

        add_settings_field(
            'seopress_xml_sitemap_html_mapping', // ID
           __("Enter a post, page or custom post type ID(s) to display the sitemap","wp-seopress"), // Title
            array( $this, 'seopress_xml_sitemap_html_mapping_callback' ), // Callback
            'seopress-settings-admin-html-sitemap', // Page
            'seopress_setting_section_html_sitemap' // Section                  
        );

        add_settings_field(
            'seopress_xml_sitemap_html_exclude', // ID
           __("Exclude some Posts, Pages or Custom Post Types","wp-seopress"), // Title
            array( $this, 'seopress_xml_sitemap_html_exclude_callback' ), // Callback
            'seopress-settings-admin-html-sitemap', // Page
            'seopress_setting_section_html_sitemap' // Section                  
        );

        add_settings_field(
            'seopress_xml_sitemap_html_date', // ID
           __("Disable the display of the publication date","wp-seopress"), // Title
            array( $this, 'seopress_xml_sitemap_html_date_callback' ), // Callback
            'seopress-settings-admin-html-sitemap', // Page
            'seopress_setting_section_html_sitemap' // Section                  
        );

        //Knowledge graph SECTION======================================================================
        add_settings_section( 
            'seopress_setting_section_social_knowledge', // ID
            '',
            //__("Knowledge graph","wp-seopress"), // Title
            array( $this, 'print_section_info_social_knowledge' ), // Callback
            'seopress-settings-admin-social-knowledge' // Page
        ); 

        add_settings_field(
            'seopress_social_knowledge_type', // ID
           __("Person or organization","wp-seopress"), // Title
            array( $this, 'seopress_social_knowledge_type_callback' ), // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section                  
        );

        add_settings_field(
            'seopress_social_knowledge_name', // ID
           __("Your name / organization","wp-seopress"), // Title
            array( $this, 'seopress_social_knowledge_name_callback' ), // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section                  
        );

        add_settings_field(
            'seopress_social_knowledge_img', // ID
           __("Your photo / organization logo","wp-seopress"), // Title
            array( $this, 'seopress_social_knowledge_img_callback' ), // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section                  
        );

        add_settings_field(
            'seopress_social_knowledge_phone', // ID
           __("Organization Phone number (only for Organization)","wp-seopress"), // Title
            array( $this, 'seopress_social_knowledge_phone_callback' ), // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section                  
        );

        add_settings_field(
            'seopress_social_knowledge_contact_type', // ID
           __("Contact type (only for Organization)","wp-seopress"), // Title
            array( $this, 'seopress_social_knowledge_contact_type_callback' ), // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section                  
        );

        add_settings_field(
            'seopress_social_knowledge_contact_option', // ID
           __("Contact option (only for Organization)","wp-seopress"), // Title
            array( $this, 'seopress_social_knowledge_contact_option_callback' ), // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section                  
        );

        //Social SECTION=====================================================================================
        add_settings_section( 
            'seopress_setting_section_social_accounts', // ID
            '',
            //__("Social","wp-seopress"), // Title
            array( $this, 'print_section_info_social_accounts' ), // Callback
            'seopress-settings-admin-social-accounts' // Page
        ); 

        add_settings_field(
            'seopress_social_accounts_facebook', // ID
           __("Facebook Page URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_facebook_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_twitter', // ID
           __("Twitter Username","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_twitter_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_google', // ID
           __("Google + URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_google_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_pinterest', // ID
           __("Pinterest URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_pinterest_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_instagram', // ID
           __("Instagram URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_instagram_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_youtube', // ID
           __("YouTube URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_youtube_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_linkedin', // ID
           __("LinkedIn URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_linkedin_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_myspace', // ID
           __("MySpace URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_myspace_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_soundcloud', // ID
           __("Soundcloud URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_soundcloud_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_tumblr', // ID
           __("Tumblr URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_tumblr_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        //Facebook SECTION=========================================================================
        add_settings_section( 
            'seopress_setting_section_social_facebook', // ID
            '',
            //__("Facebook","wp-seopress"), // Title
            array( $this, 'print_section_info_social_facebook' ), // Callback
            'seopress-settings-admin-social-facebook' // Page
        );

        add_settings_field(
            'seopress_social_facebook_og', // ID
           __("Enable Open Graph Data","wp-seopress"), // Title
            array( $this, 'seopress_social_facebook_og_callback' ), // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section                  
        );

        add_settings_field(
            'seopress_social_facebook_img', // ID
           __("Select a default image","wp-seopress"), // Title
            array( $this, 'seopress_social_facebook_img_callback' ), // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section                  
        );

        add_settings_field(
            'seopress_social_facebook_link_ownership_id', // ID
           __("Facebook Link Ownership ID","wp-seopress"), // Title
            array( $this, 'seopress_social_facebook_link_ownership_id_callback' ), // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section                  
        );

        add_settings_field(
            'seopress_social_facebook_admin_id', // ID
           __("Facebook Admin ID","wp-seopress"), // Title
            array( $this, 'seopress_social_facebook_admin_id_callback' ), // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section                  
        );

        add_settings_field(
            'seopress_social_facebook_app_id', // ID
           __("Facebook App ID","wp-seopress"), // Title
            array( $this, 'seopress_social_facebook_app_id_callback' ), // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section                  
        );

        //Twitter SECTION==========================================================================
        add_settings_section( 
            'seopress_setting_section_social_twitter', // ID
            '',
            //__("Twitter","wp-seopress"), // Title
            array( $this, 'print_section_info_social_twitter' ), // Callback
            'seopress-settings-admin-social-twitter' // Page
        );

        add_settings_field(
            'seopress_social_twitter_card', // ID
           __("Enable Twitter Card","wp-seopress"), // Title
            array( $this, 'seopress_social_twitter_card_callback' ), // Callback
            'seopress-settings-admin-social-twitter', // Page
            'seopress_setting_section_social_twitter' // Section                  
        );

        add_settings_field(
            'seopress_social_twitter_card_og', // ID
           __("Use Open Graph if no Twitter Cards filled","wp-seopress"), // Title
            array( $this, 'seopress_social_twitter_card_og_callback' ), // Callback
            'seopress-settings-admin-social-twitter', // Page
            'seopress_setting_section_social_twitter' // Section                  
        );     

        add_settings_field(
            'seopress_social_twitter_card_img', // ID
           __("Default Twitter Image","wp-seopress"), // Title
            array( $this, 'seopress_social_twitter_card_img_callback' ), // Callback
            'seopress-settings-admin-social-twitter', // Page
            'seopress_setting_section_social_twitter' // Section                  
        );   

        add_settings_field(
            'seopress_social_twitter_card_img_size', // ID
           __("Image size for Twitter Summary card","wp-seopress"), // Title
            array( $this, 'seopress_social_twitter_card_img_size_callback' ), // Callback
            'seopress-settings-admin-social-twitter', // Page
            'seopress_setting_section_social_twitter' // Section                  
        );

        //Google Analytics Enable SECTION==========================================================
        add_settings_section(
            'seopress_setting_section_google_analytics_enable', // ID
            '',
            //__("Google Analytics","wp-seopress"), // Title
            array( $this, 'print_section_info_google_analytics_enable' ), // Callback
            'seopress-settings-admin-google-analytics-enable' // Page
        );      

        add_settings_field(
            'seopress_google_analytics_enable', // ID
           __("Enable Google Analytics tracking","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_enable_callback' ), // Callback
            'seopress-settings-admin-google-analytics-enable', // Page
            'seopress_setting_section_google_analytics_enable' // Section                  
        );

        add_settings_field(
            'seopress_google_analytics_ua', // ID
           __("Enter your tracking ID","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_ua_callback' ), // Callback
            'seopress-settings-admin-google-analytics-enable', // Page
            'seopress_setting_section_google_analytics_enable' // Section                  
        );        

        add_settings_field(
            'seopress_google_analytics_roles', // ID
           __("Exclude user roles from tracking","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_roles_callback' ), // Callback
            'seopress-settings-admin-google-analytics-enable', // Page
            'seopress_setting_section_google_analytics_enable' // Section                  
        );

        //Google Analytics Tracking SECTION========================================================

        add_settings_section(
            'seopress_setting_section_google_analytics_features', // ID
            '',
            //__("Google Analytics","wp-seopress"), // Title
            array( $this, 'print_section_info_google_analytics_features' ), // Callback
            'seopress-settings-admin-google-analytics-features' // Page
        );

        add_settings_field(
            'seopress_google_analytics_remarketing', // ID
           __("Enable remarketing, demographics and interest reporting","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_remarketing_callback' ), // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section                  
        );

        add_settings_field(
            'seopress_google_analytics_ip_anonymization', // ID
           __("Enable IP Anonymization","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_ip_anonymization_callback' ), // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section                  
        );

        add_settings_field(
            'seopress_google_analytics_link_attribution', // ID
           __("Enhanced Link Attribution","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_link_attribution_callback' ), // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section                  
        );        

        add_settings_field(
            'seopress_google_analytics_cross_domain_enable', // ID
           __("Enable cross-domain tracking","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_cross_enable_callback' ), // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section                  
        );

        add_settings_field(
            'seopress_google_analytics_cross_domain', // ID
           __("Cross domains","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_cross_domain_callback' ), // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section                  
        );

        //Google Analytics Events SECTION==========================================================

        add_settings_section(
            'seopress_setting_section_google_analytics_events', // ID
            '',
            //__("Google Analytics","wp-seopress"), // Title
            array( $this, 'print_section_info_google_analytics_events' ), // Callback
            'seopress-settings-admin-google-analytics-events' // Page
        );  

        add_settings_field(
            'seopress_google_analytics_link_tracking_enable', // ID
           __("Enable external links tracking (all links with target _blank attribute)","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_link_tracking_enable_callback' ), // Callback
            'seopress-settings-admin-google-analytics-events', // Page
            'seopress_setting_section_google_analytics_events' // Section                  
        );

        add_settings_field(
            'seopress_google_analytics_download_tracking_enable', // ID
           __("Enable downloads tracking (eg: PDF, XLSX, DOCX...)","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_download_tracking_enable_callback' ), // Callback
            'seopress-settings-admin-google-analytics-events', // Page
            'seopress_setting_section_google_analytics_events' // Section                  
        );

        add_settings_field(
            'seopress_google_analytics_download_tracking', // ID
           __("Track click downloads","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_download_tracking_callback' ), // Callback
            'seopress-settings-admin-google-analytics-events', // Page
            'seopress_setting_section_google_analytics_events' // Section                  
        );

        add_settings_field(
            'seopress_google_analytics_affiliate_tracking_enable', // ID
           __("Enable affiliate / outbound links tracking (eg: aff, go, out, recommends","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_affiliate_tracking_enable_callback' ), // Callback
            'seopress-settings-admin-google-analytics-events', // Page
            'seopress_setting_section_google_analytics_events' // Section                  
        );

        add_settings_field(
            'seopress_google_analytics_affiliate_tracking', // ID
           __("Track affiliate / outbound links","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_affiliate_tracking_callback' ), // Callback
            'seopress-settings-admin-google-analytics-events', // Page
            'seopress_setting_section_google_analytics_events' // Section                  
        );

        //Google Analytics Custom Dimensions SECTION===============================================

        add_settings_section(
            'seopress_setting_section_google_analytics_custom_dimensions', // ID
            '',
            //__("Google Analytics","wp-seopress"), // Title
            array( $this, 'print_section_info_google_analytics_custom_dimensions' ), // Callback
            'seopress-settings-admin-google-analytics-custom-dimensions' // Page
        );  

        add_settings_field(
            'seopress_google_analytics_cd_author', // ID
           __("Track Authors","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_cd_author_callback' ), // Callback
            'seopress-settings-admin-google-analytics-custom-dimensions', // Page
            'seopress_setting_section_google_analytics_custom_dimensions' // Section                  
        );

        add_settings_field(
            'seopress_google_analytics_cd_category', // ID
           __("Track Categories","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_cd_category_callback' ), // Callback
            'seopress-settings-admin-google-analytics-custom-dimensions', // Page
            'seopress_setting_section_google_analytics_custom_dimensions' // Section                  
        );

        add_settings_field(
            'seopress_google_analytics_cd_tag', // ID
           __("Track Tags","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_cd_tag_callback' ), // Callback
            'seopress-settings-admin-google-analytics-custom-dimensions', // Page
            'seopress_setting_section_google_analytics_custom_dimensions' // Section                  
        );

        add_settings_field(
            'seopress_google_analytics_cd_post_type', // ID
           __("Track Post Types","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_cd_post_type_callback' ), // Callback
            'seopress-settings-admin-google-analytics-custom-dimensions', // Page
            'seopress_setting_section_google_analytics_custom_dimensions' // Section                  
        );

        add_settings_field(
            'seopress_google_analytics_cd_logged_in_user', // ID
           __("Track Logged In Users","wp-seopress"), // Title
            array( $this, 'seopress_google_analytics_cd_logged_in_user_callback' ), // Callback
            'seopress-settings-admin-google-analytics-custom-dimensions', // Page
            'seopress_setting_section_google_analytics_custom_dimensions' // Section                  
        );

        //Advanced SECTION=========================================================================
        add_settings_section( 
            'seopress_setting_section_advanced_advanced', // ID
            '',
            //__("Advanced","wp-seopress"), // Title
            array( $this, 'print_section_info_advanced_advanced' ), // Callback
            'seopress-settings-admin-advanced-advanced' // Page
        ); 

        add_settings_field(
            'seopress_advanced_advanced_attachments', // ID
           __("Redirect attachment pages to post parent","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_attachments_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_tax_desc_editor', // ID
           __("Add WP Editor to taxonomy description textarea","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_tax_desc_editor_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_stop_words', // ID
           __("Remove stop words from URL","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_stop_words_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );        

        add_settings_field(
            'seopress_advanced_advanced_category_url', // ID
           __("Remove /category/ in URL","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_category_url_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_trailingslash', // ID
           __("Disable trailing slash for metas","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_trailingslash_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_wp_generator', // ID
           __("Remove WordPress generator meta tag","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_wp_generator_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_wp_shortlink', // ID
           __("Remove WordPress shortlink meta tag","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_wp_shortlink_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_wp_wlw', // ID
           __("Remove Windows Live Writer meta tag","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_wp_wlw_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_wp_rsd', // ID
           __("Remove RSD meta tag","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_wp_rsd_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_google', // ID
           __("Google site verification","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_google_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_bing', // ID
           __("Bing site verification","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_bing_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );        

        add_settings_field(
            'seopress_advanced_advanced_pinterest', // ID
           __("Pinterest site verification","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_pinterest_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_yandex', // ID
           __("Yandex site verification","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_yandex_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );  

        //Appearance SECTION=======================================================================
        add_settings_section( 
            'seopress_setting_section_advanced_appearance', // ID
            '',
            //__("Appearance","wp-seopress"), // Title
            array( $this, 'print_section_info_advanced_appearance' ), // Callback
            'seopress-settings-admin-advanced-appearance' // Page
        ); 

        add_settings_field(
            'seopress_advanced_appearance_adminbar', // ID
           __("SEOPress in admin bar","wp-seopress"), // Title
            array( $this, 'seopress_advanced_appearance_adminbar_callback' ), // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section                  
        );

        add_settings_field(
            'seopress_advanced_appearance_metabox_position', // ID
           __("Move SEOPress metaboxe position","wp-seopress"), // Title
            array( $this, 'seopress_advanced_appearance_metaboxe_position_callback' ), // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section                  
        );

        add_settings_field(
            'seopress_advanced_appearance_notifications', // ID
           __("Hide SEOPress Notifications Center","wp-seopress"), // Title
            array( $this, 'seopress_advanced_appearance_notifications_callback' ), // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section                  
        );

        add_settings_field(
            'seopress_advanced_appearance_title_col', // ID
           __("Show Title tag column in post types","wp-seopress"), // Title
            array( $this, 'seopress_advanced_appearance_title_col_callback' ), // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section                  
        ); 

        add_settings_field(
            'seopress_advanced_appearance_meta_desc_col', // ID
           __("Show Meta description column in post types","wp-seopress"), // Title
            array( $this, 'seopress_advanced_appearance_meta_desc_col_callback' ), // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section                  
        );

        add_settings_field(
            'seopress_advanced_appearance_canonical', // ID
           __("Show canonical URL column in post types","wp-seopress"), // Title
            array( $this, 'seopress_advanced_appearance_canonical_callback' ), // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section                  
        );

        add_settings_field(
            'seopress_advanced_appearance_target_kw_col', // ID
           __("Show Target Keyword column in post types","wp-seopress"), // Title
            array( $this, 'seopress_advanced_appearance_target_kw_col_callback' ), // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section                  
        );

        add_settings_field(
            'seopress_advanced_appearance_noindex_col', // ID
           __("Show noindex column in post types","wp-seopress"), // Title
            array( $this, 'seopress_advanced_appearance_noindex_col_callback' ), // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section                  
        );

        add_settings_field(
            'seopress_advanced_appearance_nofollow_col', // ID
           __("Show nofollow column in post types","wp-seopress"), // Title
            array( $this, 'seopress_advanced_appearance_nofollow_col_callback' ), // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section                  
        );

        add_settings_field(
            'seopress_advanced_appearance_words_col', // ID
           __("Show total number of words column in post types","wp-seopress"), // Title
            array( $this, 'seopress_advanced_appearance_words_col_callback' ), // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section                  
        );

        add_settings_field(
            'seopress_advanced_appearance_w3c_col', // ID
           __("Show W3C validator column in post types","wp-seopress"), // Title
            array( $this, 'seopress_advanced_appearance_w3c_col_callback' ), // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section                  
        );
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            add_settings_field(
                'seopress_advanced_appearance_ps_col', // ID
               __("Show Google Page Speed column in post types","wp-seopress"), // Title
                array( $this, 'seopress_advanced_appearance_ps_col_callback' ), // Callback
                'seopress-settings-admin-advanced-appearance', // Page
                'seopress_setting_section_advanced_appearance' // Section                  
            );
        }

        add_settings_field(
            'seopress_advanced_appearance_genesis_seo_metaboxe', // ID
           __("Hide Genesis SEO Metaboxe","wp-seopress"), // Title
            array( $this, 'seopress_advanced_appearance_genesis_seo_metaboxe_callback' ), // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section                  
        );

        //Security SECTION=======================================================================
        add_settings_section( 
            'seopress_setting_section_advanced_security', // ID
            '',
            //__("Security","wp-seopress"), // Title
            array( $this, 'print_section_info_advanced_security' ), // Callback
            'seopress-settings-admin-advanced-security' // Page
        ); 

        add_settings_field(
            'seopress_advanced_security_metaboxe_role', // ID
           __("Block SEO metaboxe to user roles","wp-seopress"), // Title
            array( $this, 'seopress_advanced_security_metaboxe_role_callback' ), // Callback
            'seopress-settings-admin-advanced-security', // Page
            'seopress_setting_section_advanced_security' // Section                  
        );

        add_settings_field(
            'seopress_advanced_security_metaboxe_ca_role', // ID
           __("Block Content analysis metaboxe to user roles","wp-seopress-pro"), // Title
            array( $this, 'seopress_advanced_security_metaboxe_ca_role_callback' ), // Callback
            'seopress-settings-admin-advanced-security', // Page
            'seopress_setting_section_advanced_security' // Section                  
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {   

        $seopress_sanitize_fields = array('seopress_titles_home_site_title', 'seopress_titles_home_site_desc', 'seopress_titles_archives_author_title', 'seopress_titles_archives_author_desc', 'seopress_titles_archives_date_title', 'seopress_titles_archives_date_desc', 'seopress_titles_archives_search_title', 'seopress_titles_archives_search_desc', 'seopress_titles_archives_404_title', 'seopress_titles_archives_404_desc', 'seopress_xml_sitemap_html_exclude', 'seopress_social_knowledge_name', 'seopress_social_knowledge_img', 'seopress_social_knowledge_phone', 'seopress_social_accounts_facebook', 'seopress_social_accounts_twitter', 'seopress_social_accounts_google', 'seopress_social_accounts_pinterest', 'seopress_social_accounts_instagram', 'seopress_social_accounts_youtube', 'seopress_social_accounts_linkedin', 'seopress_social_accounts_myspace', 'seopress_social_accounts_soundcloud', 'seopress_social_accounts_tumblr', 'seopress_social_facebook_link_ownership_id', 'seopress_social_facebook_admin_id', 'seopress_social_facebook_app_id', 'seopress_google_analytics_ua', 'seopress_google_analytics_download_tracking' );

        $seopress_sanitize_site_verification = array('seopress_advanced_advanced_google', 'seopress_advanced_advanced_bing', 'seopress_advanced_advanced_pinterest', 'seopress_advanced_advanced_yandex' );
        
        foreach ($seopress_sanitize_fields as $value) {
            if( !empty( $input[$value] ) )
                $input[$value] = sanitize_text_field( $input[$value] );
        }
        
        foreach ($seopress_sanitize_site_verification as $value) {
            if( !empty( $input[$value] ) ) {
                if (preg_match('#content=\'([^"]+)\'#', $input[$value], $m)) {
                    $input[$value] = $m[1];
                } elseif (preg_match('#content="([^"]+)"#', $input[$value], $m)) {
                    $input[$value] = $m[1];
                }
            }
                
        }

        return $input;
    }

    /** 
     * Print the Section text
     */
     
    public function print_section_info_titles()
    {
        print __('<p>Customize your titles & metas for homepage</p>', 'wp-seopress');
    }   

    public function print_section_info_single()
    {
        print __('<p>Customize your titles & metas for Single Custom Post Types</p>', 'wp-seopress');
    }

    public function print_section_info_advanced()
    {
        print __('<p>Customize your metas for all pages</p>', 'wp-seopress');
    }    

    public function print_section_info_tax()
    {
        print __('<p>Customize your metas for all taxonomies archives</p>', 'wp-seopress');
    }    

    public function print_section_info_archives()
    {
        print __('<p>Customize your metas for all archives</p>', 'wp-seopress');
    }     

    public function print_section_info_xml_sitemap_general()
    {
        if (get_option('permalink_structure') =='') {
            echo '<div class="error notice is-dismissable">';
            echo '<p>'. __('Your permalinks are not SEO Friendly! Enable pretty permalinks to fix this.','wp-seopress');
            echo ' <a href="'.admin_url("options-permalink.php").'">'.__('Change this settings','wp-seopress').'</a></p>';
            echo '</div>';
        }
        echo '<p>'.__('To view your sitemap, enable permalinks (not default one), and save settings to flush them.', 'wp-seopress').'</p>';
        echo '<p>'.__('Only the last 1000 items are listed in Sitemaps for performances issues.', 'wp-seopress').'</p>';
        if ( ( isset( $_SERVER['SERVER_SOFTWARE'] ) && stristr( $_SERVER['SERVER_SOFTWARE'], 'nginx' ) === true ) ) { //IF NGINX
            echo '<p>'.__('Your server use NGINX. If XML Sitemaps doesn\'t work, you have to add this rules to your configuration:', 'wp-seopress').'</p><br>';
            echo '<pre style="margin:0;padding:10px;font-weight: bold;background:#F3F3F3;display:inline-block;width: 100%">
                location ~ ([^/]*)sitemap(.*)\.x(m|s)l$ {              
                    ## SEOPress
                    rewrite ^/sitemaps\.xml$ /index.php?seopress_sitemap=1 last;
                    rewrite ^/sitemaps_xsl\.xsl$ /index.php?seopress_sitemap_xsl=1 last;  
                    rewrite ^/sitemaps/([_0-9a-zA-Z-]+)?\.xml$ /index.php?seopress_cpt=$1 last;
                }
            </pre>';
        }
        echo '<p>'.__('Noindex content will not be displayed in Sitemaps.', 'wp-seopress').'</p><br>';

        echo '<a href="'.home_url().'/sitemaps.xml" target="_blank" class="button"><span class="dashicons dashicons-visibility"></span>'.__('View your sitemap','wp-seopress').'</a>';
        echo '&nbsp;';
        echo '<a href="http://www.google.com/ping?sitemap='.home_url().'/sitemaps/" target="_blank" class="button"><span class="dashicons dashicons-share-alt2"></span>'.__('Ping Google manually','wp-seopress').'</a>';        
        echo '&nbsp;';
        echo '<button id="seopress-flush-permalinks" class="button"><span class="dashicons dashicons-admin-links"></span>'.__('Flush permalinks','wp-seopress').'</button>';
        echo '<span class="spinner"></span>';
    } 

    public function print_section_info_html_sitemap()
    {
        print __('<p>Create an HTML for your visitors and boost your SEO.</p>', 'wp-seopress');

        if (function_exists('seopress_get_locale')) {
            if (seopress_get_locale() =='fr') {
                $seopress_docs_link['sitemaps']['html'] = 'https://www.seopress.org/fr/support/guides/activer-plan-de-site-html/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            } else {
                $seopress_docs_link['sitemaps']['html'] = 'https://www.seopress.org/support/guides/enable-html-sitemap/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            }
        }

        echo '<a class="seopress-doc" href="'.$seopress_docs_link['sitemaps']['html'].'" target="_blank"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">'. __('Guide to enable HTML Sitemap - new window','wp-seopress').'</span></a></p>';
    }

    public function print_section_info_xml_sitemap_post_types()
    {
        print __('<p>Include / Exclude Post Types.</p>', 'wp-seopress');
    }

    public function print_section_info_xml_sitemap_taxonomies()
    {
        print __('<p>Include / Exclude Taxonomies.</p>', 'wp-seopress');
    }    

    public function print_section_info_social_knowledge()
    {
        print __('<p>Configure Google Knowledge Graph.</p>', 'wp-seopress');
    }     

    public function print_section_info_social_accounts()
    {
        print __('<p>Link your site with your social accounts.</p>', 'wp-seopress');
    }    

    public function print_section_info_social_facebook()
    {
        print __('<p>Manage Open Graph datas.</p>', 'wp-seopress');
    }    

    public function print_section_info_social_twitter()
    {
        print __('<p>Manage your Twitter card.</p>', 'wp-seopress');
    }    

    public function print_section_info_google_analytics_enable()
    {
        print __('<p>Link your Google Analytics to your website.</p>', 'wp-seopress');
    }

    public function print_section_info_google_analytics_features()
    {
        print __('<p>Configure your Google Analytics tracking code.</p>', 'wp-seopress');
    }    
    
    public function print_section_info_google_analytics_events()
    {
        print __('<p>Track events in Google Analytics.</p>', 'wp-seopress');
    }    

    public function print_section_info_google_analytics_custom_dimensions()
    {
        print __('<p>Configure your Google Analytics custom dimensions. <br>Custom dimensions and custom metrics are like default dimensions and metrics in your Analytics account,<br> except you create them yourself. You can use them to collect and analyze data that Analytics doesn\'t automatically track.<br> Notice that you have to setup your custom dimensions in your Google Analytics account too. More info by clicking on help icon.', 'wp-seopress');
        
        if (function_exists('seopress_get_locale')) {
            if (seopress_get_locale() =='fr') {
                $seopress_docs_link['support']['analytics']['custom_dimensions'] = 'https://www.seopress.org/fr/support/guides/creer-dimensions-personnalisees-google-analytics/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            } else {
                $seopress_docs_link['support']['analytics']['custom_dimensions'] = 'https://www.seopress.org/support/guides/create-custom-dimension-google-analytics/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            }
        }
                        
        echo '<a class="seopress-doc" href="'.$seopress_docs_link['support']['analytics']['custom_dimensions'].'" target="_blank"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">'. __('Guide to create custom dimensions in Google Analytics - new window','wp-seopress').'</span></a></p>';
    }

    public function print_section_info_advanced_advanced()
    {
        print __('<p>Advanced SEO options.</p>', 'wp-seopress');
    }

    public function print_section_info_advanced_appearance()
    {
        print __('<p>Customize SEOPress to fit your needs.</p>', 'wp-seopress');
    } 

    public function print_section_info_advanced_security()
    {
        print __('<p>Manage security.</p>', 'wp-seopress');
    }    

    /** 
     * Get the settings option array and print one of its values
     */
    
    //Titles & metas
    public function seopress_titles_home_site_title_callback()
    {
        printf(
            '<input type="text" id="seopress_titles_home_site_title" name="seopress_titles_option_name[seopress_titles_home_site_title]" placeholder="'.esc_html__('My awesome website','wp-seopress').'" aria-label="'.__('Site title','wp-seopress').'" value="%s"/>',
            esc_html( $this->options['seopress_titles_home_site_title'])
        );        
        echo '<div class="wrap-tags"><span id="seopress-tag-site-title" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Site Title','wp-seopress').'</span>';
        echo '<span id="seopress-tag-site-desc" data-tag="%%tagline%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Tagline','wp-seopress').'</span>';
        echo '<span id="seopress-quick-help" class="tag-title more-tags"><span class="dashicons dashicons-menu"></span>'.__('More tags','wp-seopress').'</span></div>';
    }

    public function seopress_titles_home_site_desc_callback()
    {
        printf(
        '<textarea id="seopress_titles_home_site_desc" name="seopress_titles_option_name[seopress_titles_home_site_desc]" placeholder="'.esc_html__('This is a cool website about Wookiees','wp-seopress').'" aria-label="'.__('Meta description','wp-seopress').'">%s</textarea>',
        esc_html( $this->options['seopress_titles_home_site_desc'])
        
        );
        echo '<div class="wrap-tags"><span id="seopress-tag-meta-desc" data-tag="%%tagline%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Tagline','wp-seopress').'</span>';
        echo '<span id="seopress-quick-help" class="tag-title more-tags"><span class="dashicons dashicons-menu"></span>'.__('More tags','wp-seopress').'</span></div>';

        if (get_option( 'page_for_posts' )) {
            echo '<p><a href="'.admin_url('post.php?post='.get_option( 'page_for_posts' ).'&action=edit').'">'.__('Looking for editing your blog page?','wp-seopress').'</a></p>';
        }
    }

    //Single CPT
    public function seopress_titles_single_titles_callback()
    {
        foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
            
            echo '<h2>'.$seopress_cpt_value->labels->name.'</h2>';

            //Single Title CPT
            echo '<div class="seopress_wrap_single_cpt">';

                _e('Title template','wp-seopress');

                $check = isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['title']) ? $this->options['seopress_titles_single_titles'][$seopress_cpt_key]['title'] : NULL;

                echo "<br/>";

                echo "<script>
                    jQuery(document).ready(function($) {
                        jQuery('#seopress-tag-single-title-".$seopress_cpt_key."').click(function() {
                            jQuery('#seopress_titles_single_titles_".$seopress_cpt_key."').val(jQuery('#seopress_titles_single_titles_".$seopress_cpt_key."').val() + ' ' + jQuery('#seopress-tag-single-title-".$seopress_cpt_key."').attr('data-tag'));
                        });
                        jQuery('#seopress-tag-single-sitetitle-".$seopress_cpt_key."').click(function() {
                            jQuery('#seopress_titles_single_titles_".$seopress_cpt_key."').val(jQuery('#seopress_titles_single_titles_".$seopress_cpt_key."').val() + ' ' + jQuery('#seopress-tag-single-sitetitle-".$seopress_cpt_key."').attr('data-tag'));
                        });
                    });
                </script>";

                printf(
                '<input type="text" id="seopress_titles_single_titles_'.$seopress_cpt_key.'" name="seopress_titles_option_name[seopress_titles_single_titles]['.$seopress_cpt_key.'][title]" value="%s"/>',
                esc_html( $check )   
                );

                echo '<div class="wrap-tags"><span id="seopress-tag-single-title-'.$seopress_cpt_key.'" data-tag="%%post_title%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Post Title','wp-seopress').'</span>';

                echo '<span id="seopress-tag-single-sitetitle-'.$seopress_cpt_key.'" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Site Title','wp-seopress').'</span>';

                echo '<span id="seopress-quick-help" class="tag-title more-tags"><span class="dashicons dashicons-menu"></span>'.__('More tags','wp-seopress').'</span></div>';

            echo '</div>';

            //Single Meta Description CPT
            echo '<div class="seopress_wrap_single_cpt">';
                
                _e('Meta description template','wp-seopress');
                echo "<br/>";
                
                $check = isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['description']) ? $this->options['seopress_titles_single_titles'][$seopress_cpt_key]['description'] : NULL;

                printf(
                '<textarea name="seopress_titles_option_name[seopress_titles_single_titles]['.$seopress_cpt_key.'][description]">%s</textarea>',
                esc_html( $check )
                );

            echo '</div>';

            //Single No-Index CPT
            echo '<div class="seopress_wrap_single_cpt">';

                $options = get_option( 'seopress_titles_option_name' );  
            
                $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']);      
                
                echo '<input id="seopress_titles_single_cpt_noindex['.$seopress_cpt_key.']" name="seopress_titles_option_name[seopress_titles_single_titles]['.$seopress_cpt_key.'][noindex]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_titles_single_cpt_noindex['.$seopress_cpt_key.']">'. __( 'noindex', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['noindex'])) {
                    esc_attr( $this->options['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']);
                }

            echo '</div>';

            //Single No-Follow CPT
            echo '<div class="seopress_wrap_single_cpt">';

                $options = get_option( 'seopress_titles_option_name' );  
            
                $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow']);      
                
                echo '<input id="seopress_titles_single_cpt_nofollow['.$seopress_cpt_key.']" name="seopress_titles_option_name[seopress_titles_single_titles]['.$seopress_cpt_key.'][nofollow]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_titles_single_cpt_nofollow['.$seopress_cpt_key.']">'. __( 'nofollow', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow'])) {
                    esc_attr( $this->options['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow']);
                }

            echo '</div>';

            //Single Published / modified date CPT
            echo '<div class="seopress_wrap_single_cpt">';

                $options = get_option( 'seopress_titles_option_name' );  
            
                $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['date']);      
                
                echo '<input id="seopress_titles_single_cpt_date['.$seopress_cpt_key.']" name="seopress_titles_option_name[seopress_titles_single_titles]['.$seopress_cpt_key.'][date]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_titles_single_cpt_date['.$seopress_cpt_key.']">'. __( 'Display date in Google search results?', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['date'])) {
                    esc_attr( $this->options['seopress_titles_single_titles'][$seopress_cpt_key]['date']);
                }

            echo '</div>';
        }
    }

    //Taxonomies
    public function seopress_titles_tax_titles_callback()
    {
        foreach (seopress_get_taxonomies() as $seopress_tax_key => $seopress_tax_value) {
            
            $check = isset($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['title']) ? $this->options['seopress_titles_tax_titles'][$seopress_tax_key]['title'] : NULL;

            echo '<h2>'.$seopress_tax_value->labels->name.'</h2>';

            //Tax Title
            echo '<div class="seopress_wrap_tax">';

                _e('Title template','wp-seopress');
                echo "<br/>";

                echo "<script>
                    jQuery(document).ready(function($) {
                        jQuery('#seopress-tag-tax-title-".$seopress_tax_key."').click(function() {
                            jQuery('#seopress_titles_tax_titles_".$seopress_tax_key."').val(jQuery('#seopress_titles_tax_titles_".$seopress_tax_key."').val() + ' ' + jQuery('#seopress-tag-tax-title-".$seopress_tax_key."').attr('data-tag'));
                        });
                        jQuery('#seopress-tag-tax-sitetitle-".$seopress_tax_key."').click(function() {
                            jQuery('#seopress_titles_tax_titles_".$seopress_tax_key."').val(jQuery('#seopress_titles_tax_titles_".$seopress_tax_key."').val() + ' ' + jQuery('#seopress-tag-tax-sitetitle-".$seopress_tax_key."').attr('data-tag'));
                        });
                    });
                </script>";

                printf(
                '<input type="text" id="seopress_titles_tax_titles_'.$seopress_tax_key.'" name="seopress_titles_option_name[seopress_titles_tax_titles]['.$seopress_tax_key.'][title]" value="%s"/>',
                esc_html($check)   
                );

                if ($seopress_tax_key =='category') {
                      echo '<div class="wrap-tags"><span id="seopress-tag-tax-title-'.$seopress_tax_key.'" data-tag="%%_category_title%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Category Title','wp-seopress').'</span>';
                } elseif ($seopress_tax_key =='post_tag') {
                    echo '<div class="wrap-tags"><span id="seopress-tag-tax-title-'.$seopress_tax_key.'" data-tag="%%tag_title%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Tag Title','wp-seopress').'</span>';
                } else {
                    echo '<div class="wrap-tags"><span id="seopress-tag-tax-title-'.$seopress_tax_key.'" data-tag="%%term_title%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Term Title','wp-seopress').'</span>';
                }

                echo '<span id="seopress-tag-tax-sitetitle-'.$seopress_tax_key.'" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Site Title','wp-seopress').'</span>';

                echo '<span id="seopress-quick-help" class="tag-title more-tags"><span class="dashicons dashicons-menu"></span>'.__('More tags','wp-seopress').'</span></div>';

            echo '</div>';

            //Tax Meta Description
            echo '<div class="seopress_wrap_tax">';
                
                $check2 = isset($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['description']) ? $this->options['seopress_titles_tax_titles'][$seopress_tax_key]['description'] : NULL;

                _e('Meta description template','wp-seopress');
                echo "<br/>";

                echo "<script>
                    jQuery(document).ready(function($) {
                        jQuery('#seopress-tag-tax-desc-".$seopress_tax_key."').click(function() {
                            jQuery('#seopress_titles_tax_desc_".$seopress_tax_key."').val(jQuery('#seopress_titles_tax_desc_".$seopress_tax_key."').val() + ' ' + jQuery('#seopress-tag-tax-desc-".$seopress_tax_key."').attr('data-tag'));
                        });
                    });
                </script>";

                printf(
                '<textarea id="seopress_titles_tax_desc_'.$seopress_tax_key.'" name="seopress_titles_option_name[seopress_titles_tax_titles]['.$seopress_tax_key.'][description]">%s</textarea>',
                esc_html($check2)
                );

                if ($seopress_tax_key =='category') {
                      echo '<div class="wrap-tags"><span id="seopress-tag-tax-desc-'.$seopress_tax_key.'" data-tag="%%_category_description%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Category Description','wp-seopress').'</span>';
                } elseif ($seopress_tax_key =='post_tag') {
                    echo '<div class="wrap-tags"><span id="seopress-tag-tax-desc-'.$seopress_tax_key.'" data-tag="%%tag_description%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Tag Description','wp-seopress').'</span>';
                } else {
                    echo '<div class="wrap-tags"><span id="seopress-tag-tax-desc-'.$seopress_tax_key.'" data-tag="%%term_description%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Term Description','wp-seopress').'</span>';
                }

                echo '<span id="seopress-quick-help" class="tag-title more-tags"><span class="dashicons dashicons-menu"></span>'.__('More tags','wp-seopress').'</span></div>';

            echo '</div>';

            //Tax No-Index
            echo '<div class="seopress_wrap_tax">';

                $options = get_option( 'seopress_titles_option_name' );  
            
                $check = isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']);      
                
                echo '<input id="seopress_titles_tax_noindex['.$seopress_tax_key.']" name="seopress_titles_option_name[seopress_titles_tax_titles]['.$seopress_tax_key.'][noindex]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_titles_tax_noindex['.$seopress_tax_key.']">'. __( 'noindex', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['noindex'])) {
                    esc_attr( $this->options['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']);
                }

            echo '</div>';

            //Tax No-Follow
            echo '<div class="seopress_wrap_tax">';

                $options = get_option( 'seopress_titles_option_name' );  
            
                $check = isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow']);      
                
                echo '<input id="seopress_titles_tax_nofollow['.$seopress_tax_key.']" name="seopress_titles_option_name[seopress_titles_tax_titles]['.$seopress_tax_key.'][nofollow]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_titles_tax_nofollow['.$seopress_tax_key.']">'. __( 'nofollow', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow'])) {
                    esc_attr( $this->options['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow']);
                }

            echo '</div>';
        }
    }

    //Archives
    public function seopress_titles_archives_titles_callback()
    {
        foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
            if (!in_array($seopress_cpt_key, array('post','page'))) {

                $check = isset($this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['title']) ? $this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['title'] : NULL;

                echo '<h2>'.$seopress_cpt_value->labels->name.'</h2>';

                //Archive Title CPT
                echo '<div class="seopress_wrap_archive_cpt">';

                    _e('Title template','wp-seopress');
                    echo "<br/>";

                    echo "<script>
                        jQuery(document).ready(function($) {
                            jQuery('#seopress-tag-archive-title-".$seopress_cpt_key."').click(function() {
                                jQuery('#seopress_titles_archive_titles_".$seopress_cpt_key."').val(jQuery('#seopress_titles_archive_titles_".$seopress_cpt_key."').val() + ' ' + jQuery('#seopress-tag-archive-title-".$seopress_cpt_key."').attr('data-tag'));
                            });
                            jQuery('#seopress-tag-archive-sitetitle-".$seopress_cpt_key."').click(function() {
                                jQuery('#seopress_titles_archive_titles_".$seopress_cpt_key."').val(jQuery('#seopress_titles_archive_titles_".$seopress_cpt_key."').val() + ' ' + jQuery('#seopress-tag-archive-sitetitle-".$seopress_cpt_key."').attr('data-tag'));
                            });
                        });
                    </script>";

                    printf(
                    '<input type="text" id="seopress_titles_archive_titles_'.$seopress_cpt_key.'" name="seopress_titles_option_name[seopress_titles_archive_titles]['.$seopress_cpt_key.'][title]" value="%s"/>',
                    esc_html($check)   
                    );

                    echo '<div class="wrap-tags"><span id="seopress-tag-archive-title-'.$seopress_cpt_key.'" data-tag="%%cpt_plural%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Post Type Archive Name','wp-seopress').'</span>';

                    echo '<span id="seopress-tag-archive-sitetitle-'.$seopress_cpt_key.'" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Site Title','wp-seopress').'</span>';

                    echo '<span id="seopress-quick-help" class="tag-title more-tags"><span class="dashicons dashicons-menu"></span>'.__('More tags','wp-seopress').'</span></div>';

                echo '</div>';

                //Archive Meta Description CPT
                echo '<div class="seopress_wrap_archive_cpt">';
                    
                    _e('Meta description template','wp-seopress');
                    echo "<br/>";

                    $check = isset($this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['description']) ? $this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['description'] : NULL;

                    printf(
                    '<textarea name="seopress_titles_option_name[seopress_titles_archive_titles]['.$seopress_cpt_key.'][description]">%s</textarea>',
                    esc_html( $check )
                    );

                echo '</div>';

                //Archive No-Index CPT
                echo '<div class="seopress_wrap_archive_cpt">';

                    $options = get_option( 'seopress_titles_option_name' );  
                
                    $check = isset($options['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex']);      
                    
                    echo '<input id="seopress_titles_archive_cpt_noindex['.$seopress_cpt_key.']" name="seopress_titles_option_name[seopress_titles_archive_titles]['.$seopress_cpt_key.'][noindex]" type="checkbox"';
                    if ('1' == $check) echo 'checked="yes"'; 
                    echo ' value="1"/>';
                    
                    echo '<label for="seopress_titles_archive_cpt_noindex['.$seopress_cpt_key.']">'. __( 'noindex', 'wp-seopress' ) .'</label>';
                    
                    if (isset($this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex'])) {
                        esc_attr( $this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex']);
                    }

                echo '</div>';

                //Archive No-Follow CPT
                echo '<div class="seopress_wrap_archive_cpt">';

                    $options = get_option( 'seopress_titles_option_name' );  
                
                    $check = isset($options['seopress_titles_archive_titles'][$seopress_cpt_key]['nofollow']);      
                    
                    echo '<input id="seopress_titles_archive_cpt_nofollow['.$seopress_cpt_key.']" name="seopress_titles_option_name[seopress_titles_archive_titles]['.$seopress_cpt_key.'][nofollow]" type="checkbox"';
                    if ('1' == $check) echo 'checked="yes"'; 
                    echo ' value="1"/>';
                    
                    echo '<label for="seopress_titles_archive_cpt_nofollow['.$seopress_cpt_key.']">'. __( 'nofollow', 'wp-seopress' ) .'</label>';
                    
                    if (isset($this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['nofollow'])) {
                        esc_attr( $this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['nofollow']);
                    }

                echo '</div>';
            }
        }
    }


    public function seopress_titles_archives_author_title_callback()
    {
        echo '<h2>'.__('Author archives','wp-seopress').'</h2>';
        
        _e('Title template','wp-seopress');
        echo "<br/>";
            
        printf(
        '<input id="seopress_titles_archive_post_author" type="text" name="seopress_titles_option_name[seopress_titles_archives_author_title]" value="%s"/>',
        esc_html( $this->options['seopress_titles_archives_author_title'])
        
        );

        echo '<div class="wrap-tags"><span id="seopress-tag-post-author" data-tag="%%post_author%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Post author','wp-seopress').'</span>';
        echo '<span id="seopress-tag-site-title-author" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Site Title','wp-seopress').'</span>';
        echo '<span id="seopress-quick-help" class="tag-title more-tags"><span class="dashicons dashicons-menu"></span>'.__('More tags','wp-seopress').'</span></div>';
        
    }

    public function seopress_titles_archives_author_desc_callback()
    {
        _e('Meta description template','wp-seopress');
        echo "<br/>";
        
        $check = isset($this->options['seopress_titles_archives_author_desc']) ? $this->options['seopress_titles_archives_author_desc'] : NULL;

        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_author_desc]">%s</textarea>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_titles_archives_author_noindex_callback()
    {

        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_archives_author_noindex']);      
        
        echo '<input id="seopress_titles_archives_author_noindex" name="seopress_titles_option_name[seopress_titles_archives_author_noindex]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_archives_author_noindex">'. __( 'noindex', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_titles_archives_author_noindex'])) {
            esc_attr( $this->options['seopress_titles_archives_author_noindex']);
        }  
    }

    public function seopress_titles_archives_author_disable_callback()
    {

        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_archives_author_disable']);      
        
        echo '<input id="seopress_titles_archives_author_disable" name="seopress_titles_option_name[seopress_titles_archives_author_disable]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_archives_author_disable">'. __( 'Disable author archives', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_titles_archives_author_disable'])) {
            esc_attr( $this->options['seopress_titles_archives_author_disable']);
        }  
    }

    public function seopress_titles_archives_date_title_callback()
    {
        echo '<h2>'.__('Date archives','wp-seopress').'</h2>';
        
        _e('Title template','wp-seopress');
        echo "<br/>";

        printf(
        '<input id="seopress_titles_archives_date_title" type="text" name="seopress_titles_option_name[seopress_titles_archives_date_title]" value="%s"/>',
        esc_html( $this->options['seopress_titles_archives_date_title'])
        
        );

        echo '<div class="wrap-tags"><span id="seopress-tag-archive-date" data-tag="%%archive_date%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Archive date','wp-seopress').'</span>';
        echo '<span id="seopress-tag-site-title-date" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Site Title','wp-seopress').'</span>';
        echo '<span id="seopress-quick-help" class="tag-title more-tags"><span class="dashicons dashicons-menu"></span>'.__('More tags','wp-seopress').'</span></div>';
        
    }

    public function seopress_titles_archives_date_desc_callback()
    {        
        _e('Meta description template','wp-seopress');
        echo "<br/>";
        
        $check = isset($this->options['seopress_titles_archives_date_desc']) ? $this->options['seopress_titles_archives_date_desc'] : NULL;

        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_date_desc]">%s</textarea>',
        esc_html( $check )
        
        );
    }

    public function seopress_titles_archives_date_noindex_callback()
    {

        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_archives_date_noindex']);      
        
        echo '<input id="seopress_titles_archives_date_noindex" name="seopress_titles_option_name[seopress_titles_archives_date_noindex]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_archives_date_noindex">'. __( 'noindex', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_titles_archives_date_noindex'])) {
            esc_attr( $this->options['seopress_titles_archives_date_noindex']);
        }  
    }

    public function seopress_titles_archives_date_disable_callback()
    {

        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_archives_date_disable']);      
        
        echo '<input id="seopress_titles_archives_date_disable" name="seopress_titles_option_name[seopress_titles_archives_date_disable]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_archives_date_disable">'. __( 'Disable date archives', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_titles_archives_date_disable'])) {
            esc_attr( $this->options['seopress_titles_archives_date_disable']);
        }  
    }

    public function seopress_titles_archives_search_title_callback()
    {
        echo '<h2>'.__('Search archives','wp-seopress').'</h2>';
        
        _e('Title template','wp-seopress');
        echo "<br/>";

        printf(
        '<input id="seopress_titles_archives_search_title" type="text" name="seopress_titles_option_name[seopress_titles_archives_search_title]" value="%s"/>',
        esc_html( $this->options['seopress_titles_archives_search_title'])
        
        );

        echo '<div class="wrap-tags"><span id="seopress-tag-search-keywords" data-tag="%%search_keywords%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Search Keywords','wp-seopress').'</span>';
        echo '<span id="seopress-tag-site-title-search" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Site Title','wp-seopress').'</span>';
        echo '<span id="seopress-quick-help" class="tag-title more-tags"><span class="dashicons dashicons-menu"></span>'.__('More tags','wp-seopress').'</span></div>';
        
    }

    public function seopress_titles_archives_search_desc_callback()
    {        
        _e('Meta description template','wp-seopress');
        echo "<br/>";

        $check = isset($this->options['seopress_titles_archives_search_desc']) ? $this->options['seopress_titles_archives_search_desc'] : NULL;
        
        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_search_desc]">%s</textarea>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_titles_archives_search_title_noindex_callback()
    {

        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_archives_search_title_noindex']);      
        
        echo '<input id="seopress_titles_archives_search_title_noindex" name="seopress_titles_option_name[seopress_titles_archives_search_title_noindex]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_archives_search_title_noindex">'. __( 'noindex', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_titles_archives_search_title_noindex'])) {
            esc_attr( $this->options['seopress_titles_archives_search_title_noindex']);
        }  
    }

    public function seopress_titles_archives_404_title_callback()
    {
        echo '<h2>'.__('404 archives','wp-seopress').'</h2>';
        
        _e('Title template','wp-seopress');
        echo "<br/>";

        printf(
        '<input id="seopress_titles_archives_404_title" type="text" name="seopress_titles_option_name[seopress_titles_archives_404_title]" value="%s"/>',
        esc_html( $this->options['seopress_titles_archives_404_title'])
        
        );
        echo '<div class="wrap-tags"><span id="seopress-tag-site-title-404" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Site Title','wp-seopress').'</span>';
        echo '<span id="seopress-quick-help" class="tag-title more-tags"><span class="dashicons dashicons-menu"></span>'.__('More tags','wp-seopress').'</span></div>';
        
    }

    public function seopress_titles_archives_404_desc_callback()
    {        
        _e('Meta description template','wp-seopress');
        echo "<br/>";
        
        $check = isset($this->options['seopress_titles_archives_404_desc']) ? $this->options['seopress_titles_archives_404_desc'] : NULL;

        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_404_desc]">%s</textarea>',
        esc_html( $check )
        
        );
        
    }

    //Advanced
    public function seopress_titles_noindex_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_noindex']);      
        
        echo '<input id="seopress_titles_noindex" name="seopress_titles_option_name[seopress_titles_noindex]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_noindex">'. __( 'noindex', 'wp-seopress' ) .'</label>';
        
        echo '<p class="description">'.__('Do not display all pages of the site in Google search results and do not display "Cached" links in search results.','wp-seopress').'</p>';
        
        if (isset($this->options['seopress_titles_noindex'])) {
            esc_attr( $this->options['seopress_titles_noindex']);
        }
    }

    public function seopress_titles_nofollow_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_nofollow']);      
        
        echo '<input id="seopress_titles_nofollow" name="seopress_titles_option_name[seopress_titles_nofollow]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_nofollow">'. __( 'nofollow', 'wp-seopress' ) .'</label>';
        
        echo '<p class="description">'.__('Do not follow links for all pages.','wp-seopress').'</p>';
        
        if (isset($this->options['seopress_titles_nofollow'])) {
            esc_attr( $this->options['seopress_titles_nofollow']);
        }
    }

    public function seopress_titles_noodp_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_noodp']);      
        
        echo '<input id="seopress_titles_noodp" name="seopress_titles_option_name[seopress_titles_noodp]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_noodp">'. __( 'noodp', 'wp-seopress' ) .'</label>';
        
        echo '<p class="description">'.__('Do not use Open Directory project metadata for titles or excerpts for all pages.','wp-seopress').'</p>';
        
        if (isset($this->options['seopress_titles_noodp'])) {
            esc_attr( $this->options['seopress_titles_noodp']);
        }
    }

    public function seopress_titles_noimageindex_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_noimageindex']);      
        
        echo '<input id="seopress_titles_noimageindex" name="seopress_titles_option_name[seopress_titles_noimageindex]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_noimageindex">'. __( 'noimageindex', 'wp-seopress' ) .'</label>';
        
        echo '<p class="description">'.__('Do not index images from the entire site.','wp-seopress').'</p>';
        
        if (isset($this->options['seopress_titles_noimageindex'])) {
            esc_attr( $this->options['seopress_titles_noimageindex']);
        }
    }

    public function seopress_titles_noarchive_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_noarchive']);      
        
        echo '<input id="seopress_titles_noarchive" name="seopress_titles_option_name[seopress_titles_noarchive]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_noarchive">'. __( 'noarchive', 'wp-seopress' ) .'</label>';
        
        echo '<p class="description">'.__('Do not display a "Cached" link in the Google search results.','wp-seopress').'</p>';
        
        if (isset($this->options['seopress_titles_noarchive'])) {
            esc_attr( $this->options['seopress_titles_noarchive']);
        }
    }

    public function seopress_titles_nosnippet_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_nosnippet']);      
        
        echo '<input id="seopress_titles_nosnippet" name="seopress_titles_option_name[seopress_titles_nosnippet]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_nosnippet">'. __( 'nosnippet', 'wp-seopress' ) .'</label>';
        
        echo '<p class="description">'.__('Do not display a description in the Google search results for all pages.','wp-seopress').'</p>';
        
        if (isset($this->options['seopress_titles_nosnippet'])) {
            esc_attr( $this->options['seopress_titles_nosnippet']);
        }
    }

    public function seopress_titles_paged_rel_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_paged_rel']);      
        
        echo '<input id="seopress_titles_paged_rel" name="seopress_titles_option_name[seopress_titles_paged_rel]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_paged_rel">'. __( 'Add rel next/prev link in head of paginated archive pages', 'wp-seopress' ) .'</label>';
        
        echo '<p class="description"><a href="https://support.google.com/webmasters/answer/1663744?hl=en" target="_blank">'.__('Learn more on Google website','wp-seopress').'</p>';
        
        if (isset($this->options['seopress_titles_paged_rel'])) {
            esc_attr( $this->options['seopress_titles_paged_rel']);
        }
    }

    public function seopress_xml_sitemap_general_enable_callback()
    {
        $options = get_option( 'seopress_xml_sitemap_option_name' );  
        
        $check = isset($options['seopress_xml_sitemap_general_enable']);      
        
        echo '<input id="seopress_xml_sitemap_general_enable" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_general_enable]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_xml_sitemap_general_enable">'. __( 'Enable XML Sitemap', 'wp-seopress' ) .'</label>';
        
        if (function_exists('seopress_get_locale')) {
            if (seopress_get_locale() =='fr') {
                $seopress_docs_link['support']['sitemaps'] = 'https://www.seopress.org/fr/support/guides/activer-sitemap-xml/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            } else {
                $seopress_docs_link['support']['sitemaps'] = 'https://www.seopress.org/support/guides/enable-xml-sitemaps/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            }
        }

        echo '<a href="'.$seopress_docs_link['support']['sitemaps'].'" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">'. __('Guide to enable XML Sitemaps - new window','wp-seopress').'</span></a>';

        if (isset($this->options['seopress_xml_sitemap_general_enable'])) {
            esc_attr( $this->options['seopress_xml_sitemap_general_enable']);
        }
    }

    public function seopress_xml_sitemap_img_enable_callback()
    {
        $options = get_option( 'seopress_xml_sitemap_option_name' );  
        
        $check = isset($options['seopress_xml_sitemap_img_enable']);      
        
        echo '<input id="seopress_xml_sitemap_img_enable" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_img_enable]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_xml_sitemap_img_enable">'. __( 'Enable Image Sitemaps', 'wp-seopress' ) .'</label>';

        if (function_exists('seopress_get_locale')) {
            if (seopress_get_locale() =='fr') {
                $seopress_docs_link['support']['sitemaps']['image'] = 'https://www.seopress.org/fr/support/guides/activer-sitemap-xml-images/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            } else {
                $seopress_docs_link['support']['sitemaps']['image'] = 'https://www.seopress.org/support/guides/enable-xml-image-sitemaps/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            }
        }

        echo '<a href="'.$seopress_docs_link['support']['sitemaps']['image'].'" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">'. __('Guide to enable XML image sitemaps - new window','wp-seopress').'</span></a>';
        
        if (isset($this->options['seopress_xml_sitemap_img_enable'])) {
            esc_attr( $this->options['seopress_xml_sitemap_img_enable']);
        }
    }

    public function seopress_xml_sitemap_html_enable_callback()
    {
        $options = get_option( 'seopress_xml_sitemap_option_name' );  
        
        $check = isset($options['seopress_xml_sitemap_html_enable']);      
        
        echo '<input id="seopress_xml_sitemap_html_enable" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_enable]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_xml_sitemap_html_enable">'. __( 'Enable HTML Sitemap', 'wp-seopress' ) .'</label>';

        if (function_exists('seopress_get_locale')) {
            if (seopress_get_locale() =='fr') {
                $seopress_docs_link['support']['sitemaps']['html'] = 'https://www.seopress.org/fr/support/guides/activer-plan-de-site-html/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            } else {
                $seopress_docs_link['support']['sitemaps']['html'] = 'https://www.seopress.org/support/guides/enable-html-sitemap/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            }
        }

        echo '<a href="'.$seopress_docs_link['support']['sitemaps']['html'].'" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">'. __('Guide to enable HTML Sitemap - new window','wp-seopress').'</span></a>';
        
        if (isset($this->options['seopress_xml_sitemap_html_enable'])) {
            esc_attr( $this->options['seopress_xml_sitemap_html_enable']);
        }
    }

    public function seopress_xml_sitemap_post_types_list_callback()
    {
        $options = get_option( 'seopress_xml_sitemap_option_name' );  
        
        $check = isset($options['seopress_xml_sitemap_post_types_list']);      
        
        global $wp_post_types;

        $args = array(
            'show_ui' => true,
            'public' => true,
        );

        $output = 'objects'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'

        $post_types = get_post_types( $args, $output, $operator ); 

        foreach ($post_types as $seopress_cpt_key => $seopress_cpt_value) {
            
            echo '<h2>'.$seopress_cpt_value->labels->name.'</h2>';

            //List all post types
            echo '<div class="seopress_wrap_single_cpt">';

                $options = get_option( 'seopress_xml_sitemap_option_name' );  
                
                $check = isset($options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include']);      
                
                echo '<input id="seopress_xml_sitemap_post_types_list_include['.$seopress_cpt_key.']" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_post_types_list]['.$seopress_cpt_key.'][include]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_xml_sitemap_post_types_list_include['.$seopress_cpt_key.']">'. __( 'Include', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include'])) {
                    esc_attr( $this->options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include']);
                }

            echo '</div>';
        }
    }

    public function seopress_xml_sitemap_taxonomies_list_callback()
    {
        $options = get_option( 'seopress_xml_sitemap_option_name' );  
        
        $check = isset($options['seopress_xml_sitemap_taxonomies_list']);      
        
        $args = array(
            'show_ui' => true,
            'public' => true,
          
        ); 
        $output = 'objects'; // or objects
        $operator = 'and'; // 'and' or 'or'
        $taxonomies = get_taxonomies( $args, $output, $operator );  

        foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) {
            
            echo '<h2>'.$seopress_tax_value->labels->name.'</h2>';

            //List all taxonomies
            echo '<div class="seopress_wrap_single_tax">';

                $options = get_option( 'seopress_xml_sitemap_option_name' );  
            
                $check = isset($options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include']);      
                
                echo '<input id="seopress_xml_sitemap_taxonomies_list_include['.$seopress_tax_key.']" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_taxonomies_list]['.$seopress_tax_key.'][include]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_xml_sitemap_taxonomies_list_include['.$seopress_tax_key.']">'. __( 'Include', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include'])) {
                    esc_attr( $this->options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include']);
                }

            echo '</div>';
        }
    }

    public function seopress_xml_sitemap_html_mapping_callback()
    {
        $check = isset($this->options['seopress_xml_sitemap_html_mapping']) ? $this->options['seopress_xml_sitemap_html_mapping'] : NULL;

        printf(
        '<input type="text" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_mapping]" placeholder="'.esc_html__('eg: 2, 28, 68','wp-seopress').'" aria-label="'.__('Enter a post, page or custom post type ID(s) to display the sitemap','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        );

        echo '<br><br><p>'.__('You can also use this shortcode:','wp-seopress').'</p>';

        echo '<pre>[seopress_html_sitemap]</pre>';
    }

    public function seopress_xml_sitemap_html_exclude_callback()
    {
        $check = isset($this->options['seopress_xml_sitemap_html_exclude']) ? $this->options['seopress_xml_sitemap_html_exclude'] : NULL;

        printf(
        '<input type="text" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_exclude]" placeholder="'.esc_html__('eg: 13, 8, 38','wp-seopress').'" aria-label="'.__('Exclude some Posts, Pages or Custom Post Types','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        );
    }

    public function seopress_xml_sitemap_html_date_callback()
    {
        $options = get_option( 'seopress_xml_sitemap_option_name' );  
        
        $check = isset($options['seopress_xml_sitemap_html_date']);      
        
        echo '<input id="seopress_xml_sitemap_html_date" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_date]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_xml_sitemap_html_date">'. __( 'Disable date after each post, page, post type?', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_xml_sitemap_html_date'])) {
            esc_attr( $this->options['seopress_xml_sitemap_html_date']);
        }
    }

    public function seopress_social_knowledge_type_callback()
    {
        $options = get_option( 'seopress_social_option_name' );    
        
        $selected = isset($options['seopress_social_knowledge_type']) ? $options['seopress_social_knowledge_type'] : NULL;
                
        echo '<select id="seopress_social_knowledge_type" name="seopress_social_option_name[seopress_social_knowledge_type]">';
            echo ' <option '; 
                if ('Person' == $selected) echo 'selected="selected"'; 
                echo ' value="Person">'. __("Person","wp-seopress") .'</option>';
            echo '<option '; 
                if ('Organization' == $selected) echo 'selected="selected"'; 
                echo ' value="Organization">'. __("Organization","wp-seopress") .'</option>';
        echo '</select>';

        if (isset($this->options['seopress_social_knowledge_type'])) {
            esc_attr( $this->options['seopress_social_knowledge_type']);
        }
    }

    public function seopress_social_knowledge_name_callback()
    {
        $check = isset($this->options['seopress_social_knowledge_name']) ? $this->options['seopress_social_knowledge_name'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_knowledge_name]" placeholder="'.esc_html__('eg: Apple','wp-seopress').'" aria-label="'.__('Your name / organization','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        );
    }

    public function seopress_social_knowledge_img_callback() 
    {
        $options = get_option( 'seopress_social_option_name' );
        
        $options_set = isset($options['seopress_social_knowledge_img']) ? $options['seopress_social_knowledge_img'] : NULL;
        
        $check = isset($options['seopress_social_knowledge_img']);      

        echo '<input id="seopress_social_knowledge_img_meta" type="text" value="'.$options_set.'" name="seopress_social_option_name[seopress_social_knowledge_img]" aria-label="'.__('Your photo / organization logo','wp-seopress').'" placeholder="'.esc_html__('Select your logo','wp-seopress').'"  />
        
        <input id="seopress_social_knowledge_img_upload" class="button" type="button" value="'.__('Upload an Image','wp-seopress').'" />';
        
        if (isset($this->options['seopress_social_knowledge_img'])) {
            esc_attr( $this->options['seopress_social_knowledge_img']);
        }

        function seopress_social_knowledge_img_option() {
            $seopress_social_knowledge_img_option = get_option("seopress_social_option_name");
            if ( ! empty ( $seopress_social_knowledge_img_option ) ) {
                foreach ($seopress_social_knowledge_img_option as $key => $seopress_social_knowledge_img_value)
                    $options[$key] = $seopress_social_knowledge_img_value;
                 if (isset($seopress_social_knowledge_img_option['seopress_social_knowledge_img'])) { 
                    return $seopress_social_knowledge_img_option['seopress_social_knowledge_img'];
                 }
            }
        }
        echo '<br>';
        echo '<br>';
        echo '<img style="width:200px;max-height:300px;" src="'.seopress_social_knowledge_img_option().'"/>';
    }

    public function seopress_social_knowledge_phone_callback()
    {
        $check = isset($this->options['seopress_social_knowledge_phone']) ? $this->options['seopress_social_knowledge_phone'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_knowledge_phone]" placeholder="'.esc_html__('eg: +33123456789 (internationalized version required)','wp-seopress').'" aria-label="'.__('Organization Phone number (only for Organization)','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        );
        
    }

    public function seopress_social_knowledge_contact_type_callback()
    {
        $options = get_option( 'seopress_social_option_name' );    
        
        $selected = isset($options['seopress_social_knowledge_contact_type']) ? $options['seopress_social_knowledge_contact_type'] : NULL;
                
        echo '<select id="seopress_social_knowledge_contact_type" name="seopress_social_option_name[seopress_social_knowledge_contact_type]">';
            echo ' <option '; 
                if ('customer support' == $selected) echo 'selected="selected"'; 
                echo ' value="customer support">'. __("Customer support","wp-seopress") .'</option>';
            echo '<option '; 
                if ('technical support' == $selected) echo 'selected="selected"'; 
                echo ' value="technical support">'. __("Technical support","wp-seopress") .'</option>';
            echo '<option '; 
                if ('billing support' == $selected) echo 'selected="selected"'; 
                echo ' value="billing support">'. __("Billing support","wp-seopress") .'</option>';
            echo '<option '; 
                if ('bill payment' == $selected) echo 'selected="selected"'; 
                echo ' value="bill payment">'. __("Bill payment","wp-seopress") .'</option>';
            echo '<option '; 
                if ('sales' == $selected) echo 'selected="selected"'; 
                echo ' value="sales">'. __("Sales","wp-seopress") .'</option>';
            echo '<option '; 
                if ('credit card support' == $selected) echo 'selected="selected"'; 
                echo ' value="credit card support">'. __("Credit card support","wp-seopress") .'</option>';
            echo '<option '; 
                if ('emergency' == $selected) echo 'selected="selected"'; 
                echo ' value="emergency">'. __("Emergency","wp-seopress") .'</option>';
            echo '<option '; 
                if ('baggage tracking' == $selected) echo 'selected="selected"'; 
                echo ' value="baggage tracking">'. __("Baggage tracking","wp-seopress") .'</option>';
            echo '<option '; 
                if ('roadside assistance' == $selected) echo 'selected="selected"'; 
                echo ' value="roadside assistance">'. __("Roadside assistance","wp-seopress") .'</option>';
            echo '<option '; 
                if ('package tracking' == $selected) echo 'selected="selected"'; 
                echo ' value="package tracking">'. __("Package tracking","wp-seopress") .'</option>';
        echo '</select>';

        if (isset($this->options['seopress_social_knowledge_contact_type'])) {
            esc_attr( $this->options['seopress_social_knowledge_contact_type']);
        }
    }

    public function seopress_social_knowledge_contact_option_callback()
    {
        $options = get_option( 'seopress_social_option_name' );    
        
        $selected = isset($options['seopress_social_knowledge_contact_option']) ? $options['seopress_social_knowledge_contact_option'] : NULL;
        
        echo '<select id="seopress_social_knowledge_contact_option" name="seopress_social_option_name[seopress_social_knowledge_contact_option]">';
            echo ' <option '; 
                if ('TollFree' == $selected) echo 'selected="selected"'; 
                echo ' value="TollFree">'. __("Toll Free","wp-seopress") .'</option>';
            echo '<option '; 
                if ('HearingImpairedSupported' == $selected) echo 'selected="selected"'; 
                echo ' value="HearingImpairedSupported">'. __("Hearing impaired supported","wp-seopress") .'</option>';
        echo '</select>';

        if (isset($this->options['seopress_social_knowledge_contact_option'])) {
            esc_attr( $this->options['seopress_social_knowledge_contact_option']);
        }
    }

    public function seopress_social_accounts_facebook_callback()
    {
        $check = isset($this->options['seopress_social_accounts_facebook']) ? $this->options['seopress_social_accounts_facebook'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_facebook]" placeholder="'.esc_html__('eg: https://www.facebook.com/your-page','wp-seopress').'" aria-label="'.__('Facebook Page URL','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_social_accounts_twitter_callback()
    {
        
        $check = isset($this->options['seopress_social_accounts_twitter']) ? $this->options['seopress_social_accounts_twitter'] : NULL;
        
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_twitter]" placeholder="'.esc_html__('eg: @wp_seopress','wp-seopress').'" aria-label="'.__('Twitter Page URL','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_social_accounts_google_callback()
    {
        $check = isset($this->options['seopress_social_accounts_google']) ? $this->options['seopress_social_accounts_google'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_google]" placeholder="'.esc_html__('eg: https://plus.google.com/+BenjaminDenis','wp-seopress').'" aria-label="'.__('Google + URL','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }    

    public function seopress_social_accounts_pinterest_callback()
    {
        $check = isset($this->options['seopress_social_accounts_pinterest']) ? $this->options['seopress_social_accounts_pinterest'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_pinterest]" placeholder="'.esc_html__('eg: https://pinterest.com/wpbuy/','wp-seopress').'" aria-label="'.__('Pinterest URL','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_social_accounts_instagram_callback()
    {
        $check = isset($this->options['seopress_social_accounts_instagram']) ? $this->options['seopress_social_accounts_instagram'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_instagram]" placeholder="'.esc_html__('eg: https://www.instagram.com/wp_seopress/','wp-seopress').'" aria-label="'.__('Instagram URL','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_social_accounts_youtube_callback()
    {
        $check = isset($this->options['seopress_social_accounts_youtube']) ? $this->options['seopress_social_accounts_youtube'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_youtube]" placeholder="'.esc_html__('eg: https://www.youtube.com/channel/UCpQzarWu55UzCIH7-OW6pwA','wp-seopress').'" aria-label="'.__('YouTube URL','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_social_accounts_linkedin_callback()
    {
        $check = isset($this->options['seopress_social_accounts_linkedin']) ? $this->options['seopress_social_accounts_linkedin'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_linkedin]" placeholder="'.esc_html__('eg: https://www.linkedin.com/in/benjamin-denis-70672b3b','wp-seopress').'" aria-label="'.__('LinkedIn URL','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_social_accounts_myspace_callback()
    {
        $check = isset($this->options['seopress_social_accounts_myspace']) ? $this->options['seopress_social_accounts_myspace'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_myspace]" aria-label="'.__('MySpace URL','wp-seopress').'" placeholder="'.esc_html__('eg: https://myspace.com/your-page','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_social_accounts_soundcloud_callback()
    {
        $check = isset($this->options['seopress_social_accounts_soundcloud']) ? $this->options['seopress_social_accounts_soundcloud'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_soundcloud]" aria-label="'.__('Soundcloud URL','wp-seopress').'" placeholder="'.esc_html__('eg: https://soundcloud.com/michaelmccannmusic','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_social_accounts_tumblr_callback()
    {
        $check = isset($this->options['seopress_social_accounts_tumblr']) ? $this->options['seopress_social_accounts_tumblr'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_tumblr]" placeholder="'.esc_html__('eg: https://your-site.tumblr.com','wp-seopress').'" aria-label="'.__('Tumblr URL','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_social_facebook_og_callback()
    {
        $options = get_option( 'seopress_social_option_name' );  
        
        $check = isset($options['seopress_social_facebook_og']);      
        
        echo '<input id="seopress_social_facebook_og" name="seopress_social_option_name[seopress_social_facebook_og]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_social_facebook_og">'. __( 'Enable OG data', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_social_facebook_og'])) {
            esc_attr( $this->options['seopress_social_facebook_og']);
        }
    }    

    public function seopress_social_facebook_img_callback()
    {
        $options = get_option( 'seopress_social_option_name' );  
        
        $options_set = isset($options['seopress_social_facebook_img']) ? $options['seopress_social_facebook_img'] : NULL;
        
        $check = isset($options['seopress_social_facebook_img']);      

        echo '<input id="seopress_social_fb_img_meta" type="text" value="'.$options_set.'" name="seopress_social_option_name[seopress_social_facebook_img]" aria-label="'.__('Select a default image','wp-seopress').'" placeholder="'.esc_html__('Select your default thumbnail','wp-seopress').'"  />
        
        <input id="seopress_social_fb_img_upload" class="button" type="button" value="'.__('Upload an Image','wp-seopress').'" />';
        
        if (isset($this->options['seopress_social_facebook_img'])) {
            esc_attr( $this->options['seopress_social_facebook_img']);
        }
    }

    public function seopress_social_facebook_link_ownership_id_callback()
    {
        $check = isset($this->options['seopress_social_facebook_link_ownership_id']) ? $this->options['seopress_social_facebook_link_ownership_id'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_facebook_link_ownership_id]" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_social_facebook_admin_id_callback()
    {
        $check = isset($this->options['seopress_social_facebook_admin_id']) ? $this->options['seopress_social_facebook_admin_id'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_facebook_admin_id]" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_social_facebook_app_id_callback()
    {
        $check = isset($this->options['seopress_social_facebook_app_id']) ? $this->options['seopress_social_facebook_app_id'] : NULL;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_facebook_app_id]" value="%s"/>',
        esc_html( $check )
        
        );

        echo '<br><br><span class="dashicons dashicons-external"></span><a href="https://developers.facebook.com/docs/apps/register" target="_blank">'.__('How-to create a Facebook App ID','wp-seopress').'</a>';
        
    }

    public function seopress_social_twitter_card_callback()
    {
        $options = get_option( 'seopress_social_option_name' );  
        
        $check = isset($options['seopress_social_twitter_card']);      
        
        echo '<input id="seopress_social_twitter_card" name="seopress_social_option_name[seopress_social_twitter_card]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_social_twitter_card">'. __( 'Enable Twitter card', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_social_twitter_card'])) {
            esc_attr( $this->options['seopress_social_twitter_card']);
        }
    }

    public function seopress_social_twitter_card_og_callback()
    {
        $options = get_option( 'seopress_social_option_name' );  
        
        $check = isset($options['seopress_social_twitter_card_og']);      
        
        echo '<input id="seopress_social_twitter_card_og" name="seopress_social_option_name[seopress_social_twitter_card_og]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_social_twitter_card_og">'. __( 'Use OG if no Twitter Cards', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_social_twitter_card_og'])) {
            esc_attr( $this->options['seopress_social_twitter_card_og']);
        }
    }

    public function seopress_social_twitter_card_img_callback()
    {      
        $options = get_option( 'seopress_social_option_name' );
        
        $options_set = isset($options['seopress_social_twitter_card_img']) ? $options['seopress_social_twitter_card_img'] : NULL;
        
        $check = isset($options['seopress_social_twitter_card_img']);      

        echo '<input id="seopress_social_twitter_img_meta" type="text" value="'.$options_set.'" name="seopress_social_option_name[seopress_social_twitter_card_img]" aria-label="'.__('Default Twitter Image','wp-seopress').'" placeholder="'.esc_html__('Select your default thumbnail','wp-seopress').'"  />
        
        <input id="seopress_social_twitter_img_upload" class="button" type="button" value="'.__('Upload an Image','wp-seopress').'" />';
        
        if (isset($this->options['seopress_social_twitter_card_img'])) {
            esc_attr( $this->options['seopress_social_twitter_card_img']);
        }
    }

    public function seopress_social_twitter_card_img_size_callback()
    {      
        $options = get_option( 'seopress_social_option_name' );    
        
        $selected = isset($options['seopress_social_twitter_card_img_size']) ? $options['seopress_social_twitter_card_img_size'] : NULL;
                
        echo '<select id="seopress_social_twitter_card_img_size" name="seopress_social_option_name[seopress_social_twitter_card_img_size]">';
            echo ' <option '; 
                if ('default' == $selected) echo 'selected="selected"'; 
                echo ' value="default">'. __("Default","wp-seopress") .'</option>';
            echo '<option '; 
                if ('large' == $selected) echo 'selected="selected"'; 
                echo ' value="large">'. __("Large","wp-seopress") .'</option>';
        echo '</select>';

        if (isset($this->options['seopress_social_twitter_card_img_size'])) {
            esc_attr( $this->options['seopress_social_twitter_card_img_size']);
        }
    }

    public function seopress_google_analytics_enable_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );  
        
        $check = isset($options['seopress_google_analytics_enable']);      
        
        echo '<input id="seopress_google_analytics_enable" name="seopress_google_analytics_option_name[seopress_google_analytics_enable]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_google_analytics_enable">'. __( 'Enable Google Analytics tracking (Global Site Tag: gtag.js)', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_google_analytics_enable'])) {
            esc_attr( $this->options['seopress_google_analytics_enable']);
        }
    }

    public function seopress_google_analytics_ua_callback()
    {
        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_ua]" placeholder="'.esc_html__('Enter your Tracking ID (UA-XXXX-XX)','wp-seopress').'" aria-label="'.__('Enter your tracking ID','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_google_analytics_ua'])
        );

        echo '<p class="description"><a href="https://support.google.com/analytics/answer/1032385?hl=en" target="_blank">'.__('Find your tracking ID','wp-seopress').'</a></p>';
        
    }

    public function seopress_google_analytics_roles_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );  
        
        global $wp_roles;

        if ( ! isset( $wp_roles ) )
            $wp_roles = new WP_Roles();
    
        foreach ($wp_roles->get_names() as $key => $value) {

            $check = isset($options['seopress_google_analytics_roles'][$key]);  

            echo '<input id="seopress_google_analytics_roles_'.$key.'" name="seopress_google_analytics_option_name[seopress_google_analytics_roles]['.$key.']" type="checkbox"';
            if ('1' == $check) echo 'checked="yes"'; 
            echo ' value="1"/>';
            
            echo '<label for="seopress_google_analytics_roles_'.$key.'">'. $value .'</label><br/>';

            if (isset($this->options['seopress_google_analytics_roles'][$key])) {
                esc_attr( $this->options['seopress_google_analytics_roles'][$key]);
            }
        }
    }

    public function seopress_google_analytics_remarketing_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );  
        
        $check = isset($options['seopress_google_analytics_remarketing']);      
        
        echo '<input id="seopress_google_analytics_remarketing" name="seopress_google_analytics_option_name[seopress_google_analytics_remarketing]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_google_analytics_remarketing">'. __( 'Enable remarketing, demographics and interest reporting', 'wp-seopress' ) .'</label>';

        echo '<p class="description">'. __('A remarketing audience is a list of cookies or mobile-advertising IDs that represents a group of users you want to re-engage because of their likelihood to convert.','wp-seopress').'
            <a href="https://support.google.com/analytics/answer/2611268?hl=en" target="_blank">'.__('Learn more','wp-seopress').'</a>
            </p>';

        if (isset($this->options['seopress_google_analytics_remarketing'])) {
            esc_attr( $this->options['seopress_google_analytics_remarketing']);
        }
    }

    public function seopress_google_analytics_ip_anonymization_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );  
        
        $check = isset($options['seopress_google_analytics_ip_anonymization']);      
        
        echo '<input id="seopress_google_analytics_ip_anonymization" name="seopress_google_analytics_option_name[seopress_google_analytics_ip_anonymization]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_google_analytics_ip_anonymization">'. __( 'Enable IP Anonymization', 'wp-seopress' ) .'</label>';

        echo '<p class="description">'. __('When a customer of Analytics requests IP address anonymization, Analytics anonymizes the address as soon as technically feasible at the earliest possible stage of the collection network.','wp-seopress').'
            <a href="https://support.google.com/analytics/answer/2763052?hl=en" target="_blank">'.__('Learn more','wp-seopress').'</a>
            </p>';

        if (isset($this->options['seopress_google_analytics_ip_anonymization'])) {
            esc_attr( $this->options['seopress_google_analytics_ip_anonymization']);
        }
    }

    public function seopress_google_analytics_link_attribution_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );  
        
        $check = isset($options['seopress_google_analytics_link_attribution']);      
        
        echo '<input id="seopress_google_analytics_link_attribution" name="seopress_google_analytics_option_name[seopress_google_analytics_link_attribution]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_google_analytics_link_attribution">'. __( 'Enhanced Link Attribution', 'wp-seopress' ) .'</label>';

        echo '<p class="description">'. __('Enhanced Link Attribution improves the accuracy of your In-Page Analytics report by automatically differentiating between multiple links to the same URL on a single page by using link element IDs.','wp-seopress').'
            <a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-link-attribution" target="_blank">'.__('Learn more','wp-seopress').'</a>
            </p>';

        if (isset($this->options['seopress_google_analytics_link_attribution'])) {
            esc_attr( $this->options['seopress_google_analytics_link_attribution']);
        }
    }    

    public function seopress_google_analytics_cross_enable_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );  
        
        $check = isset($options['seopress_google_analytics_cross_enable']);      
        
        echo '<input id="seopress_google_analytics_cross_enable" name="seopress_google_analytics_option_name[seopress_google_analytics_cross_enable]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_google_analytics_cross_enable">'. __( 'Enable cross-domain tracking', 'wp-seopress' ) .'</label>';

        echo '<p class="description">'. __('Cross domain tracking makes it possible for Analytics to see sessions on two related sites (such as an ecommerce site and a separate shopping cart site) as a single session. This is sometimes called site linking.','wp-seopress').'
            <a href="https://support.google.com/analytics/answer/1034342?hl=en" target="_blank">'.__('Learn more','wp-seopress').'</a>
            </p>';

        if (isset($this->options['seopress_google_analytics_cross_enable'])) {
            esc_attr( $this->options['seopress_google_analytics_cross_enable']);
        }
    }

    public function seopress_google_analytics_cross_domain_callback()
    {
        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_cross_domain]" placeholder="'.esc_html__('Enter yours domains (eg: https://www.seopress.org/)','wp-seopress').'" value="%s" aria-label="'.__('Cross domains','wp-seopress').'"/>',
        esc_html($this->options['seopress_google_analytics_cross_domain'])
        
        );
        
    }

    public function seopress_google_analytics_link_tracking_enable_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );  
        
        $check = isset($options['seopress_google_analytics_link_tracking_enable']);      
        
        echo '<input id="seopress_google_analytics_link_tracking_enable" name="seopress_google_analytics_option_name[seopress_google_analytics_link_tracking_enable]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_google_analytics_link_tracking_enable">'. __( 'Enable external links tracking', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_google_analytics_link_tracking_enable'])) {
            esc_attr( $this->options['seopress_google_analytics_link_tracking_enable']);
        }
    }

    public function seopress_google_analytics_download_tracking_enable_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );  
        
        $check = isset($options['seopress_google_analytics_download_tracking_enable']);      
        
        echo '<input id="seopress_google_analytics_download_tracking_enable" name="seopress_google_analytics_option_name[seopress_google_analytics_download_tracking_enable]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_google_analytics_download_tracking_enable">'. __( 'Enable download tracking', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_google_analytics_download_tracking_enable'])) {
            esc_attr( $this->options['seopress_google_analytics_download_tracking_enable']);
        }
    }

    public function seopress_google_analytics_download_tracking_callback()
    {
        $check = isset($this->options['seopress_google_analytics_download_tracking']) ? $this->options['seopress_google_analytics_download_tracking'] : NULL;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_download_tracking]" placeholder="'.esc_html__('pdf|docx|pptx|zip','wp-seopress').'" aria-label="'.__('Track click downloads','wp-seopress').'" value="%s"/>',
        esc_html( $check)
        );

        echo '<p class="description">'. __('Separate each file type extensions with a pipe "|"','wp-seopress').'</a>
            </p>';
        
    }

    public function seopress_google_analytics_affiliate_tracking_enable_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );  
        
        $check = isset($options['seopress_google_analytics_affiliate_tracking_enable']);      
        
        echo '<input id="seopress_google_analytics_affiliate_tracking_enable" name="seopress_google_analytics_option_name[seopress_google_analytics_affiliate_tracking_enable]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_google_analytics_affiliate_tracking_enable">'. __( 'Enable affiliate / outbound tracking', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_google_analytics_affiliate_tracking_enable'])) {
            esc_attr( $this->options['seopress_google_analytics_affiliate_tracking_enable']);
        }
    }

    public function seopress_google_analytics_affiliate_tracking_callback()
    {
        $check = isset($this->options['seopress_google_analytics_affiliate_tracking']) ? $this->options['seopress_google_analytics_affiliate_tracking'] : NULL;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_affiliate_tracking]" placeholder="'.esc_html__('aff|go|out','wp-seopress').'" aria-label="'.__('Track affiliate / outbound links','wp-seopress').'" value="%s"/>',
        esc_html( $check)
        );

        echo '<p class="description">'. __('Separate each keyword with a pipe "|"','wp-seopress').'</a>
            </p>';
        
    }

    public function seopress_google_analytics_cd_author_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );    
        
        $selected = $options['seopress_google_analytics_cd_author'];
        
        echo '<select id="seopress_google_analytics_cd_author" name="seopress_google_analytics_option_name[seopress_google_analytics_cd_author]">';
            echo ' <option '; 
                if ('none' == $selected) echo 'selected="selected"'; 
                echo ' value="none">'. __("None","wp-seopress") .'</option>';
            echo ' <option '; 
                if ('dimension1' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension1">'. __("Custom Dimension #1","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension2' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension2">'. __("Custom Dimension #2","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension3' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension3">'. __("Custom Dimension #3","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension4' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension4">'. __("Custom Dimension #4","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension5' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension5">'. __("Custom Dimension #5","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension6' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension6">'. __("Custom Dimension #6","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension7' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension7">'. __("Custom Dimension #7","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension8' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension8">'. __("Custom Dimension #8","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension9' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension9">'. __("Custom Dimension #9","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension10' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension10">'. __("Custom Dimension #10","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension11' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension11">'. __("Custom Dimension #11","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension12' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension12">'. __("Custom Dimension #12","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension13' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension13">'. __("Custom Dimension #13","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension14' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension14">'. __("Custom Dimension #14","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension15' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension15">'. __("Custom Dimension #15","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension16' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension16">'. __("Custom Dimension #16","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension17' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension17">'. __("Custom Dimension #17","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension18' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension18">'. __("Custom Dimension #18","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension19' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension19">'. __("Custom Dimension #19","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension20' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension20">'. __("Custom Dimension #20","wp-seopress") .'</option>';
        echo '</select>';

        if (isset($this->options['seopress_google_analytics_cd_author'])) {
            esc_attr( $this->options['seopress_google_analytics_cd_author']);
        }
    }

    public function seopress_google_analytics_cd_category_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );    
        
        $selected = $options['seopress_google_analytics_cd_category'];
        
        echo '<select id="seopress_google_analytics_cd_category" name="seopress_google_analytics_option_name[seopress_google_analytics_cd_category]">';
            echo ' <option '; 
                if ('none' == $selected) echo 'selected="selected"'; 
                echo ' value="none">'. __("None","wp-seopress") .'</option>';
            echo ' <option '; 
                if ('dimension1' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension1">'. __("Custom Dimension #1","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension2' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension2">'. __("Custom Dimension #2","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension3' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension3">'. __("Custom Dimension #3","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension4' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension4">'. __("Custom Dimension #4","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension5' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension5">'. __("Custom Dimension #5","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension6' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension6">'. __("Custom Dimension #6","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension7' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension7">'. __("Custom Dimension #7","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension8' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension8">'. __("Custom Dimension #8","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension9' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension9">'. __("Custom Dimension #9","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension10' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension10">'. __("Custom Dimension #10","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension11' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension11">'. __("Custom Dimension #11","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension12' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension12">'. __("Custom Dimension #12","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension13' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension13">'. __("Custom Dimension #13","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension14' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension14">'. __("Custom Dimension #14","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension15' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension15">'. __("Custom Dimension #15","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension16' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension16">'. __("Custom Dimension #16","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension17' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension17">'. __("Custom Dimension #17","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension18' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension18">'. __("Custom Dimension #18","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension19' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension19">'. __("Custom Dimension #19","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension20' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension20">'. __("Custom Dimension #20","wp-seopress") .'</option>';
        echo '</select>';

        if (isset($this->options['seopress_google_analytics_cd_category'])) {
            esc_attr( $this->options['seopress_google_analytics_cd_category']);
        }
    }

    public function seopress_google_analytics_cd_tag_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );    
        
        $selected = $options['seopress_google_analytics_cd_tag'];
        
        echo '<select id="seopress_google_analytics_cd_tag" name="seopress_google_analytics_option_name[seopress_google_analytics_cd_tag]">';
            echo ' <option '; 
                if ('none' == $selected) echo 'selected="selected"'; 
                echo ' value="none">'. __("None","wp-seopress") .'</option>';
            echo ' <option '; 
                if ('dimension1' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension1">'. __("Custom Dimension #1","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension2' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension2">'. __("Custom Dimension #2","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension3' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension3">'. __("Custom Dimension #3","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension4' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension4">'. __("Custom Dimension #4","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension5' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension5">'. __("Custom Dimension #5","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension6' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension6">'. __("Custom Dimension #6","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension7' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension7">'. __("Custom Dimension #7","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension8' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension8">'. __("Custom Dimension #8","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension9' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension9">'. __("Custom Dimension #9","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension10' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension10">'. __("Custom Dimension #10","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension11' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension11">'. __("Custom Dimension #11","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension12' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension12">'. __("Custom Dimension #12","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension13' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension13">'. __("Custom Dimension #13","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension14' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension14">'. __("Custom Dimension #14","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension15' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension15">'. __("Custom Dimension #15","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension16' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension16">'. __("Custom Dimension #16","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension17' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension17">'. __("Custom Dimension #17","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension18' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension18">'. __("Custom Dimension #18","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension19' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension19">'. __("Custom Dimension #19","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension20' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension20">'. __("Custom Dimension #20","wp-seopress") .'</option>';
        echo '</select>';

        if (isset($this->options['seopress_google_analytics_cd_tag'])) {
            esc_attr( $this->options['seopress_google_analytics_cd_tag']);
        }
    }

    public function seopress_google_analytics_cd_post_type_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );    
        
        $selected = $options['seopress_google_analytics_cd_post_type'];
        
        echo '<select id="seopress_google_analytics_cd_post_type" name="seopress_google_analytics_option_name[seopress_google_analytics_cd_post_type]">';
            echo ' <option '; 
                if ('none' == $selected) echo 'selected="selected"'; 
                echo ' value="none">'. __("None","wp-seopress") .'</option>';
            echo ' <option '; 
                if ('dimension1' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension1">'. __("Custom Dimension #1","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension2' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension2">'. __("Custom Dimension #2","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension3' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension3">'. __("Custom Dimension #3","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension4' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension4">'. __("Custom Dimension #4","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension5' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension5">'. __("Custom Dimension #5","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension6' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension6">'. __("Custom Dimension #6","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension7' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension7">'. __("Custom Dimension #7","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension8' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension8">'. __("Custom Dimension #8","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension9' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension9">'. __("Custom Dimension #9","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension10' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension10">'. __("Custom Dimension #10","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension11' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension11">'. __("Custom Dimension #11","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension12' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension12">'. __("Custom Dimension #12","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension13' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension13">'. __("Custom Dimension #13","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension14' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension14">'. __("Custom Dimension #14","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension15' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension15">'. __("Custom Dimension #15","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension16' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension16">'. __("Custom Dimension #16","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension17' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension17">'. __("Custom Dimension #17","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension18' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension18">'. __("Custom Dimension #18","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension19' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension19">'. __("Custom Dimension #19","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension20' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension20">'. __("Custom Dimension #20","wp-seopress") .'</option>';
        echo '</select>';

        if (isset($this->options['seopress_google_analytics_cd_post_type'])) {
            esc_attr( $this->options['seopress_google_analytics_cd_post_type']);
        }
    }

    public function seopress_google_analytics_cd_logged_in_user_callback()
    {
        $options = get_option( 'seopress_google_analytics_option_name' );    
        
        $selected = $options['seopress_google_analytics_cd_logged_in_user'];
        
        echo '<select id="seopress_google_analytics_cd_logged_in_user" name="seopress_google_analytics_option_name[seopress_google_analytics_cd_logged_in_user]">';
            echo ' <option '; 
                if ('none' == $selected) echo 'selected="selected"'; 
                echo ' value="none">'. __("None","wp-seopress") .'</option>';
            echo ' <option '; 
                if ('dimension1' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension1">'. __("Custom Dimension #1","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension2' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension2">'. __("Custom Dimension #2","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension3' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension3">'. __("Custom Dimension #3","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension4' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension4">'. __("Custom Dimension #4","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension5' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension5">'. __("Custom Dimension #5","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension6' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension6">'. __("Custom Dimension #6","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension7' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension7">'. __("Custom Dimension #7","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension8' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension8">'. __("Custom Dimension #8","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension9' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension9">'. __("Custom Dimension #9","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension10' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension10">'. __("Custom Dimension #10","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension11' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension11">'. __("Custom Dimension #11","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension12' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension12">'. __("Custom Dimension #12","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension13' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension13">'. __("Custom Dimension #13","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension14' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension14">'. __("Custom Dimension #14","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension15' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension15">'. __("Custom Dimension #15","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension16' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension16">'. __("Custom Dimension #16","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension17' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension17">'. __("Custom Dimension #17","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension18' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension18">'. __("Custom Dimension #18","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension19' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension19">'. __("Custom Dimension #19","wp-seopress") .'</option>';
            echo '<option '; 
                if ('dimension20' == $selected) echo 'selected="selected"'; 
                echo ' value="dimension20">'. __("Custom Dimension #20","wp-seopress") .'</option>';
        echo '</select>';

        if (isset($this->options['seopress_google_analytics_cd_logged_in_user'])) {
            esc_attr( $this->options['seopress_google_analytics_cd_logged_in_user']);
        }
    }

    public function seopress_advanced_advanced_attachments_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_advanced_attachments']);      
        
        echo '<input id="seopress_advanced_advanced_attachments" name="seopress_advanced_option_name[seopress_advanced_advanced_attachments]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_advanced_attachments">'. __( 'Redirect attachment pages to post parent (or homepage if none)', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_advanced_advanced_attachments'])) {
            esc_attr( $this->options['seopress_advanced_advanced_attachments']);
        }
    }

    public function seopress_advanced_advanced_tax_desc_editor_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_advanced_tax_desc_editor']);      
        
        echo '<input id="seopress_advanced_advanced_tax_desc_editor" name="seopress_advanced_option_name[seopress_advanced_advanced_tax_desc_editor]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_advanced_tax_desc_editor">'. __( 'Add TINYMCE editor to term description', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_advanced_advanced_tax_desc_editor'])) {
            esc_attr( $this->options['seopress_advanced_advanced_tax_desc_editor']);
        }
    }

    public function seopress_advanced_advanced_stop_words_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_advanced_stop_words']);      
        
        echo '<input id="seopress_advanced_advanced_stop_words" name="seopress_advanced_option_name[seopress_advanced_advanced_stop_words]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_advanced_stop_words">'. __( 'Remove stop words in permalinks', 'wp-seopress' ) .'</label><span class="dashicons dashicons-info" title="'.__('Setting based on site language. Supported language EN, FR, ES, DE, IT, PT.','wp-seopress').'"></span>';

        if (isset($this->options['seopress_advanced_advanced_stop_words'])) {
            esc_attr( $this->options['seopress_advanced_advanced_stop_words']);
        }
    }

    public function seopress_advanced_advanced_category_url_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_advanced_category_url']);      
        
        echo '<input id="seopress_advanced_advanced_category_url" name="seopress_advanced_option_name[seopress_advanced_advanced_category_url]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_advanced_category_url">'. __( 'Remove /category/ in your permalinks', 'wp-seopress' ) .'</label><span class="dashicons dashicons-info" title="'.__('You have to flush your permalinks each time you change this settings','wp-seopress').'"></span>';

        if (isset($this->options['seopress_advanced_advanced_category_url'])) {
            esc_attr( $this->options['seopress_advanced_advanced_category_url']);
        }
    }

    public function seopress_advanced_advanced_trailingslash_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_advanced_trailingslash']);      
        
        echo '<input id="seopress_advanced_advanced_trailingslash" name="seopress_advanced_option_name[seopress_advanced_advanced_trailingslash]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_advanced_trailingslash">'. __( 'Disable trailing slash for metas', 'wp-seopress' ) .'</label><span class="dashicons dashicons-info" title="'.__('You must check this box if the structure of your permalinks DOES NOT contain a slash at the end (eg: /%postname%)','wp-seopress').'"></span>';

        if (isset($this->options['seopress_advanced_advanced_trailingslash'])) {
            esc_attr( $this->options['seopress_advanced_advanced_trailingslash']);
        }
    }

    public function seopress_advanced_advanced_wp_generator_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_advanced_wp_generator']);      
        
        echo '<input id="seopress_advanced_advanced_wp_generator" name="seopress_advanced_option_name[seopress_advanced_advanced_wp_generator]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_advanced_wp_generator">'. __( 'Remove WordPress meta generator in source code', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_advanced_advanced_wp_generator'])) {
            esc_attr( $this->options['seopress_advanced_advanced_wp_generator']);
        }
    }

    public function seopress_advanced_advanced_wp_shortlink_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_advanced_wp_shortlink']);      
        
        echo '<input id="seopress_advanced_advanced_wp_shortlink" name="seopress_advanced_option_name[seopress_advanced_advanced_wp_shortlink]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_advanced_wp_shortlink">'. __( 'Remove WordPress shortlink meta tag in source code (eg:', 'wp-seopress' ) .'<em>'.esc_attr('<link rel="shortlink" href="https://www.seopress.org/"/>').'</em>)</label>';

        if (isset($this->options['seopress_advanced_advanced_wp_shortlink'])) {
            esc_attr( $this->options['seopress_advanced_advanced_wp_shortlink']);
        }
    }

    public function seopress_advanced_advanced_wp_wlw_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_advanced_wp_wlw']);      
        
        echo '<input id="seopress_advanced_advanced_wp_wlw" name="seopress_advanced_option_name[seopress_advanced_advanced_wp_wlw]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_advanced_wp_wlw">'. __( 'Remove Windows Live Writer meta tag in source code (eg:', 'wp-seopress' ) .'<em>'.esc_attr('<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="https://www.seopress.org/wp-includes/wlwmanifest.xml" />').'</em>)</label>';

        if (isset($this->options['seopress_advanced_advanced_wp_wlw'])) {
            esc_attr( $this->options['seopress_advanced_advanced_wp_wlw']);
        }
    }

    public function seopress_advanced_advanced_wp_rsd_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_advanced_wp_rsd']);      
        
        echo '<input id="seopress_advanced_advanced_wp_rsd" name="seopress_advanced_option_name[seopress_advanced_advanced_wp_rsd]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_advanced_wp_rsd">'. __( 'Remove Really Simple Discovery meta tag in source code (eg:', 'wp-seopress' ) .'<em>'.esc_attr('<link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://www.seopress.dev/xmlrpc.php?rsd" />').'</em>)</label>';

        if (isset($this->options['seopress_advanced_advanced_wp_rsd'])) {
            esc_attr( $this->options['seopress_advanced_advanced_wp_rsd']);
        }
    }

    public function seopress_advanced_advanced_google_callback()
    {
        $check = isset($this->options['seopress_advanced_advanced_google']) ? $this->options['seopress_advanced_advanced_google'] : NULL;

        printf(
        '<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_google]" placeholder="'.esc_html__('Enter Google meta value site verification','wp-seopress').'" aria-label="'.__('Google site verification','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_advanced_advanced_bing_callback()
    {
       $check = isset($this->options['seopress_advanced_advanced_bing']) ? $this->options['seopress_advanced_advanced_bing'] : NULL;

       printf(
        '<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_bing]" placeholder="'.esc_html__('Enter Bing meta value site verification','wp-seopress').'" aria-label="'.__('Bing site verification','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_advanced_advanced_pinterest_callback()
    {
        $check = isset($this->options['seopress_advanced_advanced_pinterest']) ? $this->options['seopress_advanced_advanced_pinterest'] : NULL;
               
        printf(
        '<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_pinterest]" placeholder="'.esc_html__('Enter Pinterest meta value site verification','wp-seopress').'" aria-label="'.__('Pinterest site verification','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_advanced_advanced_yandex_callback()
    {
        $check = isset($this->options['seopress_advanced_advanced_yandex']) ? $this->options['seopress_advanced_advanced_yandex'] : NULL;

        printf(
        '<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_yandex]" aria-label="'.__('Yandex site verification','wp-seopress').'" placeholder="'.esc_html__('Enter Yandex meta value site verification','wp-seopress').'" value="%s"/>',
        esc_html( $check )
        
        );
        
    }

    public function seopress_advanced_appearance_adminbar_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_appearance_adminbar']);      
        
        echo '<input id="seopress_advanced_appearance_adminbar" name="seopress_advanced_option_name[seopress_advanced_appearance_adminbar]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_appearance_adminbar">'. __( 'Remove SEOPress from Admin Bar in backend and frontend', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_advanced_appearance_adminbar'])) {
            esc_attr( $this->options['seopress_advanced_appearance_adminbar']);
        }
    }

    public function seopress_advanced_appearance_metaboxe_position_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );    
        
        $selected = isset($options['seopress_advanced_appearance_metaboxe_position']) ? $options['seopress_advanced_appearance_metaboxe_position'] : NULL;
                
        echo '<select id="seopress_advanced_appearance_metaboxe_position" name="seopress_advanced_option_name[seopress_advanced_appearance_metaboxe_position]">';
            echo ' <option '; 
                if ('high' == $selected) echo 'selected="selected"'; 
                echo ' value="high">'. __("High priority (top)","wp-seopress") .'</option>';
            echo '<option '; 
                if ('default' == $selected) echo 'selected="selected"'; 
                echo ' value="default">'. __("Normal priority (default)","wp-seopress") .'</option>';
            echo '<option '; 
                if ('low' == $selected) echo 'selected="selected"'; 
                echo ' value="low">'. __("Low priority","wp-seopress") .'</option>';
        echo '</select>';

        if (isset($this->options['seopress_advanced_appearance_metaboxe_position'])) {
            esc_attr( $this->options['seopress_advanced_appearance_metaboxe_position']);
        }
    }

    public function seopress_advanced_appearance_notifications_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_appearance_notifications']);      
        
        echo '<input id="seopress_advanced_appearance_notifications" name="seopress_advanced_option_name[seopress_advanced_appearance_notifications]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_appearance_notifications">'. __( 'Hide Notifications Center in SEOPress Dashboard page', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_advanced_appearance_notifications'])) {
            esc_attr( $this->options['seopress_advanced_appearance_notifications']);
        }
    }

    public function seopress_advanced_appearance_title_col_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_appearance_title_col']);      
        
        echo '<input id="seopress_advanced_appearance_title_col" name="seopress_advanced_option_name[seopress_advanced_appearance_title_col]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_appearance_title_col">'. __( 'Add title column', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_advanced_appearance_title_col'])) {
            esc_attr( $this->options['seopress_advanced_appearance_title_col']);
        }
    }

    public function seopress_advanced_appearance_meta_desc_col_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_appearance_meta_desc_col']);      
        
        echo '<input id="seopress_advanced_appearance_meta_desc_col" name="seopress_advanced_option_name[seopress_advanced_appearance_meta_desc_col]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_appearance_meta_desc_col">'. __( 'Add meta description column', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_advanced_appearance_meta_desc_col'])) {
            esc_attr( $this->options['seopress_advanced_appearance_meta_desc_col']);
        }
    }

    public function seopress_advanced_appearance_canonical_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_appearance_canonical']);      
        
        echo '<input id="seopress_advanced_appearance_canonical" name="seopress_advanced_option_name[seopress_advanced_appearance_canonical]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_appearance_canonical">'. __( 'Add canonical URL column', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_advanced_appearance_canonical'])) {
            esc_attr( $this->options['seopress_advanced_appearance_canonical']);
        }
    }

    public function seopress_advanced_appearance_target_kw_col_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_appearance_target_kw_col']);      
        
        echo '<input id="seopress_advanced_appearance_target_kw_col" name="seopress_advanced_option_name[seopress_advanced_appearance_target_kw_col]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_appearance_target_kw_col">'. __( 'Add target keyword column', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_advanced_appearance_target_kw_col'])) {
            esc_attr( $this->options['seopress_advanced_appearance_target_kw_col']);
        }
    }

    public function seopress_advanced_appearance_noindex_col_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_appearance_noindex_col']);      
        
        echo '<input id="seopress_advanced_appearance_noindex_col" name="seopress_advanced_option_name[seopress_advanced_appearance_noindex_col]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_appearance_noindex_col">'. __( 'Display noindex status', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_advanced_appearance_noindex_col'])) {
            esc_attr( $this->options['seopress_advanced_appearance_noindex_col']);
        }
    }

    public function seopress_advanced_appearance_nofollow_col_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_appearance_nofollow_col']);      
        
        echo '<input id="seopress_advanced_appearance_nofollow_col" name="seopress_advanced_option_name[seopress_advanced_appearance_nofollow_col]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_appearance_nofollow_col">'. __( 'Display nofollow status', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_advanced_appearance_nofollow_col'])) {
            esc_attr( $this->options['seopress_advanced_appearance_nofollow_col']);
        }
    } 

    public function seopress_advanced_appearance_words_col_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_appearance_words_col']);      
        
        echo '<input id="seopress_advanced_appearance_words_col" name="seopress_advanced_option_name[seopress_advanced_appearance_words_col]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_appearance_words_col">'. __( 'Display total number of words in content', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_advanced_appearance_words_col'])) {
            esc_attr( $this->options['seopress_advanced_appearance_words_col']);
        }
    }

    public function seopress_advanced_appearance_w3c_col_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_appearance_w3c_col']);      
        
        echo '<input id="seopress_advanced_appearance_w3c_col" name="seopress_advanced_option_name[seopress_advanced_appearance_w3c_col]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_appearance_w3c_col">'. __( 'Display W3C column to check code quality', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_advanced_appearance_w3c_col'])) {
            esc_attr( $this->options['seopress_advanced_appearance_w3c_col']);
        }
    }

    public function seopress_advanced_appearance_ps_col_callback()
    {
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $options = get_option( 'seopress_advanced_option_name' );  
            
            $check = isset($options['seopress_advanced_appearance_ps_col']);      
            
            echo '<input id="seopress_advanced_appearance_ps_col" name="seopress_advanced_option_name[seopress_advanced_appearance_ps_col]" type="checkbox"';
            if ('1' == $check) echo 'checked="yes"'; 
            echo ' value="1"/>';
            
            echo '<label for="seopress_advanced_appearance_ps_col">'. __( 'Display Page Speed column to check performances', 'wp-seopress' ) .'</label>';

            if (isset($this->options['seopress_advanced_appearance_ps_col'])) {
                esc_attr( $this->options['seopress_advanced_appearance_ps_col']);
            }
        }
    }
    
    public function seopress_advanced_appearance_genesis_seo_metaboxe_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_appearance_genesis_seo_metaboxe']);      
        
        echo '<input id="seopress_advanced_appearance_genesis_seo_metaboxe" name="seopress_advanced_option_name[seopress_advanced_appearance_genesis_seo_metaboxe]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_appearance_genesis_seo_metaboxe">'. __( 'Remove Genesis SEO Metaboxe', 'wp-seopress' ) .'</label>';

        if (isset($this->options['seopress_advanced_appearance_genesis_seo_metaboxe'])) {
            esc_attr( $this->options['seopress_advanced_appearance_genesis_seo_metaboxe']);
        }
    }    

    public function seopress_advanced_security_metaboxe_role_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        global $wp_roles;

        if ( ! isset( $wp_roles ) )
            $wp_roles = new WP_Roles();
    
        foreach ($wp_roles->get_names() as $key => $value) {

            $check = isset($options['seopress_advanced_security_metaboxe_role'][$key]);  

            echo '<input id="seopress_advanced_security_metaboxe_role_'.$key.'" name="seopress_advanced_option_name[seopress_advanced_security_metaboxe_role]['.$key.']" type="checkbox"';
            if ('1' == $check) echo 'checked="yes"'; 
            echo ' value="1"/>';
            
            echo '<label for="seopress_advanced_security_metaboxe_role_'.$key.'">'. $value .'</label><br/>';

            if (isset($this->options['seopress_advanced_security_metaboxe_role'][$key])) {
                esc_attr( $this->options['seopress_advanced_security_metaboxe_role'][$key]);
            }
        }
    }

    public function seopress_advanced_security_metaboxe_ca_role_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        global $wp_roles;

        if ( ! isset( $wp_roles ) )
            $wp_roles = new WP_Roles();
    
        foreach ($wp_roles->get_names() as $key => $value) {

            $check = isset($options['seopress_advanced_security_metaboxe_ca_role'][$key]);  

            echo '<input id="seopress_advanced_security_metaboxe_ca_role_'.$key.'" name="seopress_advanced_option_name[seopress_advanced_security_metaboxe_ca_role]['.$key.']" type="checkbox"';
            if ('1' == $check) echo 'checked="yes"'; 
            echo ' value="1"/>';
            
            echo '<label for="seopress_advanced_security_metaboxe_ca_role_'.$key.'">'. $value .'</label><br/>';

            if (isset($this->options['seopress_advanced_security_metaboxe_ca_role'][$key])) {
                esc_attr( $this->options['seopress_advanced_security_metaboxe_ca_role'][$key]);
            }
        }
    }
}
    
if( is_admin() )
    $my_settings_page = new seopress_options();
    
?>