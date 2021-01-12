<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Short extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'url',
        'last_hit',
        'counter'
    ];

    protected $hidden = [
        'counter',
        'last_hit',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'counter' => 'integer'
    ];

    public function getShortAttribute(){
        return route('link', $this->code);
    }

}
