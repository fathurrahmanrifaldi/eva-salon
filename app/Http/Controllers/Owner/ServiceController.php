<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Staff;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/** Kelola layanan dan kategori (owner). Tidak ada hapus: layanan dinonaktifkan lewat is_available (LY-04). */
class ServiceController extends Controller
{
    public function __construct(private ImageService $images) {}

    public function index(): View
    {
        Gate::authorize('viewAny', Service::class);

        return view('owner.services.index', [
            'categories' => ServiceCategory::with(['services' => fn ($q) => $q->with('staff')->orderBy('sort_order')->orderBy('name')])
                ->orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Service::class);

        return $this->form(new Service(['duration_minutes' => 60, 'buffer_minutes' => 10, 'is_available' => true]));
    }

    public function edit(Service $service): View
    {
        Gate::authorize('update', $service);

        return $this->form($service->load('staff'));
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        Gate::authorize('create', Service::class);

        $service = Service::create($this->data($request));
        $service->staff()->sync($request->staff_ids ?? []);

        return redirect()->route('owner.services.index')->with('success', "Layanan \"{$service->name}\" ditambahkan.");
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        Gate::authorize('update', $service);

        // Harga baru hanya berlaku untuk booking baru: booking lama memakai snapshot di booking_items (LY-03).
        $data = $this->data($request);
        if (isset($data['image_path'])) {
            $this->images->delete($service->image_path);
        }
        $service->update($data);
        $service->staff()->sync($request->staff_ids ?? []);

        return redirect()->route('owner.services.index')->with('success', "Layanan \"{$service->name}\" diperbarui.");
    }

    public function storeCategory(CategoryRequest $request): RedirectResponse
    {
        Gate::authorize('create', ServiceCategory::class);

        ServiceCategory::create(['name' => $request->name, 'sort_order' => $request->sort_order ?? 0]);

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function updateCategory(CategoryRequest $request, ServiceCategory $category): RedirectResponse
    {
        Gate::authorize('update', $category);

        $category->update(['name' => $request->name, 'sort_order' => $request->sort_order ?? 0]);

        return back()->with('success', 'Kategori diperbarui.');
    }

    private function form(Service $service): View
    {
        return view('owner.services.form', [
            'service' => $service,
            'categories' => ServiceCategory::orderBy('sort_order')->get(),
            'staff' => Staff::orderBy('sort_order')->get(),
            'assigned' => $service->exists ? $service->staff->pluck('id')->all() : [],
        ]);
    }

    private function data(ServiceRequest $request): array
    {
        $data = $request->safe()->except(['image', 'staff_ids']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        foreach (['is_available', 'is_featured', 'is_sensitive'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }
        if ($request->hasFile('image')) {
            $data['image_path'] = $this->images->store($request->file('image'), 'services');
        }

        return $data;
    }
}
