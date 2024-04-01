<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinsHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'operation_type', 'balance_before', 'balance_after'];
}
