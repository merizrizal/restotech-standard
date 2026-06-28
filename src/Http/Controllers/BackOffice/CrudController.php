<?php

namespace Restotech\Standard\Http\Controllers\BackOffice;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Restotech\Standard\BackOffice\CrudResource;

abstract class CrudController extends Controller
{
    abstract protected function resource(): CrudResource;

    public function index(): View
    {
        $resource = $this->resource();
        $items = $resource->indexQuery($resource->newModel()->newQuery())->get();

        return view('restotech-standard::back-office.crud.index', [
            'resource' => $resource,
            'items' => $items,
            'columns' => $resource->columns(),
        ]);
    }

    public function create(): View
    {
        $resource = $this->resource();

        return view('restotech-standard::back-office.crud.form', [
            'resource' => $resource,
            'model' => $resource->newModel(),
            'fields' => $resource->fields(),
            'heading' => 'Create ' . $resource->singularLabel(),
            'formAction' => route($resource->route('store')),
            'formMethod' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $resource = $this->resource();
        $model = $resource->newModel();
        $validated = $request->validate($resource->rules($model));
        $payload = $resource->preparePayload($validated, $request, $model);
        $model->fill($payload);
        $model->save();

        return redirect()
            ->route($resource->route('index'))
            ->with('status', $resource->singularLabel() . ' created.');
    }

    public function edit(string|int $id): View
    {
        $resource = $this->resource();
        $model = $this->findModel($resource, $id);

        return view('restotech-standard::back-office.crud.form', [
            'resource' => $resource,
            'model' => $model,
            'fields' => $resource->fields(),
            'heading' => 'Edit ' . $resource->singularLabel(),
            'formAction' => route($resource->route('update'), $model->getKey()),
            'formMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, string|int $id): RedirectResponse
    {
        $resource = $this->resource();
        $model = $this->findModel($resource, $id);
        $validated = $request->validate($resource->rules($model));
        $payload = $resource->preparePayload($validated, $request, $model);
        $model->fill($payload);
        $model->save();

        return redirect()
            ->route($resource->route('index'))
            ->with('status', $resource->singularLabel() . ' updated.');
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $resource = $this->resource();
        $model = $this->findModel($resource, $id);
        $model->delete();

        return redirect()
            ->route($resource->route('index'))
            ->with('status', $resource->singularLabel() . ' deleted.');
    }

    protected function findModel(CrudResource $resource, string|int $id): Model
    {
        $class = $resource->modelClass();

        return $class::query()->findOrFail($id);
    }
}
