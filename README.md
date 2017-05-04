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

            new Reliefapps\ReliefappsNotificationBundle\ReliefappsNotificationBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3 : Configure the Bundle
-----------------------------

In your project, add at the end of the file app/config/config.yml the following configuration :
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
curl --http2 -I https://nghttp2.org/
```

If it works, you can remove the ``` protocol: legacy ``` line in your app/config/config.yml file.

