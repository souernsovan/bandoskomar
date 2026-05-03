<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = in_array($request->input('per_page', 25), [25, 50, 100]) ? $request->input('per_page', default: 25) : 25;
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('admin.products.index', compact('products', 'perPage'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['image'] = null;

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storeImage($request->file('image'));
        }

        $product = Product::create($validated);

        $newData = [
            'title' => $product->title,
            'slug' => $product->slug,
            'status' => $product->status,
            'description' => Str::limit($product->description, 200),
            'image' => $product->image,
        ];
        AuditLogService::logCreate(AuditLog::MODULE_PRODUCT, $product->title, $newData);

        return redirect()->route('admin.products.index')->with('success', 'Program created successfully.');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product->id)],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['image'] = $product->image;

        if ($request->input('remove_image') === '1') {
            if ($product->image) {
                $this->deleteImage($product->image);
            }
            if (!$request->hasFile('image')) {
                $validated['image'] = null;
            }
        }

        $oldData = [
            'title' => $product->title,
            'slug' => $product->slug,
            'status' => $product->status,
            'description' => $product->description,
            'image' => $product->image,
        ];

        if ($request->hasFile('image')) {
            $this->deleteImage($product->image);
            $validated['image'] = $this->storeImage($request->file('image'));
        }

        $product->update($validated);

        $newData = [
            'title' => $product->title,
            'slug' => $product->slug,
            'status' => $product->status,
            'description' => $product->description,
            'image' => $product->image,
        ];
        AuditLogService::logEdit(AuditLog::MODULE_PRODUCT, $product->title, $oldData, $newData);

        return redirect()->route('admin.products.index')->with('success', 'Program updated successfully.');
    }

    public function destroy(Product $product)
    {
        $title = $product->title;
        $this->deleteImage($product->image);
        $product->delete();

        AuditLogService::logDelete(AuditLog::MODULE_PRODUCT, $title);

        return redirect()->route('admin.products.index')->with('success', 'Program deleted successfully.');
    }

    private function storeImage($file): string
    {
        $uploadPath = public_path('images/products');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);

        return 'images/products/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && str_starts_with($path, 'images/products/')) {
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}
