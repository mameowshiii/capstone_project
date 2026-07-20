<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Summon extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_number',
        'complainant_name',
        'complainant_contact',
        'respondent_name',
        'respondent_contact',
        'complain_details',
        'schedule_date',
        'status',
        'hearing_remarks',
        'complainant_resident_id',
        'respondent_resident_id',
    ];

    protected $casts = [
        'schedule_date' => 'datetime',
    ];

    public function complainantResident()
    {
        return $this->belongsTo(Resident::class, 'complainant_resident_id');
    }

    public function respondentResident()
    {
        return $this->belongsTo(Resident::class, 'respondent_resident_id');
    }
}
