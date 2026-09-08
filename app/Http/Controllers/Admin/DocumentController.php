<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    private function checkOwnership(Document $model)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && $user->hasRole('Staf') && $model->user_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda tidak diizinkan mengubah/menghapus konten milik orang lain.');
        }
    }

    public function index()
    {
        $documents = Document::latest()->paginate(10);
        return view('admin.documents.index', compact('documents'));
    }

    private function getDocumentCategories()
    {
        // Cari ID Menu Dokumen murni berdasarkan judul (tanpa ID hardcoded)
        $menuDokumen = \App\Models\Menu::where('title', 'DOKUMEN')->first();

        if ($menuDokumen) {
            // Ambil semua anak/submenu dari Menu Dokumen tersebut
            return \App\Models\Menu::where('parent_id', $menuDokumen->id)
                ->orderBy('order_index')
                ->get();
        }

        return collect();
    }

    public function create()
    {
        $categories = $this->getDocumentCategories();
        return view('admin.documents.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validCategories = $this->getDocumentCategories()->pluck('title')->toArray();

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => ['required', 'string', 'max:100', function ($attribute, $value, $fail) use ($validCategories) {
                if (!in_array($value, $validCategories)) {
                    $fail('Klasifikasi tidak valid. Harap pilih klasifikasi yang tersedia di bawah Menu Dokumen.');
                }
            }],
            'document_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf|max:102400', // Max 100MB PDF
            'zip_file' => 'nullable|file|mimes:zip|max:51200', // Max 50MB ZIP
        ], [
            'file.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima file PDF. Silakan pilih file dengan format .pdf.',
            'file.max' => '⚠️ Ukuran file PDF maksimal 100 MB.',
            'zip_file.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima file ZIP.',
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
        $this->checkOwnership($document);
        $categories = $this->getDocumentCategories();
        return view('admin.documents.edit', compact('document', 'categories'));
    }

    public function update(Request $request, Document $document)
    {
        $this->checkOwnership($document);

        $validCategories = $this->getDocumentCategories()->pluck('title')->toArray();
        // Izinkan kategori lama tetap valid saat diedit jika sebelumnya sudah ada
        if (!empty($document->category)) {
            $validCategories[] = $document->category;
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => ['required', 'string', 'max:100', function ($attribute, $value, $fail) use ($validCategories) {
                if (!in_array($value, $validCategories)) {
                    $fail('Klasifikasi tidak valid. Harap pilih klasifikasi yang tersedia di bawah Menu Dokumen.');
                }
            }],
            'document_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf|max:102400',
            'zip_file' => 'nullable|file|mimes:zip|max:51200',
        ], [
            'file.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima file PDF. Silakan pilih file dengan format .pdf.',
            'file.max' => '⚠️ Ukuran file PDF maksimal 100 MB.',
            'zip_file.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima file ZIP.',
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
        $this->checkOwnership($document);

        if ($document->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
        }

        if ($document->zip_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->zip_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($document->zip_path);
        }

        $document->delete();
        return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil dihapus');
    }
}


