<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        $brands = Brand::orderBy('name')->paginate(20);

        return view('brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('brands.create', ['brand' => new Brand()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Brand::create($this->validatedBrand($request));

        return redirect()->route('brands.index')->with('status', 'Brand added successfully.');
    }

    public function edit(Brand $brand): View
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $brand->update($this->validatedBrand($request, $brand));

        return redirect()->route('brands.index')->with('status', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();

        return redirect()->route('brands.index')->with('status', 'Brand deleted successfully.');
    }

    private function validatedBrand(Request $request, ?Brand $brand = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:brands,name,'.($brand?->id ?? 'NULL')],
        ]);
    }
}
