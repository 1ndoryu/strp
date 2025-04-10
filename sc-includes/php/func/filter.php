<?php 

class Filter
{

    static function getFilter($cat)
    {
        $cat = getCat($cat);
        $id_cat = $cat['ID_cat'] != -1 ? $cat['parent_cat'] : $cat['ID_cat'];
        $filters = rawQuerySQL("SELECT * FROM sc_filter WHERE cats LIKE '%[$id_cat]%' OR cats LIKE '%[0]%'");
        if(count($filters) == 0)
            return array();
        $filter = '';
        foreach($filters as $key => $value)
        {
            if($key != 0)
                $filter .= ',';
            $filter .= $value['words'];
        }
        $filter = explode(',', $filter);
        return $filter;
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
    
}