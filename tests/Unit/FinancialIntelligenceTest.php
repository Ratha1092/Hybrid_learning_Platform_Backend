<?php

namespace Tests\Unit;

use App\Filament\Pages\BI\FinancialIntelligence;
use PHPUnit\Framework\TestCase;

class FinancialIntelligenceTest extends TestCase
{
    public function test_negative_amounts_are_clamped_to_zero_for_wallet_totals(): void
    {
        $this->assertSame(0.0, FinancialIntelligence::normalizeWalletValue(-0.76));
        $this->assertSame(25.5, FinancialIntelligence::normalizeWalletValue(25.5));
        $this->assertSame(0.0, FinancialIntelligence::normalizeWalletValue(-0.01));
    }
}
