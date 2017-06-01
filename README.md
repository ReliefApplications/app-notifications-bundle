Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require reliefapps-notification "0.1.4"
```

*NB: Before version 1, backward compatibility may be broken. It is advised to force a single version of the bundle has shown here*

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Reliefapps\NotificationBundle\ReliefappsNotificationBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3 : Configure the Bundle
-----------------------------

In your project, add at the end of the file `app/config/config.yml` the following configuration :
```yml
reliefapps_notification:
    android:
        server_key: **your_gcm_key**
    ios:
        push_certificate: **path_to_your_ios_certificate**
        push_passphrase: **your_passphrase_for_the_certificate**
        protocol: legacy
```

**Optional** (but recommended) **:**

For improved error management for iOS notifications, you will need http2 support for cURL on your machine. [See this tutorial.](https://serversforhackers.com/video/curl-with-http2-support)

Once you have followed this tutorial, check that cURL supports HTTP/2 by running :
```bash
$ curl --http2 -I https://nghttp2.org/
```

If it works, you can remove the line :
```yml
            protocol: legacy
```
in your `app/config/config.yml` file.


Usage
=====

Step 1: Create a Device Entity
------------------------------

First you will need to create you own Device Entity extending this Bundle's ReliefappsNotificationBundle:Device entity.

Then complete your `app/config/config.yml` file with :
```yml
reliefapps_notification:
    #...
    device:
        class: YourBundle\Entity\Device
```

Step 2: Register some devices
-----------------------------

In a stragic api controler (login, homepage), register the device UUID and token :
```php

<?php

// ...
class YourController
{
    // ...
    public function YourAction()
    {
        // ...
        // Get the Device Manager
        $deviceManager = $this->get('reliefapps_notification.device.manager');

        // Create a new device
        $newDevice = $deviceManager->createDevice($uuid, $platform);
        $newDevice->setToken($token);

        // Save the device in database
        $deviceManager->udpateDevice($newDevice);

    }
}
```

Step 3: Create a notification Body
----------------------------------

The class *NotificationBody* allows you to create the content of a push notification.

```php
<?php

use Reliefapps\NotificationBundle\Resources\Model\NotificationBody;

// ...
class YourController
{
    // ...
    public function YourAction()
    {
        // ...
        $body = new NotificationBody();
        $body ->setTitle('Notification Title')      // Title of the notification
              ->setBody('This is a notification !') // Text of the notification
              ->setBadge(42);                       // Badge on the app icon (iOS only)
    }
}
```

Step 4: Send a push notification
--------------------------------

You are ready to send your first Push Notification !

The function *sendPush* takes an array of devices and a notification body, and sends the Push notifications to the devices !

If a token is invalid, it will be set to null on your database automatically.

```php
<?php

// ...
class YourController
{
    // ...
    public function YourAction()
    {
        // ...
        // Get the Push Manager
        $pushManager = $this->container->get('reliefapps_notification.push_manager');

        // Send a push notification to devices $device1 and $device2
        $pushManager->sendPush(Array($device1, $device2), $body);
    }
}
```

Documentation
=============

Payloads
--------

Entity : Reliefapps\NotificationBundle\Resources\Model\NotificationBody

|    Key   | Description                                | iOS | Android |
| -------- | ------------------------------------------ | --- | ------- |
|    title | Title                                      | [x] |   [x]   |
|     body | Main text                                  | [x] |   [x]   |
| ledColor | Led color on front of the phone            | [ ] |   [x]   |
|  actions | List of action                             | [ ] |   [x]   |
|    badge | Badge number on app icon                   | [x] |   [ ]   |
| category | iOS category tag (defined in your app)     | [x] |   [ ]   |

Android Action
--------------

Entity : Reliefapps\NotificationBundle\Resources\Model\AndroidAction

|     Key    | Description                                | iOS | Android |
| ---------- | ------------------------------------------ | --- | ------- |
|       icon | Icon (name of an app drawable ressource)   | [ ] |   [x]   |
|      title | Action text                                | [ ] |   [x]   |
|   callback | Function to call as the button is clicked  | [ ] |   [x]   |
| foreground | Open the app after click ? (default true)  | [ ] |   [x]   |
|     inline | Use quick reply field ? (default false)    | [ ] |   [x]   |
