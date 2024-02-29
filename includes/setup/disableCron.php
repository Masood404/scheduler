<?php
require_once __DIR__.DIRECTORY_SEPARATOR."core-setup.php";

// Check if a cron job already exists.
if(!empty($existingCronjob)){
    // Then delete the existing cronjob.
    shell_exec("wsl crontab -l | wsl grep -v 'check_tasks.php' | wsl crontab -");
}
// Check if a cron job already exists.
if(!empty($existingDailyCron)){
    // Then delete the existing daily cronjob.
    shell_exec("wsl crontab -l | wsl grep -v 'check_tasks_dismiss.php' | wsl crontab -");
}
// Do nothing else if the cron job does not exist.