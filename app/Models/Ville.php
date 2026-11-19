<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ville extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    /**
     * Grandes villes qui doivent apparaître en tête des listes déroulantes
     * (vente, demandes, filtres achat...), suivies des autres villes par
     * ordre alphabétique. Les accents / tirets / variantes orthographiques
     * sont normalisés pour matcher les noms tels qu'ils sont stockés.
     */
    public const FEATURED = [
        'casablanca',
        'rabat',
        'marrakech',
        'tanger',
        'agadir',
        'fes',
        'meknes',
        'sale',
        'oujda',
        'kenitra',
        'tetouan',
    ];

    public function scopeOrdered($query)
    {
        $norm = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE("
            . "name,'è','e'),'é','e'),'ê','e'),'ë','e'),'à','a'),'â','a'),'ï','i'),'î','i'),'ô','o'),'ç','c'))";
        $cases = [];
        $bindings = [];
        foreach (array_values(self::FEATURED) as $i => $v) {
            $cases[] = "WHEN ? THEN $i";
            $bindings[] = $v;
        }
        $expr = "CASE $norm " . implode(' ', $cases) . " ELSE 999 END";
        return $query
            ->orderByRaw($expr, $bindings)
            ->orderBy('name');
    }
}
