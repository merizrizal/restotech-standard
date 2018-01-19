<?php

namespace backend\components;

use yii\validators\Validator;

class String2Validator extends Validator {

    public function validateAttribute($model, $attribute) {
        if (!ctype_alnum(str_replace('_', '', $model->$attribute))) {
            $this->addError($model, $attribute, $model->attributeLabels()[$attribute] . ' must contain letters or _ (underscore).');
        }
    }

}
