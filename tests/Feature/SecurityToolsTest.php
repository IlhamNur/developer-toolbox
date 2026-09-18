<?php

namespace Tests\Feature;

use App\Services\Security\HashToolService;
use Tests\TestCase;

class SecurityToolsTest extends TestCase
{
    public function test_hash_generator_outputs_expected_values(): void
    {
        $service = new HashToolService();

        $this->assertSame(hash('sha256', 'hello world'), $service->generate('hello world', 'sha256'));
        $this->assertSame(hash('md5', 'hello world'), $service->generate('hello world', 'md5'));
    }

    public function test_hash_comparator_matches_expected_hash_values(): void
    {
        $service = new HashToolService();

        $this->assertTrue($service->compare('hello world', hash('sha256', 'hello world')));
        $this->assertTrue($service->compare('hello world', hash('sha1', 'hello world')));
        $this->assertTrue($service->compare('hello world', hash('md5', 'hello world')));
        $this->assertFalse($service->compare('hello world', hash('sha256', 'goodbye')));
    }
}
