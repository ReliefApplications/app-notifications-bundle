<?php

namespace Reliefapps\NotificationBundle\Utils;

use Monolog\Logger;
use Reliefapps\NotificationBundle\Model\Device;
use Reliefapps\NotificationBundle\Resources\Model\NotificationBody;

class PushManager
{
    // iOS notifications are send by series of this length. Set to -1 to disable.
    const IOS_NOTIFICATION_CHAIN_LENGTH = 1;

    const IOS_HTTP_TIMEOUT = 1000;

    public function __construct($em, $ios_push_certificate, $ios_push_passphrase, $ios_protocol, $android_server_key, $container)
    {
        $thi->em = $em;
        $this->iosCertificate = $ios_push_certificate;
        $this->iosPassphrase  = $ios_push_passphrase;
        $this->iosProtocol = $ios_protocol;
        $this->android_server_key = $android_server_key;
        $this->container = $container;
    }

    /**
     * Send push notifications directly to mobile devices
     *
     * @param devices Array[ReliefappsNotificationBundle:Device] List of devices to send notifications to
     * @param body NotificationBody Body of the notification
     */
    public function sendPush($devices, NotificationBody $body)
    {
        $ios_devices = [];
        $android_devices = [];

        $logger = $this->container->get('logger');

        foreach ($devices as $device) {
            if ($device->getType() == Device::TYPE_IOS) {
                array_push($ios_devices, $device);
                $logger->debug("iOS device detected. Key : ".$device->getToken());
            }
            elseif ($device->getType() == Device::TYPE_ANDROID) {
                array_push($android_devices, $device);
                $logger->debug("Android device detected. Key : ".$device->getToken());
            } else{
                $logger->warning('Invalid Device type ' . $device->getToken() . ' (type ' . $device->getType() . ') ');
            }
        }

        $this->sendPushAndroid($android_devices, $body);
        if( $this->iosProtocol == 'legacy'){
            $this->sendPushIOSLegacy($ios_devices, $body);
        }else{
            $this->sendPushIOSHttp2($ios_devices, $body);
        }

    }

    /**
     * Send push notifications for Android
     *
     * @param devices : Array of Devices - device that should receive the notification
     * @param title           : title of the notification
     */
    public function sendPushAndroid($devices, $body)
    {
        $logger = $this->container->get('logger');
        // ANDROID
        $url = 'https://android.googleapis.com/gcm/send';
        $apiKey = $this->android_server_key;

        $getToken = function($obj){ return $obj->getToken(); };

        $deviceTokens = array_map($getToken, $devices);

        $fields = array(
            'registration_ids'  => $deviceTokens,
            'data'              => $body->getPayload(NotificationBody::PAYLOAD_ARRAY_ANDROID),
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

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($httpcode == 0){
            $logger->error('GCM server return an error : ' . $response);
        }
        elseif($httpcode != 200){
            $logger->error('GCM server returned an error : (' . $httpcode . ') ' . $response);
        }else{
            $logger->debug('GCM server returned : ' . $response);
        }

        curl_close($ch);
    }

    /**
     * Send push notifications for IOS (HTTP/2 APNS protocol)
     *
     * @param deviceTokens : Array of ids - device token that should receive the notification
     * @param body          : body of the notification
     */
    public function sendPushIOSHttp2($devices, $body)
    {
        $logger = $this->container->get('logger');
        //IOS HTTP/2 APNs Protocol
        if (!(curl_version()["features"] & CURL_VERSION_HTTP2 !== 0)) {
            $logger->warning('HTTP2 does not seem to be supported by CURL on your server. Please upgrade your setup (with nghttp2) or use the APNs\' "legacy" protocol.');
            return false;
        }
        //$headers = array("authorization: ", "apns-id: ", "apns-expiration: ", "apns-priority: ", "apns-topic: ", "apns-collapse-id: ")
        $headers = array("apns-topic: org.reliefapps.emalsys");

        $fields_json = $body->getPayload(NotificationBody::PAYLOAD_JSON_IOS);

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

        foreach($devices as $device){
            $token = $device->getToken();
            $url = "https://api.push.apple.com/3/device/$token";
            curl_setopt( $ch, CURLOPT_URL, $url );

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            switch ($httpcode) {
                case 200:
                    $logger->debug('APNs server returned : ' . $response);
                    break;
                case 0:
                    $logger->error('APNs server return an error : ' . $response);
                    if( preg_match('/HTTP\/2/', $response) ){
                        $logger->warning('HTTP2 does not seem to be supported by CURL on your server. Please upgrade your setup (with nghttp2) or use the APNs\' "legacy" protocol.');
                    }
                    break;
                case 410: // 410 The device token is no longer active for the topic.
                    $response_array = json_decode($response);
                    $logger->debug('APNs server returned  : (' . $httpcode . ') ' . $response_array["reason"]);
                    break;
                case 400:
                    $response_array = json_decode($response);
                    $logger->debug('APNs server returned  : (' . $httpcode . ') ' . $response_array["reason"]);
                    if( $response_array["reason"] == 'BadDeviceToken'){
                        $logger->warning('Bad device Token, token removed from database.');
                    }
                    break;

                default:
                    $logger->error('APNs server returned an error : (' . $httpcode . ') ' . $response);
                    break;
            }
            if($httpcode == 0){

            }
            elseif($httpcode != 200){
                $logger->error('APNs server returned an error : (' . $httpcode . ') ' . $response);
            }elseif($httpcode != 410){
                $logger->debug('Invalid token detected : (' . $httpcode . ') ' . $response);
            }else{
                $logger->debug('APNs server returned : ' . $response);
            }
        }

        curl_close($ch);


        // 200 Success
        // 400 Bad request
        // 403 There was an error with the certificate or with the provider authentication token
        // 405 The request used a bad :method value. Only POST requests are supported.
        /
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
    public function sendPushIOSLegacy($devices, $body)
    {
        $getToken = function($obj){ return $obj->getToken(); };
        $deviceTokens = array_map($getToken, $devices);

        $logger = $this->container->get('logger');

        if (file_exists($this->iosCertificate))  // Si le certificat est présent = environnement de prod
        {
            $logger->info("iOS certificate detected");

            // Encode the payload as JSON
            $payload = $body->getPayload(NotificationBody::PAYLOAD_JSON_IOS);

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

