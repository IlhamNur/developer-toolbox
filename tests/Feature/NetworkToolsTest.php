<?php

namespace Tests\Feature;

use App\Services\Network\NetworkToolService;
use Tests\TestCase;

class NetworkToolsTest extends TestCase
{
    public function test_ip_validator_detects_private_and_public_addresses(): void
    {
        $service = new NetworkToolService();

        $this->assertTrue($service->isValidIp('8.8.8.8'));
        $this->assertTrue($service->isValidIp('10.0.0.1'));
        $this->assertFalse($service->isValidIp('999.999.999.999'));
    }

    public function test_ip_range_checker_classifies_private_and_public_ranges(): void
    {
        $service = new NetworkToolService();

        $this->assertSame('private', $service->ipClassification('10.0.0.1'));
        $this->assertSame('public', $service->ipClassification('8.8.8.8'));
    }

    public function test_ipv6_local_ranges_are_classified_as_private(): void
    {
        $service = new NetworkToolService();

        $this->assertSame('private', $service->ipClassification('::1'));
        $this->assertSame('private', $service->ipClassification('fe80::1'));
        $this->assertSame('private', $service->ipClassification('fd00::1'));
    }
}
