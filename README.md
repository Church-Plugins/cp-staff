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

#### 1.0.1
* Add settings for Staff message modal
* Update CP core

#### 1.0.0
* Initial release
