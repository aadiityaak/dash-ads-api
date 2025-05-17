<?php

namespace App\Http\Controllers\Dash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class OptionsController extends Controller
{

    public function get(string $key)
    {
        $result = [];

        switch ($key) {
            case 'roles':
                $result = $this->roles();
                break;
            case 'permissions':
                $result = $this->permissions();
                break;
            default:
                $result = [];
        }

        return response()->json($result);
    }
    private function roles()
    {
        //get roles
        $roles = Role::all();

        //convert to array
        $result = [];
        foreach ($roles as $role) {
            $result[] = [
                'value' => $role->name,
                'label' => $role->name
            ];
        }

        return $result;
    }

    private function permissions()
    {
        //get all permissions
        $permissions = Permission::all();

        //convert to array
        $result = [];
        foreach ($permissions as $permission) {
            $result[] = [
                'value' => $permission->name,
                'label' => $permission->name
            ];
        }

        return $result;
    }
}
