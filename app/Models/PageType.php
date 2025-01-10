<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int|null $max_count
 */
class PageType extends Model
{
    protected $fillable = ['name', 'max_count'];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}
