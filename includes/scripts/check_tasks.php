<?php
/*
This file is used to send notification on schedule and auto completes task after 10 minutes.
It is run every 5 minutes using a cron job.
*/

require_once realpath(__DIR__."/../DBConn.php");
require_once __SCRIPTS__.DIR_S."Users.php";
require_once __VENDOR__.DIR_S."autoload.php";

use Minishlink\WebPush\WebPush;

// Get the Database 
$DBConn = DBConn::getInstance();

// DateTime variable which holds the current time
$dummyCurrTime = (new DateTime("now"))->getTimestamp();

#region Send a push notification to the user's subscription.
// Query checks if the next task's startTime falls within one minute of the current time
$query = 
<<<SQL
SELECT 
    tasks.username, tasks.title, tasks.startTime, tasks.endTime
FROM tasks
WHERE tasks.startTime >= ? 
      AND tasks.startTime < (? + 60 * 5)
      AND tasks.completed = 0
ORDER BY tasks.startTime ASC;
SQL;

// Execute query with parameters bind, fetch the result as associative array.
$result = $DBConn->executeQuery($query, [$dummyCurrTime, $dummyCurrTime])->fetch_all(MYSQLI_ASSOC);

// Run a loop for all the users when their task is started
foreach($result as $usersTask){
    try{
        // VAPID authentication
        $auth = [
            "VAPID" => [
                "subject" => "mailto:me@website.com",
                "publicKey" => MY_CONFIG["Public_VAPID"],
                "privateKey" => MY_CONFIG["Private_VAPID"],
            ]
        ];
        $webPush = new WebPush($auth);

        //payload for push in json 
        $payload = json_encode([
            "title" => "Scheduler",
            "body" => "Task Reminder: " . $usersTask["title"],
            "timings" => [
                "startTime" => $usersTask["startTime"],
                "endTime" => $usersTask["endTime"]
            ],
            "url" => "http://localhost/scheduler/",
            "icon" => "http://localhost/scheduler/assets/images/Gpt Icon.png"
        ]);

        // Get the subscription.
        $subscription = Users::getSubscription($usersTask["username"]);

        //executing a single push notification to the subscription 
        $webPush->sendOneNotification(
            $subscription,
            $payload,
            /*
                Time To Live (TTL, in seconds) is how long a push message is retained by the push service (eg. Mozilla) in case
                the user browser is not yet accessible (eg. is not connected). You may want to use a very long time for important 
                notifications. The default TTL is 4 weeks. However, if you send multiple nonessential notifications, 
                set a TTL of 0: the push notification will be delivered only if the user is currently connected. 
                For other cases, you should use a minimum of one day if your users have multiple time zones, and if they don't 
                several hours will suffice.
            */
            ["TTL" => 5000]
        );
    }
    catch(SubscriptionNotFoundException $e){
        // When no subscription is found stop the execution the rest of the script.
        die();
    }
    catch(Exception $e){
        // Log the exception 
        php_error_log($e->getMessage());
    }
}
#endregion

#region Auto complete finished tasks

// Query to automatically update the completed status of a task record after 10 minutes of its endtime.
$query = 
<<<SQL
UPDATE tasks
SET tasks.completed = 1
WHERE tasks.endTime < (? - 60 * 10);
SQL;

// Execute the query 
$DBConn->executeQuery($query, $dummyCurrTime);
#endregion
