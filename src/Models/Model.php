<?php
namespace RA\Auth\Models;

use Illuminate\Database\Eloquent\Model as LaravelModel;
use RA\Auth\Services\ClassName;

class Model extends LaravelModel
{
    protected $guarded = [
        'id', 'created_at', 'updated_at',
    ];

    protected function serializeDate(\DateTimeInterface $date): string {
        return $date->format('Y-m-d');
    }

    public function loadMeta($keys = []) {
        $model_name = \Str::studly($this->getTable().'_meta');
        $metas = ClassName::Model($model_name)::where('user_id', $this->id)->get();
        $item_meta = [];

        foreach ( $metas as $meta ) {
            if ( !count($keys) ) {
                $item_meta[$meta->key] = $meta->value;
            }
            else if ( in_array($meta->key, $keys) ) {
                $item_meta[$meta->key] = $meta->value;
            }
        }

        $this->meta = $item_meta;
    }

    public static function loadMetaForMany($items, $keys = []) {
        if ( !count($items) ) {
            return $items;
        }

        $exp = explode('\\', get_called_class());
        $model_name = end($exp).'Meta';
        $grouped_metas = ClassName::Model($model_name)::whereIn('user_id', pluck($items))->get()->groupBy('user_id');

        foreach ( $items as $item ) {
            $metas = $grouped_metas[$item->id] ?? [];
            $item_meta = [];

            foreach ( $metas as $meta ) {
                if ( !count($keys) ) {
                    $item_meta[$meta->key] = $meta->value;
                }
                else if ( in_array($meta->key, $keys) ) {
                    $item_meta[$meta->key] = $meta->value;
                }
            }

            $item->meta = $item_meta;
        }

        return $items;
    }
}
