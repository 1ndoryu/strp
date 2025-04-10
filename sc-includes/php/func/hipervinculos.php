<?php 
    include_once ABSPATH . 'sc-includes/mod/hyperlinks_data.php';
    class Hipervinculos
    {
        static function crear_hipervinculos($texto, $id_cat, $id_provincia)
        {
            $data = self::get_data_from_settings($id_cat);
            if(empty($data))
                return $texto;
            $data = self::replace_city($data, $id_provincia); 
            $anchors = array();
            foreach ($data as $d) {
                if(isset($d['provincia']) && $d['provincia'] != $id_provincia)
                    continue;
                $matches = self::match_text($d['matches'], $texto);
                if(empty($matches))
                    continue;
                $matches = self::ordenar_links($matches);
                $links = self::crear_links($d, $matches, $anchors);
                $texto = self::reemplasar_link($texto, $links);
            }

            $texto = self::reemplasar_anchor($texto, $anchors);

            return $texto;

        }

        static function ordenar_links($m)
        {
            // Remove duplicates while preserving the original order
            $m = array_values(array_unique($m));

            usort($m, function($a, $b) {
                return strlen($b) - strlen($a);
            });
            
            return $m;
        }

        static function reemplasar_anchor($texto, $anchors)
        {
            foreach ($anchors as $key => $anchor) {
                $texto = str_replace("%k$key%", $anchor, $texto);
            }

            return $texto;
        }

        static function reemplasar_link($texto, $links)
        {
            foreach ($links as $link) {
                $texto = str_replace($link['anchor'], $link['link'], $texto);
            }
            return $texto;
        }

        static function crear_links($data, $matches, &$anchors)
        {
            $link = $data['url'];
            $links = array();
            foreach($matches as $m)
            {
                $key = count($anchors);
                $links[$key] = array(
                    'link' => '<a href="'.$link.'/" >%k'.$key.'%</a>',
                    'anchor' => $m
                );

                $anchors[] = $m;
                
            }

            return $links;
        }

        static function match_text($data, $texto)
        {
            // Remove special characters and convert to lowercase
            $data = array_map(function($str) {
                $str = strtolower($str); // Convert to lowercase
                $str = self::transformToPattern($str);
                return $str;
            }, $data);

            $regex_pattern = '/(?<!\pL)(' . implode('|', $data) . ')(?!\pL)/iu';

            $matches = array();

            preg_match_all($regex_pattern, $texto, $matches);

            return $matches[0];

        }

        static function transformToPattern($text) {
            // Escape special characters in the input text
            $escapedText = preg_quote($text, '/');

            // Replace each vowel with character classes for both regular and accented forms
            $pattern = preg_replace('/á/iu', '[aá]', $escapedText);
            $pattern = preg_replace('/é/iu', '[eé]', $pattern);
            $pattern = preg_replace('/í/iu', '[ií]', $pattern);
            $pattern = preg_replace('/ó/iu', '[oó]', $pattern);
            $pattern = preg_replace('/ú/iu', '[uú]', $pattern);

            // Add word boundaries and case-insensitive flag
            $pattern = '\b' . $pattern . '\b';

            return $pattern;
        }

        static function replace_city($data, $id_provincia) 
        {
            global $con;
            try {
                $select = selectSQL("sc_region", $w = array(
                   "ID_region" => $id_provincia
               ));
               if(empty($select))
                   return array();
                $res = $select[0];
                $seo_name = $res['name_seo'];
                $name = $res['name'];
                $count = count($data);
                for ($i=0; $i < $count; $i++) { 
                    $data[$i]['url'] = str_replace("%ciudad%", $seo_name, $data[$i]['url']);
                    $data[$i]['matches'] = str_replace("%ciudad%", $name, $data[$i]['matches']);
                }
                return $data;
            } catch (\Throwable $th) {
                //throw $th;
                return array();
            }
        }

        static function get_data_from_settings($id_cat)
        {
            global $HYPERLINKS_DATA;
            if(isset($HYPERLINKS_DATA[$id_cat]))
                return $HYPERLINKS_DATA[$id_cat];
            return array();
        }
    }