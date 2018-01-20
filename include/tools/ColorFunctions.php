<?php

namespace Fitapp\tools;

class ColorFunctions {

    /* FUNCIONES DE GRADUACIÓN */
    // Dados dos colores RGB (0-255,0-255,0-255) y un número de colores deseados, regresa un array con todos los colores de la gradación.   
    public static function graduateRGB($c1,$c2,$nc){
        $c = array();
        $dc = array(($c2[0]-$c1[0])/($nc-1),($c2[1]-$c1[1])/($nc-1),($c2[2]-$c1[2])/($nc-1));
        for ($i=0;$i<$nc;$i++){
            $c[$i][0]= round($c1[0]+$dc[0]*$i);
            $c[$i][1]= round($c1[1]+$dc[1]*$i);
            $c[$i][2]= round($c1[2]+$dc[2]*$i);
        }
        return $c;
    }
    
    public static function blendRGB($color1 = [], $color2= [], $color1Mult = 3) {
        $div = $color1Mult+1;
        $c0 = round((($color1Mult*$color1[0])+$color2[0])/$div);
        $c1 = round((($color1Mult*$color1[1])+$color2[1])/$div);
        $c2 = round((($color1Mult*$color1[2])+$color2[0])/$div);
        return static::RGB2Hex([$c0, $c1, $c2]);
    }

    public static function RGB2Hex($vals = []) {
        return sprintf('%02x%02x%02x',$vals[0], $vals[1], $vals[2]);
    }

}