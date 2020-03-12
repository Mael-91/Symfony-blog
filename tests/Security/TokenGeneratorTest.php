<?php

namespace App\Tests;

use App\Security\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{

    public function testCreateToken()
    {
        $tokenGenerator = new TokenGenerator();
        $token = $tokenGenerator->generateToken();
        $getToken = $token;

        $this->assertSame($token, $getToken);
    }
}
