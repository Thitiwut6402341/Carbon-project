<?php

namespace App\Http\Controllers;

use App\Http\Libraries\JWT\JWTUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Models\TreesDetails;
use App\Models\TreesVariables;
use App\Models\vw_cc_zone;
use App\Models\vw_cc_zone_all_parameter;

class CalculationController extends Controller
{
    private $jwtUtils;
    public function __construct()
    {
        $this->jwtUtils = new JWTUtils();
    }


    //* [GET] /calculation/sum-carbon
    function calculationCTT(Request $request)
    {
        try {
            // $header = $request->header('Authorization');
            // $jwt = $this->jwtUtils->verifyToken($header);
            // if (!$jwt->state) return response()->json([
            //     "status" => "error",
            //     "message" => "Unauthorized",
            //     "data" => [],
            // ], 401);

            //! Caching
            $cacheKey = "/iccs/calculation/ctt/";
            $cachedTime = '1 minutes';
            $cached = Cache::get($cacheKey);
            if (!is_null($cached)) return response()->json([
                "status" => "success",
                "message" => "Data from cached " . $cachedTime,
                "data" => [json_decode($cached)],
            ]);
            //! ./Caching



            $allDataCalTT0 = [];
            //! Query tree by is_verify = true and status != inavailable
            $check = TreesDetails::select(
                "trees_code",
                "is_verify",
                "status",
                "zone",
                "type",
                "circumference",
                "height",
            )->get(); //->where('trees_id', 'ff73de91-f7b0-48ce-9fcb-502ea2d132b5')

            foreach ($check as $value) {
                if ($value->is_verify == true) {
                    if ($value->status != "unavailable") {
                        if ($value->type == "1") {
                            $result = TreesVariables::select(
                                "tree_s_1",
                                "tree_s_2",
                                "tree_s_3",
                                "tree_b_1",
                                "tree_b_2",
                                "tree_b_3",
                                "tree_l_1",
                                "tree_l_2",
                                "tree_l_3",
                                "cf_tree",
                                "r_tree",
                                "area_a",
                                "area_aa",
                            )->get();

                            foreach ($result as $info) {
                                $tree_s_1 = (float)$info['tree_s_1'];
                                $tree_s_2 = (float)$info['tree_s_2'];
                                $tree_s_3 = (float)$info['tree_s_3'];

                                $tree_b_1 = (float)$info['tree_b_1'];
                                $tree_b_2 = (float)$info['tree_b_2'];
                                $tree_b_3 = (float)$info['tree_b_3'];

                                $tree_l_1 = (float)$info['tree_l_1'];
                                $tree_l_2 = (float)$info['tree_l_2'];
                                $tree_l_3 = (float)$info['tree_l_3'];
                                $cf_tree = (float)$info['cf_tree'];
                                $r_tree = (float)$info['r_tree'];
                                $area_a = (float)$info['area_a'];
                                $area_aa = (float)$info['area_aa'];
                            }

                            $calWS =  $tree_s_1 * pow((pow($value->circumference, $tree_s_2) * $value->height), $tree_s_3);
                            $calWB = $tree_b_1 * pow((pow($value->circumference, $tree_b_2) * $value->height), $tree_b_3);
                            $calWL = pow(($tree_l_1 / ($calWS + $calWB + $tree_l_2)), $tree_l_3);
                            $calWT = $calWS + $calWB + $calWL;
                            $calMJ = $calWT / 1000;
                            $calABG = ($calMJ * $cf_tree * (44 / 12)) * ($area_a / $area_aa);
                            $calBLG = $calABG * $r_tree;
                            $calTT0 = $calABG + $calBLG;
                            array_push($allDataCalTT0, $calTT0);
                        } else if ($value->type == "2") {
                            $result = TreesVariables::select(
                                "sapling_s_1",
                                "sapling_s_2",
                                "sapling_s_3",
                                "sapling_b_1",
                                "sapling_b_2",
                                "sapling_b_3",
                                "sapling_l_1",
                                "sapling_l_2",
                                "sapling_l_3",
                                "cf_sapling",
                                "r_sapling",
                                "area_a",
                                "area_aa",
                            )->get();

                            foreach ($result as $info) {
                                $sapling_s_1 = (float)$info['sapling_s_1'];
                                $sapling_s_2 = (float)$info['sapling_s_2'];
                                $sapling_s_3 = (float)$info['sapling_s_3'];

                                $sapling_b_1 = (float)$info['sapling_b_1'];
                                $sapling_b_2 = (float)$info['sapling_b_2'];
                                $sapling_b_3 = (float)$info['sapling_b_3'];

                                $sapling_l_1 = (float)$info['sapling_l_1'];
                                $sapling_l_2 = (float)$info['sapling_l_2'];
                                $sapling_l_3 = (float)$info['sapling_l_3'];
                                $cf_sapling = (float)$info['cf_sapling'];
                                $r_sapling = (float)$info['r_sapling'];
                                $area_a = (float)$info['area_a'];
                                $area_aa = (float)$info['area_aa'];
                            }

                            $calWS =  $sapling_s_1 * pow((pow($value->circumference, $sapling_s_2) * $value->height), $sapling_s_3);
                            $calWB = $sapling_b_1 * pow((pow($value->circumference, $sapling_b_2) * $value->height), $sapling_b_3);
                            $calWL = $sapling_l_1 * pow((pow($value->circumference, $sapling_l_2) * $value->height), $sapling_l_3);
                            $calWT = $calWS + $calWB + $calWL;

                            $calMJ = $calWT / 1000;
                            $calABG = ($calMJ * $cf_sapling * (44 / 12)) * ($area_a / $area_aa);;
                            $calBLG = $calABG * $r_sapling;

                            $calTT0 = $calABG + $calBLG;
                            array_push($allDataCalTT0, $calTT0);
                        } else if ($value->type == "3") {
                            $result = TreesVariables::select(
                                "palm_t_1",
                                "palm_t_2",
                                "palm_t_3",
                                "cf_palm",
                                "r_palm",
                                "area_a",
                                "area_aa",
                            )->get();

                            foreach ($result as $info) {
                                $palm_t_1 = (float)$info['palm_t_1'];
                                $palm_t_2 = (float)$info['palm_t_2'];
                                $palm_t_3 = (float)$info['palm_t_3'];
                                $cf_palm = (float)$info['cf_palm'];
                                $r_palm = (float)$info['r_palm'];
                                $area_a = (float)$info['area_a'];
                                $area_aa = (float)$info['area_aa'];
                            }

                            $l = 2.3026 * log($value->height, 10);
                            $b = pow($value->height, $palm_t_3);
                            $calWT = $palm_t_1 + ($palm_t_2 * $l) * $b;
                            $calMJ = $calWT / 1000;
                            $calABG = ($calMJ * $cf_palm * (44 / 12)) * ($area_a / $area_aa);;
                            $calBLG = $calABG * $r_palm;
                            $calTT0 = $calABG + $calBLG;
                            array_push($allDataCalTT0, $calTT0);
                        }
                    }
                }
            }

            $sumTT0 = array_sum($allDataCalTT0);

            $dataSequent = [];
            $t = 0.26;
            $A = 74.615;
            $c = 0.95;
            // $increase = ($t * $A * $c);
            function calculateCarbon($index, $sumTT0, $t, $A, $c)
            {
                $increase = ($t * $A * $c * $index);
                $calSQ = ($sumTT0 + $increase);
                $carbonDiff = $calSQ - $sumTT0;

                if ($index == 0) {
                    return [
                        "year"  => 2022 + $index,
                        "carbon" => $sumTT0,
                        "carbon_increase" => $sumTT0,
                        "carbon_diff" => 0
                    ];
                } else {
                    return [
                        "year"  => 2022 + $index,
                        "carbon" => $sumTT0,
                        "carbon_increase" => $calSQ,
                        "carbon_diff" => $carbonDiff
                    ];
                }
            }

            for ($i = 0; $i < 11; $i++) {
                $list = calculateCarbon($i, $sumTT0, $t, $A, $c);
                array_push($dataSequent, $list);
            }

            $result = [
                "sum_TT0" => $sumTT0,
                "carbon_sequence" => $dataSequent
            ];

            //! Caching
            Cache::put($cacheKey, \json_encode($result, JSON_UNESCAPED_UNICODE), \DateInterval::createFromDateString($cachedTime));
            //! ./Caching

            return response()->json([
                "status" => 'success',
                "message" => "Get of Carbon calculate tree successfully",
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


    //* [GET] /calculation/all-tree
    function calculationAllTreeInfo(Request $request)
    {
        try {
            // $header = $request->header('Authorization');
            // $jwt = $this->jwtUtils->verifyToken($header);
            // if (!$jwt->state) return response()->json([
            //     "status" => "error",
            //     "message" => "Unauthorized",
            //     "data" => [],
            // ], 401);

            // return response()->json($queryData);


            //! Caching
            $cacheKey = "/iccs/calculation/all_trees_ctt/";
            $cachedTime = '1 minutes';
            $cached = Cache::get($cacheKey);
            if (!is_null($cached)) return response()->json([
                "status" => "success",
                "message" => "Data from cached " . $cachedTime,
                "data" => json_decode($cached),
            ]);
            //! ./Caching


            $dataCarbonPerDay = [];

            // //! Query data
            $queryData = vw_cc_zone_all_parameter::select(
                "trees_id",
                "trees_code",
                "trees_name",
                "scientific_name",
                "plant_date",
                "trees_age",
                "latitude",
                "longitude",
                "status",
                "is_verify",
                "old_zone",
                "image",
                "zone",
                "type",
                "circumference",
                "height",
                "botanical_characteristics",
                "care",
                "trees_family",
                "benefits",
                "reference",
                "generals",
                "scientific_name",
                // "tree_s_1",
                // "tree_s_2",
                // "tree_s_3",
                // "tree_b_1",
                // "tree_b_2",
                // "tree_b_3",
                // "tree_l_1",
                // "tree_l_2",
                // "tree_l_3",
                // "sapling_s_1",
                // "sapling_s_2",
                // "sapling_s_3",
                // "sapling_b_1",
                // "sapling_b_2",
                // "sapling_b_3",
                // "sapling_l_1",
                // "sapling_l_2",
                // "sapling_l_3",
                // "palm_t_1",
                // "palm_t_2",
                // "palm_t_3",
                // "cf_tree",
                // "cf_sapling",
                // "cf_palm",
                // "r_tree",
                // "r_sapling",
                // "r_palm",
                // "area_a",
                // "area_b",
                // "area_c",
                // "area_d",
                // "area_park",
                // "area_water",
                // "area_aa",
                // "area_bb",
                // "area_cc",
                // "area_dd",
                // "area_pp",
                // "area_ww",
                // "rate",
                // "percentage",
                "ws",
                "wb",
                "wl",
                "wt",
                "c_c",
                "abg",
                "blg",
                "c_zone",
            )->get();


            foreach ($queryData as $value) {
                $value->botanical_characteristics = json_decode($value->botanical_characteristics);
                $value["carbon_per_day"] = ($value['c_zone'] * 1000) / 365;
            }

            $modifiedData = $queryData->map(function ($item) {
                $item['carbon'] = $item['c_zone'];
                unset($item['c_zone']);
                return $item;
            });

            //! Caching
            Cache::put($cacheKey, \json_encode($queryData, JSON_UNESCAPED_UNICODE), \DateInterval::createFromDateString($cachedTime));
            //! ./Caching


            return response()->json([
                "status" => 'success',
                "message" => "Get infomation of tree infomation successfully",
                "data" => $queryData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }

    //* [POST] /calculation/credit
    function calculationCredit(Request $request)
    {
        try {
            // $header = $request->header('Authorization');
            // $jwt = $this->jwtUtils->verifyToken($header);
            // if (!$jwt->state) return response()->json([
            //     "status" => "error",
            //     "message" => "Unauthorized",
            //     "data" => [],
            // ], 401);

            // //! Caching
            // $cacheKey = "/iccs/calculation/credit/";
            // $cachedTime = '1 minutes';
            // $cached = Cache::get($cacheKey);
            // if (!is_null($cached)) return response()->json([
            //     "status" => "success",
            //     "message" => "Data from cached " . $cachedTime,
            //     "data" => [json_decode($cached)],
            // ]);
            // //! ./Caching

            $rules =        [
                'circumference'         => 'required | numeric',
                'height'                => 'required | numeric',
                'type'                  => 'required | numeric ',
            ];


            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    "status" => "error",
                    "message" => "Bad request",
                    "data" => [
                        [
                            "validator" => $validator->errors()
                        ]
                    ]
                ], 400);
            }

            $circumference = $request->circumference;
            $height = $request->height;
            $type = $request->type;

            $allDataCalCredit = [];

            $testParameter = [];

            // //! Query data
            if ($type == "1") {
                $result = TreesVariables::select(
                    "tree_s_1",
                    "tree_s_2",
                    "tree_s_3",
                    "tree_b_1",
                    "tree_b_2",
                    "tree_b_3",
                    "tree_l_1",
                    "tree_l_2",
                    "tree_l_3",
                    "cf_tree",
                    "r_tree",
                    "area_a",
                    "area_aa",
                )->get();

                foreach ($result as $info) {
                    $tree_s_1 = (float)$info['tree_s_1'];
                    $tree_s_2 = (float)$info['tree_s_2'];
                    $tree_s_3 = (float)$info['tree_s_3'];

                    $tree_b_1 = (float)$info['tree_b_1'];
                    $tree_b_2 = (float)$info['tree_b_2'];
                    $tree_b_3 = (float)$info['tree_b_3'];

                    $tree_l_1 = (float)$info['tree_l_1'];
                    $tree_l_2 = (float)$info['tree_l_2'];
                    $tree_l_3 = (float)$info['tree_l_3'];
                    $cf_tree = (float)$info['cf_tree'];
                    $r_tree = (float)$info['r_tree'];
                    $area_a = (float)$info['area_a'];
                    $area_aa = (float)$info['area_aa'];
                }

                $calWS =  $tree_s_1 * pow((pow($circumference, $tree_s_2) * $height), $tree_s_3);
                $calWB = $tree_b_1 * pow((pow($circumference, $tree_b_2) * $height), $tree_b_3);
                $calWL = pow(($tree_l_1 / ($calWS + $calWB + $tree_l_2)), $tree_l_3);
                $calWT = $calWS + $calWB + $calWL;
                $calMJ = $calWT / 1000;
                $calABG = ($calMJ * $cf_tree * (44 / 12)) * ($area_a / $area_aa);
                $calBLG = $calABG * $r_tree;
                $calTT0 = $calABG + $calBLG;
                array_push($allDataCalCredit, $calTT0);
                array_push($testParameter, $calWS, $calWB, $calWL, $calWT, $calMJ, $calABG, $calBLG, $calTT0);
            } else if ($type == "2") {
                $result = TreesVariables::select(
                    "sapling_s_1",
                    "sapling_s_2",
                    "sapling_s_3",
                    "sapling_b_1",
                    "sapling_b_2",
                    "sapling_b_3",
                    "sapling_l_1",
                    "sapling_l_2",
                    "sapling_l_3",
                    "cf_sapling",
                    "r_sapling",
                    "area_a",
                    "area_aa",
                )->get();

                foreach ($result as $info) {
                    $sapling_s_1 = (float)$info['sapling_s_1'];
                    $sapling_s_2 = (float)$info['sapling_s_2'];
                    $sapling_s_3 = (float)$info['sapling_s_3'];

                    $sapling_b_1 = (float)$info['sapling_b_1'];
                    $sapling_b_2 = (float)$info['sapling_b_2'];
                    $sapling_b_3 = (float)$info['sapling_b_3'];

                    $sapling_l_1 = (float)$info['sapling_l_1'];
                    $sapling_l_2 = (float)$info['sapling_l_2'];
                    $sapling_l_3 = (float)$info['sapling_l_3'];
                    $cf_sapling = (float)$info['cf_sapling'];
                    $r_sapling = (float)$info['r_sapling'];
                    $area_a = (float)$info['area_a'];
                    $area_aa = (float)$info['area_aa'];
                }

                $calWS =  $sapling_s_1 * pow((pow($circumference, $sapling_s_2) * $height), $sapling_s_3);
                $calWB = $sapling_b_1 * pow((pow($circumference, $sapling_b_2) * $height), $sapling_b_3);
                $calWL = $sapling_l_1 * pow((pow($circumference, $sapling_l_2) * $height), $sapling_l_3);
                $calWT = $calWS + $calWB + $calWL;

                $calMJ = $calWT / 1000;
                $calABG = ($calMJ * $cf_sapling * (44 / 12)) * ($area_a / $area_aa);;
                $calBLG = $calABG * $r_sapling;

                $calTT0 = $calABG + $calBLG;
                array_push($allDataCalCredit, $calTT0);
                array_push($testParameter, $calWS, $calWB, $calWL, $calWT, $calMJ, $calABG, $calBLG, $calTT0);
            } else if ($type == "3") {
                $result = TreesVariables::select(
                    "palm_t_1",
                    "palm_t_2",
                    "palm_t_3",
                    "cf_palm",
                    "r_palm",
                    "area_a",
                    "area_aa",
                )->get();

                foreach ($result as $info) {
                    $palm_t_1 = (float)$info['palm_t_1'];
                    $palm_t_2 = (float)$info['palm_t_2'];
                    $palm_t_3 = (float)$info['palm_t_3'];
                    $cf_palm = (float)$info['cf_palm'];
                    $r_palm = (float)$info['r_palm'];
                    $area_a = (float)$info['area_a'];
                    $area_aa = (float)$info['area_aa'];
                }

                $l = 2.3026 * log($height, 10);
                $b = pow($height, $palm_t_3);
                $calWT = $palm_t_1 + ($palm_t_2 * $l) * $b;
                $calMJ = $calWT / 1000;
                $calABG = ($calMJ * $cf_palm * (44 / 12)) * ($area_a / $area_aa);;
                $calBLG = $calABG * $r_palm;
                $calTT0 = $calABG + $calBLG;
                array_push($allDataCalCredit, $calTT0);
                array_push($testParameter,  $calWT, $calMJ, $calABG, $calBLG, $calTT0);
            }

            $responseData = [
                "circumference" => $circumference,
                "height" => $height,
                "type" => $type,
                "carbon_credit" => $allDataCalCredit[0],

            ];

            return response()->json([
                "status" => 'success',
                "message" => "Calculate carbon credit successfully",
                "data" => [$responseData]
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
