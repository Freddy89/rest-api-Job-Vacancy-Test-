<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['job_id', 'user_id', 'answer'];
}
