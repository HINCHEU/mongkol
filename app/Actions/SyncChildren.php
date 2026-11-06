<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Relations\HasMany;

// Makes an invitation's list (venues, events, gift accounts) match the editor's rows, in order.
class SyncChildren
{
    /**
     * Rows with an id update that row, rows without one are created, and rows missing from the
     * list are deleted. Ids are looked up through the relation, so another invitation's rows
     * can never be touched.
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<int> the ids in the given order
     */
    public static function handle(HasMany $relation, array $rows, bool $sorted = false): array
    {
        $ids = [];

        foreach (array_values($rows) as $i => $row) {
            $attributes = collect($row)->except('id')->all() + ($sorted ? ['sort' => $i] : []);
            $model = isset($row['id']) ? $relation->getQuery()->clone()->findOrFail($row['id']) : $relation->make();
            $model->fill($attributes)->save();
            $ids[] = $model->id;
        }

        // Delete one by one, so each row's events fire (the invitation's cached view is cleared).
        $relation->getQuery()->clone()->whereNotIn('id', $ids)->get()->each->delete();

        return $ids;
    }
}
