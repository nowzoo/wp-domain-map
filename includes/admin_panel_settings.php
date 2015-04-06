<?php
use NowZoo\WPDomainMap\Plugin;
/**
 * @var array $option
 * @var bool $error
 * @var string $message
 * @var $sites
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
            foreach($sites as $site){
                ?>
                <tr>
                    <td>
                        <?php echo $site->blog_id?>
                    </td>
                    <td>
                        <label for="<?php echo Plugin::SITE_OPTION_DOMAINS?>_<?php echo $site->blog_id?>">
                            <?php echo $site->domain?>
                        </label>

                    </td>
                    <td>
                        <?php
                        if (intval($site->blog_id) === BLOG_ID_CURRENT_SITE){
                            ?>
                            Main Site
                            <?php
                        } else {
                            if (isset($option[$site->blog_id])){
                                $domain = $option[$site->blog_id];
                            } else {
                                $domain = '';
                            }
                            ?>

                            <input
                                type="text"
                                class="widefat"
                                placeholder="example.com"
                                name="<?php echo Plugin::SITE_OPTION_DOMAINS?>[<?php echo $site->blog_id?>]"
                                id="<?php echo Plugin::SITE_OPTION_DOMAINS?>_<?php echo $site->blog_id?>"
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


