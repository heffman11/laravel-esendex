# Laravel-Esendex
Laravel 5 wrapper for Esendex SMS

- [Laravel-Esendex on Packagist](https://packagist.org/packages/davidcb/laravel-esendex)
- [Laravel-Esendex on GitHub](https://github.com/davidcb/laravel-esendex)


## Installation

Install via Composer;
```
composer require davidcb/laravel-esendex
```

If you're using Laravel >= 5.5, you can skip this as this package will be auto-discovered.
Add the service provider to `config/app.php`
```php
Davidcb\Esendex\EsendexServiceProvider::class,
```

You can register the facade in the `aliases` array in the `config/app.php` file
```php
'Esendex' => Davidcb\Esendex\Facades\Esendex::class,
```

Publish the config file
```
$ php artisan vendor:publish --provider="Davidcb\Esendex\EsendexServiceProvider"
```

Set your API key and Client ID on your .env file
```
ESENDEX_ACCOUNT=EX000000
ESENDEX_EMAIL=user@example.com
ESENDEX_PASSWORD=secret
```

## Usage

You can find all the methods in the original [esendex/esendex-php-sdk package](https://github.com/esendex/esendex-php-sdk).

Examples:
```php
// Send an SMS
$result = Esendex::sendMessage('Sender', '555000000', 'This is the text of the SMS');
```
```php
// Retrieve inbox messages
$messages = Esendex::inboxMessages();
```
```php
// Get a message's status
$status = Esendex::messageStatus('123456');
```
```php
// Get a message's body
$body = Esendex::messageBody('123456');
```
