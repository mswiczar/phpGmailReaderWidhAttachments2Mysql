<?php
  //  error_reporting(E_ERROR);
    date_default_timezone_set('America/Los_Angeles');
    include_once "import_IM.php";
    include_once "import_ADX.php";
    include_once "import_ADWORDS.php";
    include_once "import_ANALYTICS.php";
    include_once "import_FB.php";

require '/usr/local/composer' . '/vendor/autoload.php';
if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}


function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Gmail API PHP Quickstart');
    $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }
    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));
            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);
            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}
// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Gmail($client);
// Print the labels in the user's account.
$user = 'me';

    
    

    
    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    function expandHomeDirectory($path) {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }
    
    /**
     * Get list of Messages in user's mailbox.
     *
     * @param Google_Service_Gmail $service Authorized Gmail API instance.
     * @param string $userId User's email address. The special value 'me'
     * can be used to indicate the authenticated user.
     * @return array Array of Messages.
     */
    function listMessages($service, $userId, $optArr = []) {
        $pageToken = NULL;
        $messages = array();
        do {
            try {
                if ($pageToken) {
                    $optArr['pageToken'] = $pageToken;
                }
                $messagesResponse = $service->users_messages->listUsersMessages($userId, $optArr);
                if ($messagesResponse->getMessages()) {
                    $messages = array_merge($messages, $messagesResponse->getMessages());
                    $pageToken = $messagesResponse->getNextPageToken();
                }
            } catch (Exception $e) {
                print 'An error occurred: ' . $e->getMessage();
            }
        } while ($pageToken);
        return $messages;
    }
    
    function getHeaderArr($dataArr) {
        $outArr = [];
        foreach ($dataArr as $key => $val) {
            $outArr[$val->name] = $val->value;
        }
        return $outArr;
    }
    
    function getBody($dataArr) {
        $outArr = [];
        foreach ($dataArr as $key => $val) {
            $outArr[] = base64url_decode($val->getBody()->getData());
            break; // we are only interested in $dataArr[0]. Because $dataArr[1] is in HTML.
        }
        return $outArr;
    }
    
    function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
    
    function getMessage($service, $userId, $messageId) {
        try {
            $message = $service->users_messages->get($userId, $messageId);
            //print 'Message with ID: ' . $message->getId() . ' retrieved.' . "\n";
            
            return $message;
        } catch (Exception $e) {
            print 'An error occurred: ' . $e->getMessage();
        }
    }
    
    function listLabels($service, $userId, $optArr = []) {
        $results = $service->users_labels->listUsersLabels($userId);
        
        if (count($results->getLabels()) == 0) {
            print "No labels found.\n";
        } else {
            print "Labels:\n";
            foreach ($results->getLabels() as $label) {
                printf("- %s\n", $label->getName());
            }
        }
    }
    
    
    function getAttachment($messageId, $partId, $userId)
    {
        try {
            $client = getClient();
            $gmail = new Google_Service_Gmail($client);
            $message = $gmail->users_messages->get($userId, $messageId);
            $message_payload_details = $message->getPayload()->getParts();
            $attachmentDetails = array();
            $attachmentDetails['attachmentId'] = $message_payload_details[$partId]['body']['attachmentId'];
            $attachmentDetails['headers'] = $message_payload_details[$partId]['headers'];
            $attachment = $gmail->users_messages_attachments->get($userId, $messageId, $attachmentDetails['attachmentId']);
            $attachmentDetails['data'] = $attachment->data;
            return ['status' => true, 'data' => $attachmentDetails];
        } catch (\Google_Service_Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
    
    function base64_to_jpeg($base64_string, $content_type) {
        $find = ["_","-"]; $replace = ["/","+"];
        $base64_string = str_replace($find,$replace,$base64_string);
        $url_str = 'data:'.$content_type.','.$base64_string;
        $base64_string = "url(".$url_str.")";
        $data = explode(',', $base64_string);
        return base64_decode( $data[ 1 ] );
    }
  

    
    // Get the messages in the user's account.
    $messages = listMessages($service, $user, [
                             //'maxResults' => 5, // Return 20 messages.
                             'labelIds' => 'INBOX', // Return messages in inbox.
                             ]);

    
    
    $elto = 0;
    foreach ($messages as $message)
    {
        $elto++;
        
        if ($elto >120)
        {
            exit;
        }
        
        
        print "Total: " .  $elto."\n";
        //print 'Message with ID: ' . $message->getId() . "\n";
        $msgObj = getMessage($service, $user, $message->getId());
        $headerArr = getHeaderArr($msgObj->getPayload()->getHeaders());


        
        $string_destination_path ="";
        $string_destination_path = "./destination/";
        
        if (empty($headerArr['Subject']))
        {
        }
        else
        {
            $pos = strpos($headerArr['Subject'],"Google Adwords Report");
            if ($pos === false)
            {
                echo "";
            }
            else
            {
                echo $headerArr['Subject'];
                echo "\n";
                echo "AdWords\n";
                $string_destination_path = $string_destination_path."AdWords".$message->getId();
                if(file_exists($string_destination_path ))
		{
			echo "El archivo ya existe ".$string_destination_path. "\n";
			 continue;

		}
                $message_payload_details = $msgObj->getPayload()->getParts();
                //print_r($message_payload_details);
                $attachId = $message_payload_details[1]['body']['attachmentId'];
                // print ("attachId: ". $attachId ."\n");
                $attach = $service->users_messages_attachments->get($user, $message->getId(), $attachId);
                $data =$attach['data'];
                //print_r($attach['data']);
                //print ("\n");
                $data = strtr($data, array('-' => '+', '_' => '/'));
                $myfile = fopen($string_destination_path, "w+");;
                fwrite($myfile, base64_decode($data));
                fclose($myfile);
                
                /*
                 foreach ($message_payload_details as $key => $value)
                 {
                 if ( isset($value->body->attachmentId) && !isset($value->body->data))
                 {
                 //print "partid: " .$value['partId']. "\n";
                 array_push($files, $value['partId']);
                 }
                 }
                 */
                
                
                echo $string_destination_path. "\n";
                
                $objADW = new MyImportADWORDS();
                
                if ($objADW->importADWORDS($string_destination_path))
                {
                    print "Imported: \n";
                }
                else
                {
                    print "Error - NOT IMPORTED \n";
                }
                $objADW = NULL;
                
                
                
                
            }
            

            $pos = strpos($headerArr['Subject'],"Intent Media  Cross Product Report");
            if ($pos === false)
            {
                echo "";
            }
            else
            {
                echo $headerArr['Subject'];
                echo "\n";
                echo "InterMedia\n";
                $string_destination_path = $string_destination_path."IM".$message->getId();
               
               if(file_exists($string_destination_path ))
                {
                        echo "El archivo ya existe ".$string_destination_path. "\n";
                         continue;

                }

                $message_payload_details = $msgObj->getPayload()->getParts();
               // print_r($message_payload_details);
                $attachId ="";
		try
		{
			$attachId = $message_payload_details[0]['body']['attachmentId'];
                } catch (\Google_Service_Exception $e) {
            		echo  $e->getMessage();
               }
	       
                if($attachId =="")
		{
                  try
                  {
                        $attachId = $message_payload_details[1]['body']['attachmentId'];
                  } catch (\Google_Service_Exception $e) {
                        echo $e->getMessage();
                  }
               }
		
        


		//print ("attachId: ". $attachId ."\n");
                $attach = $service->users_messages_attachments->get($user, $message->getId(), $attachId);
                $data =$attach['data'];
                //print_r($attach['data']);
                //print ("\n");
                $data = strtr($data, array('-' => '+', '_' => '/'));
                $myfile = fopen($string_destination_path, "w+");;
                fwrite($myfile, base64_decode($data));
                fclose($myfile);
                
                /*
                foreach ($message_payload_details as $key => $value)
                {
                    if ( isset($value->body->attachmentId) && !isset($value->body->data))
                    {
                        //print "partid: " .$value['partId']. "\n";
                        array_push($files, $value['partId']);
                    }
                }
               */
                
                
                echo $string_destination_path. "\n";
                
                $objIM = new MyImportIM();
               
                if ($objIM->importIMCross($string_destination_path))
                {
                    print "Imported: \n";
                }
                else
                {
                    print "Error - NOT IMPORTED \n";
                }
                $objIM = NULL;
            }
            
            
            /*
            $pos = strpos($headerArr['Subject'],"Your Daily Facebook Ads Report");
            if ($pos === false)
            {
                echo "";
            }
            else
            {
                print_r($headerArr);
                die();
                
                echo $headerArr['Subject'];
                echo "\n";
                echo "Facebook\n";


            }
            */
            
            


            
            $pos = strpos($headerArr['Subject'],"ADX Revenue By Country and Device");
            if ($pos === false)
            {
                echo "";
            }
            else
            {
                echo $headerArr['Subject'];
                echo "\n";
                echo "ADX\n";
                $string_destination_path = $string_destination_path."ADX".$message->getId();
          if(file_exists($string_destination_path ))
                {
                        echo "El archivo ya existe ".$string_destination_path. "\n";
                         continue;

                }    

            //echo $string_destination_path. "\n";
                $message_payload_details = $msgObj->getPayload()->getParts();
                //print_r($message_payload_details);
                $attachId = $message_payload_details[1]['body']['attachmentId'];
                //print ("attachId: ". $attachId ."\n");
                $attach = $service->users_messages_attachments->get($user, $message->getId(), $attachId);
                $data =$attach['data'];
                //print_r($attach['data']);
                //print ("\n");
                $data = strtr($data, array('-' => '+', '_' => '/'));
                $myfile = fopen($string_destination_path, "w+");;
                fwrite($myfile, base64_decode($data));
                fclose($myfile);
                
                /*
                 foreach ($message_payload_details as $key => $value)
                 {
                 if ( isset($value->body->attachmentId) && !isset($value->body->data))
                 {
                 //print "partid: " .$value['partId']. "\n";
                 array_push($files, $value['partId']);
                 }
                 }
                 */
                
                
                echo $string_destination_path. "\n";
                
                $objADX = new MyImportADX();
                
                if ($objADX->importGoogleADX($string_destination_path))
                {
                    print "Imported: \n";
                }
                else
                {
                    print "Error - NOT IMPORTED \n";
                }
                $objADX = NULL;
                
                
                
                
                
            }
            
            
            

            $pos = strpos($headerArr['Subject'],"Google Analytics: Click Out By Country and Device,Click Out By Country and Device");
            if ($pos === false)
            {
                echo "";
            }
            else
            {
                echo $headerArr['Subject'];
                echo "\n";
                echo "Analytics\n";
                $string_destination_path = $string_destination_path."Analytics".$message->getId();
                

          if(file_exists($string_destination_path ))
                {
                        echo "El archivo ya existe ".$string_destination_path. "\n";
                         continue;

                }
 
			$message_payload_details = $msgObj->getPayload()->getParts();
                //print_r($message_payload_details);
                $attachId = $message_payload_details[2]['body']['attachmentId'];
             //   print ("attachId: ". $attachId ."\n");
                $attach = $service->users_messages_attachments->get($user, $message->getId(), $attachId);
                $data =$attach['data'];
                //print_r($attach['data']);
                //print ("\n");
                $data = strtr($data, array('-' => '+', '_' => '/'));
                $myfile = fopen($string_destination_path, "w+");;
                fwrite($myfile, base64_decode($data));
                fclose($myfile);
                
                /*
                 foreach ($message_payload_details as $key => $value)
                 {
                 if ( isset($value->body->attachmentId) && !isset($value->body->data))
                 {
                 //print "partid: " .$value['partId']. "\n";
                 array_push($files, $value['partId']);
                 }
                 }
                 */
                
                
                echo $string_destination_path. "\n";
                
                $objAN = new MyImportANALYTICS();
                
                if ($objAN->importAnalytics($string_destination_path))
                {
                    print "Imported: \n";
                }
                else
                {
                    print "Error - NOT IMPORTED \n";
                }
                $objAN = NULL;
                

            }
            

            
            
            
        }
        
        echo "-----------------------------\n";
    }
  

    // ProcesarlaDataReporte();
    
    
    
    
    
    // If there is no access token, there will show url
    if (isset ($authUrl)) {
        echo $authUrl;
    }
 
// [END gmail_quickstart]
    
    
    //echo 'Message-ID: ' . (empty($headerArr['Message-ID']) ? '' : $headerArr['Message-ID']);
    //echo "\n";
    //echo 'In-Reply-To: ' . (empty($headerArr['In-Reply-To']) ? '' : $headerArr['In-Reply-To']);
    //echo "\n";
    //echo 'References: ' . (empty($headerArr['References']) ? '': $headerArr['References']);
    //echo "\n";

    
    
    
    
    /*
     $optParams = [];
     $optParams['maxResults'] = 5; // Return Only 5 Messages
     $optParams['labelIds'] = 'INBOX'; // Only show messages in Inbox
     $messages = $service->users_messages->listUsersMessages('me',$optParams);
     $list = $messages->getMessages();
     $messageId = $list[0]->getId(); // Grab first Message
     
     
     $optParamsGet = [];
     $optParamsGet['format'] = 'full'; // Display message in payload
     $message = $service->users_messages->get('me',$messageId,$optParamsGet);
     $messagePayload = $message->getPayload();
     $headers = $message->getPayload()->getHeaders();
     $parts = $message->getPayload()->getParts();
     
     $body = $parts[0]['body'];
     $rawData = $body->data;
     $sanitizedData = strtr($rawData,'-_', '+/');
     $decodedMessage = base64_decode($sanitizedData);
     
     var_dump($decodedMessage);
     
     */
    
    
    /*
     
     
     $results = $service->users_labels->listUsersLabels($user);
     if (count($results->getLabels()) == 0)
     {
     print "No labels found.\n";
     }
     else
     {
     print "Labels:\n";
     foreach ($results->getLabels() as $label)
     {
     printf("- %s\n", $label->getName());
     }
     }
     
     */
    
    
    
    /*
     // Get the API client and construct the service object.
     $client = getClient();
     $service = new Google_Service_Gmail($client);
     $opt_param = array();
     $opt_param['labelIds'] =  'INBOX';
     $opt_param['maxResults'] = 1;
     $messages = $service->users_messages->listUsersMessages($userId, $opt_param);
     
     foreach ($messages as $message_thread)
     {
     $message = $service->users_messages->get($userId, $message_thread['id']);
     $message_parts = $message->getPayload()->getParts();
     $files = array();
     $attachId = $message_parts[1]['body']['attachmentId'];
     $attach = $service->users_messages_attachments->get($userId, $message['id'], $attachId);
     foreach ($message_parts as $key => $value)
     {
     if ( isset($value->body->attachmentId) && !isset($value->body->data))
     {
     array_push($files, $value['partId']);
     }
     }
     }
     
     
     
     
     if(isset($_GET['messageId']) && $_GET['part_id'])
     { // This is After Clicking an Attachment
     $attachment = getAttachment($_GET['messageId'], $_GET['part_id'], $userId);
     $content_type = "";
     foreach ($attachment['data']['headers'] as $key => $value)
     {
     if($value->name == 'Content-Type')
     {
     $content_type = $value->value;
     }
     header($value->name.':'.$value->value);
     }
     $content_type_val = current(explode("/",$content_type));
     $media_types = ["video", "image", "application"];
     if(in_array($content_type_val, $media_types )){
     echo base64_to_jpeg($attachment['data']['data'], $content_type); // Only for Image files
     } else {
     echo base64_decode($attachment['data']['data']); // Other than Image Files
     }
     } else { // Listing All Attachments
     if(!empty($files)) {
     foreach ($files as $key => $value) {
     echo '<a target="_blank" href="index.php?messageId='.$message['id'].'&part_id='.$value.'">Attachment '.($key+1).'</a><br/>';
     }
     }
     
     }
     
     */
