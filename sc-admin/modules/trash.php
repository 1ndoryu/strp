<?php 

class Trash
{
    public static function catch()
    {
        if(isset($_GET['save-comment']))
        {
            $id = $_GET['save-comment'];
            $data = array();
            $data['trash_comment'] = $_GET['comment'];
       
            updateSQL('sc_ad', $data, $w = array('ID_ad' => $id));
            return "Comentario actualizado";
        }

        if(isset($_POST['action']) && $_POST['action'] == "1")
        {
            foreach ($_POST['anuncio'] as $key => $value) {
                deleteAdRoot($value, true);
            
                # code...
            }
            return "Anuncios eliminados";
        }

        return false;
    }

    public static function filter()
    {
        if(isset($_GET['type'])){
            $filter= "trash = 1 AND ";
            switch ($_GET['type']) 
            {
                case "del":
                    $filter .= "motivo < " . Motivo::Cancelado;
                    break;
                case "des":
                    $filter .= "motivo >= " . Motivo::Cancelado . " AND motivo < " . Motivo::Denunciado;
                    break;
                case "den":
                    $filter .= "motivo >= " . Motivo::Denunciado;
                    break;
                default:
                    return "";
            }
            return $filter;
        }

        return "";

    }

    public static function parseMotivo($motivo)
    {
        $motivo = intval($motivo);
        switch ($motivo) {
            case Motivo::Usuario:
                return "Eliminado <br> (Usuario)";
            case Motivo::Cancelado:
                return "Cancelado<br> (no publicado)";
            case Motivo::Desactivado:
                return "Desactivado<br> (publicado)";
            case Motivo::INCUMPLIMIENTO:
                return "Desaprobado<br> (no publicado)";
            case Motivo::Repetido:
                return "Repetido";
            case Motivo::Denunciado:
                return "Denunciado";
            default:
                return $motivo;
        }
    }
}