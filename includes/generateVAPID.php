<?php
    /**
     * This file prints VAPID keys to the command line.
     * Take the printed keys and add them to the array of $PATH_TO_YOUR_XAMPP/.config/config.php with keys being:
     * Public_VAPID => "$YOUR_PUBLIC_VAPID",
     * Private_VAPID => "$YOUR_PRIVATE_VAPID.
     * Remember to replace your VAPID keys to the actual generated keys from this file.
     */
    require_once "packages\/vendor\/autoload.php";

    use Minishlink\WebPush\VAPID;

    print_r(VAPID::createVapidKeys());

?>