<?php 
//motivos de eliminacion
class Motivo
{
    const Caducado = 8;
    const INCUMPLIMIENTO = 9;
    const Repetido = 10;

    const Usuario = 100;
    
    const Cancelado = 200;
    const Desactivado = 201;
    
    const Denunciado = 300;
    const SIN_AVISO = 301;

}

class Bloqueos
{
    const Denuncias = 1;
    const Incumplimiento = 2;
    const Spam = 3;
    const Actividad_ilegal = 4;
    const Suplantacion = 5;
}

class UserRole
{
    const Visitante = 0;
    const Particular = 1;
    const Centro = 2;
    const Publicista = 3;
    const Profesional = 4;

    static function NAME($role)
    {
        switch ($role) {
            case self::Visitante:
                return "Visitante";
            case self::Particular:
                return "Particular";
            case self::Centro:
                return "Centro";
            case self::Publicista:
                return "Publicista";
            case self::Profesional:
                return "Profesional";
        }
    }
}

class Language
{
    const ES = 1;
    const CA = 2;
    const EN = 3;
    const PT = 4;
    const IT = 5;
    const FR = 6;
    const RU = 7;
    const DE = 8;

    
    const COUNT = 8;


    static function NAME($lang)
    {
        switch ($lang) {
            case self::ES:
                return "Español";
            case self::CA:
                return "Catalán";
            case self::EN:
                return "Inglés";
            case self::PT:
                return "Portugués";
            case self::IT:    
                return "Italiano";
            case self::RU:
                return "Ruso";
            case self::FR:
                return "Francés";
            case self::DE:
                return "Alemán";
        }
    }
}

class LocationType
{
    //ciudades de la provincia
    const City = 1;
    //distritos de la ciudad
    const District = 2;
}

class BannerPosition
{
    const Bottom = 0;
    const Top = 1;
    const Middle = 2;

}

class ImageStatus
{
    const Delete = 0;
    const Active = 1;
    const Inactive = 2;
}

class renovationType
{
    const Diario = 1;
    const Autodiario = 2;
    const Autorenueva = 3;
}

class adStatus
{
    const Active = 1;
    const Inactive = 2;
    const None = 0;
}