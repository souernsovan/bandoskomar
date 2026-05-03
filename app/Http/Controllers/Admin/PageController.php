<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('id')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|alpha_dash|unique:pages,slug',
            'page_category' => 'required|in:main,resources,get-involved,donation,contact',
            'icon' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:published,draft',
        ]);

        $slug = $validated['slug'] ?: $this->uniqueSlug($validated['title']);

        $page = Page::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'page_category' => $validated['page_category'],
            'icon' => $validated['icon'] ?? 'file',
            'content' => [
                'header' => [
                    'title' => $validated['title'],
                    'description' => $validated['meta_description'] ?? '',
                ],
                'body' => $validated['body'] ?? '',
            ],
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('pages', 'slug')->ignore($page->id)],
            'page_category' => 'required|in:main,resources,get-involved,donation,contact',
            'icon' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:published,draft',
        ]);

        $content = $request->input('content');
        
        // If content is a string (from general textarea) and looks like JSON, decode it
        if (is_string($content) && !empty($content)) {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $content = $decoded;
            }
        }

        if (is_array($content)) {
            $content = array_replace_recursive($page->content ?? [], $content);
        }

        $page->update(array_merge($validated, ['content' => $content]));

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted successfully.');
    }

    private function uniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $count = 2;

        while (Page::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

}
