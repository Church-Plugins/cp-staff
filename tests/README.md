# Tests

PHPUnit is not installed with Composer. `vendor/` is part of the plugin, so the test runner stays outside it.

Use PHPUnit 11 from a phar or a global install:

```
php phpunit.phar --configuration phpunit.xml.dist
```

```
phpunit --configuration phpunit.xml.dist
```
