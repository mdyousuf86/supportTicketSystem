<?php

namespace App\Traits;

trait SortOrder
{
    public static function sortOrder($column = 'sort_order')
    {
        $modelName = get_class();
        $request = request();
        foreach ($request->sorting as $key => $sorting) {
            $model = $modelName::where('id', $key)->first();
            if ($model) {
                $model->$column = $sorting;
                $model->save();
            }
        }
    }
}
