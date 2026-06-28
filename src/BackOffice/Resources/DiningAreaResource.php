<?php

namespace Restotech\Standard\BackOffice\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Restotech\Standard\BackOffice\CrudResource;
use Restotech\Standard\Models\DiningArea;

class DiningAreaResource extends CrudResource
{
    public function modelClass(): string
    {
        return DiningArea::class;
    }

    public function routeNamePrefix(): string
    {
        return 'restotech.standard.back_office.dining-areas';
    }

    public function title(): string
    {
        return 'Dining Areas';
    }

    public function singularLabel(): string
    {
        return 'Dining Area';
    }

    public function columns(): array
    {
        return [
            ['name' => 'code', 'label' => 'Code'],
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'sort_order', 'label' => 'Sort Order'],
            ['name' => 'is_active', 'label' => 'Active'],
        ];
    }

    public function fields(): array
    {
        return [
            ['name' => 'code', 'label' => 'Code', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
            ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'required' => true],
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox', 'required' => false],
            ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea', 'required' => false],
        ];
    }

    public function rules(?Model $model = null): array
    {
        $uniqueCode = Rule::unique('restotech_dining_areas', 'code');

        if ($model instanceof DiningArea && $model->exists) {
            $uniqueCode->ignore($model->getKey());
        }

        return [
            'code' => ['required', 'string', 'max:255', $uniqueCode],
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function indexQuery(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function preparePayload(array $validated, Request $request, ?Model $model = null): array
    {
        $validated['sort_order'] = (int) $validated['sort_order'];
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
