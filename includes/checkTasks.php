<?php
    /*
        Run this file in a cron job or task scheduler with intervals being 1 minute.
        This file will send notifications based on task's time from the database,
        it will add other task's based on current task's interval expression.
        An Interval expressions is like a cron expression. For more information on cron expressions:
        https://docs.oracle.com/cd/E12058_01/doc/doc.1014/e12030/cron_expressions.htm
    */

    require_once __DIR__ . DIRECTORY_SEPARATOR ."Task.php";
    require_once __VENDOR__ . DIR_S . "autoload.php";

    use Minishlink\WebPush\WebPush;
    use Minishlink\WebPush\Subscription;


    $timezone = new DateTimeZone("UTC");

    //get current time which is snapped to 00 seconds
    $currentTime = new DateTime("now");
    $currentTime->setTime( $currentTime->format("h") , $currentTime->format("i") , 0);

    $currentTime->setTimezone($timezone);
            
    $nextTask = getNextTask();
    $allTasks = getTask();

    if(isset($nextTask["startTime"])){

        //get next time which is snapped to 00 seconds
        $nextTaskTime = new DateTime("@" . $nextTask["startTime"]);
        $nextTaskTime->setTime( $nextTaskTime->format("h") , $nextTaskTime->format("i") , 0);

        $nextTaskTime->setTimezone($timezone);

        if($currentTime->getTimestamp() == $nextTaskTime->getTimestamp()){
            $nextTaskStart = $nextTask["startTime"];
            $nextTaskEnd = $nextTask["endTime"];

            //run on when current time becomes the same as next pending tasks 

            //send push notification 

            //browser authorization
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
                "body" => "Task Reminder: " . $nextTask["title"],
                "timings" => [
                    "startTime" => $nextTaskStart,
                    "endTime" => $nextTaskEnd
                ],
                "url" => "http://localhost/scheduler/",
                "icon" => "http://localhost/scheduler/assets/images/Gpt Icon.png"
            ]);
        
            //executing a single push notification to the subscription 
            $webPush->sendOneNotification(
                //converting subscription type from associative to subscription which is located in json format in endpoint.json
                Subscription::create(json_decode(file_get_contents(__DIR__ . "\/endpoint.json"), true)),
                $payload,
                /*
                * Time To Live (TTL, in seconds) is how long a push message is retained by the push service (eg. Mozilla) in case
                * the user browser is not yet accessible (eg. is not connected). You may want to use a very long time for important 
                * notifications. The default TTL is 4 weeks. However, if you send multiple nonessential notifications, 
                * set a TTL of 0: the push notification will be delivered only if the user is currently connected. 
                * For other cases, you should use a minimum of one day if your users have multiple time zones, and if they don't 
                * several hours will suffice.
                */
                ["TTL" => 5000]
            );
        }
        
    }   

    for($i = 0; $i < count($allTasks); $i++){
        $taskEndTime = new DateTime("@" . $allTasks[$i]["endTime"], $timezone);

        $taskEndTime->setTimezone($timezone);

        //If the current time is passed 24 hours from any task instance auto delete or dismiss that task
        if($currentTime->getTimestamp() > $taskEndTime->getTimestamp() + (60 * 60 * 24)){
            deleteTask($allTasks[$i]["id"]);
        }
    }
?>