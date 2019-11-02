# Laravel-USPS

Laravel-USPS is a composer package that allows you to integrate the USPS Address / Shipping API. This backage is ported from @author Vincent Gabriel https://github.com/VinceG/USPS-php-api

  - Requires a valid USPS API Username
  - Tested on Laravel 5+

## Installation

Begin by installing this package through Composer. Run this command from the Terminal:

```bash
composer require johnpaulmedina/laravel-usps
```
## Laravel integration

For Laravel 5.5 and later, this package will be auto discovered and registered.

To wire this up in your Laravel 5.4 project you need to add the service provider.
Open `config/app.php`, and add a new item to the providers array.

```php
Johnpaulmedina\Usps\UspsServiceProvider::class,
```
Then you must also specify the alias in `config/app.php`. Add a new item to the Aliases array.

```php
'Usps' => Johnpaulmedina\Usps\Facades\Usps::class,
```
This will allow integration by adding the Facade `Use Usps;` 

## Laravel Config
Add your USPS username config in `config/services.php`. 

```php
'usps' => [
		'username' => "XXXXXXXXXXXX",
		'testmode' => false,
	],
```

## Example Controller Usage
The only method completed for Laravel is the `Usps::validate` which is defined in `vendor/johnpaulmedina/laravel-usps/src/Usps/Usps.php`. As this package was developed for internal use I did not bring over all the features but you are more than welcome to contribute the methods you need and I will merge them. I suggest looking at the original PHP-Wrapper by @VinceG [USPS PHP-Api](https://github.com/VinceG/USPS-php-api "USPS PHP-Api by VinceG") as I ported those clases and autoloaded them to use in the `Usps.php` file.
```php
<?php

namespace app\Http\Controllers;
use app\Http\Requests;
use app\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Usps;

class USPSController extends Controller
{
    public function index() {
        return response()->json(
            Usps::validate( 
                Request::input('Address'), 
                Request::input('Zip'), 
                Request::input('Apartment'), 
                Request::input('City'), 
                Request::input('State')
            )
        );
    }

    public function trackConfirm() {
        return response()->json(
            Usps::trackConfirm( 
                Request::input('id')
            )
        );
    }

    public function trackConfirmRevision1() {
        return response()->json(
            Usps::trackConfirm( 
                Request::input('id'),
                'Acme, Inc'
            )
        );
    }
}
```

@VinceG Original README.MD

USPS PHP API
===========

This wrapper allows you to perform some basic calls to the USPS api. Some of the features currently supported are:

- Rate Calculator (Both domestic and international)
- Zip code lookup by address
- City/State lookup by zip code
- Verify address
- Create Priority Shipping Labels
- Create Open & Distribute Shipping Labels
- Create International Shipping Labels (Express, Priority, First Class)
- Service Delivery Calculator
- Confirm Tracking

Requirements
============

- PHP >= 5.4 configured with the following extensions:
  - cURL
- USPS API Username


Authors
=======
- Vincent Gabriel <http://vadimg.com> (Original PHP-Wrapper)

Contributors
============
@pdbreen
@bredmor
@scs-ben
