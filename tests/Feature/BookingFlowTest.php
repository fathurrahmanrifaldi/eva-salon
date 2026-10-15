<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\Role;
use App\Models\BusinessHour;
use App\Models\Customer;
use App\Models\SalonProfile;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Staff;
use App\Models\StaffSchedule;
use App\Models\User;
use App\Support\Tz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    private Service $service;
    private Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        SalonProfile::create([
            'name' => 'Eva Salon',
            'address' => 'Jl. Pengujian No. 1',
            'phone' => '+6281234567890',
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

        $category = ServiceCategory::create(['name' => 'Hair Styling', 'sort_order' => 1]);
        $this->service = Service::create([
            'category_id' => $category->id,
            'name' => 'Cut & Blow VIP',
            'price' => 120000,
            'duration_minutes' => 60,
            'buffer_minutes' => 10,
            'is_available' => true,
            'is_featured' => true,
        ]);

        $this->staff = Staff::create(['name' => 'Sinta', 'is_active' => true]);
        $this->staff->services()->attach($this->service->id);

        foreach (range(0, 6) as $dow) {
            StaffSchedule::create([
                'staff_id' => $this->staff->id,
                'day_of_week' => $dow,
                'start_time' => '09:00',
                'end_time' => '20:00',
            ]);
        }
    }

    public function test_public_pages_load_successfully(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('Eva Salon')->assertSee('Cut & Blow VIP');
        $this->get('/layanan')->assertStatus(200)->assertSee('Cut & Blow VIP');
        $this->get('/kebijakan-privasi')->assertStatus(200)->assertSee('Kebijakan Privasi');
        $this->get('/booking')->assertStatus(200)->assertSee('Pilih Layanan');
        $this->get('/status')->assertStatus(200)->assertSee('Cek Status Booking');
    }

    public function test_customer_booking_and_status_check_flow(): void
    {
        $targetDate = Tz::local(Tz::now()->addDays(2))->toDateString();

        $response = $this->post('/booking', [
            'service_ids' => [$this->service->id],
            'staff_id' => $this->staff->id,
            'date' => $targetDate,
            'time' => '14:00',
            'name' => 'Citra Lestari',
            'phone' => '081234567890',
            'notes' => 'Tolong sampo wangi herbal',
            'consent' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'customer_name' => 'Citra Lestari',
            'status' => BookingStatus::Pending->value,
            'total_price' => 120000,
        ]);

        $booking = \App\Models\Booking::where('customer_name', 'Citra Lestari')->first();
        $this->assertNotNull($booking);

        // Cek status booking via form status publik
        $check = $this->post('/status', [
            'code' => $booking->booking_code,
            'phone' => '081234567890',
        ]);

        $check->assertStatus(200)
            ->assertSee($booking->booking_code)
            ->assertSee('Citra Lestari')
            ->assertSee('Menunggu konfirmasi');

        // Batalkan booking mandiri oleh pelanggan
        $cancel = $this->post('/status/batal', [
            'code' => $booking->booking_code,
            'phone' => '081234567890',
            'reason' => 'Ada acara keluarga mendadak',
        ]);

        $cancel->assertStatus(200)->assertSee('Booking berhasil dibatalkan');
        $booking->refresh();
        $this->assertEquals(BookingStatus::Cancelled, $booking->status);
    }

    public function test_booking_fails_without_consent(): void
    {
        $targetDate = Tz::local(Tz::now()->addDays(2))->toDateString();

        $response = $this->post('/booking', [
            'service_ids' => [$this->service->id],
            'staff_id' => $this->staff->id,
            'date' => $targetDate,
            'time' => '14:00',
            'name' => 'Citra Lestari',
            'phone' => '081234567890',
            'notes' => 'Tolong sampo wangi herbal',
            'consent' => 0,
        ]);

        $response->assertSessionHasErrors(['consent']);
    }

    public function test_role_based_access_control(): void
    {
        $kasir = User::create([
            'name' => 'Kasir Salon',
            'email' => 'kasir@evasalon.id',
            'password' => bcrypt('password'),
            'role' => Role::Kasir,
            'is_active' => true,
        ]);

        $owner = User::create([
            'name' => 'Owner Salon',
            'email' => 'owner@evasalon.id',
            'password' => bcrypt('password'),
            'role' => Role::Owner,
            'is_active' => true,
        ]);

        // Tamu tanpa login ditolak ke dashboard
        $this->get('/admin/hari-ini')->assertRedirect('/login');

        // Kasir bisa melihat antrean hari ini, kalender, dan data pelanggan
        $this->actingAs($kasir)->get('/admin/hari-ini')->assertStatus(200);
        $this->actingAs($kasir)->get('/admin/kalender')->assertStatus(200);
        $this->actingAs($kasir)->get('/admin/pelanggan')->assertStatus(200);

        // Kasir TIDAK BISA mengakses menu kelola owner (Layanan, Tambah Staf, Kasir, Profil)
        $this->actingAs($kasir)->get('/admin/layanan')->assertStatus(403);
        $this->actingAs($kasir)->get('/admin/staf/tambah')->assertStatus(403);
        $this->actingAs($kasir)->get('/admin/kasir')->assertStatus(403);
        $this->actingAs($kasir)->get('/admin/profil')->assertStatus(403);

        // Owner BISA mengakses semua halaman
        $this->actingAs($owner)->get('/admin/hari-ini')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/layanan')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/staf')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/kasir')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/profil')->assertStatus(200);
    }

    public function test_walk_in_booking_by_cashier(): void
    {
        $kasir = User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir1@evasalon.id',
            'password' => bcrypt('password'),
            'role' => Role::Kasir,
            'is_active' => true,
        ]);

        $response = $this->actingAs($kasir)->post('/admin/walk-in', [
            'service_ids' => [$this->service->id],
            'staff_id' => $this->staff->id,
            'start_time' => '15:00',
            'name' => 'Tamu Langsung',
            'phone' => '081233334444',
            'notes' => 'Potong pendek',
        ]);

        $response->assertRedirect('/admin/hari-ini');
        $this->assertDatabaseHas('bookings', [
            'customer_name' => 'Tamu Langsung',
            'source' => \App\Enums\BookingSource::WalkIn->value,
            'status' => BookingStatus::Confirmed->value,
        ]);
    }

    public function test_phone_hold_and_release_flow(): void
    {
        $kasir = User::create([
            'name' => 'Kasir 2',
            'email' => 'kasir2@evasalon.id',
            'password' => bcrypt('password'),
            'role' => Role::Kasir,
            'is_active' => true,
        ]);

        $today = Tz::local(Tz::now())->toDateString();

        $response = $this->actingAs($kasir)->post('/admin/hold', [
            'service_ids' => [$this->service->id],
            'staff_id' => $this->staff->id,
            'date' => $today,
            'time' => '17:00',
            'name' => 'Ibu Wati Telepon',
            'phone' => '081255556666',
            'hold_minutes' => 15,
            'notes' => 'Sedang di jalan',
        ]);

        $response->assertRedirect();
        $booking = \App\Models\Booking::where('customer_name', 'Ibu Wati Telepon')->first();
        $this->assertNotNull($booking);
        $this->assertEquals(BookingStatus::Hold, $booking->status);
        $this->assertNotNull($booking->hold_until);

        // Lepas slot
        $release = $this->actingAs($kasir)->post("/admin/booking/{$booking->id}/lepas");
        $release->assertRedirect();
        $booking->refresh();
        $this->assertEquals(BookingStatus::Cancelled, $booking->status);
    }

    public function test_payment_recording_and_status_completion(): void
    {
        $kasir = User::create([
            'name' => 'Kasir 3',
            'email' => 'kasir3@evasalon.id',
            'password' => bcrypt('password'),
            'role' => Role::Kasir,
            'is_active' => true,
        ]);

        $today = Tz::local(Tz::now())->toDateString();
        $this->actingAs($kasir)->post('/admin/walk-in', [
            'service_ids' => [$this->service->id],
            'staff_id' => $this->staff->id,
            'start_time' => '11:00',
            'name' => 'Pelanggan Bayar',
            'phone' => '081277778888',
        ]);

        $booking = \App\Models\Booking::where('customer_name', 'Pelanggan Bayar')->first();

        // Rekam pembayaran tunai
        $payResponse = $this->actingAs($kasir)->post("/admin/booking/{$booking->id}/bayar", [
            'amount' => 120000,
            'method' => 'cash',
            'kind' => 'payment',
            'note' => 'Pelunasan tunai',
        ]);

        $payResponse->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'amount' => 120000,
            'method' => 'cash',
        ]);

        // Tandai selesai
        $completeResponse = $this->actingAs($kasir)->post("/admin/booking/{$booking->id}/transisi/completed");
        $completeResponse->assertRedirect();
        $booking->refresh();
        $this->assertEquals(BookingStatus::Completed, $booking->status);
    }

    public function test_today_dashboard_renders_with_strict_lazy_loading(): void
    {
        \Illuminate\Database\Eloquent\Model::preventLazyLoading(true);

        $kasir = User::create([
            'name' => 'Kasir 4',
            'email' => 'kasir4@evasalon.id',
            'password' => bcrypt('password'),
            'role' => Role::Kasir,
            'is_active' => true,
        ]);

        $this->actingAs($kasir)->post('/admin/walk-in', [
            'service_ids' => [$this->service->id],
            'staff_id' => $this->staff->id,
            'start_time' => '14:00',
            'name' => 'Pelanggan Dashboard',
            'phone' => '081299990000',
        ]);

        $response = $this->actingAs($kasir)->get('/admin/hari-ini');
        $response->assertOk();
        $response->assertSee('Pelanggan Dashboard');
    }

    public function test_calendar_renders_with_bookings_and_badge_classes(): void
    {
        $kasir = User::create([
            'name' => 'Kasir 5',
            'email' => 'kasir5@evasalon.id',
            'password' => bcrypt('password'),
            'role' => Role::Kasir,
            'is_active' => true,
        ]);

        $this->actingAs($kasir)->post('/admin/walk-in', [
            'service_ids' => [$this->service->id],
            'staff_id' => $this->staff->id,
            'start_time' => '15:00',
            'name' => 'Pelanggan Kalender',
            'phone' => '081211112222',
        ]);

        $today = Tz::local(Tz::now())->toDateString();
        $response = $this->actingAs($kasir)->get("/admin/kalender?date={$today}");
        $response->assertOk();
        $response->assertSee('Pelanggan Kalender');
        $this->assertEquals('badge-confirmed', BookingStatus::Confirmed->badgeClass());
    }

    public function test_customer_pages_render_without_lazy_loading_violations(): void
    {
        \Illuminate\Database\Eloquent\Model::preventLazyLoading(true);

        $kasir = User::create([
            'name' => 'Kasir 6',
            'email' => 'kasir6@evasalon.id',
            'password' => bcrypt('password'),
            'role' => Role::Kasir,
            'is_active' => true,
        ]);

        $this->actingAs($kasir)->post('/admin/walk-in', [
            'service_ids' => [$this->service->id],
            'staff_id' => $this->staff->id,
            'start_time' => '16:00',
            'name' => 'Pelanggan Rina',
            'phone' => '081234567890',
        ]);

        $customer = Customer::where('phone', '+6281234567890')->first();
        $this->assertNotNull($customer);

        // Halaman Daftar Pelanggan (/admin/pelanggan)
        $indexResponse = $this->actingAs($kasir)->get('/admin/pelanggan');
        $indexResponse->assertOk();
        $indexResponse->assertSee('Pelanggan Rina');
        $indexResponse->assertSee('0812-3456-7890');

        // Halaman Detail Pelanggan (/admin/pelanggan/{customer})
        $showResponse = $this->actingAs($kasir)->get("/admin/pelanggan/{$customer->id}");
        $showResponse->assertOk();
        $showResponse->assertSee('Pelanggan Rina');

        // Verifikasi paidTotal pada booking tanpa relasi payments ter-load
        $rawBooking = \App\Models\Booking::without(['payments'])->find($customer->bookings()->first()->id);
        $this->assertFalse($rawBooking->relationLoaded('payments'));
        $this->assertEquals(0, $rawBooking->paidTotal());

        // Halaman Detail Booking (/admin/booking/{booking})
        $booking = $customer->bookings()->first();
        $bookingResponse = $this->actingAs($kasir)->get("/admin/booking/{$booking->id}");
        $bookingResponse->assertOk();
        $bookingResponse->assertSee('Chat WA');

        $owner = User::create([
            'name' => 'Owner Test',
            'email' => 'owner_lazy@evasalon.id',
            'password' => bcrypt('password'),
            'role' => Role::Owner,
            'is_active' => true,
        ]);
        $this->actingAs($owner)->get('/admin/layanan')->assertOk();
        $this->actingAs($owner)->get('/admin/staf')->assertOk();
    }
}



