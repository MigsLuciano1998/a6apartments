<?php

namespace Tests\Unit;

use App\Services\BillingFormulaEvaluator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BillingFormulaEvaluatorTest extends TestCase
{
    public function test_it_evaluates_the_default_water_formula(): void
    {
        $amount = (new BillingFormulaEvaluator)->evaluate(
            BillingFormulaEvaluator::WATER_FORMULA,
            12,
            2,
        );

        $this->assertSame(1750.0, $amount);
    }

    public function test_it_evaluates_the_default_electricity_formula(): void
    {
        $amount = (new BillingFormulaEvaluator)->evaluate(
            BillingFormulaEvaluator::ELECTRICITY_FORMULA,
            110,
            100,
        );

        $this->assertSame(130.0, $amount);
    }

    public function test_it_rejects_unknown_tokens(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new BillingFormulaEvaluator)->evaluate('current_reading + alert(1)', 10, 0);
    }
}
