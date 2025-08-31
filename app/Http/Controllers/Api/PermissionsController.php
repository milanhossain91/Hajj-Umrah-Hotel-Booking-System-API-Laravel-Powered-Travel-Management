<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $permissions = Permission::all();

        return response([
            'permissions' => $permissions,
            'message' => 'All Permission List',
            'status'=>'Success'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return response([
            'message' => 'Create',
            'status'=>'Success'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        $permission = Permission::create(['name' => $request->get('name'), 'guard_name' => 'web']);

            return response([
                'permission' =>  $permission,
                'message' => 'Permission created successfully',
                'status'=>'Success'
            ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(String $id)
    {
        //
        return response(
            [
                "permission" => Permission::find($id),
                "message" => "Permission Individual Edit",
                "status" => "Success",
            ],
            200
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        //
        $validated = Validator::make($request->all(), [
            "name" => "required|unique:permissions,name," . $id,
        ]);

        if ($validated->fails()) {
            return response(
                [
                    "message" => $validated->errors(),
                    "status" => "error",
                ],
                200
            );
        }

        try {
            $permission = Permission::find($id);
            if($permission){

                $permission->update($request->only("name"));

                return response(
                    [
                        "permission" => $permission,
                        "message" => "Permission updated successfully",
                        "status" => "Success",
                    ],
                    200
                );
            }
            return response(
                [
                    "message" => "Permission Not Found",
                    "status" => "Success",
                ],
                200
            );
        } catch (\Exception $e) {
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $permission = Permission::find($id);
            if($permission){

                $permission->delete();

                return response(
                    [
                        "message" => "Permission deleted successfully",
                        "status" => "Success",
                    ],
                    200
                );
            }
            return response(
                [
                    "message" => "Permission Not Found",
                    "status" => "Success",
                ],
                200
            );
        } catch (\Exception $e) {
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }

    }
}
