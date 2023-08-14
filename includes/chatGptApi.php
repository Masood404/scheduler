<?php
    require_once __DIR__ . "/index.php";
    //header('Content-Type: application/json; charset=utf-8');
    
    $apiKey = $config["Open_Ai_Key"];
    
    $hostname = "localhost";
    $database = "scheduler";
    $username = "root";
    $password = "";

    $conn = new mysqli($hostname, $username, $password, $database); // opening a mysqli database connection

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $is_requests_set = isset($_POST["gptInstance"]);

        if($is_requests_set){
            //Returns all the contents of the current gpt in the db
            function getGptContent(){
                global $conn;
                global $id;

                $query = "SELECT * FROM gptcontents
                WHERE id = $id;";
                $gptcontents = $conn->query($query)->fetch_all();

                return $gptcontents;
            }
            //Adds new content to the gptcontents db 
            function addGptContent(){
                global $conn;
                global $id;
                global $message;
                global $response;

                $query = "INSERT INTO gptcontents (id, message, response)
                VALUES ($id, '$message', '$response');";

                $conn->query($query);
            }

            $gptInstance =  $_POST["gptInstance"];

            $id = $gptInstance["id"];
            $message = $gptInstance["message"];
            $title = $gptInstance["title"];
            $response = "static response";

            if(isset(getGptInstances()[$id])){
                //add a new content record
                addGptContent();
            }
            else{
                //add a new gptinstance with a content record
                $gptInsertQuery = "INSERT INTO gptinstances (id, currentmessage, title)
                VALUE ($id, '$message', '$title');"; //adds a new gpt instance to the db

                $conn->query($gptInsertQuery);

                addGptContent();
            }
            $responseArr = array(
                "title" => $title,
                "currentMessage" => $message,
                "response" => $response
            );

            echo json_encode($responseArr, JSON_PRETTY_PRINT);

        }
        else{
            header("HTTP/1.0 403 Forbidden");
            echo '0';
        }
    }
    else if($_SERVER["REQUEST_METHOD"] === 'GET'){
        $features = array(
            "getGpt" => function(){
                global $conn;

                $gptInstances = getGptInstances();
                $gptContents = getGptContents();
                $gptInstancesObj = array();

                for($i = 0; $i < count($gptInstances); $i++){
                    $gptInstancesObj[$i] = $gptInstances[$i];
                    $gptInstancesObj[$i]["contents"] = array();

                    for($j = 0; $j < count($gptContents); $j++){
                        if($gptContents[$j]["id"] == $i){
                            array_push(
                                $gptInstancesObj[$i]["contents"],
                                $gptContents[$j]
                            );
                        }
                    }
                }
                
                $gptInstancesObj = json_encode($gptInstancesObj, JSON_PRETTY_PRINT);

                echo $gptInstancesObj;
            }
        );

        $isFeatureRequestSet = isset($_GET['feature']);

        if($isFeatureRequestSet){
            $feature = $_GET['feature'];
            $features['getGpt']();
        }

    }
    $conn->close();

    //Returns all the gptInstances in the db
    function getGptInstances(){
        global $conn;

        $query = "SELECT * FROM gptinstances";
        $gptInstances = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

        return $gptInstances;
    }
    //Returns all the gptContents in ascending order in the db
    function getGptContents(){
        global $conn;

        $query = "SELECT * FROM gptcontents
        ORDER BY id ASC";
        $gptContents = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

        return $gptContents;
    }
    function OpenAIAPI($requestParams = array(), $apiKey){
        $curl = curl_init();
        $headers = array(
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json"
        );
        $curlParams = $requestParams;

        $curlOptions = array(
            CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($curlParams),
            CURLOPT_RETURNTRANSFER => true
        );

        curl_setopt_array($curl, $curlOptions);

        $result = curl_exec($curl);

        return json_decode($result);
    }

?>