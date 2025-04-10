<?php
class Searchs 
{
    static function getSearchs($id_cat, $region)
    {
        $region_name = $region['name'];
        $region_name_seo = $region['name_seo'];
        $region_ID = $region['ID_region'];
        
        $query = "SELECT * FROM sc_search WHERE ID_cat = $id_cat AND (ID_region = 0 OR ID_region = $region_ID)";
        $searchs = rawQuerySQL($query);

        foreach ($searchs as $key => $search) {
            $searchs[$key]['query_search'] = str_replace("%ciudad%",$region_name, $search['query_search']);
            
            $searchs[$key]['query_url'] = str_replace("%ciudad%",$region_name_seo, $search['query_url']);
        }

        return $searchs;
    }

    static function getKeyword($keyword)
    {
        $searchs = selectSQL("sc_keyword", $w = array(
            "keyword" => $keyword
        )); 

        return $searchs[0];
    }
}