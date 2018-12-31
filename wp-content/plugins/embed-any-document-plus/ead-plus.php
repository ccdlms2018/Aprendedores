<?php
/*
Plugin Name: Embed Any Document Plus
Plugin URI: http://awsm.in/ead-plus-documentation/
Description: Embed Any Document WordPress plugin lets you upload and embed your documents easily in your WordPress website without any additional browser plugins like Flash or Acrobat reader. The plugin lets you choose between Google Docs Viewer and Microsoft Office Online to display your documents. 
Version: 2.2.1
Author: Awsm Innovations
Author URI: http://awsm.in
License: GPL V3
Text Domain: embed-any-document-plus
Domain Path: /language
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define('EAD_PLUS', true);
define('EAD_PLUS_VERSION', '2.2.1');

class Ead_plus
{
    private static $instance = null;
    private $plugin_path;
    private $plugin_url;
    private $plugin_base;
    private $plugin_file;
    private $plugin_version;
    private $settings_slug;
    private $meta_url;
    private $plugin_slug;
    
    /**
     * Creates or returns an instance of this class.
     */
    public static function get_instance()
    {
        // If an instance hasn't been created and set to $instance create an instance and set it to $instance.
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    /**
     * Pro Exists Check
     */
    public static function pro_exists()
    {
        return self::$_instance;
    }
    /**
     * Initializes the plugin by setting localization, hooks, filters, and administrative functions.
     */
    private function __construct()
    {
        
        $this->plugin_path      =   plugin_dir_path( __FILE__ );
        $this->plugin_url       =   plugin_dir_url( __FILE__ );
        $this->plugin_base      =   dirname(plugin_basename( __FILE__ ));
        $this->plugin_file      =   __FILE__;
        $this->settings_slug    =   'ead-plus-settings';
        $this->plugin_version   =   EAD_PLUS_VERSION;
        $this->meta_url         =   'https://kernl.us/api/v1/updates/59043132ecdf270f2afe5d76/';
        $this->plugin_slug      =   'embed-any-document-plus';
        
        //Language Support
        $this->load_plugin_textdomain();
        
        //Plugin init
        add_action( 'init', array( $this, 'wp_plugin_update' ) );
        
        //embeddoc shortcode support
        add_shortcode( 'embeddoc', array( $this, 'embed_shortcode' ) );
        
        //default options
        register_activation_hook( $this->plugin_file, array( $this, 'defaults' ) );

        //Initialize block
        include_once $this->plugin_path . 'blocks/document.php';

        $this->adminfunctions();
    }
    /**
     * Localisation
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( 'embed-any-document-plus',FALSE, basename( dirname( __FILE__ ) ) . '/language/' );
    }
    /**
     * Register admin Settings style
     */
    function setting_styles()
    {
        wp_register_style( 'embed-settings', plugins_url('css/settings.css', $this->plugin_file), false, $this->plugin_version, 'all');
        wp_enqueue_style( 'embed-settings' );
    }
    
    /**
     * Embed any Docs Button
     */
    public function embedbutton($args = array())
    {
        
        // Check user previlage
        if (!current_user_can( 'edit_posts' ))
            return;
        
        // Prepares button target
        $target = is_string( $args ) ? $args : 'content';
        
        // Prepare args
        $args = wp_parse_args($args, array(
            'target' => $target,
            'text' => __( 'Add Document', 'embed-any-document-plus' ),
            'class' => 'awsm-embed button',
            'icon' => plugins_url( 'images/ead-small.png', __FILE__ ),
            'echo' => true,
            'shortcode' => false
        ));
        
        // Prepare EAD icon
        if ($args['icon'])
            $args['icon'] = '<img src="' . esc_url( $args['icon'] ) . '" alt="add document" role="presentation"/> ';
        
        // Print button in media column
        $button = '<a href="javascript:void(0);" class="' .  esc_attr( $args['class'] ) . '" title="' .  esc_attr( $args['text'] ) . '" data-target="' .  esc_attr( $args['target'] ) . '" >' . $args['icon'] . esc_html( $args['text'] ) . '</a>';
        
        // Request assets
        wp_enqueue_media();
        
        // Print/return result
        if ($args['echo'])
            echo $button;
        return $button;
    }
    
    /**
     * Admin Easy access settings link
     */
    function settingslink($links)
    {
        $settings_link = '<a href="options-general.php?page=' . esc_attr( $this->settings_slug ) . '">' . esc_html__('Settings', 'embed-any-document-plus') . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
    
    /**
     * Embed Form popup
     */
    function embedpopup()
    {
        if (wp_script_is('ead_media_button')) {
            add_thickbox();
            include( $this->plugin_path . 'inc/popup.php' );
        }
    }
    
    /**
     * Register admin scripts
     */
    function embed_helper()
    {
        $script_deps = array('jquery');
        if(function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if(method_exists($screen, 'is_block_editor') && $screen->is_block_editor()) {
                $script_deps[] = 'wp-blocks';
            }
        }
        wp_enqueue_script( 'ead_media_button', plugins_url( 'js/ead.js', $this->plugin_file), $script_deps, $this->plugin_version, true );
        wp_enqueue_style( 'ead_media_button', plugins_url( 'css/embed.css', $this->plugin_file), false, $this->plugin_version, 'all' );
        wp_localize_script( 'ead_media_button', 'emebeder', $this->embedjsdata() );
    }
    
    /**
     * Localize array
     */
    function embedjsdata()
    {
        $jsdata = array(
            'height'        =>  get_option( 'ead_height', '500px' ),
            'width'         =>  get_option( 'ead_width', '100%' ),
            'download'      =>  get_option( 'ead_download', 'none' ),
            'viewer'        =>  get_option( 'ead_provider', 'google' ),
            'text'          =>  get_option( 'ead_text', __('Download','embed-any-document-plus' ) ),
            'cache'         =>  0,
            'insert_text'   =>  __( 'Select', 'embed-any-document-plus' ),
            'ajaxurl'       =>  admin_url( 'admin-ajax.php' ),
            'validtypes'    =>  $this->validembedtypes(),
            'msextension'   =>  $this->validextensions( 'ms' ),
            'drextension'   =>  $this->validextensions( 'all' ),
            'nocontent'     =>  __( 'Nothing to insert', 'embed-any-document-plus'),
            'nopublic'      =>  __( 'The document you have chosen is a not public.', 'embed-any-document-plus') . __(' Only the owner and explicitly shared collaborators will be able to view it.', 'embed-any-document-plus' ),
            'invalidurl'    =>  __( 'Invalid URL', 'embed-any-document-plus' ),
            'from_url'      =>  __( 'From URL', 'embed-any-document-plus' ),
            'pluginname'    =>  __( 'Embed Any Document Plus', 'embed-any-document-plus' ),
            'no_api'        =>  __( 'No API key', 'embed-any-document-plus' ),
            'driveapiKey'   =>  false,
            'driveclientId' =>  false,
            'boxapikey'     =>  false,
            'DropboxApi'    =>  false
        );
        if (get_option( 'ead_drivekey' ) && get_option( 'ead_driveClient' )) {
            $jsdata['driveapiKey']   = get_option( 'ead_drivekey' );
            $jsdata['driveclientId'] = get_option( 'ead_driveClient' );
        }
        if (get_option('ead_dropbox'))
            $jsdata['DropboxApi'] = get_option( 'ead_dropbox' );
        if (get_option('ead_box'))
            $jsdata['boxapikey'] = get_option( 'ead_box' );
        return $jsdata;
    }
    
    /**
     * Shortcode Functionality
     */
    function embed_shortcode($atts)
    {
        $embed            = "";
        $durl             = "";
        $default_width    = $this->sanitize_dims(get_option( 'ead_width', '100%' ));
        $default_height   = $this->sanitize_dims(get_option( 'ead_height', '500px' ));
        $default_provider = get_option( 'ead_provider', 'google' );
        $default_download = get_option( 'ead_download', 'none' );
        $default_filesize = get_option( 'ead_filesize', 'yes' );
        $default_text     = get_option( 'ead_text', __('Download','embed-any-document-plus' ));
        $show             = false;
        $shortcode_atts   = shortcode_atts( array(
                                                'url'       =>  '',
                                                'drive'     =>  '',
                                                'id'        =>  false,
                                                'width'     =>  $default_width,
                                                'height'    =>  $default_height,
                                                'language'  =>  'en',
                                                'text'      =>  __($default_text,'embed-any-document-plus'),
                                                'viewer'    =>  $default_provider,
                                                'download'  =>  $default_download,
                                                'cache'     =>  'on',
                                                'boxtheme'  =>  'dark',
                                        ), $atts);
  
        if ( isset( $shortcode_atts['url'] ) || isset( $shortcode_atts['id'] )):
            
            $durl        = '';
            $privatefile = '';
            if ( $this->allowdownload( $shortcode_atts['viewer'] ) )
                if ( $shortcode_atts['download'] === 'alluser' or $shortcode_atts['download'] === 'all' ) {
                    $show = true;
                } elseif ( $shortcode_atts['download'] == 'logged' AND is_user_logged_in() ) {
                    $show = true;
                }
            if ($show) {
                $filesize = 0;
                $url      = esc_url( $shortcode_atts['url'] , array(
                    'http',
                    'https'
                ));

                $file_html = '';

                if( $show && $default_filesize == 'yes' ){
                    $filedata    = wp_remote_head(  $shortcode_atts['url'] );
                    if ( !is_wp_error( $filedata ) && isset( $filedata['headers']['content-length'] ) ) {
                        $filesize = $this->human_filesize($filedata['headers']['content-length']);
                        $file_html = ' [' . $filesize . ']';
                    }    
                }
                
                $durl = '<p class="embed_download"><a href="' . esc_url( $shortcode_atts['url'] ) . '" download>' . esc_attr( $shortcode_atts['text'] ) . $file_html . '</a></p>';


            }

            $providerList = array( 'google', 'microsoft', 'drive', 'box' );

            if ( !in_array( $shortcode_atts['viewer'] , $providerList )){
                $viewer = 'google';
            }
            
            if ($shortcode_atts['cache']  === 'off' AND $shortcode_atts['viewer'] === 'google') {
                if ($this->url_get_param($url)) {
                    $shortcode_atts['url'] .= "?" . time();
                } else {
                    $shortcode_atts['url'] .= "&" . time();
                }
            }
            
            $url = esc_url( $shortcode_atts['url'] , array(
                'http',
                'https'
            ));
            $iframe                 =   '';      
            switch ( $shortcode_atts['viewer'] ) {
                case 'google':
                    $embedsrc       =   '//docs.google.com/viewer?url=%1$s&embedded=true&hl=%2$s';
                    $iframe         =   sprintf( $embedsrc, urlencode( $url ), esc_attr( $shortcode_atts['language'] ) );
                    break;
                
                case 'microsoft':
                    $embedsrc       =   '//view.officeapps.live.com/op/embed.aspx?src=%1$s';
                    $iframe         =   sprintf( $embedsrc, urlencode( $url ) );
                    break;
                
                case 'drive':
                    if( $shortcode_atts['id'] ){
                        $embedsrc   =   '//drive.google.com/file/d/%s/preview';
                        $iframe     =   sprintf( $embedsrc,   $shortcode_atts['id'] );
                    }else{
                       $iframe      =   $url; 
                    }
                    break;
                
                case 'box':
                    $embedsrc       =   $this->boxembed($url);
                    $iframe         =   sprintf( $embedsrc, urlencode( $shortcode_atts['boxtheme'] ) );
                    break;
            }
            $min_height =   '';
            if($this->in_percentage( $shortcode_atts['height'] )){
                $min_height         =   ' min-height:500px;';
            }
            if($this->check_responsive( $shortcode_atts['height'] ) AND $this->check_responsive( $shortcode_atts['width'] )){
                $iframe_style       = 'style="width:100%; height:100%; border: none; position: absolute;left:0;top:0;"';
                $doc_style          = 'style="position:relative;padding-top:90%;"';
            }else{
                $iframe_style       =  sprintf('style="width:%s; height:%s; border: none;%s"',esc_html( $shortcode_atts['width'] ),esc_html( $shortcode_atts['height'] ), $min_height);
                $doc_style          = 'style="position:relative;"';
            }

            $iframe = sprintf( '<iframe src="%s" title="%s" %s></iframe>', esc_attr( $iframe ), esc_html__( 'Embedded Document', 'embed-any-document'), $iframe_style);
            $show      = false;
            $embed = '<div class="ead-preview"><div class="ead-document" '.  $doc_style.'>' . $iframe . $privatefile . '</div>'.$durl.'</div>';
        else:
            $embed = __('No Url Found', 'embed-any-document-plus' );
        endif;
        return $embed;
    }
    /**
     * Check value in percentage
     *
     * @since   1.2
     * @return  Int Dimenesion
     */
    function in_percentage( $dim ){
        if (strstr($dim, '%')) {
            return true;
        }
        return false;
    }
    /**
     * Enable Resposive
     *
     * @since   1.2
     * @return  Boolean
     */
    function check_responsive( $dim ){
        if (strstr($dim, '%')) {
            $dim = preg_replace("/[^0-9]*/", '', $dim);
            if ((int)$dim == 100) {
                return true;
            }
        }
        return false;
    }
    /**
     * Private File Style
     */
    function private_style()
    {
    echo '<style type="text/css">
        .ead-document{ position:relative;}
        .ead-private{ position:absolute; width: 100%; height: 100%; left:0; top:0; background:rgba(248,237,235,0.8); text-align: center;}
        .ead-lock{ display: inline-block; vertical-align: middle;max-width: 98%;}
        .ead-dummy{ display: inline-block; vertical-align: middle; height:100%; width: 1px;}
    </style>';
    }
    /**
     * Admin menu setup
     */
    public function admin_menu()
    {
        $eadsettings = add_options_page( 'Embed Any Document Plus', 'Embed Any Document Plus', 'manage_options', $this->settings_slug, array( $this, 'settings_page' ));
        add_action('admin_print_styles-' . $eadsettings, array( $this, 'setting_styles' ));
    }
    /**
     * Admin settings page
     */
    public function settings_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        include($this->plugin_path . 'inc/settings.php');
    }
    
    /**
     * Register Settings
     */
    function register_eadsettings()
    {
        register_setting( 'ead-settings-group', 'ead_width', array( $this,'sanitize_dims' ) );
        register_setting( 'ead-settings-group', 'ead_height', array( $this,'sanitize_dims' ) );
        register_setting( 'ead-settings-group', 'ead_download' );
        register_setting( 'ead-settings-group', 'ead_filesize' );
        register_setting( 'ead-settings-group', 'ead_provider' );
        register_setting( 'ead-settings-group', 'ead_text' );
        register_setting( 'ead-settings-envato', 'ead_envato_key', array( $this,'envato_verify' ) );
        register_setting( 'ead-cloud-group', 'ead_drivekey' );
        register_setting( 'ead-cloud-group', 'ead_driveClient' );
        register_setting( 'ead-cloud-group', 'ead_dropbox' );
        register_setting( 'ead-cloud-group', 'ead_box' );
    }
    
    /**
     * Admin Functions init
     */
    function adminfunctions()
    {
        if (is_admin()) {
            add_action( 'wp_enqueue_media', array($this, 'embed_helper') );
            add_action( 'admin_footer', array($this, 'embedpopup') );
            add_action( 'wp_head', array( $this, 'private_style' ) ); 
            add_action( 'media_buttons', array( $this, 'embedbutton' ), 1000) ; 
            add_action( 'admin_menu', array( $this, 'admin_menu' ) ); 
            add_action( 'admin_init', array( $this, 'register_eadsettings' ) ); 
            add_filter( "plugin_action_links_" . plugin_basename(__FILE__), array( $this, 'settingslink' ) );
            add_action( 'after_plugin_row_' . plugin_basename(__FILE__), array( $this, 'plugin_row' ), 11, 3 );
            add_filter( 'upload_mimes', array( $this, 'additional_mimes' ) );
            add_action( 'admin_notices', array( $this, 'purchase_key_notice' ) );
            add_filter( 'puc_manual_check_link-embed-any-document-plus', array( $this, 'check_for_update' ) );
        }
    }
    
    /**
     * Adds additional mimetype for media uploader
     */
    function additional_mimes($mimes)
    {
        return array_merge($mimes, array(
            'svg' => 'image/svg+xml',
            'ai' => 'application/postscript'
        ));
    }
    
    /**
     * To get Overlay link
     */
    function providerlink($keys, $id, $provider)
    {
   
        if ( $this->isprovider_api($keys) ) {
            $link      =    'options-general.php?page=' . $this->settings_slug . '&tab=cloud';
            $id        =    "";
            $configure =    sprintf('<span class="overlay"><strong>%s</strong><i></i></span>',esc_html__( 'Configure', 'embed-any-document-plus' ));
            $target    =    'target="_blank"';
        } else {
            $configure =    '';
            $link      =    '#';
            $target    =    "";
        }

        $imageurl      =  $this->plugin_url . 'images/icon-' .strtolower($provider) . '.png';
        /* translators: %1$s: Service provider */
        $linktext      =  sprintf( esc_html__( 'Add from %1$s', 'embed-any-document-plus' ), $provider );

        printf( wp_kses( '<a href="%1$s" id="%2$s" %3$s><span><img src="%4$s" alt="%2$s" />%5$s %6$s</span></a>', array(  'a' => array( 'href' => array(), 'id' => array(), 'target' => array())  , 'span' => array(), 'img' => array( 'src' => array(), 'alt'=> array() ), ) ), esc_url( $link ), esc_attr( $id ), $target, esc_url( $imageurl ), $linktext ,$configure );

    }
    
    /**
     * To initialize default options
     */
    function defaults()
    {
        $o = array(
            'ead_width' => '100%',
            'ead_height' => '500px',
            'ead_download' => 'none',
            'ead_provider' => 'google',
            'ead_mediainsert' => '1',
            'ead_filesize' =>'yes',
        );
        foreach ($o as $k => $v) {
            if (!get_option($k))
                update_option( $k, $v );
        }
        return;
    }
    
    /**
     * Dropdown Builder
     *
     * @since   1.0
     * @return  String select html
     */
    function selectbuilder( $name, $options, $selected = "", $class = "",$attr="" )
    {
        if (is_array($options)):
            $select_html = '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" class="' . esc_attr( $class ) . '" '.$attr.'>';
            foreach ($options as $key => $option) {
                $selected_html ="";
                if ($key == $selected) {
                    $selected_html = ' selected="selected"';
                }
                $select_html .= '<option value="'. esc_attr( $key ) .'" ' . $selected_html . '>' . esc_html( $option ) . '</option>';
                
            }
            echo $select_html .= '</select>';
        endif;
    }
    /**
     * Human Readable filesize
     *
     * @since   1.0
     * @return  Human readable file size
     * @note    Replaces old gde_sanitizeOpts function
     */
    function human_filesize( $bytes, $decimals = 2 )
    {
        $size   = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        $size   = isset( $size[$factor] ) ? $size[$factor] : '';
        return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . $size;
    }
    /**
     * Sanitize dimensions (width, height)
     *
     * @since   1.0
     * @return  string Sanitized dimensions, or false if value is invalid
     * @note    Replaces old gde_sanitizeOpts function
     */
    function sanitize_dims( $dim )
    {
        
        // remove any spacing junk
        $dim = trim(str_replace(" ", "", $dim));
        
        if (!strstr($dim, '%')) {
            $type = "px";
            $dim  = preg_replace("/[^0-9]*/", '', $dim);
        } else {
            $type = "%";
            $dim  = preg_replace("/[^0-9]*/", '', $dim);
            if ((int) $dim > 100) {
                $dim = "100";
            }
        }
        
        if ($dim) {
            return $dim . $type;
        } else {
            return false;
        }
    }
    /**
     * get box embed url
     *
     * @since   1.0
     * @return  string embed src
     */
    function boxembed( $url )
    {
        $boxdata = parse_url($url);
        if ($boxdata['host'] AND $boxdata['path']) {
            return 'https://' . $boxdata['host'] . '/embed_widget/' . $boxdata['path'] . '?theme=%1$s';
        } else {
            return '';
        }
    }
 
    /**
     * Validate Source mime type
     *
     * @since   1.0
     * @return  boolean
     */
    function validmimetypes()
    {
        $mimetypes = array(
            // Text formats
            'txt|asc|c|cc|h'    =>  'text/plain',
            'rtx'               =>  'text/richtext',
            'css'               =>  'text/css',
            // Misc application formats
            'js'                =>  'application/javascript',
            'pdf'               =>  'application/pdf',
            'ai'                =>  'application/postscript',
            'tif'               =>  'image/tiff',
            'tiff'              =>  'image/tiff',
            // MS Office formats
            'doc'               =>  'application/msword',
            'pot|pps|ppt'       =>  'application/vnd.ms-powerpoint',
            'xla|xls|xlt|xlw'   =>  'application/vnd.ms-excel',
            'docx'              =>  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'dotx'              =>  'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'dotm'              =>  'application/vnd.ms-word.template.macroEnabled.12',
            'xlsx'              =>  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xlsm'              =>  'application/vnd.ms-excel.sheet.macroEnabled.12',
            'pptx'              =>  'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'ppsx'              =>  'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            // iWork formats
            'pages'             =>  'application/vnd.apple.pages',
            //Additional Mime Types
            'svg'               =>  'image/svg+xml'
        );
        return $mimetypes;
    }
    /**
     * Checks Url Validity
     *
     * @since   1.0
     * @return  boolean
     */
    function validtype( $url )
    {
        $doctypes = $this->validmimetypes();
        if (is_array($doctypes)) {
            $allowed_ext = implode("|", array_keys($doctypes));
            if (preg_match("/\.($allowed_ext)$/i", $url)) {
                return true;
            }
        } else {
            return false;
        }
    }
    /**
     * Get allowed Mime Types
     *
     * @since   1.0
     * @return  string Mimetypes
     */
    function validembedtypes()
    {
        $doctypes = $this->validmimetypes();
        return $allowedtype = implode(',', $doctypes);
    }
    /**
     * Get allowed Extensions
     *
     * @since   1.0
     * @return  string Extenstion
     */
    function validextensions($list = 'all')
    {
        $extensions['all'] = array('.css', '.js', '.pdf', '.ai', '.tif', '.tiff', '.doc', '.txt', '.asc', '.c', '.cc', '.h', '.pot', '.pps', '.ppt', '.xla', '.xls', '.xlt', '.xlw', '.docx', '.dotx', '.dotm', '.xlsx', '.xlsm', '.pptx', '.pages', '.svg', '.ppsx');
        $extensions['ms']  = array('.doc', '.pot', '.pps', '.ppt', '.xla', '.xls', '.xlt', '.xlw', '.docx', '.dotx', '.dotm', '.xlsx', '.xlsm', '.pptx', '.ppsx');
        return $allowedtype = implode(',', $extensions[$list]);
    }
    /**
     * Get allowed Mime Types for microsoft
     *
     * @since   1.0
     * @return  array Mimetypes
     */
    function microsoft_mimes()
    {
        $micro_mime = array(
            'doc'               =>  'application/msword',
            'pot|pps|ppt'       =>  'application/vnd.ms-powerpoint',
            'xla|xls|xlt|xlw'   =>  'application/vnd.ms-excel',
            'docx'              =>  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'dotx'              =>  'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'dotm'              =>  'application/vnd.ms-word.template.macroEnabled.12',
            'xlsx'              =>  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xlsm'              =>  'application/vnd.ms-excel.sheet.macroEnabled.12',
            'pptx'              =>  'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        );
        return $micro_mime;
    }
    /**
     * Check Allow Download
     *
     * @since   1.0
     * @return  Boolean
     */
    function allowdownload( $provider )
    {
        $blacklist = array(
            'drive',
            'box'
        );
        if (in_array($provider, $blacklist)) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Check Provider API
     *
     * @since   1.0
     * @return  boolean
     */
    function isprovider_api( $keys )
    {
        $itemflag = false;
        if (is_array($keys)) {
            foreach ($keys as $key) {
                if (!get_option($key)) {
                    $itemflag = true;
                    break;
                }
            }
        } else {
            if (!get_option($keys)) {
                $itemflag = true;
            }
        }
        return $itemflag;
    }
    /**
     * Get Active Menu Class
     *
     * @since   1.0
     * @return  string Class name
     */
    function getactive_menu( $tab, $needle )
    {
        if ($tab == $needle) {
            echo 'nav-tab-active';
        }
    }
    
    /**
     * Checks for url query parameter
     *
     * @since   1.1
     * @return  Boolean
     */
    function url_get_param( $url )
    {
        $urldata = parse_url($url);
        if (isset($urldata['query'])) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Wp Plugin updater integration for automatic plugin update
     * since 2.1
     */
    function wp_plugin_update(){
        $license_key =  get_option('ead_envato_key');
        if($license_key){
            require_once( $this->plugin_path . 'lib/plugin_update_check.php' );
            $ead_updates = new PluginUpdateChecker_2_0 ( $this->meta_url, __FILE__, $this->plugin_slug, 1 );
            $ead_updates->purchaseCode = $license_key;   
        }  
    }
    /**
     * Envato Purchase code verification
     * since 2.1.0
     */
    function envato_verify( $field ){
 
        $options = array(
            'timeout'       =>  10,  
            'headers'       =>  array(
                'Accept'    =>  'application/json'
            ),
        );
        $args['code']       =   urlencode( $field );
        $url                =   add_query_arg( $args, $this->meta_url );
        $result             =   wp_remote_get( $url, $options );
        if ( !is_wp_error( $result ) && isset( $result['response']['code'] ) && ( $result['response']['code'] == 200) && !empty( $result['body'] ) ){
            return $field;
        }else{
           add_settings_error( 'ead_envato_key', 'api_error', __('Invalid Envato purchase key', 'embed-any-document-plus' ), 'error');
           return false; 
        }
    }
    /**
     * To show message to enter purchase code
     * since 2.1.0
     */
    function plugin_row(){
        $license_key    =   get_option('ead_envato_key');
        if(!$license_key){
        $url            =   'options-general.php?page=' . $this->settings_slug . '&tab=updates';
        /* translators: %s: link to settings page to add the license key */
        $notice         =   sprintf( wp_kses( __( 'Please <a href="%s">activate your copy</a> of Embed Any Document Plus to receive automatic updates.', 'embed-any-document-plus' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $url ) );
        ?>
            <tr class="notice inline notice-warning notice-alt">
                <td colspan="3" class="plugin-update">
                    <div class="update-message"><span class="bd-licence-activate-notice"><?php echo $notice;?></span></div>
                </td>
            </tr> 
        <?php  
        }

    }
    /**
     * To show admin notice to enter purchase for automatic updates
     * since 2.1.0
     */
    function purchase_key_notice(){
        if ( current_user_can( 'install_plugins' ) && !get_option( 'ead_envato_key' ) ) {
            $class          =   'notice notice-error';
            $url            =   'options-general.php?page=' . $this->settings_slug . '&tab=updates';
            $notice         =   sprintf( wp_kses( __( 'Please <a href="%s">activate your copy</a> of Embed Any Document Plus to receive automatic updates.', 'embed-any-document-plus' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $url ) );
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ),  $notice  ); 
        }
    }
    /**
     * To hide update check link
     * since 2.1.0
     */
    function check_for_update(){
       return false;
    }
}

Ead_plus::get_instance();
