<?php

namespace App\Http\Controllers;

use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $warehouses = Warehouse::query()
            ->when($search, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Warehouses/Index', [
            'warehouses' => $warehouses,
            'filters' => [
                'search' => $search
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Warehouses/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWarehouseRequest $request)
    {
        Warehouse::create($request->validated());

        return redirect()
            ->route('warehouses.index')
            ->with('success', 'Warehouse created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse)
    {
        return redirect()->route('warehouses.edit', $warehouse);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse): Response
    {
        return Inertia::render('Warehouses/Edit', [
            'warehouse' => $warehouse
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        $warehouse->update($request->validated());

        return redirect()
            ->route('warehouses.index')
            ->with('success', 'Warehouse updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()
            ->route('warehouses.index')
            ->with('success', 'Warehouse deleted successfully.');
    }
}