<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RolesPermissionController extends Controller
{
    public function index()
    {
        $routeList = get_route_list();
        $roles = Role::where('id','!=','2')->get();
        return view('backend.pages.roles-permission.index', compact('roles', 'routeList'));
    }

    public function create()
    {
        $routeList = get_route_list();
        return view('backend.pages.roles-permission.create', compact('routeList'));
    }

    public function store(Request $request)
    {
        // return response()->json($request->all());
        $routeList = get_route_list();
        $permissions = $request->permission ?? [];
        foreach ($permissions as $key => $value) {
            if (array_key_exists($key, $routeList)) {
                foreach ($routeList[$key] as $k => $route) {
                    if (in_array($k, $value)) {
                        $routeList[$key][$k] = true;
                    } else {
                        $routeList[$key][$k] = false;
                    }
                }
            }
        }
        $userRole = new Role();
        $userRole->name = $request->name;
        $userRole->slug = slugify($request->name);
        $userRole->permission = json_encode($routeList);
        $userRole->save();

        session()->flash('success', __('Role created successfully'));
        return redirect('roles-permission');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $routeList = get_route_list();

        if ($role->permission == null) {
            $role->permission = json_encode($routeList);
            $role->save();
        }

        // $routeList = json_decode($role->permission, true);

        foreach ($routeList as $key => $value) {
            foreach ($value as $k => $v) {
                if (array_key_exists($key, json_decode($role->permission, true))) {
                    if (array_key_exists($k, json_decode($role->permission, true)[$key])) {
                        $routeList[$key][$k] = json_decode($role->permission, true)[$key][$k];
                    }
                } else {
                    $routeList[$key][$k] = false;
                }
            }
        }
        return view('backend.pages.roles-permission.edit', compact('role', 'routeList'));
    }

    public function update(Request $request, $id)
    {
        $routeList = get_route_list();
        $list = $request->permission;
        if ($list == null) {
            $list = $routeList;
        }
        foreach ($list as $key => $value) {
            if (array_key_exists($key, $routeList)) {
                foreach ($routeList[$key] as $k => $route) {
                    if (in_array($k, $value)) {
                        $routeList[$key][$k] = true;
                    } else {
                        $routeList[$key][$k] = false;
                    }
                }
            }
        }
        $userRole = Role::findOrFail($id);
        $userRole->name = $request->name;
        $userRole->slug = slugify($request->name);
        $userRole->permission = json_encode($routeList);
        $userRole->save();

        session()->flash('success', __('Role updated successfully'));
        return redirect('roles-permission');
    }
}
