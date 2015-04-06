# NowZoo WordPress Domain Mapping

Simplified domain mapping for WordPress networks, installed via Composer.

This package is meant for WordPress installations already using Composer. If you need a "normal" WordPress plugin, that you can install without Composer, you might try [MU Domain Mapping](https://wordpress.org/plugins/wordpress-mu-domain-mapping/). We might turn this into a plugin at some point.

## Why?

I got frustrated with MU Domain Mapping -- it's too complicated for the purposes of a small network.

Some advantages of this code:

- Custom domain information is stored in one site option. 
- The `home` and `siteurl` options for each blog are ignored and not changed.
- Works well with a mix of subdomain sites and sites with custom domains.
- It's independent of DNS. You don't have to tell it IP addresses or CNAMEs.

## Installation

````
$ composer require nowzoo/wp-domain-map
````

After installation, copy `sunrise.php` (included in the package root directory) to the `wp-content` directory.

Instantiate the admin panel by including this code somewhere (e.g in `wp-content/mu-plugins/index.php`):

````
<?php
NowZoo\WPDomainMap\AdminSettingsPanel::inst();
````

Of course, you need to include the autoloader before this. I put this at the top `wp-config.php`:

````
require_once __DIR__ . '/vendor/autoload.php';
````


