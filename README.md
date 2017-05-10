Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require reliefapps-notification "~1"
```

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

First you will need to create you own Device Entity extending this Bundle's *ReliefappsNotificationBundle:Device* entity.

For example this entity extends *Device* and adds an id and an User field.

```php
<?php

namespace YourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Reliefapps\NotificationBundle\Model\Device as BaseDevice;

/**
 * UserDevice : link an user to a Device
 * This class extends Reliefapps\NotificationBundle\Model\Device
 *
 * @ORM\Entity
 */
class UserDevice extends BaseDevice
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="YourBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    // ...
}
```

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
        $body ->setTitle("The Whale")                       // Title of the notification
              ->setBody("Ahhh! Woooh! What's happening?")   // Text of the notification
              ->setBadge(42);                               // Badge on the app icon (iOS only)
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

