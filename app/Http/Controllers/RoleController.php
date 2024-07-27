<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignRoleToUserRequest;
use App\Models\Site;
use App\Models\User;
use App\Models\UserSite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * get list users for auth user
     *
     * @return JsonResponse
     */
    public function getListRoleForAuthUser():JsonResponse{
        $user = User::find(Auth()->user()->id);
        $listRoles = null;
        if($user->hasRole(['Responsable de site'])){
            $excludedRoles = ['Super-Admin', 'Responsable general']; // Remplace par les rôles que tu veux exclure
            $listRoles = Role::whereNotIn('name', $excludedRoles)
                            ->get();
        }else if($user->hasRole(['Responsable general'])){
            $excludedRoles = ['Super-Admin']; // Remplace par les rôles que tu veux exclure
            $listRoles = Role::whereNotIn('name', $excludedRoles)
                            ->get();
        }else{
            $listRoles = Role::all();
        }
        if(isset($listRoles)){
            return $this->success([
                'list_roles' => $listRoles
            ], "List roles fetched successfully");
        }else{
            return $this->error("Error while fetching list roles");
        }
    }

    /**
     * assigns role to one user
     *
     * @param AssignRoleToUserRequest
     * @return JsonResponse
     */
    public function assignRoleToUser(AssignRoleToUserRequest $request): JsonResponse{
        $data = $request->validated();
        $role = Role::find($data["role_id"]);
        $user = User::where("id", $data["user_id"])->with(['roles'])->first();
        $userSite = null;
        if(isset($role) && isset($user)){
            if(count($user->roles) > 0){
                $user->syncRoles([]);
            }
            $statusRole = $user->assignRole($role);
            if($role->name == "Responsable de site"){
                $site = Site::find($data["site_id"]);
                $userSite = UserSite::where("user_id", $user->id)->first();
                if(isset($userSite)){
                    $userSite->update([
                        "user_id" => $user->id,
                        "site_id" => $site->id
                    ]);
                }else{
                    $userSite = UserSite::create([
                        "user_id" => $user->id,
                        "site_id" => $site->id
                    ]);
                }
                if(isset($userSite)){
                    return $this->success([
                        'user_site' => $userSite,
                        "role_status" => $statusRole
                    ], "User site assigned successfully");
                }else{
                    return $this->error("Error while creating user site");
                }
            }else{
                $userSite = UserSite::where("user_id", $user->id)->first();
                if(isset($userSite)){
                    $userSite->delete();
                }
                return $this->success([
                    "role_status" => $statusRole
                ], "Role assigned to user successfully");
            }
        }
        return $this->error("Error while getting user and role");
    }
}
