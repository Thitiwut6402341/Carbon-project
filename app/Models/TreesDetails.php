<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreesDetails extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_trees_details';

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
        "created_at",
        "updated_at",
        "scientific_name",

    ];
}
