<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Si el usuario está autenticado en la API, filtramos por su ID
        if (auth()->check()) {
            $builder->where($model->getTable() . '.arrendador_id', auth()->id());
        }
    }
}