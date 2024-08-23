<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Sensor;
use App\Mail\AlertsMail;
use App\Models\Notification;
use App\Models\SensorRecord;
use Illuminate\Http\Request;
use App\Events\NewRecordSend;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use App\Models\TypeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreSensorRequest;
use App\Http\Requests\DeleteSensorRequest;
use App\Http\Requests\UpdateSensorRequest;
use App\Http\Requests\ActifSensorBySiteIdRequest;
use App\Http\Requests\ListSensorsBySiteIdRequest;
use App\Http\Requests\FindLastSensorRecordBySiteIdRequest;

class SensorController extends Controller
{
    /**
     * gets list of all sensors
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = Sensor::with(['site'])->get();
        if (isset($data)) {
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
    public function findListSensorsBySiteId(ListSensorsBySiteIdRequest $request): JsonResponse
    {
        $data = $request->validated();
        $listSensors = Sensor::where("site_id", $data["site_id"])->get();
        if (isset($listSensors)) {
            return $this->success([
                "list_sensors" => $listSensors
            ], "List sensors fetched successfully");
        }
        return $this->error("Error while getting list sensors");
    }

    /**
     * store a new sensor
     *
     * @param StoreSensorRequest $request
     * @return JsonResponse
     */
    public function store(StoreSensorRequest $request): JsonResponse
    {
        $data = $request->validated();
        $sensor = Sensor::create($data);
        if (isset($sensor)) {
            return $this->success([
                "sensor" => $sensor
            ], "Sensor created successfully");
        }
        return $this->error("Error while creating Sensor");
    }

    /**
     * update specific sensor
     *
     * @param UpdateSensorRequest $request
     * @return JsonResponse
     */
    public function update(UpdateSensorRequest $request): JsonResponse
    {
        $data = $request->validated();
        $sensor = Sensor::where("sensor_reference", $data["sensor_reference"])
            ->where("id", $data['sensor_id'])
            ->first();
        if (!isset($sensor)) {
            $findExistRef = $sensor = Sensor::where("sensor_reference", $data["sensor_reference"])
                ->first();
            if (isset($findExistRef)) {
                return $this->error("Error sensor reference already exists");
            } else {
                $sensor = Sensor::find($data['sensor_id']);
                if (isset($sensor)) {
                    $dataUpdate = array_diff_key($data, array_flip(["sensor_id"]));
                    $status = $sensor->update($dataUpdate);
                    if (isset($status) && $status == true) {
                        return $this->success([
                            "sensor" => $sensor,
                            "action_status" => $status
                        ], "Sensor updated successfully");
                    }
                    return $this->error("Error while updating sensor");
                }
                return $this->error("Error while trying to get sensor database for updating");
            }
        } else {
            $dataUpdate = array_diff_key($data, array_flip(["sensor_id"]));
            $status = $sensor->update($dataUpdate);
            if (isset($status) && $status == true) {
                return $this->success([
                    "sensor" => $sensor,
                    "action_status" => $status
                ], "Sensor updated successfully");
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
    public function delete(DeleteSensorRequest $request): JsonResponse
    {
        $data = $request->validated();
        $sensor = Sensor::find($data['sensor_id']);
        if (isset($sensor)) {
            $sensorStatus = $sensor->delete();
            if (isset($sensorStatus) && $sensorStatus == true) {
                return $this->success([
                    "action_status" => $sensorStatus,
                ], "Sensor deleted successfully");
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
    public function findListSensorsWithLastRecord(FindLastSensorRecordBySiteIdRequest $request): JsonResponse
    {
        $data = $request->validated();
        $listSensors = Sensor::where("site_id", $data["site_id"])
            ->with(['sensorRecords' => function ($query) {
                $query->orderBy('created_at', 'desc')
                    ->limit(1)->get();
            }])
            ->get();
        if (isset($listSensors) && count($listSensors) > 0) {
            return $this->success([
                "data" => $listSensors
            ], "List sensors with last record fetched successfully");
        } else {
            if (count($listSensors) <= 0) {
                return $this->error("Error while fetching list sensors: No sensors assigned to that site");
            }
            return $this->error("Error while fetching list sensors with last record");
        }
    }



    static public function findListSensorsInside(int $siteId)
    {

        $listSensors = Sensor::where("site_id", $siteId)
            ->with(['sensorRecords' => function ($query) {
                $query->orderBy('created_at', 'desc')
                    ->limit(1)->get();
            }])->get();
        return $listSensors ?? [];
    }

    /**
     * find actif sensors for the current day
     *
     * @param ActifSensorBySiteIdRequest $request
     * @return JsonResponse
     */
    public function findActifSensors(ActifSensorBySiteIdRequest $request): JsonResponse
    {
        $data = $request->validated();
        $site = Site::find($data["site_id"]);
        $listActifSensors = [];
        $notifications = [];
        $listLowBatSensors = [];
        $listSensorsWithRecords = [];
        if (isset($site)) {
            $dateStart = Carbon::now()->startOfDay()->subHours($site->gmt);
            $dateEnd = Carbon::now()->endOfDay();
            $listSensors = Sensor::where("site_id", $data["site_id"])
                ->get();
            $listSensorsWithLastRecords = Sensor::where("site_id", $data["site_id"])
                ->with(['sensorRecords' => function ($query) use ($dateStart, $dateEnd) {
                    $query->whereBetween("created_at", [$dateStart, $dateEnd])
                        ->orderBy('created_at', 'desc')
                        ->limit(1)->get();
                }], $dateStart, $dateEnd)
                ->get();
            $listSensorsWithRecordsRaw = Sensor::where("site_id", $data["site_id"])
                ->with(['sensorRecords' => function ($query) use ($dateStart, $dateEnd) {
                    $query->orderBy('created_at', 'desc')
                        ->limit(1)->get();
                }], $dateStart, $dateEnd)
                ->get();
            if (isset($listSensors) && isset($listSensorsWithLastRecords)) {
                for ($i = 0; $i < count($listSensorsWithLastRecords); $i++) {
                    if (
                        isset($listSensorsWithLastRecords[$i]->sensorRecords) &&
                        count($listSensorsWithLastRecords[$i]->sensorRecords) > 0
                    ) {
                        array_push($listActifSensors, $listSensorsWithLastRecords[$i]);

                        $batteryVoltage = (int) $listSensorsWithLastRecords[$i]->sensorRecords[0]["battery"];
                        $batteryPercentage = ($batteryVoltage / 3.7) * 100;


                        if ($batteryPercentage <= 20) {
                            array_push($listLowBatSensors, $listSensorsWithLastRecords[$i]);
                            if (count($listLowBatSensors) > 0) {



                                $type_notification = TypeNotification::where('code', 'bat-fb')->first();


                                $notification  = Notification::create([
                                    'batteryPercent' => $batteryPercentage,
                                    'sensor_reference' => $listSensorsWithLastRecords[$i]->sensor_reference,
                                    'typeNotification_id' => $type_notification->id,
                                    'sensor_id' => $listSensorsWithLastRecords[$i]->id,
                                    'description' => $type_notification->wording,


                                ]);
                                array_push($notifications, $notification);

                                Mail::to('merveillenitcheu12@gmail.com')
                                    ->send(new AlertsMail($notifications));
                            }
                        }
                    }
                }
                for ($i = 0; $i < count($listSensorsWithRecordsRaw); $i++) {
                    if (
                        isset($listSensorsWithRecordsRaw[$i]->sensorRecords) &&
                        count($listSensorsWithRecordsRaw[$i]->sensorRecords) > 0
                    ) {
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
                ], "Actif sensors fetched successfully");
            }
            return $this->error("Error when getting list sensors and last records");
        }
        return $this->error("Error when getting site data");
    }

    public function addNotification(Request $request): JsonResponse
    {
        $type_notification = TypeNotification::where('code', 'hs-pe')->first();
        $sensors = $request->all();
        $notifications = [];
        foreach ($sensors as $key => $sensor) {
            $notification = Notification::create([
                'batteryPercent' => $sensor['sensor_records'][0]['battery'],
                'sensor_reference' => $sensor['sensor_reference'],
                'typeNotification_id' => $type_notification->id,
                'sensor_id' => $sensor['sensor_records'][0]['sensor_id'],
                'description' => $type_notification->wording,


            ]);
            array_push($notifications, $notification);
        }


        Mail::to('merveillenitcheu12@gmail.com')
            ->send(new AlertsMail($notifications));
        return $this->success([
            'data' => $notifications
        ], "Actif sensors fetched successfully");
    }

    public function getNotification(): JsonResponse
    {
        $notifications = Notification::select('sensor_reference', 'batteryPercent', 'created_at', 'description')
            ->orderByDesc('created_at')
            ->get()
            ->unique('sensor_reference', 'typeNotification_id');


        return $this->success([
            'notifications' => $notifications
        ], "Notifications fetched successfully");
    }
}
