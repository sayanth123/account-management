<?php

namespace Tests\Unit;

use App\Helpers\LuhnHelper;
use Tests\TestCase;

class LuhnHelperTest extends TestCase
{
    /** @test */
    public function it_generates_a_valid_luhn_compliant_account_number()
    {
        $accountNumber = LuhnHelper::generateAccountNumber();
        $this->assertTrue(LuhnHelper::validateAccountNumber($accountNumber));
    }

    /** @test */
    public function it_detects_an_invalid_luhn_number()
    {
        $invalidNumber = '123456789012';
        $this->assertFalse(LuhnHelper::validateAccountNumber($invalidNumber));
    }
}
