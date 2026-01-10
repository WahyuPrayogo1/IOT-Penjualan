<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DeviceController extends Controller
{
    // INDEX
    public function index()
    {
        $devices = Device::orderBy('device_id')->get();
        return view('backend.devices.index', compact('devices'));
    }
    
    // CREATE
    public function create()
    {
        return view('backend.devices.form', ['isEdit' => false]);
    }
    
    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|unique:devices,device_id',
            'status' => 'required|in:active,inactive',
        ]);
        
        Device::create([
            'device_id' => $request->device_id,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('devices.index')
                         ->with('success', 'Device created successfully!');
    }
    
    // EDIT
    public function edit($id)
    {
        $device = Device::findOrFail($id);
        return view('backend.devices.form', ['isEdit' => true, 'device' => $device]);
    }
    
    // UPDATE
    public function update(Request $request, $id)
    {
        $device = Device::findOrFail($id);
        
        $request->validate([
            'device_id' => 'required|unique:devices,device_id,' . $id,
            'status' => 'required|in:active,inactive',
        ]);
        
        $device->update([
            'device_id' => $request->device_id,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('devices.index')
                         ->with('success', 'Device updated successfully!');
    }
    
    // DESTROY
    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();
        
        return redirect()->route('devices.index')
                         ->with('success', 'Device deleted successfully!');
    }
    
    // BULK GENERATE QR CODES
    public function generateQRCodes()
    {
        $devices = Device::where('status', 'active')->get();
        
        return view('backend.devices.qrcodes-bulk', compact('devices'));
    }
    
    // PRINT ALL QR CODES
    public function printAllQRCodes()
    {
        $devices = Device::where('status', 'active')->get();
        
        return view('backend.devices.qrcodes-print', compact('devices'));
    }
}