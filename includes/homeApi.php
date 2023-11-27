<?php

use Minishlink\WebPush\Subscription;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "Task.php";
    require_once __DIR__ . DIR_S ."Users.php";
    
    //$apiKey = MY_CONFIG["Open_Ai_Key"];
    
    /**
     * A task is array/object/instance/record stored in the db. When recieving properties of this from http requests,
     * the id and other properties will be set or proccessed by this file.
     */

    if($_SERVER["REQUEST_METHOD"] === "GET"){
        if(isset($_GET["taskId"])){
            //Echo requested tasks instance object from the database
            $taskId = $_GET["taskId"];
            echo json_encode(getTask($taskId));
        }
        else if(isset($_GET["feature"])){
            $features = array(
                /**
                 * Echo's VAPID public key.
                 */
                "getVapid" => function () {
                    echo MY_CONFIG["Public_VAPID"];
                },
                /**
                 * Echo's RSA Public Key
                 */
                "getPublicKey" => function() {
                    echo MY_CONFIG["Public_Key"];
                }
            );
            if(isset($features[$_GET["feature"]])){
                $features[$_GET["feature"]]();
            }
            else{
                //feature not available
                http_response_code(501);
            }
        }
        else{
            //Echo all the task objects from the database
            echo json_encode(getTask());
        }
    }
    else if($_SERVER["REQUEST_METHOD"] === "POST"){
        if(isset($_POST["task"])){
            //Create a task object to the database and echo the task object

            //Get parameters
            $task = $_POST["task"];
            $title = $task["title"];

            //Seconds data not included thus the seconds would be snap to 00
            $startTime = $task["startTime"] / 1000;
            $endTime = $task["endTime"] / 1000;

            $tasks = getTask();


            //Checking conditions, example: Date overlap between tasks
            if(!hasDateOverlap($task)){    
                //Generate Index for id
                $id = count(getTask()); //Id is the value of index to the tasks array;
        
                //Query 
                $addQ = "INSERT INTO tasks (id ,title, startTime, endTime)
                VALUES (?, ?, ?, ?);";
        
                $stmt = $conn->prepare($addQ);
        
                if($stmt){
                    //Bind Parameters
                    $stmt->bind_param("isii", $id, $title, $startTime, $endTime);
        
                    //Exectute add task query
                    if($stmt->execute()){
                        //Select current task instance
                        echo json_encode(getTask($id));
                    }
                    //Execution failure
                    else{
                        http_response_code(406);
                    }
                }
                else{
                    //Statement perperation faliure
                    http_response_code(500);
                }
            }
            else{
                //Conditions not met
                http_response_code(406);
            }
        }
        else if(isset($_POST["userData"])){
           $enc_data = $_POST["userData"];
           $subscription = null;

           if($_POST["aesKey"]){
                $aesKey = $_POST["aesKey"];
                $subscription = $_POST["subscription"];

               //Decrypt the AES key with RSA
               $aesKey = Users::decrypt($aesKey);
    
               //Decrypt the subscription with the aquired decrypted AES key
               $subscription = Users::decryptWithAes($subscription, $aesKey);
           }
            
           //Decrypt and convert the data to an associative array
           $user_data = Users::decrypt($enc_data);
           $user_data = json_decode($user_data, true);
           
           try{
                Users::createUser($user_data["username"], $user_data["password"], $user_data["email"], $subscription);
           }
           catch(Exception $e){
                if($e->getCode() == 1062){
                    //Duplicate username entry found
                    http_response_code(409);
                    echo "Duplicate Entry";
                }
                else{
                    http_response_code(500);
                    echo "Error: " . $e->getMessage();
                }
           }
        }
        else if(isset($_POST["loginData"])){
            $enc_data = $_POST["loginData"];

            //Decrypt and convert data to an associative array
            $user_data = Users::decrypt($enc_data);
            $user_data = json_decode($user_data, true);

            try{
                //Echo the authorization token
                echo Users::Authenticate($user_data["username"], $user_data["password"]);
            }
            catch(Exception $e){
                http_response_code(401);
                echo $e->getMessage();
            }
        }
        else if($_POST["subscription"]){
            /*Get the subscribtion object and store it in the endpoint file.
            I am sorry that i currently can not make it multi-user oriented,
            I did not considered this from the start and it would break my database
            if i start now without much knowledge.*/
            $subscription = json_encode($_POST["subscription"], JSON_PRETTY_PRINT);
            file_put_contents("endpoint.json", $subscription);
        }
    }
    else if($_SERVER["REQUEST_METHOD"] === "PUT"){
        //Put request getter variable
        parse_str(file_get_contents("php://input"), $_PUT);

        $taskId = $_PUT["taskId"];

        if(isset($_PUT["completedStatus"])){
            //Update task instance's completed status in the db

            $completedStatus = $_PUT["completedStatus"];
            
    
            //Task id does not exist or out of bounds
            if($taskId > count(getTask()) || $taskId < 0){
                http_response_code(406);
            }
            else{
                $updateStatusQ = "UPDATE tasks
                SET completed = $completedStatus
                WHERE id = $taskId;";
    
                $conn->query($updateStatusQ);
            }
        }
        else if(isset($_PUT["intervalStr"])){
            //Update task instance's day interval string in the db

            $intervalStr = $_PUT["intervalStr"];

            $updateIntervalStrQ =  "UPDATE tasks
            SET dayIntervals = $intervalStr
            WHERE id = $taskId;";

            $conn->query($updateIntervalStrQ);
        }
    }
    else if($_SERVER["REQUEST_METHOD"] === "DELETE"){
        //Delete request getter variable
        parse_str(file_get_contents("php://input"), $_DELETE);

        if(isset($_DELETE["taskId"])){
            //Delete task instance from the db
    
            //Get Properties
            $taskId = $_DELETE["taskId"];
    
            if(!deleteTask($taskId)){
                http_response_code(406);
            }
        }

    }
