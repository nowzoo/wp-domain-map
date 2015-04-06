<?php
namespace NowZoo\WPDomainMap;

class Plugin{

    const SITE_OPTION_DOMAINS = 'nowzoo-domain-map';

    private static $instance = null;

    public static function inst(){
        if (is_null(self::$instance)){
            self::$instance = new Plugin;
        }
        return self::$instance;
    }

    private function __construct(){
        if (! is_multisite()) return;
        AdminSettingsPanel::inst();

    }

    /**
     * @return array
     */
    public static function get_option(){
        $option = get_site_option(self::SITE_OPTION_DOMAINS);
        if (! is_array($option)){
            $option = array();
        }
        return $option;
    }

    private function initialize($map, $url_map){
        global $wpdb, $current_blog, $blog_id, $site_id, $current_site;
        /** @var \wpdb $wpdb */
        $this->domain = $_SERVER[ 'HTTP_HOST' ];
        $this->map = $map;
        $this->url_map = $url_map;
        add_action('plugins_loaded', array($this, '_action_plugins_loaded'));
        if (! isset($this->map[$this->domain])){
            return;
        }
        $this->blog_id = $this->map[$this->domain];
        $sql = sprintf('SELECT * FROM %s WHERE blog_id =%s', $wpdb->blogs, $this->blog_id);
        $current_blog = $wpdb->get_row($sql);
        $current_blog->domain = $_SERVER[ 'HTTP_HOST' ];
        $current_blog->path = '/';
        $blog_id = $this->blog_id;
        $site_id = $current_blog->site_id;
        define( 'COOKIE_DOMAIN', $_SERVER[ 'HTTP_HOST' ] );
        $sql = sprintf('SELECT * FROM %s WHERE id =%s', $wpdb->site, $current_blog->site_id);
        $current_site = $wpdb->get_row($sql);
        $current_site->blog_id = 1;

    }

    public function _action_plugins_loaded(){
        add_filter('option_home', array($this, '_filter_url'),  9999, 1);
        add_filter('option_siteurl', array($this, '_filter_url'),  9999, 1);
    }

    public function _filter_url($url){
        $parsed = parse_url($url);
        if (! isset($this->url_map[$parsed['host']])){
            return $url;
        }
        return $parsed['scheme'] . '://' . $this->url_map[$parsed['host']];
    }


    public static function lib_path($p = false){
        $lib_path = dirname(dirname(dirname(__DIR__)));
        if ($p && ! empty($p)){
            $lib_path .= '/' . $p;
        }
        return $lib_path;
    }

    public static function require_lib_path($p = false, $data = array()){
        extract($data);
        require self::lib_path($p);
    }

}