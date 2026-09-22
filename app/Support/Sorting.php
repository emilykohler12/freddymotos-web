<?php

namespace App\Support;

use Illuminate\Support\Str;

class Sorting
{
    /** Versión en PHP del mismo "folding" de {@see self::foldedName()}, para armar el LIKE del buscador. */
    public static function fold(string $value): string
    {
        return Str::lower(Str::ascii($value));
    }

    /**
     * Expresión SQL para ordenar por $column ignorando mayúsculas/minúsculas y
     * tildes (á, é, í, ó, ú, ñ, ü...). Sin esto, el orden A-Z/Z-A de Postgres y
     * SQLite compara por valor de byte: "Á" (mayúscula) y las vocales con tilde
     * quedan muy lejos de su letra "normal" (ej. "áros" aparece después de "z").
     * No depende de extensiones (unaccent) ni de la configuración regional de
     * la base, así que funciona igual en SQLite (local) y Postgres (producción).
     */
    public static function foldedName(string $column): string
    {
        // SQLite's LOWER() solo baja de caja ASCII (una "Á" mayúscula le queda
        // igual), así que el mapa tiene que traer las dos variantes de caja de
        // cada vocal con tilde: no alcanza con hacer LOWER() y mapear solo la
        // minúscula, porque una "Á" nunca llegaría a bajarse a "á" primero.
        $expr = "LOWER({$column})";

        $map = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
            'ñ' => 'n',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'À' => 'a', 'È' => 'e', 'Ì' => 'i', 'Ò' => 'o', 'Ù' => 'u',
            'Ä' => 'a', 'Ë' => 'e', 'Ï' => 'i', 'Ö' => 'o', 'Ü' => 'u',
            'Ñ' => 'n',
        ];

        foreach ($map as $from => $to) {
            $expr = "REPLACE({$expr}, '{$from}', '{$to}')";
        }

        return $expr;
    }
}
