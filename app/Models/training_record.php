<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class training_record extends Model
{
    use HasFactory;

    protected $table = 'training_records';

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
    ];

    protected $fillable = ['doc_ref', 'station', 'category_id', 'training_name', 'trainer_name', 'rev', 'date_start', 'date_end', 'training_duration', 'status', 'approval', 'comment', 'attachment', 'user_id', 'training_skill_id'];

    public function trainingCategory()
    {
        return $this->belongsTo(category::class, 'category_id');
    }

    public function hasil_peserta()
    {
        return $this->hasMany(hasil_peserta::class, 'training_record_id');
    }
    public function pesertas()
    {
        return $this->belongsToMany(peserta::class, 'hasil_peserta')->withPivot('level', 'final_judgement', 'license', 'theory_result', 'practical_result');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function training_skills()
    {
        return $this->belongsToMany(training_skill::class);
    }

    public function training_skill_record()
    {
        return $this->hasMany(Trainingskillrecord::class, 'training_record_id');
    }

    public function comments()
    {
        return $this->hasMany(training_comment::class, 'training_record_id');
    }


    public function latestComment()
    {
        return $this->hasOne(training_comment::class)->select('comment_training.id', 'comment_training.training_record_id', 'comment_training.approval', 'comment_training.created_at')->latestOfMany();
    }

    public function getFormattedDateRangeAttribute()
    {
        $start = Carbon::parse($this->date_start)->format('d');
        $end = Carbon::parse($this->date_end)->format('d F Y');

        return "{$start} - {$end}";
    }

    public function getDateStartFormattedAttribute()
    {
        return Carbon::parse($this->date_start)->format('d-m-Y');
    }

    public function getDateEndFormattedAttribute()
    {
        return Carbon::parse($this->date_end)->format('d-m-Y');
    }

    public function getDateDurationFormattedAttribute()
    {

        if (!$this->training_duration) {
            return 0;
        }

        $time = Carbon::createFromFormat('H:i:s', $this->training_duration);

        return ($time->hour * 60) + $time->minute;
    }

    public function scopeByUserRole($query, $user)
    {
        if ($user->role === 'Super Admin') {
            return $query;
        } elseif ($user->role === 'Admin' || $user->role === 'User') {
            $dept = $user->pesertaLogin->dept;
            return $query->whereHas('user.pesertaLogin', function ($q) use ($dept) {
                $q->where('dept', $dept);
            });
        }

        return $query->where('id', null); 
    }
}
