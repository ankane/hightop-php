<?php

namespace Hightop;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Builder
{
    public static function register()
    {
        EloquentBuilder::macro('top', function ($column, $limit = null, $null = false, $min = null, $distinct = null) {
            if (is_null($distinct)) {
                $op = 'count(*)';
            } else {
                $quotedDistinct = $this->getGrammar()->wrap($distinct);
                $op = "count(distinct $quotedDistinct)";
            }

            $relation = $this->select($column)->selectRaw($op)->groupBy($column)->orderByRaw('1 desc')->orderBy($column);

            if (!is_null($limit)) {
                $relation = $relation->limit($limit);
            }

            if (!$null) {
                $relation = $relation->whereNotNull($column);
            }

            if (!is_null($min)) {
                $relation = $relation->havingRaw("$op >= ?", [$min]);
            }

            // can't use pluck with expressions in Postgres without an alias
            $rows = $relation->get()->toArray();
            $result = [];
            foreach ($rows as $row) {
                $values = array_values($row);
                $result[$values[0]] = $values[1];
            }
            return $result;
        });
    }
}
