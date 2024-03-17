<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PredefinedReplyCategory extends Model
{
    use SoftDeletes, Searchable;
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id')->with('parent');
    }

    public function subcategories()
    {
        return $this->hasMany(static::class, 'parent_id')->orderBy('name', 'asc');
    }

    public function allSubcategories()
    {
        return $this->subcategories()->with('allSubcategories');
    }

    public function scopeIsParent($query)
    {
        return $query->where('parent_id', 0)->orderBy('name', 'asc');
    }

    public function slug()
    {
        return slug($this->name) . '-' . $this->id;
    }
}
