<?php
namespace App\Traits;

trait WithoutTrashedRelations
{
    public function scopeWithOutTrashedRelations($query, ...$relationName)
    {
        foreach ((array) $relationName as $relation) {
            $query->whereHas($relation, fn($q) => $q->whereNull('deleted_at'));
        }
    }
}