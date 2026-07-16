<?php

namespace App\Traits;

use App\Scopes\TenantScope;

trait BelongsToTenant
{
    /**
     * El método boot del trait se ejecuta automáticamente en el modelo.
     */
    protected static function bootBelongsToTenant(): void
    {
        // Aplicar el filtro automático de Tenant
        static::addGlobalScope(new TenantScope());

        // Evento de creación: Asigna automáticamente el arrendador_id antes de guardar
        static::creating(function ($model) {
            if (auth()->check() && !$model->arrendador_id) {
                $model->arrendador_id = auth()->id();
            }
        });
    }
}