<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\RegisteredUser;
use App\Models\QrCode as QrCodeModel;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\VehicleQrMail;
use Illuminate\Support\Facades\Mail;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('registeredUser', 'activeQrCode')->latest()->paginate(10);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $registeredUsers = RegisteredUser::orderBy('first_name')->get();
        return view('admin.vehicles.create', compact('registeredUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'registered_user_id' => 'required|exists:registered_users,id',
            'plate_number'       => 'required|string|unique:vehicles,plate_number',
            'vehicle_brand'      => 'required|string|max:100',
            'vehicle_model'      => 'required|string|max:100',
            'vehicle_color'      => 'required|string|max:50',
            'vehicle_type'       => 'required|string|max:50',
            'expiry_date'        => 'nullable|date|after:today',
        ]);

        $vehicle = Vehicle::create([
            'registered_user_id' => $validated['registered_user_id'],
            'plate_number'       => $validated['plate_number'],
            'vehicle_brand'      => $validated['vehicle_brand'],
            'vehicle_model'      => $validated['vehicle_model'],
            'vehicle_color'      => $validated['vehicle_color'],
            'vehicle_type'       => $validated['vehicle_type'],
            'registration_date'  => now(),
        ]);

        $expiry = $validated['expiry_date'] ?? null;
        QrCodeModel::issueFor($vehicle, $expiry ? \Carbon\Carbon::parse($expiry) : null);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle registered and QR code issued successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load('registeredUser', 'qrCodes');
        return view('admin.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $registeredUsers = RegisteredUser::orderBy('first_name')->get();
        return view('admin.vehicles.edit', compact('vehicle', 'registeredUsers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'registered_user_id' => 'required|exists:registered_users,id',
            'plate_number'       => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'vehicle_brand'      => 'required|string|max:100',
            'vehicle_model'      => 'required|string|max:100',
            'vehicle_color'      => 'required|string|max:50',
            'vehicle_type'       => 'required|string|max:50',
        ]);

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle deleted successfully.');
    }

    /**
     * Revoke the current QR code and issue a fresh one.
     * Implements spec requirement v: "deactivate or suspend QR codes...
     * in cases of loss, theft, or revocation."
     */
    public function reissueQr(Vehicle $vehicle)
    {
        $vehicle->activeQrCode?->update(['status' => 'revoked']);
        QrCodeModel::issueFor($vehicle);

        return redirect()->route('admin.vehicles.show', $vehicle)
            ->with('success', 'Previous QR code revoked. A new QR code has been issued.');
    }

    public function emailQr(Vehicle $vehicle)
    {
        $vehicle->load('registeredUser', 'activeQrCode');

        abort_if(!$vehicle->activeQrCode, 404, 'No active QR code to send.');

        Mail::to($vehicle->registeredUser->email)->send(new VehicleQrMail($vehicle));

        return redirect()->route('admin.vehicles.show', $vehicle)
            ->with('success', 'QR code emailed to ' . $vehicle->registeredUser->email . '.');
    }

    public function downloadQr(Vehicle $vehicle)
    {
        $qr = $vehicle->activeQrCode;
        abort_if(!$qr, 404, 'No active QR code for this vehicle.');

        $qrImage = QrCode::format('svg')->size(400)->margin(1)->generate($qr->encrypted_payload);

        return response($qrImage)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="vehicle-' . $vehicle->plate_number . '-qr.svg"');
    }

    public function printQr(Vehicle $vehicle)
    {
        $vehicle->load('registeredUser');
        return view('admin.vehicles.print-qr', compact('vehicle'));
    }
}
