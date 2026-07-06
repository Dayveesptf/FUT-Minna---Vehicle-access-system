<?php

namespace App\Http\Controllers;

use App\Models\GatePoint;
use Illuminate\Http\Request;

class GatePointController extends Controller
{
    public function index()
    {
        $gates = GatePoint::withCount('accessLogs')->latest()->get();
        return view('admin.gates.index', compact('gates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gate_name' => 'required|string|max:100',
            'location'  => 'required|string|max:255',
        ]);

        GatePoint::create($validated);

        return redirect()->route('admin.gates.index')->with('success', 'Gate point added successfully.');
    }

    public function update(Request $request, GatePoint $gate)
    {
        $validated = $request->validate([
            'gate_name' => 'required|string|max:100',
            'location'  => 'required|string|max:255',
            'status'    => 'required|in:active,inactive',
        ]);

        $gate->update($validated);

        return redirect()->route('admin.gates.index')->with('success', 'Gate point updated successfully.');
    }

    public function destroy(GatePoint $gate)
    {
        $gate->delete();
        return redirect()->route('admin.gates.index')->with('success', 'Gate point deleted successfully.');
    }
}
