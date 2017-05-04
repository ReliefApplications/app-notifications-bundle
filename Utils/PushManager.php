<?php

namespace Reliefapps\NotificationBundle\Utils;

use Monolog\Logger;

class PushManager
{
    // iOS notifications are send by series of this length. Set to -1 to disable.
    const IOS_NOTIFICATION_CHAIN_LENGTH = 1;

    const IOS_HTTP_TIMEOUT = 1000;

    public function __construct($ios_push_certificate, $ios_push_passphrase, $ios_protocol, $android_server_key, $container)
    {
        $this->iosCertificate = $ios_push_certificate;
        $this->iosPassphrase  = $ios_push_passphrase;
        $this->iosProtocol = $ios_protocol;
        $this->android_server_key = $android_server_key;
        $this->container = $container;
    }

    /**
     * Send push notifications directly to mobile devices
     *
     * @param registrationIDs : Array of ids - device token that should receive the notification
     * @param title           : title of the notification
     */
    public function sendPush($registrationIDs, $title)
    {
        // Android device tokens are 152 - IOs device tokens 64
        // Create 2 arrays ios_tokens and android tokens
        $ios_tokens = [];
        $android_tokens = [];

        $logger = $this->container->get('logger');

        foreach ($registrationIDs as $key) {
            if (strlen($key) == 64) {
                array_push($ios_tokens, $key);
                $logger->info("iOS device detected. Key : ".$key);
            }
            elseif (strlen($key) == 152) {
                array_push($android_tokens, $key);
                $logger->info("Android device detected. Key : ".$key);
            } else{
                $logger->warning('Invalid Push token ' . $key . ' (length ' . strlen($key) . ') ');
            }
        }

        $this->sendPushAndroid($android_tokens, $title);
        if( $this->iosProtocol == 'legacy'){
            $this->sendPushIOSLegacy($ios_tokens, $title);
        }else{
            $this->sendPushIOSHttp2($ios_tokens, $title);
        }

    }

    /**
     * Send push notifications for Android
     *
     * @param deviceTokens : Array of ids - device token that should receive the notification
     * @param title           : title of the notification
     */
    public function sendPushAndroid($deviceTokens, $title)
    {
        $logger = $this->container->get('logger');
        // ANDROID
        $url = 'https://android.googleapis.com/gcm/send';
        $apiKey = $this->android_server_key;

        $fields = array(
            'registration_ids'  => $deviceTokens,
            'data'              => array( "title" => "Crises" , "message" => $title ),
            );

        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
            );

        $ch = curl_init();

        if(!$ch){
            $logger->error('Could not connect to GCM server.');
            return false;
        }

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

        $result = curl_exec($ch);

        curl_close($ch);
    }

    /**
     * Send push notifications for IOS (HTTP/2 APNS protocol)
     *
     * @param deviceTokens : Array of ids - device token that should receive the notification
     * @param title           : title of the notification
     */
    public function sendPushIOSHttp2($deviceTokens, $title)
    {
        $logger = $this->container->get('logger');
        //IOS HTTP/2 APNs Protocol
        if (!(curl_version()["features"] & CURL_VERSION_HTTP2 !== 0)) {
            $logger->warning('HTTP2 does not seem to be supported by CURL on your server. Please upgrade your setup (with nghttp2) or use the APNs\' "legacy" protocol.');
            return false;
        }
        //$headers = array("authorization: ", "apns-id: ", "apns-expiration: ", "apns-priority: ", "apns-topic: ", "apns-collapse-id: ")
        $headers = array("apns-topic: org.reliefapps.emalsys");

        $fields = array("aps" => array(
                            "alert" => $title,
                            "sound" => "default",
                        ));
        $fields_json = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        curl_setopt($ch, CURLOPT_SSLCERT, $this->iosCertificate);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->iosPassphrase);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::IOS_HTTP_TIMEOUT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, count($fields_json));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_json);

        foreach($deviceTokens as $token){
            $url = "https://api.push.apple.com/3/device/$token";
            curl_setopt( $ch, CURLOPT_URL, $url );

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($httpcode == 0){
                $logger->error('APNs server return an error : ' . $response);
                if( preg_match('/HTTP\/2/', $response) ){
                    $logger->warning('HTTP2 does not seem to be supported by CURL on your server. Please upgrade your setup (with nghttp2) or use the APNs\' "legacy" protocol.');
                }
            }
            elseif($httpcode != 200){
                $logger->error('APNs server returned an error : (' . $httpcode . ') ' . $response);
            }else{
                $logger->debug('APNs server returned : ' . $response);
            }
        }

        curl_close($ch);


        // 200 Success
        // 400 Bad request
        // 403 There was an error with the certificate or with the provider authentication token
        // 405 The request used a bad :method value. Only POST requests are supported.
        // 410 The device token is no longer active for the topic.
        // 413 The notification payload was too large.
        // 429 The server received too many requests for the same device token.
        // 500 Internal server error
        // 503 The server is shutting down and unavailable.
    }

    /**
     * Send push notifications for IOS (Legacy Binary APNS protocol)
     *
     * @param deviceTokens : Array of ids - device token that should receive the notification
     * @param title           : title of the notification
     */
    public function sendPushIOSLegacy($deviceTokens, $title)
    {
        $logger = $this->container->get('logger');

        if (file_exists($this->iosCertificate))  // Si le certificat est présent = environnement de prod
        {
            $logger->info("iOS certificate detected");
            // Create the payload body
            $body['aps'] = array(
                'alert' => $title,
                'sound' => 'default',
            );

            // Encode the payload as JSON
            $payload = json_encode($body);

            // Slicing the tokens in arrays of 10 to limit damage in case of error
            if(self::IOS_NOTIFICATION_CHAIN_LENGTH > 1){
                $chunked_tokens = array_chunk($deviceTokens, self::IOS_NOTIFICATION_CHAIN_LENGTH);
            }else{
                $chunked_tokens = array($deviceTokens);
            }

            foreach( $chunked_tokens as $token_chain ){

                $ctx = stream_context_create();
                stream_context_set_option($ctx, 'ssl', 'local_cert', $this->iosCertificate);
                stream_context_set_option($ctx, 'ssl', 'passphrase', $this->iosPassphrase);

                // Open a connection to the APNS server
                $fp = stream_socket_client(
                    'ssl://gateway.push.apple.com:2195', $err,
                    $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

                    if( !$fp ){
                        $logger->error('Connection du APNS server failed (code ' . $err . ') : ' . $errstr);
                    }

                    foreach($token_chain as $id)
                    {
                        // Build the binary notification
                        $msg = chr(0) . pack('n', 32) . pack('H*', $id) . pack('n', strlen($payload)) . $payload;

                        // Send it to the server
                        $result = fwrite($fp, $msg, strlen($msg));
                    }
                    $logger->debug('iOS notification chain sent.');

                    fclose($fp);
            }
        }
    }
}

