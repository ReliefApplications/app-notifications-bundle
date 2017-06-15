<?php

namespace Reliefapps\NotificationBundle\Utils;

use Reliefapps\NotificationBundle\Model\Context;


/**
 *  Give access to contexts created from the Bundle's configuration
 */
class ContextManager
{
    /**
     *  @var Context
     */
    private $defaultContext;

    /**
     *  @var Context[]
     */
    private $contexts;

    public function __construct($ios_push_certificate, $ios_push_passphrase, $ios_protocol,  $apns_server, $apns_topic, $android_server_key, $gcm_server, $contextsArrays)
    {
        $this->defaultContext = new Context('default', $android_server_key, $gcm_server, $ios_push_certificate, $ios_push_passphrase, $ios_protocol, $apns_server, $apns_topic);
        $this->contexts = array();
        foreach ($contextsArrays as $name => $contextArray) {
            $this->contexts[$name] = $this->createContext($name, $contextArray);
        }
    }

    /**
     *  Get the context with the given name
     *
     *  @param String
     *
     *  @return Context
     */
    public function getContext($name)
    {
        if( array_key_exists($name, $this->contexts) )
        {
            return $this->contexts[$name];
        }else{
            return $this->getDefaultContext();
        }
    }

    /**
     *  Get the default context
     *
     *  @return Context
     */
    public function getDefaultContext()
    {
        return $this->defaultContext;
    }

    /**
     *  Get all contexts
     *
     *  @return Context[]
     */
    public function getContexts()
    {
        $contexts = $this->contexts;
        $contexts['default'] = $this->defaultContext;
        return $contexts;
    }

    /**
     *  Create a context object from an array of parameters
     *
     *  @param String
     *  @param Array[]
     *
     *  @return Context
     */
    private function createContext($name, $contextArray)
    {
        return new Context(
            $name,
            array_key_exists("android", $contextArray) && array_key_exists("server_key", $contextArray["android"]) ? $contextArray["android"]["server_key"] : $this->defaultContext->getAndroidServerKey(),
            array_key_exists("android", $contextArray) && array_key_exists("gcm_server", $contextArray["android"]) ? $contextArray["android"]["gcm_server"] : $this->defaultContext->getAndroidGcmServer(),
            array_key_exists("ios", $contextArray) && array_key_exists("push_certificate", $contextArray["ios"]) ? $contextArray["ios"]["push_certificate"] : $this->defaultContext->getIosPushCertificate(),
            array_key_exists("ios", $contextArray) && array_key_exists("push_passphrase", $contextArray["ios"]) ? $contextArray["ios"]["push_passphrase"] : $this->defaultContext->getIosPushPassphrase(),
            array_key_exists("ios", $contextArray) && array_key_exists("protocol", $contextArray["ios"]) ? $contextArray["ios"]["protocol"] : $this->defaultContext->getIosProtocol(),
            array_key_exists("ios", $contextArray) && array_key_exists("apns_server", $contextArray["ios"]) ? $contextArray["ios"]["apns_server"] : $this->defaultContext->getIosApnsServer(),
            array_key_exists("ios", $contextArray) && array_key_exists("apns_topic", $contextArray["ios"]) ? $contextArray["ios"]["apns_topic"] : $this->defaultContext->getIosApnsTopic()
        );
    }
}