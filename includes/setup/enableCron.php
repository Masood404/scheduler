<?php
require_once __DIR__.DIRECTORY_SEPARATOR."core-setup.php";

// If no previous duplicate cron job is found
if(empty($existingCronjob)){
    // Then create a new cronjob which runs every minute.
    shell_exec("($wsl crontab -l ; echo \"$cronjob\") | $wsl crontab -");
}
// If no previous duplicate daily cron job is found
if(empty($existingDailyCron)){
    // Then create a new cronjob which runs daily .
    shell_exec("($wsl crontab -l ; echo \"$dailyCron\") | $wsl crontab -");
}
// Do nothing else if they already exists.