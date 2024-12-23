<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidasOF extends Model
{

    use HasFactory;
    protected $table = 'partidas_of'; 
    protected $fillable = [
        'orden_fabricacion_id',
    ];

    public function ordenFabricacion()
    {
        return $this->belongsTo(OrdenFabricacion::class, 'OrdenFabricacion_id');
    }

    public function partidasArea()
    {
        return $this->hasMany(PartidasArea::class, 'PartidasOF_id');
    } 

}
