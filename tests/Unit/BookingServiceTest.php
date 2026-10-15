<?php

namespace Tests\Unit;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\PaymentKind;
use App\Enums\PaymentMethod;
use App\Exceptions\BookingRuleException;
use App\Exceptions\SlotUnavailableException;
use App\Models\BusinessHour;
use App\Models\SalonProfile;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Staff;
use App\Models\StaffSchedule;
use App\Models\User;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use App\Support\Tz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $bookingService;
    private AvailabilityService $availabilityService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->availabilityService = app(AvailabilityService::class);
        $this->bookingService = app(BookingService::class);

        SalonProfile::create([
            'name' => 'Eva Salon',
            'address' => 'Jl. Pengujian No. 1',
            'wa_number' => '+6281234567890',
            'hold_default_minutes' => 15,
            'hold_max_minutes' => 60,
            'cancel_limit_minutes' => 30,
            'pending_alert_hours' => 2,
            'require_payment_to_complete' => true,
        ]);

        foreach (range(0, 6) as $dow) {
            BusinessHour::create([
                'day_of_week' => $dow,
                'open_time' => '09:00',
                'close_time' => '21:00',
                'is_closed' => false,
            ]);
        }
    }

    public function test_booking_slot_blocks_and_includes_buffer(): void
    {
        $category = ServiceCategory::create(['name' => 'Hair', 'sort_order' => 1]);
        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Haircut',
            'price' => 100000,
            'duration_minutes' => 60,
            'buffer_minutes' => 10,
            'is_available' => true,
        ]);

        $staff = Staff::create(['name' => 'Rina', 'is_active' => true]);
        $staff->services()->attach($service->id);
        // Shift Senin 09:00 - 18:00 (day_of_week = 1)
        StaffSchedule::create([
            'staff_id' => $staff->id,
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        // Tanggal Senin 2026-10-19 jam 10:00 WIB
        $startAt = Tz::parse('2026-10-19 10:00:00');

        $booking = $this->bookingService->create([
            'service_ids' => [$service->id],
            'staff_id' => $staff->id,
            'start_at' => $startAt,
            'name' => 'Dewi',
            'phone' => '081299998888',
            'source' => BookingSource::Online,
            'status' => BookingStatus::Confirmed,
        ]);

        $this->assertEquals(BookingStatus::Confirmed, $booking->status);
        $this->assertEquals(60, $booking->start_at->diffInMinutes($booking->end_at));
        // blocked_until harus mencakup buffer 10 menit (total 70 menit)
        $this->assertEquals(70, $booking->start_at->diffInMinutes($booking->blocked_until));

        // Coba pesan slot yang menabrak waktu layanan (10:30) -> harus gagal
        $this->expectException(SlotUnavailableException::class);
        $this->bookingService->create([
            'service_ids' => [$service->id],
            'staff_id' => $staff->id,
            'start_at' => Tz::parse('2026-10-19 10:30:00'),
            'name' => 'Sari',
            'phone' => '081299997777',
            'source' => BookingSource::Online,
        ]);
    }

    public function test_booking_slot_blocks_during_buffer_time(): void
    {
        $category = ServiceCategory::create(['name' => 'Hair', 'sort_order' => 1]);
        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Haircut',
            'price' => 100000,
            'duration_minutes' => 60,
            'buffer_minutes' => 15,
            'is_available' => true,
        ]);

        $staff = Staff::create(['name' => 'Budi', 'is_active' => true]);
        $staff->services()->attach($service->id);
        StaffSchedule::create([
            'staff_id' => $staff->id,
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        $startAt = Tz::parse('2026-10-19 10:00:00');
        $this->bookingService->create([
            'service_ids' => [$service->id],
            'staff_id' => $staff->id,
            'start_at' => $startAt,
            'name' => 'Tamu 1',
            'phone' => '081211112222',
            'source' => BookingSource::WalkIn,
            'status' => BookingStatus::Confirmed,
        ]);

        // Booking 1: 10:00 - 11:00, buffer sampai 11:15.
        // Booking jam 11:05 harus gagal karena berada dalam rentang buffer.
        $this->expectException(SlotUnavailableException::class);
        $this->bookingService->create([
            'service_ids' => [$service->id],
            'staff_id' => $staff->id,
            'start_at' => Tz::parse('2026-10-19 11:05:00'),
            'name' => 'Tamu 2',
            'phone' => '081233334444',
            'source' => BookingSource::WalkIn,
            'status' => BookingStatus::Confirmed,
        ]);
    }

    public function test_payment_recording_and_completion_guard(): void
    {
        $category = ServiceCategory::create(['name' => 'Treatment', 'sort_order' => 1]);
        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Creambath',
            'price' => 150000,
            'duration_minutes' => 60,
            'buffer_minutes' => 0,
            'is_available' => true,
        ]);

        $staff = Staff::create(['name' => 'Maya', 'is_active' => true]);
        $staff->services()->attach($service->id);
        StaffSchedule::create([
            'staff_id' => $staff->id,
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        $user = User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir1@evasalon.id',
            'password' => bcrypt('password'),
            'role' => \App\Enums\Role::Kasir,
            'is_active' => true,
        ]);

        $booking = $this->bookingService->create([
            'service_ids' => [$service->id],
            'staff_id' => $staff->id,
            'start_at' => Tz::parse('2026-10-19 14:00:00'),
            'name' => 'Ani',
            'phone' => '081288889999',
            'source' => BookingSource::Online,
            'status' => BookingStatus::Confirmed,
        ]);

        $this->assertEquals(150000, $booking->total_price);
        $this->assertEquals(150000, $booking->remaining());
        $this->assertFalse($booking->isPaid());

        // Tidak boleh ditandai selesai jika belum lunas (require_payment_to_complete = true)
        try {
            $this->bookingService->transition($booking, BookingStatus::Completed, $user);
            $this->fail('Harusnya melempar BookingRuleException karena belum lunas.');
        } catch (BookingRuleException $e) {
            $this->assertStringContainsString('pembayaran', strtolower($e->getMessage()));
        }

        // Catat pembayaran DP 50.000
        $this->bookingService->recordPayment($booking, $user, 50000, PaymentMethod::Transfer, PaymentKind::Payment, 'DP');
        $booking->refresh();
        $this->assertEquals(100000, $booking->remaining());

        // Catat pelunasan 100.000
        $this->bookingService->recordPayment($booking, $user, 100000, PaymentMethod::Cash, PaymentKind::Payment, 'Lunas');
        $booking->refresh();
        $this->assertEquals(0, $booking->remaining());
        $this->assertTrue($booking->isPaid());

        // Sekarang transisi ke completed harus berhasil
        $this->bookingService->transition($booking, BookingStatus::Completed, $user);
        $booking->refresh();
        $this->assertEquals(BookingStatus::Completed, $booking->status);
    }
}
