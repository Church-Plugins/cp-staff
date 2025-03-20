# Church Plugins Staff
Church staff plugin.

##### First-time installation  #####

- Copy or clone the code into `wp-content/plugins/cp-staff/`
- Run these commands
```
composer install
npm install
cd app
npm install
npm run build
```

##### Dev updates  #####

- There is currently no watcher that will update the React app in the WordPress context, so changes are executed through `npm run build` which can be run from either the `cp-staff`

### Change Log

### 1.2.1
* Add support for hierarchical departments in staff archive
* Add migration framework
* Configure archive page to be disabled by default
* Improve staff archive template structure
* Update default staff ordering to use menu_order for better control
* Add compatibility with plugins like Simple Page Ordering and WP Term Order
* Add Staff settings for controlling labels and archive page

### 1.2.0
* Add staff archive page
* Add staff social links
* Add add email/phone buttons to staff cards
* Change staff card interaction

#### 1.1.0
* Add security hardening for staff messaging

#### 1.0.1
* Add settings for Staff message modal
* Update CP core

#### 1.0.0
* Initial release
