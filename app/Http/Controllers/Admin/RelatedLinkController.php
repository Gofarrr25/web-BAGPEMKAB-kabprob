<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RelatedLink;
use Illuminate\Http\Request;

class RelatedLinkController extends Controller
{
    public function create()
    {
        return view('admin.related_links.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'logo_url' => 'nullable|string|max:2000',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'order' => 'nullable|integer',
        ]);

        $data = $request->only(['name', 'url']);
        $data['is_active'] = $request->has('is_active');
        $data['order'] = $request->order ?? 0;

        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store('related_links', 'public');
            $data['logo_url'] = asset('storage/' . $path);
        } else {
            $data['logo_url'] = $request->logo_url;
        }

        RelatedLink::create($data);

        return redirect()->route('admin.settings.index')->with('success', 'Link Terkait berhasil ditambahkan!');
    }

    public function edit(RelatedLink $related_link)
    {
        return view('admin.related_links.edit', compact('related_link'));
    }

    public function update(Request $request, RelatedLink $related_link)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'logo_url' => 'nullable|string|max:2000',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'order' => 'nullable|integer',
        ]);

        $data = $request->only(['name', 'url']);
        $data['is_active'] = $request->has('is_active');
        $data['order'] = $request->order ?? 0;

        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store('related_links', 'public');
            $data['logo_url'] = asset('storage/' . $path);
        } else {
            $data['logo_url'] = $request->logo_url;
        }

        $related_link->update($data);

        return redirect()->route('admin.settings.index')->with('success', 'Link Terkait berhasil diperbarui!');
    }

    public function destroy(RelatedLink $related_link)
    {
        $related_link->delete();

        return redirect()->route('admin.settings.index')->with('success', 'Link Terkait berhasil dihapus!');
    }
}
