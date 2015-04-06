<?php
use NowZoo\WPDomainMap\Plugin;
/**
 * @var array $option
 * @var bool $error
 * @var string $message
 * @var $sites
 * @var $ids_to_domains
 */
?>



<div class="wrap">
    <h2>Network Domain Settings</h2>

    <?php
    if (! empty($message)){
        ?>
        <div id="message" class="<?php echo ($error) ? 'error' : 'updated'?>">
            <p><?php echo $message?></p>
        </div>
    <?php
    }
    ?>

    <p>
        Enter custom domain names for your sites below.
    </p>
    <p>
        Don't include the URL scheme or a trailing slash. For example,
        <code>example.com</code> should be entered as is, <strong>not</strong>
        as <code>http://example.com/</code>.
    </p>
    <p>
        Leave the custom domain field blank for those sites you want
        to keep as subdomains of your main site.
    </p>





    <form method="post">
        <?php
        wp_nonce_field(Plugin::SITE_OPTION_DOMAINS, Plugin::SITE_OPTION_DOMAINS . '_nonce');
        ?>
        <table class="widefat">
            <thead>
            <tr>
                <th>ID</th>
                <th>Subdomain</th>
                <th>Custom Domain</th>

            </tr>
            </thead>
            <tbody>
            <?php
            foreach($sites as $id => $site){
                $id = intval($id);
                ?>
                <tr>
                    <td>
                        <?php echo $id ?>
                    </td>
                    <td>
                        <label for="<?php echo Plugin::SITE_OPTION_DOMAINS?>_<?php echo $id?>">
                            <?php echo $site->domain?>
                        </label>

                    </td>
                    <td>
                        <?php
                        if ($id === BLOG_ID_CURRENT_SITE){
                            ?>
                            Main Site
                            <?php
                        } else {
                            if (isset($ids_to_domains[$id])){
                                $domain = $ids_to_domains[$id]['domain'];
                            } else {
                                $domain = '';
                            }
                            ?>

                            <input
                                type="text"
                                class="widefat"
                                placeholder="example.com"
                                name="<?php echo Plugin::SITE_OPTION_DOMAINS?>[<?php echo $id?>]"
                                id="<?php echo Plugin::SITE_OPTION_DOMAINS?>_<?php echo $id?>"
                                value="<?php echo esc_attr($domain)?>"
                                >


                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
        submit_button('Save Settings');
        ?>
    </form>

</div>



