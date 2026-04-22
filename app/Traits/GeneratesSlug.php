<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GeneratesSlug
{
    protected static function generateUniqueSlug(Model $model, string $name, ?int $excludeId = null, bool $withTrashed = false): string
    {
        $slugBase = Str::slug($name);
        $slug = $slugBase !== '' ? $slugBase : Str::random(8);
        $query = $model->newQuery()->where('slug', $slug);

        if ($withTrashed) {
            $query->withTrashed();
        }

        if ($excludeId) {
            $query->whereKeyNot($excludeId);
        }

        $i = 2;
        while ($query->exists()) {
            $slug = "{$slugBase}-{$i}";
            $query = $model->newQuery()->where('slug', $slug);
            if ($withTrashed) {
                $query->withTrashed();
            }
            if ($excludeId) {
                $query->whereKeyNot($excludeId);
            }
            $i++;
        }

        return $slug;
    }
}
