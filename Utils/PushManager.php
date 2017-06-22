<?php

namespace Reliefapps\NotificationBundle\Utils;

use Reliefapps\NotificationBundle\Model\Device;
use Reliefapps\NotificationBundle\Model\DeviceManager;
use Reliefapps\NotificationBundle\Model\Context;
use Reliefapps\NotificationBundle\Utils\ContextManager;
use Reliefapps\NotificationBundle\Resources\Model\NotificationBody;
use Monolog\Logger;


/**
 *  Implements all the protocols to send notifications.
 *  Supported protocols: APNs Legacy (Apple) for iOS, APNs HTTP2 (Apple) for iOS and GCM (Google) for Android
 */
class PushManager
{
    // APNs Legacy iOS notifications are send by series of this length. Set to -1 to disable.
    // Warning: When set to 1, the system is not scalable. To many notifications will be consider a DDoS attack by APNs servers
    const IOS_NOTIFICATION_CHAIN_LENGTH = 1;

    const IOS_HTTP_TIMEOUT = 1000;

    public function __construct(ContextManager $contextManager, DeviceManager $deviceManager, Logger $logger)
    {
        $this->contextManager = $contextManager;
        $this->device_manager = $deviceManager;
        $this->logger = $logger;
    }

    /**
     * Send push notifications directly to mobile devices
     *
     * @param devices Array[ReliefappsNotificationBundle:Device] List of devices to send notifications to
     * @param body NotificationBody Body of the notification
     * @param contextName string Name of the context to use to send the notification
     */
    public function sendPush($devices, NotificationBody $body, $contextName = "default")
    {
        $ios_devices = [];
        $android_devices = [];

        $logger = $this->logger;
        $ctx = $this->contextManager->getContext($contextName);

        foreach ($devices as $device) {
            if($device->getToken() === null){
                $logger->debug("Tokenless device ignored. UUID : ".$device->getUUID());
            }
            elseif ($device->getType() == Device::TYPE_IOS) {
                if($device->getAcceptPush()){
                    array_push($ios_devices, $device);
                    $logger->debug("iOS device detected. Key : ".$device->getToken());
                }
            }
            elseif ($device->getType() == Device::TYPE_ANDROID) {
                if($device->getAcceptPush()){
                    array_push($android_devices, $device);
                    $logger->debug("Android device detected. Key : ".$device->getToken());
                }
            } else{
                $logger->warning('Invalid Device type ' . $device->getToken() . ' (type ' . $device->getType() . ') ');
            }
        }

        $this->sendPushAndroid($android_devices, $body, $ctx);
        if($ctx->getIosProtocol() == 'legacy'){
            $this->sendPushIOSLegacy($ios_devices, $body, $ctx);
        }else{
            $this->sendPushIOSHttp2($ios_devices, $body, $ctx);
        }

    }

    /**
     * Send push notifications for Android
     *
     * @param devices : Array of Devices - device that should receive the notification
     * @param body           : title of the notification
     */
    public function sendPushAndroid(array $devices, NotificationBody $body, Context $ctx)
    {
        if(empty($devices)){
            return true;
        }

        $logger = $this->logger;
        // ANDROID
        $url = "https://" . $ctx->getAndroidGcmServer() . "/gcm/send";
        $apiKey = $ctx->getAndroidServerkey();

        $getToken = function(Device $obj) {return $obj->getToken();};
        $deviceTokens = array_map($getToken, $devices);


        $fields = array(
            'registration_ids'  => $deviceTokens,
            'data'              => $body->getPayload(NotificationBody::PAYLOAD_ARRAY_ANDROID),
            );
        $logger->debug("Android Payload : " . json_encode($fields));

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

        switch ($httpcode) {
            case 200:
                $logger->debug('GCM server returned : ' . $response);
                break;
            case 0:
                $logger->error('Unable to connect to the GCM server : ' . curl_error($ch));
                break;
            default:
                $logger->error('GCM server returned an error : (' . $httpcode . ') ' . $response);
                break;
        }

        curl_close($ch);
    }

    /**
     * Send push notifications for IOS (HTTP/2 APNS protocol)
     *
     * @param deviceTokens : Array of ids - device token that should receive the notification
     * @param body         : body of the notification
     */
    public function sendPushIOSHttp2(array $devices, NotificationBody $body, Context $ctx)
    {
        if(empty($devices)){
            return true;
        }

        $logger = $this->logger;

        //IOS HTTP/2 APNs Protocol
        if (!(curl_version()["features"] & CURL_VERSION_HTTP2 !== 0)) {
            $logger->error('HTTP2 does not seem to be supported by CURL on your server. Please upgrade your setup (with nghttp2) or use the APNs\' "legacy" protocol.');
            return false;
        }
        $headers = array("apns-topic: " . $ctx->getIosApnsTopic());

        $fields_json = $body->getPayload(NotificationBody::PAYLOAD_JSON_IOS);
        $logger->debug("iOS Payload : $fields_json");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        curl_setopt($ch, CURLOPT_SSLCERT, $ctx->getIosPushCertificate());
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $ctx->getIosPushPassphrase());
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::IOS_HTTP_TIMEOUT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, count($fields_json));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_json);

        foreach($devices as $device){
            $token = $device->getToken();
            $url = "https://".$ctx->getIosApnsServer()."/3/device/$token";
            curl_setopt( $ch, CURLOPT_URL, $url );

            $response = curl_exec($ch);
            // Then, after your curl_exec call:
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $body = substr($response, $header_size);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            switch ($httpcode) {
                case 200: // 200 Success
                    $logger->debug('APNs server returned : ' . $response);
                    break;
                case 0:
                    $logger->error('Unable to connect to the APNs server : ' . $response . curl_error($ch));
                    if( preg_match('/HTTP\/2/', $response) ){
                        $logger->warning('HTTP2 does not seem to be supported by CURL on your server. Please upgrade your setup (with nghttp2) or use the APNs\' "legacy" protocol.');
                    }
                    break;
                case 410: // 410 The device token is no longer active for the topic.
                    $response_array = json_decode($body, true);
                    $logger->debug('APNs server returned  : (' . $httpcode . ') ' . $response_array["reason"]);
                    $device->setToken(null);
                    $this->device_manager->updateDevice($device);
                    $logger->debug('Device token is no longer active, token removed from database.');
                    break;
                case 400: // 400 Bad request
                    $response_array = json_decode($body, true);
                    $logger->debug('APNs server returned  : (' . $httpcode . ') ' . $response_array["reason"]);
                    if( $response_array["reason"] == 'BadDeviceToken'){
                        $device->setToken(null);
                        $this->device_manager->updateDevice($device);
                        $logger->warning('Bad device Token, token removed from database.');
                    }
                    break;

                default:
                    // 403 There was an error with the certificate or with the provider authentication token
                    // 405 The request used a bad :method value. Only POST requests are supported.
                    // 413 The notification payload was too large.
                    // 429 The server received too many requests for the same device token.
                    // 500 Internal server error
                    // 503 The server is shutting down and unavailable.
                    $logger->error('APNs server returned an error : (' . $httpcode . ') ' . $response);
                    break;
            }
        }
        curl_close($ch);
    }

    /**
     * Send push notifications for IOS (Legacy Binary APNS protocol)
     *
     * @param deviceTokens : Array of ids - device token that should receive the notification
     * @param title           : title of the notification
     */
    public function sendPushIOSLegacy(array $devices, NotificationBody $body, Context $ctx)
    {
        if(empty($devices)){
            return true;
        }

        $getToken = function(Device $obj) {return $obj->getToken();};
        $deviceTokens = array_map($getToken, $devices);

        $logger = $this->logger;

        if (file_exists($ctx->getIosPushCertificate()))  // Si le certificat est présent = environnement de prod
        {
            $logger->info("iOS certificate detected");

            // Encode the payload as JSON
            $payload = $body->getPayload(NotificationBody::PAYLOAD_JSON_IOS, $additionalFields);

            // Slicing the tokens in arrays of 10 to limit damage in case of error
            if(self::IOS_NOTIFICATION_CHAIN_LENGTH > 0){
                $chunked_tokens = array_chunk($deviceTokens, self::IOS_NOTIFICATION_CHAIN_LENGTH);
            }else{
                $chunked_tokens = array($deviceTokens);
            }

            foreach( $chunked_tokens as $token_chain ){

                $stream_ctx = stream_context_create();
                stream_context_set_option($stream_ctx, 'ssl', 'local_cert', $ctx->getIosPushCertificate());
                stream_context_set_option($stream_ctx, 'ssl', 'passphrase', $ctx->getIosPushPassphrase());

                // Open a connection to the APNS server
                $fp = stream_socket_client(
                    'ssl://gateway.push.apple.com:2195', $err,
                    $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $stream_ctx);

                    if( !$fp ){
                        $logger->error('Connection du APNS server failed (code ' . $err . ') : ' . $errstr);
                    }

                    foreach($token_chain as $id)
                    {
                        // Build the binary notification
                        $msg = chr(0) . pack('n', 32) . pack('H*', $id) . pack('n', strlen($payload)) . $payload;

                        // Send it to the server
                        fwrite($fp, $msg, strlen($msg));
                    }
                    $logger->debug('iOS notification chain sent.');

                    fclose($fp);
            }
        }else{
            $logger->error("No iOS certificate detected.");
        }
    }
}

