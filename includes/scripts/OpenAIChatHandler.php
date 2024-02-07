<?php
    /**
     * This class contains static methods for handling chat prompts, 
     * generating titles, and interacting with the OpenAI chat completion API.
     */
    class OpenAIChatHandler{
        private function __construct(){
            //Uninstantiable
        }
        /**
         * Generates a message prompt using GPT-3.5 Turbo model based on the given prompt.
         *
         * @param string $prompt The prompt to be used for generating the message.
         * @param null|array $prevMessages Optional. An array containing previous messages (if any).
         * @return string The generated message response.
         */
        public static function messagePrompt(string $prompt, null|array $prevMessages = null){
            // Constructing the request parameters for the OpenAI API
            $requestParams = [
                "model" => "gpt-3.5-turbo",
                "messages" => [ 
                    [
                        "role" => "user",
                        "content" => $prompt
                    ]
                ]
            ];
            // If there are previous messages, prepend them to the current message
            if($prevMessages != null){
                //Prepend previous messages
                array_unshift($requestParams["messages"], $prevMessages);
            }

            try{
                // Call the ChatCompletion method
                $response = self::ChatCompletion($requestParams);

                if (isset($response["choices"][0]["message"]["content"])) {
                    // Retrieve the generated response message from the API response
                    $responseContent = $response["choices"][0]["message"]["content"];
                    return $responseContent;
                }
                else{
                    $error = json_encode($response);
                    throw new Exception("Unexpected response format from the external API, Open Ai: $error");
                }
            }
            catch(Exception $e){
                // Handle API call failures or unexpected response formats
                throw new Exception('Failed to generate response from the chatbot: ' . $e->getMessage());
            }
        }
        /**
         * Generate a title using the gpt-3.5-turbo chat completer.
         * @param string $prompt Preferably a human readable text.
         */
        public static function GenerateTitle($prompt) : string{
            $systemMessage = [
                "role" => "system",
                "content" => "You give a title name based on context while the max title limit being 15 letters."
            ];

            $title = self::messagePrompt($prompt, $systemMessage);

            return $title;
        }
        /**
         * Sends an HTTP request to the OpenAI chat completion endpoint using cURL.
         *
         * This method allows you to interact with OpenAI's chat completion endpoint to generate text based on provided parameters.
         *
         * @param array $requestParams An array containing parameters for the request. Check the documentation on the official OpenAI site for possible parameters:
         *                            - https://platform.openai.com/docs/guides/text-generation/json-mode
         * @return array|bool Returns an array containing the API response in case of success. Returns false if the request fails.
         */
        public static function ChatCompletion(array $requestParams) : array{
            $apiKey = MY_CONFIG["Open_Ai_Key"];
            
            $curl = curl_init();
            $headers = [
                "Authorization: Bearer $apiKey",
                "Content-Type: application/json"
            ];

            $curlOptions = [
                CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($requestParams),
                CURLOPT_RETURNTRANSFER => true
            ];

            curl_setopt_array($curl, $curlOptions);

            try{
                $response = curl_exec($curl);
                if(!$response){
                    throw new Exception(curl_error($curl), curl_errno($curl));
                }
            }
            catch(Exception $e){
                // Handle the exception.
                // Log the error or perform actions based on the exception.
                // Example: Logging the exception.
                // error_log("cURL Exception: " . $e->getMessage() . " (Error Code: " . $e->getCode() . ")");

                // Close the cURL session.
                curl_close($curl);

                // re-throw the exception
                throw new Exception("ChatCompletion, cURL Exception: " . $e->getMessage() . " (Error Code: " . $e->getCode() . ")");
            }

            //Close the cUrl session and return the api's response decoded to an assoc array.
            curl_close($curl);
            return json_decode($response, true);
        }
    }

?>