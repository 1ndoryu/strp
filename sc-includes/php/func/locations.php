<?php

class Locations 
{
    public static function getLocations($ID_region, $type)
    {
        $locations = selectSQL("sc_city", $w = array(
            'ID_region' => $ID_region,
            'type' => $type
        ));
        return $locations;
    }

    static function getNameLocation($name_seo)
    {
        $loc = selectSQL("sc_city", $w = array(
            'name_seo' => $name_seo
        ));
        
        return $loc[0]['name'];
    
    }

    public static function makeRows($array, $numColumnas) {
        $matriz = [];
        $columnaActual = 0;
        $elementosPorColumna = ceil(count($array) / $numColumnas); // Calculamos el número máximo de elementos por columna
    
        for ($i = 0; $i < count($array); $i++) {
            if (!isset($matriz[$columnaActual])) {
                $matriz[$columnaActual] = [];
            }
            $matriz[$columnaActual][] = $array[$i];
    
            // Pasamos a la siguiente columna si ya llenamos la actual o si es el último elemento
            if (($i + 1) % $elementosPorColumna == 0 || $i == count($array) - 1) {
                $columnaActual++;
            }
        }
    
        return $matriz;
    }

    public static function makeColumns($locations, $columns)
    {
        $c_locations = array_fill(0, $columns, []); // Inicializamos las columnas
        $index = 0;
    
        foreach ($locations as $location) {
            $c_locations[$index % $columns][] = $location;
            $index++;
        }
    
        return $c_locations;
    }
    
}