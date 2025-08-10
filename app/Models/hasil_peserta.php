<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class hasil_peserta extends Model
{
    use HasFactory;

    protected $table = 'hasil_peserta';

    protected $fillable = ['peserta_id', 'training_record_id', 'level', 'practical_result', 'theory_result', 'final_judgement', 'license', 'certificate', 'expired_date', 'category'];

    public function trainingrecord()
    {
        return $this->belongsTo(training_record::class, 'training_record_id');
    }
    public function pesertas()
    {
        return $this->belongsTo(peserta::class, 'peserta_id');
    }



    public function getExpiredDateAttribute()
    {
        return $this->attributes['expired_date']
            ? Carbon::parse($this->attributes['expired_date'])->format('d F Y')
            : null;
    }


    public function getStatusAttribute()
    {
        $originalExpiredDate = $this->getRawOriginal('expired_date'); // Ambil nilai asli dari database
        if (empty($originalExpiredDate)) {
            return 'Non Active';
        }
        return Carbon::parse($originalExpiredDate)->isPast() && !Carbon::parse($originalExpiredDate)->isToday() ? 'Non Active' : 'Active';
    }

    public function scopeByUserRole($query, $user)
    {
        if ($user->role === 'Super Admin') {
            return $query;
        }

        if ($user->role === 'Admin') {
            $dept = $user->pesertaLogin->dept;
            return $query->whereHas('pesertas', function ($q) use ($dept) {
                $q->where('dept', $dept);
            });
        }

        if ($user->role === 'User') {
            if ($user->pesertaLogin) {
                return $query->where('peserta_id', $user->pesertaLogin->id);
            }
            return $query->whereNull('id');
        }

        return $query->whereNull('id');
    }
}
