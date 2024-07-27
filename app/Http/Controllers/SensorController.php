<?php

namespace App\Http\Controllers;

use App\Events\NewRecordSend;
use App\Http\Requests\ActifSensorBySiteIdRequest;
use App\Http\Requests\DeleteSensorRequest;
use App\Http\Requests\FindLastSensorRecordBySiteIdRequest;
use App\Http\Requests\ListSensorsBySiteIdRequest;
use App\Http\Requests\StoreSensorRequest;
use App\Http\Requests\UpdateSensorRequest;
use App\Models\Sensor;
use App\Models\SensorRecord;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SensorController extends Controller
{
    /**
     * gets list of all sensors
     *
     * @return JsonResponse
     */
    public function index():JsonResponse{
        $data = Sensor::with(['site'])->get();
        if(isset($data)){
            return $this->success([
                "list_sensors" => $data,
            ], "List sensors fetched successfully");
        }
        return $this->error("Error while fetching list sensors");
    }

    /**
     * gets list sensors by site id
     *
     * @param ListSensorsBySiteRequest $request
     * @return JsonResponse
     */
    public function findListSensorsBySiteId(ListSensorsBySiteIdRequest $request): JsonResponse{
        $data = $request->validated();
        $listSensors = Sensor::where("site_id", $data["site_id"])->get();
        if(isset($listSensors)){
            return $this->success([
                "list_sensors" => $listSensors
            ],"List sensors fetched successfully");
        }
        return $this->error("Error while getting list sensors");
    }

    /**
     * store a new sensor
     *
     * @param StoreSensorRequest $request
     * @return JsonResponse
     */
    public function store(StoreSensorRequest $request):JsonResponse {
        $data = $request->validated();
        $sensor = Sensor::create($data);
        if(isset($sensor)){
            return $this->success([
                "sensor" => $sensor
            ],"Sensor created successfully");
        }
        return $this->error("Error while creating Sensor");
    }

    /**
     * update specific sensor
     *
     * @param UpdateSensorRequest $request
     * @return JsonResponse
     */
    public function update(UpdateSensorRequest $request):JsonResponse{
        $data = $request->validated();
        $sensor = Sensor::where("sensor_reference", $data["sensor_reference"])
                        ->where("id", $data['sensor_id'])
                        ->first();
        if(!isset($sensor)){
            $findExistRef = $sensor = Sensor::where("sensor_reference", $data["sensor_reference"])
                                            ->first();
            if(isset($findExistRef)){
                return $this->error("Error sensor reference already exists");
            }else{
                $sensor = Sensor::find($data['sensor_id']);
                if(isset($sensor)){
                    $dataUpdate = array_diff_key($data, array_flip(["sensor_id"]));
                    $status = $sensor->update($dataUpdate);
                    if(isset($status) && $status == true){
                        return $this->success([
                            "sensor" => $sensor,
                            "action_status" => $status
                        ],"Sensor updated successfully");
                    }
                    return $this->error("Error while updating sensor");
                }
                return $this->error("Error while trying to get sensor database for updating");
            }

        }else{
            $dataUpdate = array_diff_key($data, array_flip(["sensor_id"]));
            $status = $sensor->update($dataUpdate);
            if(isset($status) && $status == true){
                return $this->success([
                    "sensor" => $sensor,
                    "action_status" => $status
                ],"Sensor updated successfully");
            }
            return $this->error("Error while updating sensor");
        }
    }

    /**
     * delete one sensor
     *
     * @param DeleteSensorRequest
     * @return JsonResponse
     */
    public function delete(DeleteSensorRequest $request):JsonResponse{
        $data = $request->validated();
        $sensor = Sensor::find($data['sensor_id']);
        if(isset($sensor)){
            $sensorStatus = $sensor->delete();
            if(isset($sensorStatus) && $sensorStatus == true){
                return $this->success([
                    "action_status" => $sensorStatus,
                ],"Sensor deleted successfully");
            }
            return $this->error("Error while deleting sensor");
        }
        return $this->error("Error while getting sensor for delete");
    }

    /**
     * find last record for list sensors of a site
     *
     * @param UserSiteRequest $request
     * @return JsonResponse
     */
    Public function findListSensorsWithLastRecord(FindLastSensorRecordBySiteIdRequest $request):JsonResponse{
        $data = $request->validated();
        $listSensors = Sensor::where("site_id", $data["site_id"])
                            ->with(['sensorRecords' => function ($query) {
                                $query->orderBy('created_at','desc')
                                    ->limit(1)->get();
                            }])
                            ->get();
        if(isset($listSensors) && count($listSensors)>0){
            return $this->success([
                "data" => $listSensors
            ],"List sensors with last record fetched successfully");
        }else{
            if(count($listSensors)<=0){
                return $this->error("Error while fetching list sensors: No sensors assigned to that site");
            }
            return $this->error("Error while fetching list sensors with last record");
        }
    }



    static public function findListSensorsInside(int $siteId){

        $listSensors = Sensor::where("site_id", $siteId)
                            ->with(['sensorRecords' => function ($query) {
                                $query->orderBy('created_at','desc')
                                    ->limit(1)->get();
                            }])->get();
        return $listSensors??[];
    }

    /**
     * find actif sensors for the current day
     *
     * @param ActifSensorBySiteIdRequest $request
     * @return JsonResponse
     */
    public function findActifSensors(ActifSensorBySiteIdRequest $request): JsonResponse{
        $data = $request->validated();
        $site = Site::find($data["site_id"]);
        $listActifSensors = [];
        $listLowBatSensors = [];
        $listSensorsWithRecords = [];
        if(isset($site)){
            $dateStart = Carbon::now()->startOfDay()->subHours($site->gmt);
            $dateEnd = Carbon::now()->endOfDay();
            $listSensors = Sensor::where("site_id", $data["site_id"])
                                ->get();
            $listSensorsWithLastRecords= Sensor::where("site_id", $data["site_id"])
                                ->with(['sensorRecords' => function ($query) use ($dateStart, $dateEnd) {
                                    $query->whereBetween("created_at", [$dateStart, $dateEnd])
                                        ->orderBy('created_at','desc')
                                        ->limit(1)->get();
                                }], $dateStart, $dateEnd)
                                ->get();
            $listSensorsWithRecordsRaw= Sensor::where("site_id", $data["site_id"])
                                ->with(['sensorRecords' => function ($query) use ($dateStart, $dateEnd) {
                                    $query->orderBy('created_at','desc')
                                        ->limit(1)->get();
                                }], $dateStart, $dateEnd)
                                ->get();
            if(isset($listSensors) && isset($listSensorsWithLastRecords)){
                for($i=0; $i<count($listSensorsWithLastRecords); $i++){
                    if(isset($listSensorsWithLastRecords[$i]->sensorRecords) &&
                    count($listSensorsWithLastRecords[$i]->sensorRecords)>0){
                        array_push($listActifSensors, $listSensorsWithLastRecords[$i]);
                        if($listSensorsWithLastRecords[$i]->sensorRecords[0]["battery"]<=20){
                            array_push($listLowBatSensors, $listSensorsWithLastRecords[$i]);
                        }
                    }
                }
                for($i=0; $i<count($listSensorsWithRecordsRaw); $i++){
                    if(isset($listSensorsWithRecordsRaw[$i]->sensorRecords) &&
                    count($listSensorsWithRecordsRaw[$i]->sensorRecords)>0){
                        array_push($listSensorsWithRecords, $listSensorsWithRecordsRaw[$i]);

                    }
                }
                return $this->success([
                    "list_sensors" => $listSensors,
                    "list_actif_sensors" => $listActifSensors,
                    "list_low_bat_sensors" => $listLowBatSensors,
                    "list_sensors_with_last_records" => $listSensorsWithLastRecords,
                    "list_sensors_on_map" => $listSensorsWithRecords,
                    "site" => $site
                ],"Actif sensors fetched successfully");
            }
            return $this->error("Error when getting list sensors and last records");
        }
        return $this->error("Error when getting site data");
    }


}
