<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\SensorRecord;
use Illuminate\Http\Request;
use App\Events\NewRecordSend;
use App\Models\SensorPayload;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreSensorRecordRequest;
use App\Http\Requests\DeleteSensorRecordRequest;
use App\Http\Requests\FindLastSensorRecordBySiteIdRequest;
use App\Http\Requests\FindBySensorAndPeriodSensorRecordRequest;

class SensorRecordController extends Controller
{
    /**
     * store new sensor record
     *
     * @param StoreSensorRecordRequest $request
     * @return JsonResponse
     */
    public function storeSensorRecord(StoreSensorRecordRequest $request):JsonResponse {
        $data = $request->validated();
        $record = SensorRecord::create($data);
        if(isset($record)){
            //TODO mise a jour plateforme
            $record->load(['sensor.site']);
            $listSensors = SensorController::findListSensorsInside($record->sensor->site_id);
            $this->sendNewRecordToMap($record, $listSensors);
            return $this->success([
                "sensor_record" => $record,
            ],"Sensor record stored successfully");
        }
        return $this->error("Error while storing sensor record");
    }


    public function storePayloadRecord(Request $request):JsonResponse {

      $payloadData = $request->all();
    //   // Extract the device ID
    //   $deviceId = $payloadData['identifiers'][0]['device_ids']['device_id'];

    //   $temperature = $payloadData['data']['uplink_message']['decoded_payload']['ic_temperature'];

    // $temperature_numerique = str_replace("°C", "", $temperature);
    // $temperature_numerique = intval($temperature_numerique);
    Log::info('Payload reçu :', ['data' => $payloadData]);

      $record = SensorPayload::create([
          'device_id' => 'true',

      ]);




          return $this->success([
              "sensor_record" => $payloadData,
          ], "Sensor record stored successfully");

  }

    /**
     * send the new record to the map
     *
     * @param SensorRecord $sensorRecord
     */
    private function sendNewRecordToMap( $sensorRecord, $data){
        broadcast(New NewRecordSend($sensorRecord, $data));
    }

    public function updateSensorRecord(){

    }

    /**
     * get list sensor records by sensor id and period
     *
     * @param FindBySensorAndPeriodSensorRecordRequest $request
     * @return JsonResponse
     */
    public function findListRecordBySensorIdAndPeriod(FindBySensorAndPeriodSensorRecordRequest $request):JsonResponse {
        $data = $request->validated();
        $sensor = Sensor::with(['site'])->find($data['sensor_id']);
        if(isset($sensor)){
            $dateStart = Carbon::parse($data["date_start"])->subHours($sensor->site->gmt);
            $dateEnd = Carbon::parse($data["date_end"])->subHours($sensor->site->gmt);
            $listSensorRecords = SensorRecord::where("sensor_id", $data['sensor_id'])
                                                ->whereBetween("created_at", [$dateStart, $dateEnd])
                                                ->orderBy("created_at", "desc")
                                                ->with(["sensor"])
                                                ->get();
            if(isset($listSensorRecords)){
                return $this->success([
                    "list_records" => $listSensorRecords,
                ],"List sensor records fetched successfully");
            }
            return $this->error("Error while fetching list sensor records");
        }
        return $this->error("Error while getting sensor for fetching sensor records");
    }

    /**
     * Deletes specific sensor record
     *
     * @param DeleteSensorRecordRequest $request
     * @return JsonResponse
     */
    public function deleteSensorRecord(DeleteSensorRecordRequest $request):JsonResponse{
        $data = $request->validated();
        $sensorRecord = SensorRecord::find($data["sensor_record_id"]);
        if(isset($sensorRecord)){
            $status = $sensorRecord->delete();
            if($status == true){
                return $this->success([
                    "action_status" => $status,
                    "sensor_record" => $sensorRecord
                ],"Sensor record deleted successfully");
            }
            return $this->error("Error while trying to deleting sensor record");
        }
        return $this->error("Error while trying to get the sensor record for deleting");
    }



}
