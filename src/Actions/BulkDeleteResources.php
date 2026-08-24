<?php

namespace Cachet\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkDeleteResources
{
    /**
     * Delete every requested model atomically after validating that all exist.
     *
     * @param  class-string<Model>  $modelClass
     * @param  Closure(Model): void  $deleteResource
     */
    public function handle(Request $request, string $modelClass, Closure $deleteResource): void
    {
        $validated = $request->validate([
            'ids' => ['required', 'string', 'regex:/^[1-9][0-9]*(,[1-9][0-9]*)*$/'],
        ]);

        /** @var array<int, int> $ids */
        $ids = array_values(array_unique(array_map('intval', explode(',', $validated['ids']))));

        DB::transaction(function () use ($ids, $modelClass, $deleteResource): void {
            /** @var Model $model */
            $model = new $modelClass;
            $resources = $model->newQuery()->whereKey($ids)->lockForUpdate()->get();
            $missingIds = array_values(array_diff($ids, $resources->modelKeys()));

            if ($missingIds !== []) {
                throw (new ModelNotFoundException)->setModel($modelClass, $missingIds);
            }

            $resources->each($deleteResource);
        });
    }
}
