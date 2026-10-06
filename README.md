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

### 1.2.2
* Fix "Enable captcha on message form" and "Prevent staff from sending emails" so unchecking them and saving turns those checks off
* Keep both features on for existing sites that never saved the setting
* Run captcha only when it is enabled and both the site key and secret key are set
* Contact form now resolves the recipient from the staff record
* Theme copies of the email modal should include `<input type="hidden" name="staff-id" class="staff-id">`. The script adds this field when it is missing.
* Sites using page caching should purge their cache after updating so the new modal script loads.

### 1.2.1
* Add support for hierarchical departments in staff archive
* Add migration framework
* Configure archive page to be disabled by default
* Improve staff archive template structure
* Update default staff ordering to use menu_order for better control
* Add compatibility with plugins like Simple Page Ordering and WP Term Order
* Add Staff settings for controlling labels and archive page
* Remove Honeypot field from staff message validation (it was returning false positives)

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
