<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Application;
use App\Models\CashBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // dd($request->all());

        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'roles' => 'required',

        ]);

        if ($validated->fails()) {
            return response([
                'message' => $validated->errors(),
                'status' => 'error',
            ], 200);
        }
        DB::beginTransaction();
        try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),

            ]);

            $user->assignRole($request->roles);

            $token = $user->createToken($request->email)->plainTextToken;
            DB::commit();
            return response([
                'token' => $token,
                'message' => 'Registration Success',
                'status' => 'success',
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }

    public function login(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validated->fails()) {
            return response([
                'message' => $validated->errors(),
                'status' => 'error',
            ], 200);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $user->roles = $user->roles;


            $user->branch = $user->branch;

            $rolePermissions = $user->roles->flatMap(function ($role) {
                return $role->permissions;
            });

            $directPermissions = $user->permissions; // Direct permissions on the user

            $allPermissions = $rolePermissions->merge($directPermissions)->unique('id')->values();

            $user->all_permissions = $allPermissions;
            $token = $user->createToken($request->email)->plainTextToken;
            return response([
                'user' => $user,
                'token' => $token,
                'message' => 'Login Success',
                'status' => 'success',
            ], 200);
        }
        return response([
            'message' => 'The Provided Credentials are incorrect',
            'status' => 'failed',
        ], 401);
    }

    public function login_user(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validated->fails()) {
            return response([
                'message' => $validated->errors(),
                'status' => 'error',
            ], 200);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $user->roles = $user->roles;


            $user->branch = $user->branch;

            $rolePermissions = $user->roles->flatMap(function ($role) {
                return $role->permissions;
            });

            $directPermissions = $user->permissions; // Direct permissions on the user

            $allPermissions = $rolePermissions->merge($directPermissions)->unique('id')->values();

            $user->all_permissions = $allPermissions;
            $token = $user->createToken($request->email)->plainTextToken;

            $application = Application::where('email', $request->email)->first();
            $application->load(['children', 'payments']);
            return response([
                'user_info' => $user,
                'applicant_info' => $application,
                'token' => $token,
                'message' => 'Login Success',
                'status' => 'success',
            ], 200);
        }
        return response([
            'message' => 'The Provided Credentials are incorrect',
            'status' => 'failed',
        ], 401);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Logout Success',
            'status' => 'success',
        ], 200);
    }

    public function logged_user()
    {

        try {
            $role = null;
            $permissions = null;
            $loggeduser = auth()->user();
            $extraPermission = collect(auth()->user()->permissions)->pluck(
                "name"
            );

            if (isset(auth()->user()->roles[0])) {
                $roleID = auth()->user()->roles[0]->id;
                $role = Role::find($roleID);

                $rolePermissions = Permission::join(
                    "role_has_permissions",
                    "role_has_permissions.permission_id",
                    "=",
                    "permissions.id"
                )
                    ->whereIn(
                        "role_has_permissions.role_id",
                        auth()
                            ->user()
                            ->roles->pluck("id")
                    )
                    ->get();

                $permissionsList = collect($rolePermissions)->pluck("name");
                $permissionsList = $permissionsList
                    ->merge($extraPermission)
                    ->unique();
            }
            return response(
                [
                    "user" => $loggeduser,
                    "Permissions List" => $permissionsList,
                    "message" => "Logged User Data",
                    "status" => "success",
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

    public function change_password(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);
        $loggeduser = auth()->user();
        $loggeduser->password = Hash::make($request->password);
        $loggeduser->update();
        return response([
            'message' => 'Password Changed Successfully',
            'status' => 'success',
        ], 200);
    }

    public function role_list()
    {
        $roles = Role::pluck('name', 'id')->all();
        return response([
            'roles' => $roles,
            'message' => 'All Role List',
            'status' => 'success',
        ], 200);
    }

    public function role_wise_user()
    {
        $data = DB::table('users as user')
            ->select('user.id as user_id', 'user.email', 'user.name', 'r.name as role_name')
            ->join('model_has_roles as mhr', 'user.id', '=', 'mhr.model_id')
            ->join('roles as r', 'mhr.role_id', '=', 'r.id')
            ->get();

        $roles = collect($data)->groupBy('role_name');

        return response([
            'roles' => $roles,
            'message' => 'Role Wise User List',
            'status' => 'success',
        ], 200);
    }

    public function permission_list()
    {
        $permission = Permission::select('name', 'id')->get();
        return response([
            'permission' => $permission,
            'message' => 'All Permission List',
            'status' => 'success',
        ], 200);
    }

    public function user_list(Request $request)
    {
        $search = $request->input('search');
        $limit  = (int) $request->input('limit', 10);

        $userList = User::when($search, function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->paginate($limit);

        $userList->getCollection()->transform(function ($data) {
            $data->roles = $data->roles;
            $data->permissions = $data->permissions;

            $rolePermissions = $data->roles->flatMap(function ($role) {
                return $role->permissions;
            });

            $directPermissions = $data->permissions;

            $allPermissions = $rolePermissions->merge($directPermissions)->unique('id')->values();

            $data->all_permissions = $allPermissions;

            return $data;
        });

        return response([
            'users'   => $userList,
            'message' => 'User list fetched successfully',
            'status'  => 'success'
        ], 200);
    }


    public function all_user_list()
    {
        $userList = User::get();

        return response([
            'users' => $userList,
            'message' => 'All User List',
            'status' => 'success'
        ], 200);
    }

    public function user_edit(String $id)
    {
        try {
            $userList = User::find($id);
            if ($userList) {
                $userList->roles;
                $userList->permissions;


                $rolePermissions = $userList->roles->flatMap(function ($role) {
                    return $role->permissions;
                });

                $directPermissions = $userList->permissions;

                $allPermissions = $rolePermissions->merge($directPermissions)->unique('id')->values();

                $userList->all_permissions = $allPermissions;

                return response([
                    'user_info' => $userList,
                    'message' => 'User Edit Info',
                    'status' => 'success',
                ], 200);
            }
            return response([
                'message' => 'User ID Not Exist!',
                'status' => 'success',
            ], 200);
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

    public function user_update(Request $request, String $id)
    {

        $validated = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email|unique:users,email," . $id,
        ]);

        if ($validated->fails()) {
            return response(
                [
                    "message" => $validated->errors(),
                    "status" => "error",
                ],
                403
            );
        }

        DB::beginTransaction();
        try {
            $user = User::find($id);
            if ($user) {
                $user->update([
                    "name" => $request->name,
                    "email" => $request->email,
                    "status" => $request->status,
                    "branch_id" => $request->branch_id,
                ]);

                $user->syncRoles($request->roles);
                DB::commit();
                return response(
                    [
                        "user" => $user,
                        "message" => "User Update successfully",
                        "status" => "success",
                    ],
                    201
                );
            }
            return response(
                [
                    "message" => "User Not Found",
                    "status" => "error",
                ],
                403
            );
        } catch (\Exception $e) {
            DB::rollback();
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }

    public function assign_permission(Request $request, $id)
    {
        // $validated = Validator::make($request->all(), [
        //     "permissions" => "required",
        // ]);

        // if ($validated->fails()) {
        //     return response(
        //         [
        //             "message" => $validated->errors(),
        //             "status" => "error",
        //         ],
        //         403
        //     );
        // }

        DB::beginTransaction();
        try {
            $user = User::find($id);
            if ($user) {
                $user->syncPermissions([]);
                $user->givePermissionTo($request->permissions);

                DB::commit();
                return response(
                    [
                        "permissions" => $request->permissions,
                        "message" => "Assign Permission Successfully",
                        "status" => "success",
                    ],
                    201
                );
            }
            return response(
                [
                    "message" => "User Not Found!",
                    "status" => "success",
                ],
                201
            );
        } catch (\Exception $e) {
            DB::rollback();
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }

    public function show(string $id)
    {

        try {
            $result = User::find($id);
            $data['result'] = $result;
            $data['status'] = 'success';
            $data['message'] = "Data Get Successfully";

            return response()->json($data, 200);
        } catch (\Exception $e) {
            $data = [];
            $data['status'] = 'error';
            $data['message'] = $e->getMessage();
            return response()->json($data, 400);
        }
    }

    public function user_delete(String $id)
    {

        try {
            $user = User::find($id);
            if ($user) {
                $user->delete();
                return response([
                    'message' => 'User deleted successfully',
                    'status' => 'Success',
                ], 200);
            }
            return response([
                'message' => 'User Not Found!',
                'status' => 'Success',
            ], 200);
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
