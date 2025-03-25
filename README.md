## Installation

You can install the package via composer:

```bash
composer require webhubworks/laravel-unusual-login
```

You can run the migrations with:

```bash
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="unusual-login-config"
```

## Usage
This package works under the hood and listens to the `Illuminate\Auth\Events\Attempting` and `Illuminate\Auth\Events\Login` event. \
On the `Login` event it will run through the checks defined in the config file. \
If the specified threshold is reached, the package will fire the `Webhubworks\LaravelUnusualLogin\Events\UnusualLoginDetected` event. \
Additionally, you may specify a notification the package will automatically send out.

You may create custom checks. Simply refer to a given check in `src/Checks`. \
Your check must extend the `Webhubworks\LaravelUnusualLogin\Checks\Check` class and implement the `handle` method. \
It receives and returns the `WebhubWorks\UnusualLogin\DTOs\UnusualLoginDetected` DTO object.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Marven Thieme](https://github.com/marventhieme)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
