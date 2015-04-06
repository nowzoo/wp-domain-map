<?php
namespace NowZoo\WPDomainMap;

use NowZoo\WPUtils\WPUtils;

/**
 * Class AdminSettingsPanel
 * @package NowZoo\WPDomainMap
 *
 * Takes care of displaying the admin panel at /wp-admin/network/settings.php?page=nowzoo-domain-map
 */
class AdminSettingsPanel {


    private $message = '';
    private $error = false;

    private static $instance = null;


    /**
     * Singleton
     *
     * @return AdminSettingsPanel
     */
    public static function inst(){
        if (is_null(self::$instance)){
            self::$instance = new AdminSettingsPanel;
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct(){
        add_action( 'plugins_loaded', array($this, 'action_plugins_loaded') );
    }

    /**
     * Adds the hooks to display the panel and save the data.
     */
    public function action_plugins_loaded(){
        if (! is_admin()){
            return;
        }
        add_action('init', array($this, 'action_init'));
        add_action('network_admin_menu', array($this, 'action_admin_menu'));
    }

    /**
     * Set up the panel...
     */
    public function action_admin_menu(){
        $cap = 'manage_network';
        add_submenu_page('settings.php', 'Domain Mapping', 'Domain Mapping', $cap, Plugin::SITE_OPTION_DOMAINS, array($this, 'panel'));
    }

    /**
     * Display the panel...
     */
    public function panel(){

        $sites = Plugin::get_sites_map();
        $option = Plugin::get_option();
        $ids_to_domains = $option['ids_to_domains'];
        $subdomains_to_domains = $option['subdomains_to_domains'];
        $error = $this->error;
        $message = $this->message;
        Plugin::require_lib_path(
            'includes/admin_panel_settings.php',
            compact('option', 'error', 'message', 'sites', 'ids_to_domains', 'subdomains_to_domains')
        );
    }


    /**
     * Save the data...
     */
    public function action_init(){
        if (! is_admin()) return;
        if (! isset($_GET['page']) || Plugin::SITE_OPTION_DOMAINS !== $_GET['page']) return;
        if (! WPUtils::is_submitting()) return;
        $cap = 'manage_network';
        if (! current_user_can($cap)) return;

        if (! wp_verify_nonce($_POST[Plugin::SITE_OPTION_DOMAINS . '_nonce'], Plugin::SITE_OPTION_DOMAINS) ){
            return;
        }

        $option = WPUtils::trim_stripslashes_deep($_POST[Plugin::SITE_OPTION_DOMAINS]);
        $ids_to_domains = array();
        $subdomains_to_domains = array();
        $sites = Plugin::get_sites_map();
        foreach($option as $site_id => $domain){
            $domain = trim($domain);
            $domain = strtolower($domain);
            if (! empty($domain)){
                $ids_to_domains[intval($site_id)] = array(
                    'domain' => $domain,
                    'subdomain' => $sites[$site_id]->domain
                );
                $subdomains_to_domains[$sites[$site_id]->domain] = $domain;

            }
        }
        Plugin::set_option(compact('ids_to_domains', 'subdomains_to_domains'));
        $this->message = 'Domain settings updated!';
    }


}