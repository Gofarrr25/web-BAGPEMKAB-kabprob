<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    public function index()
    {
        $agendas = Agenda::latest('event_date')->paginate(10);
        return view('admin.agendas.index', compact('agendas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
        ]);

        $config = \HTMLPurifier_Config::createDefault();
        $purifier = new \HTMLPurifier($config);
        $cleanDescription = $request->description ? $purifier->purify($request->description) : null;

        Agenda::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $cleanDescription,
            'event_date' => $request->event_date,
            'location' => $request->location,
        ]);

        return redirect()->route('admin.agendas.index')->with('success', 'Agenda berhasil ditambahkan');
    }

    public function destroy(Agenda $agenda)
    {
        
        $agenda->delete();
        return redirect()->route('admin.agendas.index')->with('success', 'Agenda berhasil dihapus');
    }
}


