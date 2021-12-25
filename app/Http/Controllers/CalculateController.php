<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// CO2E Calculate
class CalculateController extends Controller
{
    private const CO2_FACTOR = 8.78;
    private const N2O_FACTOR = 0.000081;
    private const CH4_FACTOR = 0.00038925;
    private const FUEL_EFFICIENCY = 36.21024; // 22.5 mil = 36.21024 km

    public function index(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'year' => ['required','integer'],
            'facility_number' => ['required','integer'],
            'activity_type' => ['required','string','max:255'],
            'distance_activity' => ['required','numeric'],
            'activity_amount' => ['required','numeric'],
            'fuel_source' => ['required','string','max:255'],
            'vehicle_type' => ['required','string','max:255'],
        ]);

        if($validator->fails()) {
            return response([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $greenhouseGas =  $this->calculateGreenhouseGas($request->get('activity_amount'));

        return [
            'status' => true,
            'greenHouseGasAmount' => $greenhouseGas,
            'unit' => 'ton'
        ];
    }

    private function calculateGreenhouseGas($activity_amount): float
    {
        $CO2 = self::CO2_FACTOR * 0.001 * $activity_amount / self::FUEL_EFFICIENCY;
        $CH4 = self::CH4_FACTOR * 0.001 * $activity_amount / self::FUEL_EFFICIENCY;
        $N2O = self::N2O_FACTOR * 0.001 * $activity_amount / self::FUEL_EFFICIENCY;

        $CO2E = ($CO2 * 1) + ($CH4 * 28) + ($N2O * 265);

        return (float) number_format($CO2E, 3);
    }
}
