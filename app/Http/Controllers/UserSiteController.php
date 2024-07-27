<?php

namespace App\Http\Controllers;

use App\Http\Requests\FindUserSiteByUserIdRequest;
use App\Http\Requests\UserSiteRequest;
use App\Models\UserSite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserSiteController extends Controller
{

    /**
     * assigns one site to a user
     *
     * @param UserSiteRequest $request
     * @return JsonResponse
     */
    public function storeUserSite(UserSiteRequest $request):JsonResponse {
        $data = $request->validated();
        $userSite = UserSite::where("user_id", $data["user_id"])
                            ->first();
        if(!isset($userSite)){
            $userSite = UserSite::create($data);
            if(isset($userSite)){
                return $this->success([
                    "user_site" => $userSite
                ],"user site created successfully");
            }
            return $this->error("Error while creating user site");
        }
        return $this->error("user already has an assigned site in database");
    }


    /**
     * gets user site by user id
     *
     * @param UserSiteRequest $request
     * @return JsonResponse
     */
    public function getUserSiteByUserId(FindUserSiteByUserIdRequest $request):JsonResponse {
        $data = $request->validated();
        $userSite = UserSite::where("user_id", $data['user_id'])
                                ->with(['user', 'site'])
                                ->first();
        if(isset($userSite) && $userSite != null){
            return $this->success([
                "user_site" => $userSite
            ],"User site fetched successfully");
        }else{
            return $this->error("Erropr while getting user site");
        }
    }

    
}
