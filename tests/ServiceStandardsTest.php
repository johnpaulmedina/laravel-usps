<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\ServiceStandards;
use Johnpaulmedina\Usps\ServiceStandardsDirectory;
use Johnpaulmedina\Usps\ServiceStandardsFiles;
use Johnpaulmedina\Usps\InternationalServiceStandards;
use Orchestra\Testbench\TestCase;

class ServiceStandardsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . md5('test-id_service-standards'), 'fake-token', 3600);
        Cache::put('usps_oauth_token_' . md5('test-id_service-standards-directory'), 'fake-token', 3600);
        Cache::put('usps_oauth_token_' . md5('test-id_service-standards-files'), 'fake-token', 3600);
        Cache::put('usps_oauth_token_' . md5('test-id_international-service-standard'), 'fake-token', 3600);
    }

    public function test_get_estimates(): void
    {
        Http::fake([
            'apis.usps.com/service-standards/v3/estimates*' => Http::response([
                ['mailClass' => 'PRIORITY_MAIL', 'serviceStandard' => ' 2'],
            ]),
        ]);

        $ss = new ServiceStandards('test-id', 'test-secret');
        $result = $ss->getEstimates('20500', '33101', ['mailClass' => 'PRIORITY_MAIL']);

        $this->assertIsArray($result);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'originZIPCode=20500'));
    }

    public function test_get_standards(): void
    {
        Http::fake([
            'apis.usps.com/service-standards/v3/standards*' => Http::response([
                ['mailClass' => 'PRIORITY_MAIL', 'days' => 2],
            ]),
        ]);

        $ss = new ServiceStandards('test-id', 'test-secret');
        $result = $ss->getStandards('20500', '33101');

        $this->assertIsArray($result);
    }

    public function test_directory_get_valid_zip5_codes(): void
    {
        Http::fake([
            'apis.usps.com/service-standards-directory/v3/zip5Codes' => Http::response([
                'originZIPCodes' => ['10001', '30301'],
                'destinationZIPCodes' => ['60601'],
            ]),
        ]);

        $ssd = new ServiceStandardsDirectory('test-id', 'test-secret');
        $result = $ssd->getValidZip5Codes();

        $this->assertArrayHasKey('originZIPCodes', $result);
    }

    public function test_directory_get_report(): void
    {
        Http::fake([
            'apis.usps.com/service-standards-directory/v3/reporter' => Http::response([
                'standards' => [['originZIPCode' => '230', 'destinationZIPCode' => '330']],
            ]),
        ]);

        $ssd = new ServiceStandardsDirectory('test-id', 'test-secret');
        $result = $ssd->getReport(['originZIPCode' => '230', 'destinationZIPCode' => '330', 'responseFormat' => '3D_BASE']);

        $this->assertArrayHasKey('standards', $result);
        Http::assertSent(fn ($r) => $r->method() === 'POST');
    }

    public function test_files_list_files(): void
    {
        Http::fake([
            'apis.usps.com/service-standards-files/v3/files' => Http::response([
                ['title' => 'Complete Service Standard Data', 'href' => 'https://example.com'],
            ]),
        ]);

        $ssf = new ServiceStandardsFiles('test-id', 'test-secret');
        $result = $ssf->listFiles();

        $this->assertIsArray($result);
    }

    public function test_files_generate_signed_url(): void
    {
        Http::fake([
            'apis.usps.com/service-standards-files/v3/files/*/generate-signed-url' => Http::response([
                'signedURL' => 'https://download.example.com/file.zip?token=abc',
                'contentLength' => 52428800,
            ]),
        ]);

        $ssf = new ServiceStandardsFiles('test-id', 'test-secret');
        $result = $ssf->generateSignedUrl('Combined_Service_Standard_Directory_MKT');

        $this->assertArrayHasKey('signedURL', $result);
    }

    public function test_get_estimates_normalizes_zip_with_spaces(): void
    {
        Http::fake([
            'apis.usps.com/service-standards/v3/estimates*' => Http::response([]),
        ]);

        $ss = new ServiceStandards('test-id', 'test-secret');
        $ss->getEstimates('205 00', '331-01');

        Http::assertSent(fn ($r) => str_contains($r->url(), 'originZIPCode=20500')
            && str_contains($r->url(), 'destinationZIPCode=33101'));
    }

    public function test_get_standards_normalizes_zip(): void
    {
        Http::fake([
            'apis.usps.com/service-standards/v3/standards*' => Http::response([]),
        ]);

        $ss = new ServiceStandards('test-id', 'test-secret');
        $ss->getStandards('20500-0005', '33101');

        Http::assertSent(fn ($r) => str_contains($r->url(), 'originZIPCode=205000005'));
    }

    public function test_international_service_standard(): void
    {
        Http::fake([
            'apis.usps.com/international-service-standard/v3/international-service-standard*' => Http::response([
                'countryCode' => 'CA',
                'mailClass' => 'PRIORITY_MAIL_INTERNATIONAL',
                'serviceStandardMessage' => 'Estimated delivery in 6-10 business days',
            ]),
        ]);

        $iss = new InternationalServiceStandards('test-id', 'test-secret');
        $result = $iss->getServiceStandard('CA', 'PRIORITY_MAIL_INTERNATIONAL');

        $this->assertEquals('CA', $result['countryCode']);
        $this->assertArrayHasKey('serviceStandardMessage', $result);
    }

    public function test_international_service_standard_accepts_country_name(): void
    {
        Http::fake([
            'apis.usps.com/international-service-standard/v3/international-service-standard*' => Http::response([
                'countryCode' => 'CA',
            ]),
        ]);

        $iss = new InternationalServiceStandards('test-id', 'test-secret');
        $iss->getServiceStandard('Canada', 'PRIORITY_MAIL_INTERNATIONAL');

        Http::assertSent(fn ($r) => str_contains($r->url(), 'countryCode=CA'));
    }

    public function test_international_service_standard_throws_for_invalid_country(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid country code: ZZ.');

        $iss = new InternationalServiceStandards('test-id', 'test-secret');
        $iss->getServiceStandard('ZZ', 'PRIORITY_MAIL_INTERNATIONAL');
    }
}
