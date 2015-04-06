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

    <pre>
        <?php var_dump($sites)
?>    </pre>

    <form method="post">
        <?php
        wp_nonce_field(Plugin::SITE_OPTION_DOMAINS, Plugin::SITE_OPTION_DOMAINS . '_nonce');
        ?>
        <table class="form-table">
            <tbody>
            </tbody>
        </table>
        <?php
        submit_button('Save Settings');
        ?>
    </form>
</div>


