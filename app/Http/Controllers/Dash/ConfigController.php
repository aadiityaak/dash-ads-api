<?php

namespace App\Http\Controllers\Dash;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Setting;

class ConfigController extends Controller
{
    private function getConfig($request)
    {

        $results = [
            'year'              => date('Y'),
            'app_name'          => Setting::get('app_name'),
            'app_description'   => Setting::get('app_description'),
            'app_logo'          => '',
            'app_logo_small'    => '',
            'app_favicon'       => '',
            'app_menus'         => ''
        ];

        $app_logo = Setting::get('app_logo');
        if ($app_logo) {
            $results['app_logo'] = asset('storage/' . $app_logo);
        }
        $app_logo_small = Setting::get('app_logo_small');
        if ($app_logo_small) {
            $results['app_logo_small'] = asset('storage/' . $app_logo_small);
        }
        $app_favicon = Setting::get('app_favicon');
        if ($app_favicon) {
            $results['app_favicon'] = asset('storage/' . $app_favicon);
        }

        //bg welcome
        $bg_welcome = Setting::get('bg_welcome');
        if ($bg_welcome) {
            $results['bg_welcome'] = asset('storage/' . $bg_welcome);
        } else {
            $results['bg_welcome'] = asset('assets/images/bg-welcome.webp');
        }

        //data user login
        $results['user'] = $request->user();

        //jika user login
        if ($request->user()) {
            // Dapatkan semua permissions
            $permissons = $request->user()->getPermissionsViaRoles();

            //collection permissions
            $results['permissions'] = collect($permissons)->pluck('name');

            //get user role
            $role = $request->user()->roles()->first();
            $role = $role ? $role->name : null;
            $results['role'] = $role;

            //get menus by role
            $path = resource_path("menus/{$role}.json");
            if (file_exists($path)) {
                $results['app_menus'] = json_decode(file_get_contents($path));
            } else {
                $path = resource_path("menus/user.json");
                $results['app_menus'] = json_decode(file_get_contents($path));
            }
        }

        return $results;
    }

    public function index(Request $request)
    {
        $results = $this->getConfig($request);
        return response()->json($results);
    }

    public function setconfig(Request $request)
    {

        // 🔐 Cek apakah user punya permission 'edit-settings'
        $user = auth()->user();
        if (! $user->can('edit-settings')) {
            return response()->json([
                'message' => 'You do not have permission.',
            ], 422);
        }

        $request->validate([
            'app_name'          => 'required',
            'app_description'   => 'required',
            'app_logo'          => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:1048',
            'app_logo_small'    => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:1048',
            'app_favicon'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg,ico|max:1048',
            'bg_welcome'        => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
        ]);

        //save setting
        Setting::set('app_name', $request->app_name);
        Setting::set('app_description', $request->app_description);

        //simpan logo
        if ($request->hasFile('app_logo')) {

            //hapus logo lama
            if (Setting::get('app_logo')) {
                Storage::disk('public')->delete(Setting::get('app_logo'));
            }

            $file = $request->file('app_logo');
            $path = $file->store('app', 'public');
            Setting::set('app_logo', $path);
        }

        //simpan logo small
        if ($request->hasFile('app_logo_small')) {

            //hapus logo lama
            if (Setting::get('app_logo_small')) {
                Storage::disk('public')->delete(Setting::get('app_logo_small'));
            }

            $file = $request->file('app_logo_small');
            $path = $file->store('app', 'public');
            Setting::set('app_logo_small', $path);
        }

        //simpan favicon
        if ($request->hasFile('app_favicon')) {

            //hapus logo lama
            if (Setting::get('app_favicon')) {
                Storage::disk('public')->delete(Setting::get('app_favicon'));
            }

            $file = $request->file('app_favicon');
            $path = $file->store('app', 'public');
            Setting::set('app_favicon', $path);
        }

        //simpan bg welcome
        if ($request->hasFile('bg_welcome')) {

            //hapus logo lama
            if (Setting::get('bg_welcome')) {
                Storage::disk('public')->delete(Setting::get('bg_welcome'));
            }

            $file = $request->file('bg_welcome');
            $path = $file->store('app', 'public');
            Setting::set('bg_welcome', $path);
        }

        $results = $this->getConfig($request);
        return response()->json($results);
    }
}
