<?php

namespace App\Http\Controllers;

use App\Http\Libraries\JWT\JWTUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TreesDetails;
use App\Models\TreesVariables;
use App\Models\LogDataTransaction;

class VariableController extends Controller
{

    private $jwtUtils;
    public function __construct()
    {
        $this->jwtUtils = new JWTUtils();
    }

    //* [POST] /variables/add-variables
    function addVariables(Request $request)
    {
        try {
            $header = $request->header('Authorization');
            $jwt = $this->jwtUtils->verifyToken($header);
            if (!$jwt->state) return response()->json([
                "status" => "error",
                "message" => "Unauthorized",
                "data" => [],
            ], 401);

            $rules = [
                "tree_s_1"          => 'required | numeric ',
                "tree_s_2"          => 'required | numeric ',
                "tree_s_3"          => 'required | numeric ',

                "tree_b_1"          => 'required | numeric ',
                "tree_b_2"          => 'required | numeric ',
                "tree_b_3"          => 'required | numeric ',

                "tree_l_1"          => 'required | numeric ',
                "tree_l_2"          => 'required | numeric ',
                "tree_l_3"          => 'required | numeric ',

                "sapling_s_1"       => 'required | numeric ',
                "sapling_s_2"       => 'required | numeric ',
                "sapling_s_3"       => 'required | numeric ',

                "sapling_b_1"       => 'required | numeric ',
                "sapling_b_2"       => 'required | numeric ',
                "sapling_b_3"       => 'required | numeric ',

                "sapling_l_1"       => 'required | numeric ',
                "sapling_l_2"       => 'required | numeric ',
                "sapling_l_3"       => 'required | numeric ',

                "palm_t_1"          => 'required | numeric ',
                "palm_t_2"          => 'required | numeric ',
                "palm_t_3"          => 'required | numeric ',

                "cf_tree"           => 'required | numeric ',
                "cf_sapling"        => 'required | numeric ',
                "cf_palm"           => 'required | numeric ',

                "r_tree"            => 'required | numeric ',
                "r_sapling"         => 'required | numeric ',
                "r_palm"            => 'required | numeric ',

                "area_a"            => 'required | numeric ',
                "area_b"            => 'required | numeric ',
                "area_c"            => 'required | numeric ',
                "area_d"            => 'required | numeric ',

                "area_park"         => 'required | numeric ',
                "area_water"        => 'required | numeric ',
                "area_aa"           => 'required | numeric ',
                "area_bb"           => 'required | numeric ',
                "area_cc"           => 'required | numeric ',
                "area_dd"           => 'required | numeric ',
                "area_pp"           => 'required | numeric ',
                "area_ww"           => 'required | numeric ',

                "rate"              => 'required | numeric ',
                "percentage"        => 'required | numeric ',
            ];


            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(["status" => "error", "message" => "Bad request", "data" => [["validator" => $validator->errors()]]], 400);
            }

            \date_default_timezone_set('Asia/Bangkok');
            $timestamp = new \DateTime();

            $variablesID = $request->variables_id;

            // //! check data
            // $check = TreesVariables::where('variables_id', $variablesID)->get();
            // if (count($check) == 0) {
            //     return response()->json([
            //         "status" => "error",
            //         "message" => "dosen't variables in system",
            //         "data" => []
            //     ]);
            // }

            $result = TreesVariables::insert([
                "tree_s_1"          => $request->tree_s_1,
                "tree_s_2"          => $request->tree_s_2,
                "tree_s_3"          => $request->tree_s_3,

                "tree_b_1"          => $request->tree_b_1,
                "tree_b_2"          => $request->tree_b_2,
                "tree_b_3"          => $request->tree_b_3,

                "tree_l_1"          => $request->tree_l_1,
                "tree_l_2"          => $request->tree_l_2,
                "tree_l_3"          => $request->tree_l_3,

                "sapling_s_1"       => $request->sapling_s_1,
                "sapling_s_2"       => $request->sapling_s_2,
                "sapling_s_3"       => $request->sapling_s_3,

                "sapling_b_1"       => $request->sapling_b_1,
                "sapling_b_2"       => $request->sapling_b_2,
                "sapling_b_3"       => $request->sapling_b_3,

                "sapling_l_1"       => $request->sapling_l_1,
                "sapling_l_2"       => $request->sapling_l_2,
                "sapling_l_3"       => $request->sapling_l_3,

                "palm_t_1"          => $request->palm_t_1,
                "palm_t_2"          => $request->palm_t_2,
                "palm_t_3"          => $request->palm_t_3,

                "cf_tree"           => $request->cf_tree,
                "cf_sapling"        => $request->cf_sapling,
                "cf_palm"           => $request->cf_palm,

                "r_tree"            => $request->r_tree,
                "r_sapling"         => $request->r_sapling,
                "r_palm"            => $request->r_palm,

                "area_a"            => $request->area_a,
                "area_b"            => $request->area_b,
                "area_c"            => $request->area_c,
                "area_d"            => $request->area_d,

                "area_park"         => $request->area_park,
                "area_water"        => $request->area_water,
                "area_aa"           => $request->area_aa,
                "area_bb"           => $request->area_bb,
                "area_cc"           => $request->area_cc,
                "area_dd"           => $request->area_dd,
                "area_pp"           => $request->area_pp,
                "area_ww"           => $request->area_ww,

                "rate"              => $request->rate,
                "percentage"        => $request->percentage,

                "created_at"        => $timestamp,
                "updated_at"        => $timestamp,
            ]);

            return response()->json([
                "status" => 'success',
                "message" => "insert new variables of calculate tree successfully",
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

    //* [PUT] /variables/edit-variables
    function editVariables(Request $request)
    {
        try {
            $header = $request->header('Authorization');
            $jwt = $this->jwtUtils->verifyToken($header);
            if (!$jwt->state) return response()->json([
                "status" => "error",
                "message" => "Unauthorized",
                "data" => [],
            ], 401);

            $rules = [
                "variables_id"      => 'required | string ',
                "tree_s_1"          => 'required | numeric ',
                "tree_s_2"          => 'required | numeric ',
                "tree_s_3"          => 'required | numeric ',

                "tree_b_1"          => 'required | numeric ',
                "tree_b_2"          => 'required | numeric ',
                "tree_b_3"          => 'required | numeric ',

                "tree_l_1"          => 'required | numeric ',
                "tree_l_2"          => 'required | numeric ',
                "tree_l_3"          => 'required | numeric ',

                "sapling_s_1"       => 'required | numeric ',
                "sapling_s_2"       => 'required | numeric ',
                "sapling_s_3"       => 'required | numeric ',

                "sapling_b_1"       => 'required | numeric ',
                "sapling_b_2"       => 'required | numeric ',
                "sapling_b_3"       => 'required | numeric ',

                "sapling_l_1"       => 'required | numeric ',
                "sapling_l_2"       => 'required | numeric ',
                "sapling_l_3"       => 'required | numeric ',

                "palm_t_1"          => 'required | numeric ',
                "palm_t_2"          => 'required | numeric ',
                "palm_t_3"          => 'required | numeric ',

                "cf_tree"           => 'required | numeric ',
                "cf_sapling"        => 'required | numeric ',
                "cf_palm"           => 'required | numeric ',

                "r_tree"            => 'required | numeric ',
                "r_sapling"         => 'required | numeric ',
                "r_palm"            => 'required | numeric ',

                "area_a"            => 'required | numeric ',
                "area_b"            => 'required | numeric ',
                "area_c"            => 'required | numeric ',
                "area_d"            => 'required | numeric ',

                "area_park"         => 'required | numeric ',
                "area_water"        => 'required | numeric ',
                "area_aa"           => 'required | numeric ',
                "area_bb"           => 'required | numeric ',
                "area_cc"           => 'required | numeric ',
                "area_dd"           => 'required | numeric ',
                "area_pp"           => 'required | numeric ',
                "area_ww"           => 'required | numeric ',

                "rate"              => 'required | numeric ',
                "percentage"        => 'required | numeric ',
            ];


            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(["status" => "error", "message" => "Bad request", "data" => [["validator" => $validator->errors()]]], 400);
            }
            $decoded = $jwt->decoded;
            $username = $decoded->username;

            \date_default_timezone_set('Asia/Bangkok');
            $timestamp = new \DateTime();

            $variablesID = $request->variables_id;

            //! check data
            $check = TreesVariables::where('variables_id', $variablesID)->get();

            if (count($check) == 0) {
                return response()->json([
                    "status" => "error",
                    "message" => "dosen't variables id in system",
                    "data" => []
                ]);
            }


            $result = TreesVariables::where('variables_id', $variablesID)->update([
                "tree_s_1"          => $request->tree_s_1,
                "tree_s_2"          => $request->tree_s_2,
                "tree_s_3"          => $request->tree_s_3,
                "tree_b_1"          => $request->tree_b_1,
                "tree_b_2"          => $request->tree_b_2,
                "tree_b_3"          => $request->tree_b_3,
                "tree_l_1"          => $request->tree_l_1,
                "tree_l_2"          => $request->tree_l_2,
                "tree_l_3"          => $request->tree_l_3,
                "sapling_s_1"       => $request->sapling_s_1,
                "sapling_s_2"       => $request->sapling_s_2,
                "sapling_s_3"       => $request->sapling_s_3,
                "sapling_b_1"       => $request->sapling_b_1,
                "sapling_b_2"       => $request->sapling_b_2,
                "sapling_b_3"       => $request->sapling_b_3,
                "sapling_l_1"       => $request->sapling_l_1,
                "sapling_l_2"       => $request->sapling_l_2,
                "sapling_l_3"       => $request->sapling_l_3,
                "palm_t_1"          => $request->palm_t_1,
                "palm_t_2"          => $request->palm_t_2,
                "palm_t_3"          => $request->palm_t_3,
                "cf_tree"           => $request->cf_tree,
                "cf_sapling"        => $request->cf_sapling,
                "cf_palm"           => $request->cf_palm,
                "r_tree"            => $request->r_tree,
                "r_sapling"         => $request->r_sapling,
                "r_palm"            => $request->r_palm,
                "area_a"            => $request->area_a,
                "area_b"            => $request->area_b,
                "area_c"            => $request->area_c,
                "area_d"            => $request->area_d,
                "area_park"         => $request->area_park,
                "area_water"        => $request->area_water,
                "area_aa"           => $request->area_aa,
                "area_bb"           => $request->area_bb,
                "area_cc"           => $request->area_cc,
                "area_dd"           => $request->area_dd,
                "area_pp"           => $request->area_pp,
                "area_ww"           => $request->area_ww,
                "rate"              => $request->rate,
                "percentage"        => $request->percentage,
                "created_at"        => $timestamp,
                "updated_at"        => $timestamp,
            ]);

            $logData = LogDataTransaction::insert([
                "trees_code"    => 0,
                "action"        => "edit-variable",
                "username"      => $username,
                "created_at"    => $timestamp,
            ]);

            return response()->json([
                "status" => 'success',
                "message" => "Edit variables of calculate tree successfully",
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

    //* [GET] /variables/get
    function getVariables(Request $request)
    {
        try {
            $header = $request->header('Authorization');
            $jwt = $this->jwtUtils->verifyToken($header);
            if (!$jwt->state) return response()->json([
                "status" => "error",
                "message" => "Unauthorized",
                "data" => [],
            ], 401);

            // //! Query data
            $queryData = TreesVariables::select(
                "variables_id",
                "tree_s_1",
                "tree_s_2",
                "tree_s_3",
                "tree_b_1",
                "tree_b_2",
                "tree_b_3",
                "tree_l_1",
                "tree_l_2",
                "tree_l_3",
                "sapling_s_1",
                "sapling_s_2",
                "sapling_s_3",
                "sapling_b_1",
                "sapling_b_2",
                "sapling_b_3",
                "sapling_l_1",
                "sapling_l_2",
                "sapling_l_3",
                "palm_t_1",
                "palm_t_2",
                "palm_t_3",
                "cf_tree",
                "cf_sapling",
                "cf_palm",
                "r_tree",
                "r_sapling",
                "r_palm",
                "area_a",
                "area_b",
                "area_c",
                "area_d",
                "area_park",
                "area_water",
                "area_aa",
                "area_bb",
                "area_cc",
                "area_dd",
                "area_pp",
                "area_ww",
                "rate",
                "percentage",
            )->get();



            return response()->json([
                "status" => 'success',
                "message" => "Get variables for calculate Carbn credit successfully",
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
}
