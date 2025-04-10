<?php
class ModFilter
{
    public static function catch()
    {
        if(isset($_POST['filter-id'])){
            $id = $_POST['filter-id'];
            $cats = Categories::stringfy($_POST['category']);
            $words = preg_replace('/\s+/', '', $_POST['words']);
            if($id == 0){
                insertSQL("sc_filter", $a=array('name'=>$_POST['name'],'words'=>$words,'cats'=>$cats));
                return "Filtro creado exitosamente";
            }
            updateSQL("sc_filter", $a=array('name'=>$_POST['name'],'words'=>$words,'cats' => $cats), $s=array('ID_filter'=>$id));
            return "Filtro editado exitosamente";
        }

        if(isset($_GET['deletefilter'])){
            deleteSQL("sc_filter", $s=array('ID_filter'=>$_GET['deletefilter']));
            return "Filtro eliminado exitosamente";
        }

        return false;
    }

    public static function getFilters()
    {
        $filters = selectSQL("sc_filter");
        $filters = array_map(function($filter){
            $filter['cats'] = Categories::parse($filter['cats']);
            $filter['cats'] = array_map(function($cat){
                if($cat == 0)
                    return array('name'=>'Todas');
                return selectSQL("sc_category", $w=array('ID_cat'=>$cat))[0];
            }, $filter['cats']);
            $filter['words'] = explode(',', $filter['words']);
            return $filter;
        }, $filters);
        return $filters;
    }

}