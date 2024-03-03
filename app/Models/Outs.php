<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outs extends Model
{
    use HasFactory;
    protected $fillable = ['game_id', 'team_id', 'content', 'image', 'type', 'status'];
}
