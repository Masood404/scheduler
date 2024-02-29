<?php
   // Identify either if php is in windows.
   $isWin = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

   // If is windows then have a prefix wsl for shell commands else leave it empty.
   $wsl = $isWin ? 'wsl' : '';

   // Php executable path for wsl. Useful for windows and won't effect other os.
   $phpExe = str_replace('C:', '/mnt/c', PHP_BINARY);

   // Cron job with the expression to run every minute.
   $cronjob = "*/5 * * * * ".$phpExe." ".realpath(__DIR__."/../scripts/check_tasks.php");

   // Replace the forward slashes to backslashes if any. Useful on windows and won't effect other os.
   $cronjob = str_replace("\\", "/", $cronjob);

   // Command to check if there is no duplicate cronjob.
   $existingCronCmd = "$wsl crontab -l | $wsl grep -F \"$cronjob\"";

   // Would be empty if a cronjob does not exists.
   $existingCronjob = trim(shell_exec($existingCronCmd));   

   // Daily Cron job which runs every 24 hours. The file which gets executed checks whether there are any tasks which should be dismissed or deleted.
   $dailyCron = "0 0 * * * $phpExe".realpath(__DIR__."/../scripts/check_tasks_dismiss.php");

   // Replace the foreward slashes to backslashes if any. 
   $dailyCron = str_replace("\\", "/", $dailyCron);

   // Command to check if there is no duplicate daily cron job.
   $existingDailyCronCmd = "$wsl crontab -l | $wsl grep -F \"$dailyCron\"";

   // WOuld be empty if the daily cronjob does not exist.
   $existingDailyCron = trim(shell_exec($existingDailyCronCmd));
    function deleteDirectory($dirPath) {
        if (is_dir($dirPath)) {
           $files = scandir($dirPath);
           foreach ($files as $file) {
              if ($file !== '.' && $file !== '..') {
                 $filePath = $dirPath . '/' . $file;
                 if (is_dir($filePath)) {
                    deleteDirectory($filePath);
                 } else {
                    unlink($filePath);
                 }
              }
           }
         rmdir($dirPath);
      }
   }
?>