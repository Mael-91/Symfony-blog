<?php

namespace App\Tests\Service;

use App\Service\TokenGeneratorService;
use PHPUnit\Framework\TestCase;

class TokenGeneratorServiceTest extends TestCase
{

    public function testCreateToken()
    {
        $tokenGenerator = new TokenGeneratorService();
        $token = $tokenGenerator->generateToken();
        $getToken = $token;

        $this->assertSame($token, $getToken);
    }
}
