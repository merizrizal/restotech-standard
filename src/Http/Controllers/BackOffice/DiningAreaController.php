<?php

namespace Restotech\Standard\Http\Controllers\BackOffice;

use Restotech\Standard\BackOffice\CrudResource;
use Restotech\Standard\BackOffice\Resources\DiningAreaResource;

class DiningAreaController extends CrudController
{
    protected function resource(): CrudResource
    {
        return new DiningAreaResource();
    }
}
