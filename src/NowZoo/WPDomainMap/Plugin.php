<?php
namespace NowZoo\WPDomainMap;

/**
 * Class Plugin
 * @package NowZoo\WPDomainMap
 *
 * Shared functionality
 */
class Plugin{

    const SITE_OPTION_DOMAINS = 'nowzoo-domain-map';


    /**
     * @return array
     */
    public static function option_default(){
        return array(
            'ids_to_domains' => array(),
            'subdomains_to_domains' => array()
        );
    }

    /**
     * Get the option before the site id has benn set...
     *
     * @return array
     */
    public static function get_raw_option(){
        global $wpdb;
        /** @var \wpdb $wpdb */
        $sql = 'SELECT meta_value FROM ' . $wpdb->sitemeta . ' WHERE site_id = %s AND meta_key = %s';
        $sql = $wpdb->prepare($sql, SITE_ID_CURRENT_SITE, self::SITE_OPTION_DOMAINS);
        $option = $wpdb->get_var($sql);
        if ($option){
            $option = unserialize($option);
        } else {
            $option = null;
        }
        if (! is_array($option)){
            $option = self::option_default();
        }
        return $option;
    }

    /**
     * Get the option in the normal way...
     *
     * @return array
     */
    public static function get_option(){
        $option = get_site_option(self::SITE_OPTION_DOMAINS);
        if (! is_array($option)){
            $option = self::option_default();
        }
        return $option;
    }

    /**
     * @return array
     */
    public static function set_option($option){
        if (! is_array($option)){
            $option = self::option_default();
        }
        update_site_option(self::SITE_OPTION_DOMAINS, $option);

    }

    /**
     * @return array
     */
    public static function get_sites_map(){
        global $wpdb;
        /** @var \wpdb $wpdb */
        $sites = $wpdb->get_results('SELECT * FROM ' . $wpdb->blogs);
        if (! is_array($sites)){
            $sites = array();
        }
        $map = array();
        foreach($sites as $site){
            $map[$site->blog_id] = $site;
        }
        return $map;
    }


    /**
     * @param bool $p
     * @return string
     */
    public static function lib_path($p = false){
        $lib_path = dirname(dirname(dirname(__DIR__)));
        if ($p && ! empty($p)){
            $lib_path .= '/' . $p;
        }
        return $lib_path;
    }

    /**
     * @param bool $p
     * @param array $data
     */
    public static function require_lib_path($p = false, $data = array()){
        extract($data);
        require self::lib_path($p);
    }

}