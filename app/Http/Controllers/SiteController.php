<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteSiteRequest;
use App\Http\Requests\FindListSiteByRoleNameRequest;
use App\Http\Requests\ListSiteByUserIdRequest;
use App\Http\Requests\StoreSiteRequest;
use App\Http\Requests\UpdateSiteRequest;
use App\Models\Configuration;
use App\Models\Site;
use App\Models\User;
use App\Models\UserSite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    /**
     * store a new site
     *
     * @param StoreSiteRequest $request
     * @return JsonResponse
     */
    public function store(StoreSiteRequest $request): JsonResponse
    {
        $data = $request->validated();
        $site = Site::create($data);
        if (isset($site)) {
            return $this->success([
                "site" => $site
            ], "Site created successfully");
        }
        return $this->error("Error while creating site");
    }

    /**
     * updates  specific site
     *
     * @param UpdateSiteRequest $request
     * @return JsonResponse
     */
    public function update(UpdateSiteRequest $request): JsonResponse
    {
        $data = $request->validated();
        $site = Site::find($data["site_id"]);
        $nbsite = $request->nbsubsite;

        if (isset($site)) {
            $dataUpdate = array_diff_key($data, array_flip(["site_id"]));
            $status = $site->update($dataUpdate);
            for ($i = 0; $i < $nbsite; $i++) {
                $site = Site::create([
                  'name' => 'sous-site'.$i,
                  'description' => $data["description"],
                  'adresse' => $request->address,
                  'gmt' => $data["gmt"],
                  'longitude' => $data["longitude"],
                  'latitude' => $data["latitude"],
                  'radius' => $data["radius"],
                  'compagny_id' => $data["compagny_id"],
                  'site_id' => $data["site_id"],
                ]);
            }
            if (isset($status) && $status == true) {
                return $this->success([
                    "action_status" => $status,
                    "site" => $site,
                ], "Site updated successfully");
            }
            $this->error("Error while updating site");
        }
        $this->error("Error while getting site for update");
    }


    /**
     * delete  specific site
     *
     * @param DeleteSiteRequest $request
     * @return JsonResponse
     */
    public function delete(DeleteSiteRequest $request): JsonResponse
    {
        $data = $request->validated();
        $site = Site::find($data["site_id"]);
        if (isset($site)) {
            $status = $site->delete();
            if (isset($status) && $status == true) {
                return $this->success([
                    "action_status" => $status,
                    "site" => $site,
                ], "Site deleted successfully");
            }
            $this->error("Error while deleting site");
        }
        $this->error("Error while getting site for delete");
    }


    /**
     * gets list site
     *
     * @return JsonResponse
     */
    public function getListSitesForUser(): JsonResponse
    {
        $user = User::find(Auth()->user()->id);
        $companyId = $user->compagny_id;

        if ($user->hasRole(['Super-Admin', 'Responsable general'])) {
            $listSites = Site::where("compagny_id", $companyId)
                ->with([
                    'sensors.sensorRecords' => function ($query) {
                        $query->orderBy('created_at', 'desc')
                            ->limit(1)->get();
                    },
                    'userSite.user'
                ])->where('site_id', null)->get();
            if (isset($listSites) && count($listSites) > 0) {
                return $this->success([
                    "list_sites" => $listSites,
                ], "User site fetched successfully");
            } else {
                return $this->error("Error while getting list sites for user");
            }
        } else {
            $userSite = UserSite::where("user_id", $user->id)->first();
            if (isset($userSite)) {
                $listSites = Site::where("id", $userSite->site_id)
                    ->with([
                        'sensors.sensorRecords' => function ($query) {
                            $query->orderBy('created_at', 'desc')
                                ->limit(1)->get();
                        },
                        'userSite.user'
                    ])->where('site_id', null)->get();

                if (isset($listSites) && count($listSites) > 0) {
                    return $this->success([
                        "list_sites" => $listSites,
                    ], "User site fetched successfully");
                } else {
                    return $this->error("Error while getting list sites for user");
                }
            } else {
                return $this->error("Error while getting user site for list sites for the current user");
            }
        }
    }

    /**
     * gets list site by user id
     *
     * @return JsonResponse
     */
    public function getListSitesByUserId(ListSiteByUserIdRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::find($data['user_id']);
        $companyId = $user->compagny_id;

        if ($user->hasRole(['Super-Admin', 'Responsable general'])) {
            $listSites = Site::where("compagny_id", $companyId)
                ->with([
                    'sensors.sensorRecords' => function ($query) {
                        $query->orderBy('created_at', 'desc')
                            ->limit(1)->get();
                    },
                    'userSite.user'
                ])->where('site_id', null)->get();
            if (isset($listSites) && count($listSites) > 0) {
                return $this->success([
                    "list_sites" => $listSites,
                ], "User site fetched successfully");
            } else {
                return $this->error("Error while getting list sites for user");
            }
        } else {
            $userSite = UserSite::where("user_id", $user->id)->first();
            if (isset($userSite)) {
                $listSites = Site::where("id", $userSite->site_id)
                    ->with([
                        'sensors.sensorRecords' => function ($query) {
                            $query->orderBy('created_at', 'desc')
                                ->limit(1)->get();
                        },
                        'userSite.user'
                    ])->where('site_id', null)->get();

                if (isset($listSites) && count($listSites) > 0) {
                    return $this->success([
                        "list_sites" => $listSites,
                    ], "User site fetched successfully");
                } else {
                    return $this->error("Error while getting list sites for user");
                }
            } else {
                return $this->error("Error while getting user site for list sites for the current user");
            }
        }
    }

    /**
     * gets list sites by role name
     *
     * @param FindListSiteByRoleNameRequest
     * @return JsonResponse
     */
    public function getListSitesByRoleName(FindListSiteByRoleNameRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::find(Auth()->user()->id);
        $companyId = $user->compagny_id;
        if ($data['role_name'] == 'Responsable de site') {
            $listSites = Site::where('compagny_id', $companyId)->where('site_id', null)->get();
        } else {
            $listSites = [];
        }
        if (isset($listSites)) {
            return $this->success([
                "list_sites" => $listSites
            ], "List sites fetched successfully");
        }
        return $this->error("Error while trying to get list sites for role name " . $data["role_name"]);
    }

    public function addTrakerColor(Request $request): JsonResponse
    {

        $data = $request->all();

        $config = Configuration::create([
            'color' => $data['color'],
            'site_id' => $data['site']['id'],
            'trakerType' => $data['trakerType']['label'],
            // 'zoom' => $sensor['sensor_records'][0]['sensor_id'],

        ]);

        if (isset($config)) {
            return $this->success([
                "config" => $config
            ], "config add successfully");
        }
        return $this->error("Error while trying to add tracker color for a site ");
    }

    public function getTrakerColorBySiteId($siteId): JsonResponse
    {

        $site = Site::findOrFail($siteId);

        $config = Configuration::where('site_id', $site->id)->get();

        if (isset($config)) {
            return $this->success([
                "config" => $config
            ], "config fetched successfully");
        }
        return $this->error("Error while trying to add tracker color for a site ");
    }


}
