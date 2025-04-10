<?php
class Categories
{
    public static function stringfy(array $categories)
    {
        $categories = array_map(function($category){
            return "[$category]";
        }, $categories);

        return implode(',', $categories);
    }

    public static function parse(string $categories)
    {
        $categories = explode(',', $categories);
        $categories = array_map(function($category){
            return trim($category, '[]');
        }, $categories);

        return $categories;
    }
}