<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Breakdown extends Model
{
    protected $fillable = [
        'unit_id', 'location_id', 'breakdown_category_id',
        'km', 'hm', 'time_in', 'time_out', 'time_ready',
        'diagnosa', 'tindakan', 'description', 'warning_part',
        'wo_number', 'breakdown_status_id', 'component_criteria_id',
        'update_pcr_time', 'update_pcr_by', 'status', 'user_id'
    ];

    protected $appends = ['duration'];

    public function getDurationAttribute()
    {
        $in = Carbon::parse($this->time_in);
        $out = Carbon::parse($this->time_out);
        return $out->diffForHumans($out);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

}
