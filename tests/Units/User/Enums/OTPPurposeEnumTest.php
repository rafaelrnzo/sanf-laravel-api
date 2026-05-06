<?php

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sanf\Core\Modules\User\Enums\OTPPurposeEnum;

class OTPPurposeEnumTest extends PHPUnitTestCase
{
    public function testEnumHasAllExpectedValues()
    {
        $this->assertEquals('login', OTPPurposeEnum::LOGIN);
        $this->assertEquals('change_password', OTPPurposeEnum::CHANGE_PASSWORD);
        $this->assertEquals('change_pin', OTPPurposeEnum::CHANGE_PIN);
        $this->assertEquals('reset_password', OTPPurposeEnum::RESET_PASSWORD);
        $this->assertEquals('reset_pin', OTPPurposeEnum::RESET_PIN);
        $this->assertEquals('registration', OTPPurposeEnum::REGISTRATION);
    }

    public function testAllContainsAllEnumValues()
    {
        $this->assertCount(6, OTPPurposeEnum::ALL);
        $this->assertContains(OTPPurposeEnum::LOGIN, OTPPurposeEnum::ALL);
        $this->assertContains(OTPPurposeEnum::CHANGE_PASSWORD, OTPPurposeEnum::ALL);
        $this->assertContains(OTPPurposeEnum::CHANGE_PIN, OTPPurposeEnum::ALL);
        $this->assertContains(OTPPurposeEnum::RESET_PASSWORD, OTPPurposeEnum::ALL);
        $this->assertContains(OTPPurposeEnum::RESET_PIN, OTPPurposeEnum::ALL);
        $this->assertContains(OTPPurposeEnum::REGISTRATION, OTPPurposeEnum::ALL);
    }

    public function testIsValidReturnsTrueForValidPurpose()
    {
        $this->assertTrue(OTPPurposeEnum::isValid(OTPPurposeEnum::LOGIN));
        $this->assertTrue(OTPPurposeEnum::isValid(OTPPurposeEnum::CHANGE_PASSWORD));
        $this->assertTrue(OTPPurposeEnum::isValid(OTPPurposeEnum::CHANGE_PIN));
        $this->assertTrue(OTPPurposeEnum::isValid(OTPPurposeEnum::RESET_PASSWORD));
        $this->assertTrue(OTPPurposeEnum::isValid(OTPPurposeEnum::RESET_PIN));
        $this->assertTrue(OTPPurposeEnum::isValid(OTPPurposeEnum::REGISTRATION));
    }

    public function testIsValidReturnsFalseForInvalidPurpose()
    {
        $this->assertFalse(OTPPurposeEnum::isValid('invalid'));
        $this->assertFalse(OTPPurposeEnum::isValid(''));
        $this->assertFalse(OTPPurposeEnum::isValid('LOGIN'));
        $this->assertFalse(OTPPurposeEnum::isValid(null));
    }
}
