<?php

namespace App\Http\Controllers;

use App\Http\Libraries\JWT\JWTUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TreesDetails;
use App\Models\TreesVariables;

class VolumeController extends Controller
{
    private $jwtUtils;
    public function __construct()
    {
        $this->jwtUtils = new JWTUtils();
    }


    //! [GET] /volume/tree-info
    public function getVerify(Request $request)
    {
        try {
            // $header = $request->header('Authorization');
            // $jwt = $this->jwtUtils->verifyToken($header);
            // if (!$jwt->state) {
            //     return response()->json([
            //         "status" => "error",
            //         "message" => "Unauthorized",
            //         "data" => [],
            //     ], 401);
            // }

            \date_default_timezone_set('Asia/Bangkok');
            $timestamp = new \DateTime();

            $trees = TreesDetails::all();

            $verifiedCount = $trees->where('is_verify', true)->count();
            $notVerifiedCount = $trees->where('is_verify', false)->count();


            return response()->json([
                "status" => 'success',
                "message" => "Successfully fetched tree verification data",
                "data" => [
                    [
                        "type" => "verified",
                        "count" => $verifiedCount
                    ],
                    [
                        "type" => "not_verified",
                        "count" => $notVerifiedCount
                    ]
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }

    //! [GET]/volume/count-by-trees_name
    public function getCountByTreesName(Request $request)
    {
        try {
            // $header = $request->header('Authorization');
            // $jwt = $this->jwtUtils->verifyToken($header);
            // if (!$jwt->state) {
            //     return response()->json([
            //         "status" => "error",
            //         "message" => "Unauthorized",
            //         "data" => [],
            //     ], 401);
            // }

            $trees = TreesDetails::all();
            $treesNameCounts = $trees->groupBy('trees_name')->map(function ($item) {
                return $item->count();
            });

            $formattedData = [];
            foreach ($treesNameCounts as $name => $count) {
                $formattedData[$name] = $count;
            }

            return response()->json([
                "status" => 'success',
                "message" => "Successfully fetched tree name data",
                "data" => [$formattedData],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }

    //! [GET] /volume/count-by-trees_year
    function getCountByYear(Request $request)
    {
        try {
            $volumeSequence = [];
            $currentYear = 2022;
            $totalVolume = 0;

            for ($i = 0; $i < 11; $i++) {
                $year = $currentYear + $i;
                $treesCount = TreesDetails::whereYear('plant_date', '=', $year)
                    ->count('trees_code');

                $totalVolume += $treesCount;

                $volumeSequence[] = [
                    "year" => $year,
                    "volume" => $treesCount,
                    "volume_increase" => $totalVolume
                ];
            }

            $result = [
                "volume_sequence" => $volumeSequence
            ];

            return response()->json([
                "status" => 'success',
                "message" => "Get of trees_year successfully",
                "data" => [$result],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }
}
