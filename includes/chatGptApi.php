<?php
    require_once __DIR__ . "/index.php";
    //header('Content-Type: application/json; charset=utf-8');
    
    $apiKey = $config["Open_Ai_Key"];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $is_requests_set = isset($_POST["gptInstance"]);

        if($is_requests_set){
            $gptInstances = (array)json_decode(file_get_contents("tempDB.json"));
            $gptInstance = $_POST["gptInstance"];
            $gptCurrentId = $gptInstance["id"];

            $gptContents = array();
            $arrToSend = array(
                "model" => "gpt-3.5-turbo",
                "messages" => array(
                    array(
                        "role" => "system",
                        "content" => "You are an AI assistant which will help users problems on any subject"
                    )
                ),
            );

            if(isset($gptInstances[$gptCurrentId])){
                $gptContents = $gptInstances[$gptCurrentId]->contents;

                for($i = 0; $i < count($gptContents); $i++){
                    array_push($arrToSend["messages"], array(
                        "role" => "user",
                        "content" => $gptContents[$i]->message
                    ));
                    array_push($arrToSend["messages"], array(
                        "role" => "assistant",
                        "content" => $gptContents[$i]->response
                    ));
                }
                array_push($arrToSend["messages"], array(
                    "role" => "user",
                    "content" => $gptInstance["message"]
                ));
                $result = OpenAIAPI($arrToSend, $apiKey);
                $response = $result->choices[0]->message->content;

                array_push($gptContents, array(
                    "message" => $gptInstance["message"],
                    "response" => $response
                ));
            }
            else{
                array_push($arrToSend["messages"], array(
                    "role" => "user",
                    "content" => $gptInstance["message"]
                ));
                $result = OpenAIAPI($arrToSend, $apiKey);
                $response = $result->choices[0]->message->content;

                array_push($gptContents, array(
                    "message" => $gptInstance["message"],
                    "response" => $response
                ));
            }
            $gptInstance["contents"] = $gptContents;

            $gptInstances[$gptCurrentId] = $gptInstance;

            file_put_contents("tempDB.json", json_encode($gptInstances, JSON_PRETTY_PRINT));

            echo json_encode($gptInstance["contents"][sizeof($gptInstance["contents"]) - 1], JSON_PRETTY_PRINT);
        }
        else{
            header("HTTP/1.0 403 Forbidden");
            echo '0';
        }
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