<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'category_id', 'amount', 'description', 'date', 'month'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

