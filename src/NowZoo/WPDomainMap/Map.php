<?php
namespace NowZoo\WPDomainMap;

/**
 * Class Map
 * @package NowZoo\WPDomainMap
 *
 * This class should be instantiated from sunrise.php
 *
 * It does two things:
 *
 * - On instantiation, sets the (ridiculous) network globals, if a custom domain has been set for the current site
 * - Filters the 'home' and 'siteurl' options for sites that have a custom domain
 *
 */
class Map{


    private static $instance = null;

    private $ids_to_domains = array();
    private $subdomains_to_domains = array();
    private $domain = array();


    /**
     * Singleton...
     *
     * @return Map
     */
    public static function inst(){
        if (is_null(self::$instance)){
            self::$instance = new Map;
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct(){
        if (! is_multisite()) return;
        //add the URL actions...
        add_action('plugins_loaded', array($this, 'action_plugins_loaded'));
        //init...
        $this->initialize();
    }


    /**
     * Initialize the data, and set the network globals
     * if a custom domain has been set for the current site
     */
    private function initialize(){
        global $wpdb, $current_blog, $blog_id, $site_id, $current_site;
        /** @var \wpdb $wpdb */
        $option = Plugin::get_raw_option();
        $this->ids_to_domains = $option['ids_to_domains'];
        $this->subdomains_to_domains = $option['subdomains_to_domains'];
        $this->domain = strtolower($_SERVER[ 'HTTP_HOST' ]);


        $id = false;
        foreach($this->ids_to_domains as $found_id => $arr){
            if (strcasecmp($arr['domain'], $this->domain) === 0){
                $id = $found_id;
            }
        }
        //bail here if a custom domain has not been set...
        if (! $id){
            return;
        }

        //set the globals...
        $sql = sprintf('SELECT * FROM %s WHERE blog_id =%s', $wpdb->blogs, $id);
        $current_blog = $wpdb->get_row($sql);
        $current_blog->domain = $_SERVER[ 'HTTP_HOST' ];
        $current_blog->path = '/';
        $blog_id = $id;
        $site_id = $current_blog->site_id;
        define( 'COOKIE_DOMAIN', $this->domain );
        $sql = sprintf('SELECT * FROM %s WHERE id =%s', $wpdb->site, $current_blog->site_id);
        $current_site = $wpdb->get_row($sql);
        $current_site->blog_id = 1;

    }

    /**
     * Add the home and siteurl filters
     */
    public function action_plugins_loaded(){
        add_filter('option_home', array($this, 'filter_url'),  9999, 1);
        add_filter('option_siteurl', array($this, 'filter_url'),  9999, 1);
    }

    /**
     * Filter the home and siteurl options for sites with custom domains...
     *
     * @param $url
     * @return string
     */
    public function filter_url($url){
        $parsed = parse_url($url);

        if (! isset($this->subdomains_to_domains[$parsed['host']])){
            return $url;
        }
        return $parsed['scheme'] . '://' . $this->subdomains_to_domains[$parsed['host']];
    }

}