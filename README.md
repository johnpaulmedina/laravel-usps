# Laravel-USPS

Laravel package for the **USPS Addresses API v3** (OAuth2). Validates and standardizes US addresses, looks up ZIP codes, and resolves city/state from ZIP codes using the official USPS API.

> **Note:** The legacy USPS Web Tools XML API was retired in January 2026. This package uses the new OAuth2-based REST/JSON API at `apis.usps.com`.

## Requirements

- PHP 8.1+
- Laravel 10, 11, 12, or 13
- USPS API credentials from [developer.usps.com](https://developer.usps.com)

## Installation

```bash
composer require johnpaulmedina/laravel-usps
```

The service provider and facade are auto-discovered.

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=usps-config
```

This creates `config/usps.php`:

```php
return [
    'client_id' => env('USPS_CLIENT_ID', ''),
    'client_secret' => env('USPS_CLIENT_SECRET', ''),
];
```

Add your credentials to `.env`:

```
USPS_CLIENT_ID=your-client-id
USPS_CLIENT_SECRET=your-client-secret
```

## Usage

### Address Validation

```php
use Usps;

$result = Usps::validate([
    'Address' => '1600 Pennsylvania Ave NW',
    'City' => 'Washington',
    'State' => 'DC',
    'Zip' => '20500',
]);

// Returns:
// ['address' => ['Address2' => '1600 PENNSYLVANIA AVE NW', 'City' => 'WASHINGTON', 'State' => 'DC', 'Zip5' => '20500', 'Zip4' => '0005']]
```

### Direct API Access

```php
use Usps;

// Full address lookup with raw v3 response
$result = Usps::addressLookup([
    'streetAddress' => '1600 Pennsylvania Ave NW',
    'city' => 'Washington',
    'state' => 'DC',
]);

// City/State lookup by ZIP
$result = Usps::cityStateLookup('20500');

// ZIP Code lookup by address
$result = Usps::zipCodeLookup([
    'streetAddress' => '1600 Pennsylvania Ave NW',
    'city' => 'Washington',
    'state' => 'DC',
]);
```

### Controller Example

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Usps;

class AddressController extends Controller
{
    public function validate(): JsonResponse
    {
        return response()->json(Usps::validate(Request::all()));
    }
}
```

## API Endpoints Used

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/addresses/v3/address` | Validate and standardize an address |
| GET | `/addresses/v3/city-state` | City/State lookup by ZIP code |
| GET | `/addresses/v3/zipcode` | ZIP code lookup by address |

## Authentication

The package automatically handles OAuth2 client credentials flow. Access tokens are cached for ~50 minutes to minimize token requests.

## License

MIT
