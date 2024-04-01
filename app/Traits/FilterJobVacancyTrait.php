<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use MongoDB\Driver\Query;

trait FilterJobVacancyTrait {

    /**
     * add filtering.
     *
     * @param  $builder: query builder.
     * @param  $filters: array of filters.
     * @return query builder.
     */
    public function scopeFilter($builder, $filters = [])
    {
        $sortByFilter = $this->defaultSortBy;
        $sortOrderFilter = $this->defaultSortOrder;

        if(!$filters) {
            return $builder;
        }
        $tableName = $this->getTable();
        foreach ($filters as $field => $value) {
            if(in_array($field, $this->dateFilterFields) && $value != null) {
                if($value == 'day') {
                    $builder->where($field, '>=', Carbon::now()->subDay()->toDateTimeString());
                }
                if($value == 'week') {
                    $builder->where($field, '>=', Carbon::now()->subWeek()->toDateTimeString());
                }
                if($value == 'month') {
                    $builder->where($field, '>=', Carbon::now()->subMonth()->toDateTimeString());
                }
                continue;
            }
            if(in_array($field, $this->likeFilterFields)) {
                $builder->where($tableName.'.'.$field, 'LIKE', "%$value%");
            }
            if (in_array($value, $this->sortFields) && $value != null) {
                $sortByFilter = $value;
            }
            if (in_array($value, $this->sortOrder) && $value != null) {
                $sortOrderFilter = $value;
            }
        }

        $builder->orderBy($sortByFilter, $sortOrderFilter);

        return $builder;
    }
}
