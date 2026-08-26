<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::latest()->paginate(10);
        return view('admin.documents.index', compact('documents'));
    }

    public function create()
    {
        $categories = \App\Models\Menu::whereNotNull('parent_id')
            ->where('module_type', 'documents')
            ->orderBy('order_index')
            ->get();
        return view('admin.documents.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'document_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf|max:51200', // Max 50MB PDF
            'zip_file' => 'nullable|file|mimes:zip|max:51200', // Max 50MB ZIP
        ], [
            'file.mimes' => 'Format file tidak valid. Hanya file PDF yang diperbolehkan.',
            'zip_file.mimes' => 'Format file tidak valid. Hanya file ZIP yang diperbolehkan.',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('documents', 'public');
        }

        $zipPath = null;
        if ($request->hasFile('zip_file')) {
            $zipPath = $request->file('zip_file')->store('documents/zips', 'public');
        }

        Document::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'category' => $request->category,
            'document_date' => $request->document_date,
            'file_path' => $filePath,
            'zip_path' => $zipPath,
        ]);

        return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil diunggah');
    }

    public function edit(Document $document)
    {
        $categories = \App\Models\Menu::whereNotNull('parent_id')
            ->where('module_type', 'documents')
            ->orderBy('order_index')
            ->get();
        return view('admin.documents.edit', compact('document', 'categories'));
    }

    public function update(Request $request, Document $document)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'document_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf|max:51200',
            'zip_file' => 'nullable|file|mimes:zip|max:51200',
        ], [
            'file.mimes' => 'Format file tidak valid. Hanya file PDF yang diperbolehkan.',
            'zip_file.mimes' => 'Format file tidak valid. Hanya file ZIP yang diperbolehkan.',
        ]);

        $filePath = $document->file_path;
        if ($request->hasFile('file')) {
            if ($filePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file')->store('documents', 'public');
        }

        $zipPath = $document->zip_path;
        if ($request->hasFile('zip_file')) {
            if ($zipPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($zipPath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($zipPath);
            }
            $zipPath = $request->file('zip_file')->store('documents/zips', 'public');
        }

        $document->update([
            'title' => $request->title,
            'category' => $request->category,
            'document_date' => $request->document_date,
            'file_path' => $filePath,
            'zip_path' => $zipPath,
        ]);

        return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil diperbarui');
    }

    public function destroy(Document $document)
    {
        
        $document->delete();
        return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil dihapus');
    }
}


