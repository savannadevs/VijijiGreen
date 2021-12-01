<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // access
        $secretKey = '';
        $captcha = $_POST['g-recaptcha-response'];

        if(!$captcha){
          echo '<p class="alert alert-warning">Please check the the captcha form.</p>';
          exit;
        }

        $from = '';
        $sendTo = '';
        $websiteName = '';
        $subject = 'Email from '. $websiteName;
        $fields = array('name' => 'Name', 'email' => 'Email', 'message' => 'Message');
        $okMessage = 'Successfully submitted. Thank you, we will get back to you soon!';
        $errorMessage = 'There was an error while submitting the form. Please try again later.';

        error_reporting(0);

        try
        {
            if(count($_POST) == 0) throw new \Exception('Form is empty');

            $ip = $_SERVER['REMOTE_ADDR'];
            $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
            $responseKeys = json_decode($response,true);

            if(intval($responseKeys["success"]) !== 1) {
                echo '<p class="alert alert-warning">Please check the the captcha form.</p>';
            } else {
                $emailText = "You have a new message from your page the JRAW Website...\n=============================\n";
                foreach ($_POST as $key => $value) {
                    if (isset($fields[$key])) {
                        $emailText .= "$fields[$key]: $value\n";
                    }
                }

                // All the necessary headers for the email.
                $headers = array('Content-Type: text/plain; charset="UTF-8";',
                    'From: ' . $from,
                    'Reply-To: ' . $_POST['email'],
                    'Return-Path: ' . $from,
                );
                mail($sendTo, $subject, $emailText, implode("\n", $headers));
                $responseArray = array('type' => 'success', 'message' => $okMessage);
            }
        }
        catch (\Exception $e)
        {
            $responseArray = array('type' => 'danger', 'message' => $errorMessage);
        }


        // if requested by AJAX request return JSON response
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $encoded = json_encode($responseArray);

            header('Content-Type: application/json');

            echo $encoded;
        }
        // else just display the message
        else {
            echo $responseArray['message'];
        }
    }else {
        http_response_code(403);
        echo '<p class="alert alert-warning">There was a problem with your submission, please try again.</p>';
    }

?>