<?php

namespace Restotech\Standard\BackOffice;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class CrudResource
{
    abstract public function modelClass(): string;

    abstract public function routeNamePrefix(): string;

    abstract public function title(): string;

    abstract public function singularLabel(): string;

    abstract public function columns(): array;

    abstract public function fields(): array;

    abstract public function rules(?Model $model = null): array;

    public function indexQuery(Builder $query): Builder
    {
        return $query;
    }

    public function preparePayload(array $validated, Request $request, ?Model $model = null): array
    {
        return $validated;
    }

    public function newModel(array $attributes = []): Model
    {
        $class = $this->modelClass();

        return new $class($attributes);
    }

    public function route(string $action): string
    {
        return $this->routeNamePrefix() . '.' . $action;
    }
}
