# CHANGELOG

### v0.1.8 Add image-type, image and notId fields for Android

- New fields can be added to the notification for Android :
  - image-type : "circle"
  - image : path to the icon used in the app
  - notId : unique id of the notification (useful to distinguish them)

### v0.1.7 Added Additional Fields

- Fields can be optionally added to the notification payload

### v0.1.6 Added Contexts

- PushServer Parameters can be changed with contexts

### v0.1.5 Add test acceptPush

- If the UserDevice has the acceptPush property set to false, no notification sent

### v0.1 First Dev release

- 2 platforms supported : **iOS** (APNs) and **Android** (Google)
- 2 APNs protocols supported : [Legacy](https://developer.apple.com/library/content/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/BinaryProviderAPI.html) (binary package) and [HTTP/2 API](https://developer.apple.com/library/content/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/APNSOverview.html).
- 1 Google protocol supported : [GCM](https://developers.google.com/cloud-messaging)

- **Device** model and **NotificationBody** class

- **DeviceManager** class

- **Doctrine ORM** natively supported

- Automatic suppression of **iOS invalid or expired token** (for HTTP/2 API)
