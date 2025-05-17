<?php

namespace App\Http\Controllers\Dash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //daftar permissions
        $permissions = Permission::all();

        return response()->json($permissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        //simpan permission
        $permission = Permission::create(['name' => $request->name, 'guard_name' => 'web']);

        return response()->json($permission);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $permission = Permission::findByName($id, 'web');

        return response()->json($permission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $permission = Permission::findByName($id, 'web');
        $permission->name = $request->name;
        $permission->save();

        return response()->json($permission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //remove permission by name
        $permission = Permission::findByName($id, 'web');
        $permission->delete();
    }
}
