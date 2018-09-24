<?php

namespace App;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    //

    protected $fillable = ['id', 'name', 'quantity', 'price'];

    public static function getAll()
    {
        try {
            $file = Storage::disk('public')->get('products.json'); // reads file
            $json_array = json_decode($file, false);
            $collection = collect($json_array); // make collection for sorting
            $products = $collection->sortByDesc('created_at')->values();
            return $products;
        } catch (FileNotFoundException $e) {
            throw new \Exception("Error in reading storage.");
        }
    }
}
