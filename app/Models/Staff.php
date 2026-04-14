<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    // Table er nam jodi Laravel vul bhabe dore, tai explicitly bole dewa valo
    protected $table = 'staffs';

    protected $fillable = [
        'name',
        'phone',
        'designation',
        'base_salary',
        'is_active',
        'created_by',
    ];

    // Ei staff ke kon user entry koreche
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
