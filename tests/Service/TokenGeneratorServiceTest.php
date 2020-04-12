<?php

namespace App\Tests;

use App\Entity\ConfirmationToken;
use App\Service\TokenGeneratorService;
use PHPUnit\Framework\TestCase;

class TokenGeneratorServiceTest extends TestCase {

    public function testGenerateToken() {
        for ($i = 1; $i <= 20; $i++) {
            $service = new TokenGeneratorService();
            $this->assertEquals($i, mb_strlen($service->generateToken($i)));
        }
    }

    public function testIsExpired() {
        $service = new TokenGeneratorService();
        $this->assertFalse($service->isExpired((new ConfirmationToken())->setCreatedAt(new \DateTime('-60 minutes'))));
        $this->assertTrue($service->isExpired((new ConfirmationToken())->setCreatedAt(new \DateTime('-5000 minutes'))));
    }
}