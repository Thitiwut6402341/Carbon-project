<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vw_cc_zone_all_parameter extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vw_cc_zone_all_parameter';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'trees_id';
    protected $fillable = [
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
        "ws",
        "wb",
        "wl",
        "wt",
        "c_c",
        "abg",
        "blg",
        "c_zone",
    ];
}
