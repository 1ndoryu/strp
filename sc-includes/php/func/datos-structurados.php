<?php 

///Datos estructurados
class DE
{
    static function init()
    {
        global $cat_data, $zoneSEO, $region_data, $keyword_data, $name_location;
        if(isset($_GET['id']) && $_GET['id']=="item")
            return;
        $base_uri = "https://www.solomasajistas.com/";
        $data = array();
        $data[] = array(
            "position" => "1",
            "id" => $base_uri,
            "name" => "Masajes"
        );
        if($cat_data)
        {
             $name = $cat_data[0]['anchor_text'] != '' ? $cat_data[0]['anchor_text'] : $cat_data[0]['name'];
             if($cat_data[0]['ID_cat']==105)
                $name = $cat_data[0]['name'];
             $name_seo = $cat_data[0]['name_seo'];
            if($region_data)
            {
                $name .= " " . $region_data[0]['name'];
                 $name_seo .= "-en-". $region_data[0]['name_seo'];
            }
            
            $data[] = array(
                "position" => "2",
                "id" => $base_uri . $name_seo . "/", 
                "name" => $name
            );

            if($keyword_data)
            {
                $h1 = $keyword_data['h1'];
                $h1 = str_replace('%ciudad%', $region_data[0]['name'], $h1);
                $data[] = array(
                    "position" => "3",
                    "id" => $base_uri . $name_seo ."/keyword/{$keyword_data['keyword']}/",
                    "name" => $h1
                );
            }
            if($name_location)
            {
                $h1 = $cat_data[0]['h1'] != '' ? $cat_data[0]['h1'] : $cat_data[0]['name'];
                $h1 .= " en {$name_location}";
                $data[] = array(
                    "position" => "3",
                    "id" => $base_uri . $name_seo ."/localidad-{$_GET['location']}/",
                    "name" => $h1
                );
            }
        }

        echo self::get_template($data);

        if(count($data) == 2)
        {
            echo self::get_template2($base_uri . $name_seo ."/");
        }

    }

    static function getProvinciaName($id)
    {
        global $con;
        $sql = "SELECT nombre FROM bd_ciudad WHERE id = $id";
        $query = mysqli_query($con, $sql);
        $res = mysqli_fetch_assoc($query);
        
        return $res['nombre'];
    }

    static function get_template($data)
    {
        $path = ABSPATH . "/templates/datos-structurados.php";
        ob_start();
            include($path);
        $m = ob_get_clean();
        return $m;
    }
    static function get_template2($url)
    {
        $path = ABSPATH . "/templates/datos-structurados2.php";
        ob_start();
            include($path);
        $m = ob_get_clean();
        return $m;
    }


}