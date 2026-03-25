# Laravel-USPS

Full Laravel package for the **USPS API v3** (OAuth2). Covers all 20 USPS API domains: addresses, tracking, labels, international labels, domestic prices, international prices, service standards, locations, carrier pickup, containers, payments, campaigns, package campaigns, adjustments, disputes, appointments, shipping options, and SCAN forms.

> **Note:** The legacy USPS Web Tools XML API was retired in January 2026. This package uses the new OAuth2-based REST/JSON API at `apis.usps.com`.

> **Reference:** Official USPS API examples and OpenAPI specs at [github.com/USPS/api-examples](https://github.com/USPS/api-examples).

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

All API domains are accessible via the `Usps` facade. Address methods are called directly; all other domains are accessed through fluent accessor methods that return typed API clients.

### Addresses

```php
use Usps;

// Validate an address
$result = Usps::validate([
    'Address' => '1600 Pennsylvania Ave NW',
    'City' => 'Washington',
    'State' => 'DC',
    'Zip' => '20500',
]);

// Full address lookup (raw v3 response)
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

### Tracking

```php
// Track packages (supports up to 35 at once)
$result = Usps::tracking()->track([
    ['trackingNumber' => '9400111899223456789012'],
]);

// Register for email notifications
Usps::tracking()->registerNotifications('9400111899223456789012', [
    'uniqueTrackingID' => 'abc-123',
    'notifyEventTypes' => ['ALL_UPDATES'],
    'recipients' => [['email' => 'user@example.com']],
]);

// Request proof of delivery
Usps::tracking()->proofOfDelivery('9400111899223456789012', [
    'uniqueTrackingID' => 'abc-123',
    'recipients' => [['email' => 'user@example.com', 'firstName' => 'John', 'lastName' => 'Doe']],
]);
```

### Domestic Labels

```php
// Create a shipping label (requires payment authorization token)
$label = Usps::labels()->createLabel($labelData, $paymentToken);
$label = Usps::labels()->createLabel($labelData, $paymentToken, 'idempotency-key');

// Create a return label
$label = Usps::labels()->createReturnLabel($labelData, $paymentToken);

// Cancel/refund a label
Usps::labels()->cancelLabel('9205500000000000000000');

// Edit label attributes
Usps::labels()->editLabel('9205500000000000000000', $patchData);

// First-Class indicia
Usps::labels()->createIndicia($indiciaData, $paymentToken);

// Intelligent Mail Barcode
Usps::labels()->createImb($imbData, $paymentToken);
Usps::labels()->cancelImb('00000000000000000000');

// Label branding
Usps::labels()->uploadBranding($svgData);
Usps::labels()->listBranding(limit: 10, offset: 0);
Usps::labels()->getBranding('image-uuid');
Usps::labels()->deleteBranding('image-uuid');
Usps::labels()->renameBranding('image-uuid', ['name' => 'new-name']);

// Reprint a label
Usps::labels()->reprintLabel('9205500000000000000000', $reprintData, $paymentToken);
```

### International Labels

```php
$label = Usps::internationalLabels()->createLabel($labelData, $paymentToken);
Usps::internationalLabels()->reprintLabel('CX123456789US', $reprintData, $paymentToken);
Usps::internationalLabels()->cancelLabel('CX123456789US');
Usps::internationalLabels()->createIndicia($indiciaData, $paymentToken);
```

### Domestic Prices

```php
// Base rate search
$rate = Usps::domesticPrices()->baseRateSearch([
    'originZIPCode' => '20500',
    'destinationZIPCode' => '33101',
    'weight' => 2.5,
    'mailClass' => 'PRIORITY_MAIL',
]);

// Extra service rate search
$rate = Usps::domesticPrices()->extraServiceRateSearch($rateIngredients);

// List eligible products
$list = Usps::domesticPrices()->baseRateListSearch($rateIngredients);

// Total rates (base + extra services)
$total = Usps::domesticPrices()->totalRateSearch($rateIngredients);

// First-Class letter rates
$letter = Usps::domesticPrices()->letterRateSearch($rateIngredients);
```

### International Prices

```php
$rate = Usps::internationalPrices()->baseRateSearch($rateIngredients);
$rate = Usps::internationalPrices()->extraServiceRateSearch($rateIngredients);
$list = Usps::internationalPrices()->baseRateListSearch($rateIngredients);
$total = Usps::internationalPrices()->totalRateSearch($rateIngredients);
$letter = Usps::internationalPrices()->letterRateSearch($rateIngredients);
```

### Service Standards

```php
// Delivery estimates (with dates and acceptance locations)
$estimates = Usps::serviceStandards()->getEstimates('20500', '33101', [
    'mailClass' => 'PRIORITY_MAIL',
]);

// Service standards (average delivery days)
$standards = Usps::serviceStandards()->getStandards('20500', '33101');
```

### Service Standards Directory

```php
// Get valid 5-digit ZIP codes
$zips = Usps::serviceStandardsDirectory()->getValidZip5Codes();

// Get directory report (paginated)
$report = Usps::serviceStandardsDirectory()->getReport([
    'originZIPCode' => '230',
    'destinationZIPCode' => '330',
    'responseFormat' => '3D_BASE',
    'mailClass' => 'PERIODICALS',
    'limit' => 10000,
]);
```

### Service Standards Files

```php
// List available files
$files = Usps::serviceStandardsFiles()->listFiles();

// Get a signed download URL
$download = Usps::serviceStandardsFiles()->generateSignedUrl('Combined_Service_Standard_Directory_MKT');
```

### International Service Standards

```php
$standard = Usps::internationalServiceStandards()->getServiceStandard('CA', 'PRIORITY_MAIL_INTERNATIONAL');
// Returns: { countryCode, mailClass, serviceStandardMessage }
```

### Locations

```php
// Dropoff locations for destination entry parcels
$locations = Usps::locations()->getDropoffLocations('33101', [
    'mailClass' => 'PARCEL_SELECT',
]);

// Post office locations
$offices = Usps::locations()->getPostOfficeLocations([
    'ZIPCode' => '33101',
    'radius' => 10,
]);

// Parcel locker locations
$lockers = Usps::locations()->getParcelLockerLocations(['ZIPCode' => '33101']);
```

### Carrier Pickup

```php
// Check pickup eligibility
$eligible = Usps::carrierPickup()->checkEligibility('123 Main St', [
    'ZIPCode' => '33101',
    'state' => 'FL',
]);

// Schedule a pickup
$pickup = Usps::carrierPickup()->schedulePickup([
    'pickupDate' => '2026-04-01',
    'pickupAddress' => [...],
    'packages' => [['packageType' => 'PRIORITY_MAIL', 'packageCount' => 2]],
    'estimatedWeight' => 5.0,
    'pickupLocation' => ['packageLocation' => 'FRONT_DOOR'],
]);

// Get, update, cancel
$details = Usps::carrierPickup()->getPickup('WTC12345');
Usps::carrierPickup()->updatePickup('WTC12345', $data, $etag);
Usps::carrierPickup()->cancelPickup('WTC12345', $etag);
```

### Containers

```php
// Create a container label
$container = Usps::containers()->createContainer($containerData);

// Add/remove packages
Usps::containers()->addPackages('CNT-001', ['trackingNumbers' => [...]]);
Usps::containers()->removePackage('CNT-001', '9400111899223456789012');
Usps::containers()->removeAllPackages('CNT-001');

// Close container and generate manifest
Usps::containers()->createManifest(['containers' => ['CNT-001']]);
```

### Payments

```php
// Create payment authorization token (required for labels)
$auth = Usps::payments()->createPaymentAuthorization([
    'roles' => [
        ['roleName' => 'PAYER', 'CRID' => '12345678', 'accountType' => 'EPS', 'accountNumber' => '1234'],
        ['roleName' => 'LABEL_OWNER', 'CRID' => '12345678', 'MID' => '123456', 'manifestMID' => '123456'],
    ],
]);
$token = $auth['paymentAuthorizationToken'];

// Check account balance
$account = Usps::payments()->getPaymentAccount('12345678', 'EPS', ['amount' => 50.00]);
```

### Informed Delivery Campaigns (Mail)

```php
$campaign = Usps::campaigns()->createCampaign($campaignData);
$list = Usps::campaigns()->searchCampaigns(['status' => 'ACTIVE']);
$detail = Usps::campaigns()->getCampaign('CAM-001');
Usps::campaigns()->updateCampaign('CAM-001', $data);
Usps::campaigns()->cancelCampaign('CAM-001');
Usps::campaigns()->addImbs('CAM-001', ['imbs' => [...]]);

// Callback key management
$keys = Usps::campaigns()->getCallbackKeys('12345');
$summary = Usps::campaigns()->getCallbackSummary('12345', 'key-abc');
$details = Usps::campaigns()->getCallbackDetails('12345', 'key-abc');
```

### Informed Delivery Package Campaigns

```php
$campaign = Usps::packageCampaigns()->createCampaign($campaignData);
$list = Usps::packageCampaigns()->searchCampaigns();
$detail = Usps::packageCampaigns()->getCampaign('PKG-001');
Usps::packageCampaigns()->updateCampaign('PKG-001', $data);
Usps::packageCampaigns()->cancelCampaign('PKG-001');
Usps::packageCampaigns()->addTrackingNumbers('PKG-001', ['trackingNumbers' => [...]]);
```

### Adjustments

```php
$adjustments = Usps::adjustments()->getAdjustments('12345678', '920011234561234567890', 'CENSUS');
$adjustments = Usps::adjustments()->getAdjustments('12345678', '920011234561234567890', 'DUPLICATES', [
    'destinationZIPCode' => '33101',
]);
```

### Disputes

```php
$dispute = Usps::disputes()->createDispute([
    'EPSTransactionID' => 'TXN-001',
    'trackingID' => '920011234561234567890',
    'CRID' => '12345678',
    'reason' => 'INCORRECT_ASSESSED_WEIGHT',
    'description' => 'Weight was measured incorrectly.',
    'name' => 'John Doe',
    'disputeCount' => '1',
]);
```

### Appointments (FAST)

```php
// Check availability
$slots = Usps::appointments()->getAvailability(['facilityId' => 'FAC-001']);

// Create, update, cancel
$appt = Usps::appointments()->createAppointment($appointmentData);
Usps::appointments()->updateAppointment($updateData);
Usps::appointments()->cancelAppointment(['appointmentId' => 'APT-001']);
```

### Shipping Options

```php
// Combined pricing + service standards + available options in one call
$options = Usps::shippingOptions()->search([
    'originZIPCode' => '20500',
    'destinationZIPCode' => '33101',
    'weight' => 2.5,
    'length' => 12,
    'width' => 8,
    'height' => 4,
    'mailClass' => 'PRIORITY_MAIL',
]);
```

### SCAN Forms

```php
// Label shipment — link tracking numbers to a single EFN
$form = Usps::scanForms()->createLabelShipment([
    'trackingNumbers' => ['9400111899223456789012', '9400111899223456789013'],
]);

// MID shipment
$form = Usps::scanForms()->createMidShipment([
    'MID' => '123456',
    'trackingNumbers' => ['9400111899223456789012'],
]);

// Manifest MID shipment
$form = Usps::scanForms()->createManifestMidShipment([
    'manifestMID' => '654321',
    'MID' => '123456',
]);
```

## API Domains

| Domain | Facade Accessor | Endpoints |
|--------|----------------|-----------|
| Addresses | Direct methods | 3 |
| Tracking | `tracking()` | 3 |
| Domestic Labels | `labels()` | 12 |
| International Labels | `internationalLabels()` | 4 |
| Domestic Prices | `domesticPrices()` | 5 |
| International Prices | `internationalPrices()` | 5 |
| Service Standards | `serviceStandards()` | 2 |
| Service Standards Directory | `serviceStandardsDirectory()` | 2 |
| Service Standards Files | `serviceStandardsFiles()` | 2 |
| International Service Standards | `internationalServiceStandards()` | 1 |
| Locations | `locations()` | 3 |
| Carrier Pickup | `carrierPickup()` | 5 |
| Containers | `containers()` | 5 |
| Payments | `payments()` | 2 |
| Campaigns | `campaigns()` | 9 |
| Package Campaigns | `packageCampaigns()` | 6 |
| Adjustments | `adjustments()` | 1 |
| Disputes | `disputes()` | 1 |
| Appointments | `appointments()` | 4 |
| Shipping Options | `shippingOptions()` | 1 |
| SCAN Forms | `scanForms()` | 3 |

## Artisan Commands

```bash
# Validate an address
php artisan usps:validate "1600 Pennsylvania Ave NW" --state=DC --zip=20500

# Track packages
php artisan usps:track 9400111899223456789012

# ZIP code lookup (city/state)
php artisan usps:zip 33101

# Calculate shipping rates
php artisan usps:price 20500 33101 16 --mail-class=PRIORITY_MAIL

# Delivery estimates
php artisan usps:standards 20500 33101

# Find USPS locations
php artisan usps:locations 33101 --type=post-office --radius=5
```

## Authentication

The package automatically handles OAuth2 client credentials flow. Access tokens are cached per-scope for ~50 minutes to minimize token requests. Each API domain requests only the scopes it needs.

## License

MIT
