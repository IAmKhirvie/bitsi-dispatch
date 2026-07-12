<?php

namespace Tests\Feature;

use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverAttendanceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_attendance_api_is_disabled_without_token_config(): void
    {
        config(['services.mobile.attendance_token' => null]);

        $this->getJson('/api/v1/driver/my-schedule?phone=09181111111')
            ->assertStatus(503)
            ->assertJson(['error' => 'mobile attendance api disabled']);
    }

    public function test_mobile_attendance_api_rejects_missing_or_invalid_token(): void
    {
        config(['services.mobile.attendance_token' => 'test-token']);

        $this->getJson('/api/v1/driver/my-schedule?phone=09181111111')
            ->assertUnauthorized();

        $this->withHeader('X-Mobile-Token', 'wrong-token')
            ->getJson('/api/v1/driver/my-schedule?phone=09181111111')
            ->assertUnauthorized();
    }

    public function test_driver_cannot_check_in_to_unassigned_entry(): void
    {
        config(['services.mobile.attendance_token' => 'test-token']);

        $assignedDriver = Driver::create([
            'name' => 'Assigned Driver',
            'phone' => '09181111111',
            'is_active' => true,
        ]);
        $otherDriver = Driver::create([
            'name' => 'Other Driver',
            'phone' => '09182222222',
            'is_active' => true,
        ]);
        $dispatchDay = DispatchDay::create([
            'service_date' => today(),
            'created_by' => User::factory()->create()->id,
        ]);
        $entry = DispatchEntry::create([
            'dispatch_day_id' => $dispatchDay->id,
            'driver_id' => $otherDriver->id,
            'scheduled_departure' => '08:00',
        ]);

        $this->withHeader('X-Mobile-Token', 'test-token')
            ->postJson('/api/v1/driver/check-in', [
                'phone' => $assignedDriver->phone,
                'dispatch_entry_id' => $entry->id,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('dispatch_entry_id');
    }

    public function test_driver_can_view_schedule_with_valid_token(): void
    {
        config(['services.mobile.attendance_token' => 'test-token']);

        $driver = Driver::create([
            'name' => 'Assigned Driver',
            'phone' => '09181111111',
            'is_active' => true,
        ]);
        $dispatchDay = DispatchDay::create([
            'service_date' => today(),
            'created_by' => User::factory()->create()->id,
        ]);
        DispatchEntry::create([
            'dispatch_day_id' => $dispatchDay->id,
            'driver_id' => $driver->id,
            'route' => 'Naga - Manila',
            'scheduled_departure' => '08:00',
        ]);

        $this->withHeader('X-Mobile-Token', 'test-token')
            ->getJson('/api/v1/driver/my-schedule?phone='.$driver->phone)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'trips');
    }
}
