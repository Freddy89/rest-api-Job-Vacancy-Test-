<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FilterJobVacancyTrait;

class JobVacancy extends Model
{
    use HasFactory, SoftDeletes, FilterJobVacancyTrait;

    protected $fillable = ['title', 'description', 'owner_id', 'answers_count'];

    protected $likeFilterFields = ['title'];

    protected $dateFilterFields = ['created_at'];

    protected $sortFields = ['created_at', 'answers_count'];

    protected $sortOrder = ['asc', 'desc'];

    protected $defaultSortBy = 'created_at';

    protected $defaultSortOrder = 'desc';
}
