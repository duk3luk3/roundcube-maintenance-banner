# roundcube-maintenance-banner

Roundcube Maintenance Banner plugin

Code inspired by https://github.com/dharmann/info_banner

License: GPL3+

## Functionality

Using the config, set the maintenance period. This will have the following effect:

* Up to `$config['maintenance_pre']` seconds before $config['maintenance_start'], a banner announcing the maintenance will be shown
* Between `$config['maintenance_start']` and `$config['maintenance_end']`, a banner announcing that the maintenance is taking place will be shown. The login form will also be hidden.


