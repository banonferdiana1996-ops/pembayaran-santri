<?php

namespace App\Http\Controllers;

use App\Support\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('setting.index');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_institusi' => ['required', 'string', 'max:120'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:2048'],
            'wa_enabled' => ['sometimes', 'boolean'],
            'wa_api_url' => ['nullable', 'url'],
            'wa_api_token' => ['nullable', 'string'],
        ]);

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                Setting::set($key, $value);
            }
        }

        foreach (['logo', 'favicon'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $name = $field.'-'.time().'.'.$file->extension();
                $file->move(public_path('uploads/settings'), $name);
                Setting::set($field, '/uploads/settings/'.$name);
            }
        }

        Setting::set('wa_enabled', $request->boolean('wa_enabled') ? '1' : '0');
        Setting::set('wa_api_url', $request->input('wa_api_url') ?: 'https://api.fonnte.com/send');
        Setting::set('wa_api_token', $request->input('wa_api_token', ''));

        Setting::flush();

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
