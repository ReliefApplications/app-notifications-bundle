# CHANGELOG

### v0.1 First Dev release

- 2 platforms supported : **iOS** (APNs) and **Android** (Google)
- 2 APNs protocols supported : [Legacy](https://developer.apple.com/library/content/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/BinaryProviderAPI.html) (binary package) and [HTTP/2 API](https://developer.apple.com/library/content/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/APNSOverview.html).
- 1 Google protocol supported : [GCM](https://developers.google.com/cloud-messaging)

- **Device** model and **NotificationBody** class

- **DeviceManager** class

- **Doctrine ORM** natively supported

- Automatic suppression of **iOS invalid or expired token** (for HTTP/2 API)
