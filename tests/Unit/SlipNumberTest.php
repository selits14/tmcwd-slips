<?php

namespace Tests\Unit;

use App\Support\SlipNumber;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SlipNumberTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('slip_number_sequences', function (Blueprint $table) {
            $table->string('prefix')->primary();
            $table->unsignedInteger('number')->default(0);
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('slip_number_sequences');

        parent::tearDown();
    }

    public function test_gate_and_gas_series_continue_when_display_year_changes(): void
    {
        $this->assertSame('PS-2026-00001', SlipNumber::next('PS-2026-', 5));
        $this->assertSame('PS-2026-00002', SlipNumber::next('PS-2026-', 5));
        $this->assertSame('GP-2026-00001', SlipNumber::next('GP-', 5, 'GP-2026-'));
        $this->assertSame('GP-2027-00002', SlipNumber::next('GP-', 5, 'GP-2027-'));
        $this->assertSame('GS-2026-0001', SlipNumber::next('GS-', 4, 'GS-2026-'));
        $this->assertSame('GS-2027-0002', SlipNumber::next('GS-', 4, 'GS-2027-'));
    }

    public function test_pass_slip_series_uses_a_continuous_counter_with_a_year_display_prefix(): void
    {
        $this->assertSame('PS-2026-00001', SlipNumber::next('PS-', 5, 'PS-2026-'));
        $this->assertSame('PS-2026-00002', SlipNumber::next('PS-', 5, 'PS-2026-'));
        $this->assertSame('PS-2027-00003', SlipNumber::next('PS-', 5, 'PS-2027-'));
    }
}
