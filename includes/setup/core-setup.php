<?php
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