<?php
/*
This dismisses|deletes tasks that have been before completed 24 hours ago.
Thus it is run every 24 hours using a cron job.
*/

require_once realpath(__DIR__."/../DBConn.php");

// Get the database.
$DBConn = DBConn::getInstance();

// The current time
$currTime = (new DateTime("now"))->getTimestamp();

// Query to delete all the tasks past 24 hours of their end time.
$query = 
<<<SQL
DELETE FROM tasks
WHERE tasks.endTime < ?
AND ? > tasks.endTime + 60 * 60 * 24;
SQL;

// Execute query after binding the parameter current time.
$DBConn->executeQuery($query, [$currTime, $currTime]);