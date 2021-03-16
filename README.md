![pUZI logo](pUZI.svg "pUZI logo")
# pUZI Laravel

[![Laravel](https://github.com/minvws/pUZI-laravel/actions/workflows/test.yml/badge.svg)](https://github.com/minvws/pUZI-laravel/actions/workflows/test.yml)

Proficient UZI pass reader in php.

The UZI card is part of an authentication mechanism for medical staff and doctors working in the Netherlands. The cards are distributed by the CIBG. More information and the relevant client software can be found at www.uziregister.nl (in Dutch).

pUZI is a simple and functional module which allows you to use the UZI cards as authentication mechanism. It consists of:

1. a reader that reads the data on the card and gives an UziUser object in return.
2. middleware (for the Laravel framework) that allows authentication and user creation based on UZI cards (this repository).

pUZI is available under the EU PL licence. It was created early 2021 during the COVID19 campaign as part of the vaccination registration project BRBA for the ‘Ministerie van Volksgezondheid, Welzijn & Sport, programma Realisatie Digitale Ondersteuning.’

Questions and contributions are welcome via [GitHub](https://github.com/minvws/pUZI-laravel/issues).

## Requirements

* Laravel 6, 7 and 8.

Apache config (or NginX equivalent):
```apacheconf
SSLEngine on
SSLProtocol -all +TLSv1.3
SSLHonorCipherOrder on
SSLCipherSuite ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384
SSLVerifyClient require
SSLVerifyDepth 3
SSLCACertificateFile /path/to/uziCA.crt
SSLOptions +StdEnvVars +ExportCertData
```

## Installation

### Composer

```sh
composer require minvws/puzi-laravel
```

### Manual

Add the following to your `composer.json` and then run `composer install`.

```json
{
    "require": {
        "minvws/puzi-laravel": "^0.1"
    }
}
```

## Usage

```sh
php artisan vendor:publish --provider="MinVWS\PUZI\Laravel\UziServiceProvider"
```

Add to `$routeMiddleware` array in `app/Http/Kernel.php`:

```php
    'auth.uzi' => \MinVWS\PUZI\Laravel\Middleware\AuthenticateWithUzi::class,
```

Add some `allowed_types` and `allowed_roles` to the `config/uzi.php`.

For example:
```php
    // Which card types are allowed to log in
    'allowed_types' => [
        \MinVWS\PUZI\UziConstants::UZI_TYPE_CARE_PROVIDER
    ],

    // Which roles are allowed to log in
    'allowed_roles' => [
        \MinVWS\PUZI\UziConstants::UZI_ROLE_DOCTOR
    ],
```

In `routes/web.php` add the middleware to some routes, for example:
```php
Route::middleware(['auth:web,auth.uzi'])->group(function () {
    Route::get('/any', [Controller::class, 'all'])->name('any');
    Route::get('/all', [Controller::class, 'all'])->name('all');
});

Route::middleware('auth.uzi')->group(function () {
    Route::get('/uzi', [Controller::class, 'uzi'])->name('uzi');
});
```

## Uses

puzi-php - [Proficient UZI pass reader in PHP](https://github.com/minvws/pUZI-php)

phpseclib - [PHP Secure Communications Library](https://phpseclib.com/)

## Contributing

1. Fork the Project

2. Ensure you have Composer installed (see [Composer Download Instructions](https://getcomposer.org/download/))

3. Install Development Dependencies

    ```sh
    composer install
    ```

4. Create a Feature Branch

5. (Recommended) Run the Test Suite

    ```sh
    vendor/bin/phpunit
    ```
6. (Recommended) Check whether your code conforms to our Coding Standards by running

    ```sh
    vendor/bin/phpstan analyse
    vendor/bin/psalm
    vendor/bin/phpcs
    ```

7. Send us a Pull Request

![pUZI](pUZI-hidden.svg "pUZI")