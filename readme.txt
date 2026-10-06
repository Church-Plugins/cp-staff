=== CP Staff ===
Stable tag: 1.2.2
License: GPL-3.0
License URI: https://opensource.org/licenses/GPL-3.0

Staff management for churches.

== Changelog ==

= 1.2.2 =
* Fix "Enable captcha on message form" and "Prevent staff from sending emails" so unchecking them and saving turns those checks off
* Keep both features on for existing sites that never saved the setting
* Run captcha only when it is enabled and both the site key and secret key are set
* Contact form now resolves the recipient from the staff record
* Theme copies of the email modal should include `<input type="hidden" name="staff-id" class="staff-id">`. The script adds this field when it is missing.
* Sites using page caching should purge their cache after updating so the new modal script loads.
