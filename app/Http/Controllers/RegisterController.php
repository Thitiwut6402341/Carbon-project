<?php

namespace App\Http\Controllers;

use App\Http\Libraries\JWT\JWTUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\TreesDetails;
use App\Models\TreesVariables;
use App\Models\LogDataTransaction;

class RegisterController extends Controller
{
    private $jwtUtils;
    public function __construct()
    {
        $this->jwtUtils = new JWTUtils();
    }

    //* [POST] /register/new-tree
    function addTree(Request $request)
    {
        try {
            $header = $request->header('Authorization');
            $jwt = $this->jwtUtils->verifyToken($header);
            if (!$jwt->state) return response()->json([
                "status" => "error",
                "message" => "Unauthorized",
                "data" => [],
            ], 401);

            $rules =        [
                'trees_code'            => 'nullable | string ',
                'trees_name'            => 'nullable | string ',
                'plant_date'            => 'nullable | string ',
                'trees_age'             => 'nullable | int',
                // 'number_of_planting'    => 'nullable | int', //!
                'latitude'              => 'nullable | string ',
                'longitude'             => 'nullable | string ',
                // 'status'                => 'nullable | string ',
                'is_verify'             => 'nullable |  boolean',
                // 'old_zone'              => 'nullable | string ',
                'image'                 => 'nullable | string ',
                'zone'                  => 'nullable | string ',
                'type'                  => 'nullable | string ',
                'circumference'         => 'nullable | numeric',
                'height'                => 'nullable | numeric',
                'botanical_characteristics'   => 'nullable | array',
                'care'                  => 'nullable | string ',
                'trees_family'          => 'nullable | string ',
                'benefits'              => 'nullable | string ',
                'reference'             => 'nullable | string ',
                'generals'               => 'nullable | string ',
                'scientific_name'        => 'nullable | string ',
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

            $decoded = $jwt->decoded;
            $username = $decoded->username;

            \date_default_timezone_set('Asia/Bangkok');
            $timestamp = new \DateTime();

            $treeCode       =   $request->trees_code;
            $treesName      =   $request->trees_name;
            $plantDate      =   $request->plant_date;
            $treesAge       =   $request->trees_age;
            // $noPlanting     =   $request->number_of_planting;
            $latitude       =   $request->latitude;
            $longitude      =   $request->longitude;
            $status         =   "available";
            $isVerify       =   $request->is_verify;
            // $old_zone          =   $request->old_zone;
            $image          =   $request->image;
            $zone           =   $request->zone;
            $type           =   $request->type;
            $circumference  =   $request->circumference;
            $height         =   $request->height;
            $characteristics   =   $request->botanical_characteristics;
            $care           =   $request->care;
            $treesFamily    =   $request->trees_family;
            $benefits       =   $request->benefits;
            $reference      =   $request->reference;
            $generals       =   $request->generals;
            $scientificName =   $request->scientific_name;

            // $dataCharacteristics = [];

            // return response()->json($characteristics);

            // $path = getcwd() . "\\..\\image\\";
            $path = getcwd() . "\\..\\..\\..\\iccs\\image\\";
            // $desiredDirectory = "\\CoDE";

            // while (substr($path, -strlen($desiredDirectory)) !== $desiredDirectory) {
            //     $path = dirname($path);
            // }
            // return response()->json($path);

            if (!is_dir($path)) mkdir($path, 0777, true);
            // $pathUsed = 'http://10.1.8.235/dev/iCSS/iCSS-v2/'; // local
            $pathUsed = "https://snc-services.sncformer.com/iccs/image/"; //server



            if ($image !== null) {
                $exp = explode("data:image/", $image);
                $exp2 = explode(";", $exp[1]);

                //! save image to local and serve
                if (str_starts_with($image, 'data:image/' . $exp2[0] . ';base64,')) {

                    $folderPath = $path . "\\";
                    $fileName = $treesName . "." . $exp2[0];
                    if (!is_dir($folderPath)) mkdir($folderPath, 0777, true);
                    file_put_contents($folderPath . $fileName, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image)));
                    $image = "/iccs/image/" . $fileName;
                }
            } else {
                $image = $request->image;
            }

            // return response()->json($image);

            $result = TreesDetails::insert([
                "trees_code"            => $treeCode,
                "trees_name"            => $treesName,
                "plant_date"            => $plantDate,
                "trees_age"             => $treesAge,
                "latitude"              => $latitude,
                "longitude"             => $longitude,
                "status"                => $status,
                "is_verify"             => $isVerify,
                // "old_zone"              => $old_zone,
                "image"                 => $image,
                "zone"                  => $zone,
                "type"                  => $type,
                "circumference"         => $circumference,
                "height"                => $height,
                "botanical_characteristics"   => json_encode($characteristics, JSON_UNESCAPED_UNICODE),
                "care"                  => $care,
                "trees_family"          => $treesFamily,
                "benefits"              => $benefits,
                "reference"             => $reference,
                "generals"              => $generals,
                "created_at"            => $timestamp,
                "updated_at"            => $timestamp,
                "scientific_name"       => $scientificName,

            ]);

            $logData = LogDataTransaction::insert([
                "trees_code"    => $treeCode,
                "action"        => "register",
                "username"      => $username,
                "created_at"    => $timestamp,
            ]);

            return response()->json([
                "status" => 'success',
                "message" => "Register new tree successfully",
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

    //* [GET] /register/get-tree-by-info
    function getTreeByInfo(Request $request)
    {
        try {
            // $header = $request->header('Authorization');
            // $jwt = $this->jwtUtils->verifyToken($header);
            // if (!$jwt->state) return response()->json([
            //     "status" => "error",
            //     "message" => "Unauthorized",
            //     "data" => [],
            // ], 401);

            $rules =        [
                'trees_code'            => 'required | string',
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

            \date_default_timezone_set('Asia/Bangkok');
            $timestamp = new \DateTime();

            $trees_code       =   $request->trees_code;

            //! check data
            $check = TreesDetails::where('trees_code', $trees_code)->get();
            if (count($check) == 0) {
                return response()->json([
                    "status" => "error",
                    "message" => "dosen't tree in system",
                    "data" => []
                ]);
            }


            //! Query data by ID
            $queryData = TreesDetails::select('*')->where('trees_code', $trees_code)->get();
            foreach ($queryData as $value) {
                $value->botanical_characteristics = json_decode($value->botanical_characteristics);
            }

            $dataTT0 = [];
            $dataCarbon_per_day = [];

            if ($queryData[0]->is_verify == true) {
                if ($queryData[0]->status != "unavailable") {
                    if ($queryData[0]->type == "1") {
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

                        $calWS =  $tree_s_1 * pow((pow($queryData[0]->circumference, $tree_s_2) * $queryData[0]->height), $tree_s_3);
                        $calWB = $tree_b_1 * pow((pow($queryData[0]->circumference, $tree_b_2) * $queryData[0]->height), $tree_b_3);
                        $calWL = pow(($tree_l_1 / ($calWS + $calWB + $tree_l_2)), $tree_l_3);
                        $calWT = $calWS + $calWB + $calWL;
                        $calMJ = $calWT / 1000;
                        $calABG = ($calMJ * $cf_tree * (44 / 12)) * ($area_a / $area_aa);
                        $calBLG = $calABG * $r_tree;
                        $calTT0 = $calABG + $calBLG;
                        $carbon_per_day = $calTT0 * 1000 / 365;
                        array_push($dataTT0, ["cal_TT0" => $calTT0]);
                        array_push($dataCarbon_per_day, ["carbon_per_day" => $carbon_per_day]);
                    } else if ($queryData[0]->type == "2") {
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

                        $calWS =  $sapling_s_1 * pow((pow($queryData[0]->circumference, $sapling_s_2) * $queryData[0]->height), $sapling_s_3);
                        $calWB = $sapling_b_1 * pow((pow($queryData[0]->circumference, $sapling_b_2) * $queryData[0]->height), $sapling_b_3);
                        $calWL = $sapling_l_1 * pow((pow($queryData[0]->circumference, $sapling_l_2) * $queryData[0]->height), $sapling_l_3);
                        $calWT = $calWS + $calWB + $calWL;

                        $calMJ = $calWT / 1000;
                        $calABG = ($calMJ * $cf_sapling * (44 / 12)) * ($area_a / $area_aa);;
                        $calBLG = $calABG * $r_sapling;

                        $calTT0 = $calABG + $calBLG;
                        $carbon_per_day = $calTT0 * 1000 / 365;
                        array_push($dataTT0, ["cal_TT0" => $calTT0]);
                        array_push($dataCarbon_per_day, ["carbon_per_day" => $carbon_per_day]);
                    } else if ($queryData[0]->type == "3") {
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

                        $l = 2.3026 * log($queryData[0]->height, 10);
                        $b = pow($queryData[0]->height, $palm_t_3);
                        $calWT = $palm_t_1 + ($palm_t_2 * $l) * $b;
                        $calMJ = $calWT / 1000;
                        $calABG = ($calMJ * $cf_palm * (44 / 12)) * ($area_a / $area_aa);;
                        $calBLG = $calABG * $r_palm;
                        $calTT0 = $calABG + $calBLG;
                        $carbon_per_day = $calTT0 * 1000 / 365;
                        array_push($dataTT0, ["cal_TT0" => $calTT0]);
                        array_push($dataCarbon_per_day, ["carbon_per_day" => $carbon_per_day]);
                    }
                }
            } else {
                $calTT0 = 0;
                $carbon_per_day = 0;
                array_push($dataTT0, ["cal_TT0" => $calTT0]);
                array_push($dataCarbon_per_day, ["carbon_per_day" => $carbon_per_day]);
            }


            $data1Array = json_decode($queryData, true);
            $tt0Value = isset($dataTT0[0]['cal_TT0']) ? $dataTT0[0]['cal_TT0'] : null;
            $mergeData = array_merge($data1Array[0], ['cal_TT0' => $tt0Value]);
            $carbon_per_day = isset($dataCarbon_per_day[0]['carbon_per_day']) ? $dataCarbon_per_day[0]['carbon_per_day'] : null;
            $mergeData2 = array_merge($mergeData, ['carbon_per_day' => $carbon_per_day]);


            return response()->json([
                "status" => 'success',
                "message" => "Get infomation of tree by trees_code successfully",
                "data" => [$mergeData2],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }

    //* [GET] /register/get-tree-info
    function getTreeInfo(Request $request)
    {
        try {
            $header = $request->header('Authorization');
            $jwt = $this->jwtUtils->verifyToken($header);
            if (!$jwt->state) return response()->json([
                "status" => "error",
                "message" => "Unauthorized",
                "data" => [],
            ], 401);

            $rules =        [
                'trees_id'            => 'required | string',
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

            \date_default_timezone_set('Asia/Bangkok');
            $timestamp = new \DateTime();

            $treeID       =   $request->trees_id;

            //! check data
            $check = TreesDetails::where('trees_id', $treeID)->get();
            if (count($check) == 0) {
                return response()->json([
                    "status" => "error",
                    "message" => "dosen't tree in system",
                    "data" => []
                ]);
            }


            //! Query data by ID
            $queryData = TreesDetails::select('*')->where('trees_id', $treeID)->get();
            foreach ($queryData as $value) {
                $value->botanical_characteristics = json_decode($value->botanical_characteristics);
            }

            $dataTT0 = [];
            $dataCarbon_per_day = [];

            if ($queryData[0]->is_verify == true) {
                if ($queryData[0]->status != "unavailable") {
                    if ($queryData[0]->type == "1") {
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

                        $calWS =  $tree_s_1 * pow((pow($queryData[0]->circumference, $tree_s_2) * $queryData[0]->height), $tree_s_3);
                        $calWB = $tree_b_1 * pow((pow($queryData[0]->circumference, $tree_b_2) * $queryData[0]->height), $tree_b_3);
                        $calWL = pow(($tree_l_1 / ($calWS + $calWB + $tree_l_2)), $tree_l_3);
                        $calWT = $calWS + $calWB + $calWL;
                        $calMJ = $calWT / 1000;
                        $calABG = ($calMJ * $cf_tree * (44 / 12)) * ($area_a / $area_aa);
                        $calBLG = $calABG * $r_tree;
                        $calTT0 = $calABG + $calBLG;
                        $carbon_per_day = $calTT0 * 1000 / 365;

                        array_push($dataTT0, ["cal_TT0" => $calTT0]);
                        array_push($dataCarbon_per_day, ["carbon_per_day" => $carbon_per_day]);
                    } else if ($queryData[0]->type == "2") {
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

                        $calWS =  $sapling_s_1 * pow((pow($queryData[0]->circumference, $sapling_s_2) * $queryData[0]->height), $sapling_s_3);
                        $calWB = $sapling_b_1 * pow((pow($queryData[0]->circumference, $sapling_b_2) * $queryData[0]->height), $sapling_b_3);
                        $calWL = $sapling_l_1 * pow((pow($queryData[0]->circumference, $sapling_l_2) * $queryData[0]->height), $sapling_l_3);
                        $calWT = $calWS + $calWB + $calWL;

                        $calMJ = $calWT / 1000;
                        $calABG = ($calMJ * $cf_sapling * (44 / 12)) * ($area_a / $area_aa);;
                        $calBLG = $calABG * $r_sapling;

                        $calTT0 = $calABG + $calBLG;
                        $carbon_per_day = $calTT0 * 1000 / 365;

                        array_push($dataTT0, ["cal_TT0" => $calTT0]);
                        array_push($dataCarbon_per_day, ["carbon_per_day" => $carbon_per_day]);
                    } else if ($queryData[0]->type == "3") {
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

                        $l = 2.3026 * log($queryData[0]->height, 10);
                        $b = pow($queryData[0]->height, $palm_t_3);
                        $calWT = $palm_t_1 + ($palm_t_2 * $l) * $b;
                        $calMJ = $calWT / 1000;
                        $calABG = ($calMJ * $cf_palm * (44 / 12)) * ($area_a / $area_aa);;
                        $calBLG = $calABG * $r_palm;
                        $calTT0 = $calABG + $calBLG;
                        $carbon_per_day = $calTT0 * 1000 / 365;

                        array_push($dataTT0, ["cal_TT0" => $calTT0]);
                        array_push($dataCarbon_per_day, ["carbon_per_day" => $carbon_per_day]);
                    }
                }
            } else {
                $calTT0 = 0;
                $carbon_per_day = 0;
                array_push($dataTT0, ["cal_TT0" => $calTT0]);
                array_push($dataCarbon_per_day, ["carbon_per_day" => $carbon_per_day]);
            }


            $data1Array = json_decode($queryData, true);
            $tt0Value = isset($dataTT0[0]['cal_TT0']) ? $dataTT0[0]['cal_TT0'] : null;
            $mergeData = array_merge($data1Array[0], ['cal_TT0' => $tt0Value]);


            $carbon_per_day = isset($dataCarbon_per_day[0]['carbon_per_day']) ? $dataCarbon_per_day[0]['carbon_per_day'] : null;
            $mergeData2 = array_merge($mergeData, ['carbon_per_day' => $carbon_per_day]);


            return response()->json([
                "status" => 'success',
                "message" => "Get infomation of tree by ID successfully",
                "data" => [$mergeData2],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }

    //* [PUT] /register/edit-tree-info
    function editTreeInfo(Request $request)
    {
        try {
            $header = $request->header('Authorization');
            $jwt = $this->jwtUtils->verifyToken($header);
            if (!$jwt->state) return response()->json([
                "status" => "error",
                "message" => "Unauthorized",
                "data" => [],
            ], 401);

            $rules =        [
                'trees_id'              => 'required | string ',
                'trees_code'            => 'nullable | string ',
                'latitude'              => 'nullable | string ',
                'longitude'             => 'nullable | string ',
                'is_verify'             => 'nullable | boolean',
                'zone'                  => 'nullable | string ',
                'status'                => ['nullable', 'string', Rule::in('unavailable', 'move', 'available')],
                'circumference'         => 'nullable | numeric',
                'height'                => 'nullable | numeric',
                // 'trees_name'            => 'required | string | min:1 | max:255',
                // 'plant_date'            => 'required | string | min:1 | max:15',
                // 'trees_age'             => 'required | int',
                // 'old_zone'              => 'nullable | string | min:1 | max:255',
                // 'image'                 => 'required | string | min:1 | max:255',
                // 'type'                  => 'required | string | min:1 | max:255',
                // 'botanical_characteristics'   => 'nullable | string | min:1 | max:255',
                // 'care'                  => 'required | string | min:1 | max:255',
                // 'trees_family'          => 'required | string | min:1 | max:255',
                // 'benefits'              => 'required | string | min:1 | max:255',
                // 'reference'             => 'required | string | min:1 | max:255',
                // 'generals'               => 'nullable | string | min:1 | max:255',
                // 'scientific_name'        => 'nullable | string | min:1 | max:255',
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

            $decoded = $jwt->decoded;
            $username = $decoded->username;

            \date_default_timezone_set('Asia/Bangkok');
            $timestamp = new \DateTime();

            $treeID         =   $request->trees_id;
            $treeCode       =   $request->trees_code;
            $latitude       =   $request->latitude;
            $longitude      =   $request->longitude;
            $isVerify       =   $request->is_verify;
            $newZone        =   $request->zone;
            $circumference  =   $request->circumference;
            $height         =   $request->height;
            $status         =   $request->status;

            //! Query old data

            $checkData = TreesDetails::select("*")->where('trees_id', $treeID)->get();

            foreach ($checkData as $value) {
                $value->botanical_characteristics = json_decode($value->botanical_characteristics);
            }

            // if ($treeID !== $checkData[0]->trees_id) {
            //     return response()->json([
            //         "status" => 'error',
            //         "message" => "Tree id doesn't exist",
            //         "data" => [],
            //     ]);
            // }

            $oldZone = $checkData[0]->zone;

            // return response()->json($checkData);

            if ($status == "move") {
                $result = TreesDetails::where('trees_id', $treeID)->update([
                    "trees_code"          => $treeCode,
                    "latitude"            => $latitude,
                    "longitude"           => $longitude,
                    "is_verify"           => $isVerify,
                    "status"              => $status,
                    "zone"                => $newZone,
                    "old_zone"            => $oldZone,
                    "circumference"       => $circumference,
                    "height"              => $height,
                    "updated_at"          => $timestamp,
                ]);
            } else {
                $result = TreesDetails::where('trees_id', $treeID)->update([
                    "trees_code"          => $treeCode,
                    "latitude"            => $latitude,
                    "longitude"           => $longitude,
                    "is_verify"           => $isVerify,
                    "status"              => $status,
                    "zone"                => $newZone,
                    // "old_zone"            => $oldZone,
                    "circumference"       => $circumference,
                    "height"              => $height,
                    "updated_at"          => $timestamp,
                ]);
            }

            $logData = LogDataTransaction::insert([
                "trees_code"    => $treeCode,
                "action"        => "edit",
                "username"      => $username,
                "created_at"    => $timestamp,
            ]);


            return response()->json([
                "status" => 'success',
                "message" => "Edit infomation of tree successfully",
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
