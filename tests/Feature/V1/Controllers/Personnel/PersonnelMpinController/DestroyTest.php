<?php

namespace Tests\Feature\V1\Controllers\Personnel\PersonnelMpinController;

use App\Models\Personnel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group controllers
     * @group controllers.personnel
     * @group controllers.personnel.mpin
     * @group controllers.personnel.mpin.destroy
     */
    public function test_personnel_can_reset_mpin()
    {
        $personnel = Personnel::factory()->create();

        $personnel->update([
            'mpin' => Hash::make('1234'),
            'pin_updated_at' => now()
        ]);

        Sanctum::actingAs($personnel, [], 'personnels');

        $this->deleteJson('/api/v1/personnel/mpin')
            ->assertSuccessful()
            ->assertJsonStructure(['message']);

        $this->assertTrue(Hash::check(Carbon::parse($personnel->birth_date)->format('Ymd'), $personnel->fresh()->mpin));
        $this->assertNull($personnel->fresh()->pin_updated_at);
    }
}
