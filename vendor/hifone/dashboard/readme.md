## Dashboard for Hifone

## Install

### 1. composer require

```
composer require "hifone/dashboard:^1.0"
```

### 2. add provider

Edit `config/app.php` in `providers` array add provider:

```php
'providers' => [
	Frozennode\Administrator\AdministratorServiceProvider::class,
]
```

### 3. publish assets/config

```
php artisan vendor:publish
```