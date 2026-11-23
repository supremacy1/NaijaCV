<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'educations';

    protected $fillable = [
        'cv_id',
        'institution',
        'qualification',
        'field_of_study',
        'start_year',
        'end_year',
        'description',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
