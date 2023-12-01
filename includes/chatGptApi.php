<?php
    require_once __DIR__ . DIRECTORY_SEPARATOR ."DBConn.php";
    //header('Content-Type: application/json; charset=utf-8');
    
    $apiKey = MY_CONFIG["Open_Ai_Key"];
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $is_requests_set = isset($_POST["gptInstance"]);

        if($is_requests_set){
            //Returns all the contents of the current gpt in the db
            function getGptContent(){
                $_DBConn = DBConn::getInstance()->getConnection();
                global $id;

                $query = "SELECT * FROM gptcontents
                WHERE id = $id;";
                $gptcontents = $_DBConn->query($query)->fetch_all(MYSQLI_ASSOC);

                return $gptcontents;
            }
            //Adds new content to the gptcontents db 
            function addGptContent(){
                $_DBConn = DBConn::getInstance()->getConnection();
                global $id;
                global $message;
                global $response;

                $query = "INSERT INTO gptcontents (id, message, response)
                VALUES (?, ?, ?);";

                $stmt = $_DBConn->prepare($query);

                if($stmt){
                    //Bind parameters to the statment
                    $stmt->bind_param("iss", $id, $message, $response);

                    if(!$stmt->execute()){
                        echo "Insert failed: ". $stmt->error;
                    }
                }
                else{
                    echo "Preperation failed: ".$_DBConn->error;
                }
            }

            $gptInstance =  $_POST["gptInstance"];

            $id = $gptInstance["id"];
            $message = $gptInstance["message"];
            $title = $gptInstance["title"];
            $response = "static response";

            $arrToSend = array(
                "model" => "gpt-3.5-turbo",
                "messages" => array(
                    array(
                        "role" => "system",
                        "content" => "You are an AI assistant which will help users problems on any subject. To display a more readable format to the user, your answer must be formatted in Markdown."
                    )
                )
            );

            if(isset(getGptInstances()[$id])){
                //add a new content record
                $gptContent = getGptContent();
                for($i = 0; $i < count($gptContent); $i++){
                    array_push(
                        $arrToSend["messages"],
                        array(
                            "role" => "user",
                            "content" => $gptContent[$i]["message"]
                        )
                    );
                    array_push(
                        $arrToSend["messages"],
                        array(
                            "role" => "assistant",
                            "content" => $gptContent[$i]["response"]
                        )
                    );
                }

                array_push(
                    $arrToSend["messages"],
                    array(
                        "role" => "user",
                        "content" => $message
                    )

                );

                $result = OpenAIAPI($arrToSend, $apiKey);
                $response = $result->choices[0]->message->content;
                
                addGptContent();
            }
            else{
                //Generate a title based on the first message with open ai
                $arrForTitle = array(
                    "model" => "gpt-3.5-turbo",
                    "messages" => array(
                        array(
                            "role" => "system",
                            "content" => "you give a title name based on context while the max title limit being 15 letters."
                        ),
                        array(
                            "role" => "user",
                            "content" => $message
                        )
                    )
                );

                $updatedTitle = OpenAIAPI($arrForTitle, $apiKey)->choices[0]->message->content;
                $title = $updatedTitle;

                //add a new gptinstance with a content record
                $gptInsertQuery = "INSERT INTO gptinstances (id, currentmessage, title)
                VALUE ($id, '$message', '$title');"; //adds a new gpt instance to the db

                $_DBConn->query($gptInsertQuery);

                array_push(
                    $arrToSend["messages"],
                    array(
                        "role" => "user",
                        "content" => $message
                    )

                );
                $result = OpenAIAPI($arrToSend, $apiKey);
                $response = $result->choices[0]->message->content;

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
                $_DBConn = DBConn::getInstance()->getConnection();

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
    else if($_SERVER["REQUEST_METHOD"] === 'DELETE'){
        $isGptDeleteRequestSet = isset($_GET['gptDelete']);

        if($isGptDeleteRequestSet){
            $gptId = $_GET['gptDelete'];

            $instanceDeleteQuery = "DELETE FROM gptinstances
            WHERE id = $gptId;";
            $contentDeleteQuery = "DELETE FROM gptcontents
            WHERE id = $gptId;";

            $_DBConn->query($instanceDeleteQuery);
            $_DBConn->query($contentDeleteQuery);
        }
    }
    $_DBConn->close();

    //Returns all the gptInstances in the db
    function getGptInstances(){
        $_DBConn = DBConn::getInstance()->getConnection();

        $query = "SELECT * FROM gptinstances";
        $gptInstances = $_DBConn->query($query)->fetch_all(MYSQLI_ASSOC);

        return $gptInstances;
    }
    //Returns all the gptContents in ascending order in the db
    function getGptContents(){
        $_DBConn = DBConn::getInstance()->getConnection();

        $query = "SELECT * FROM gptcontents
        ORDER BY id ASC";
        $gptContents = $_DBConn->query($query)->fetch_all(MYSQLI_ASSOC);

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