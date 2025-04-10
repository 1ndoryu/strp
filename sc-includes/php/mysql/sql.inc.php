<?php
function insertSQL($table,$data=array(),$error=false){
	global $Connection;
	$keys = array_keys($data);
	foreach($data as $index => $value){ 
		$data_n[] = $value; 
	}
	$query = "INSERT INTO `".$table."` (";
	for($i=0;$i<count($keys);$i++){
		if($i!=0){ $query.=","; }
		$query.= "`". $keys[$i] ."`";
	}
	$query.=") VALUES(";
	for($j=0;$j<count($data_n);$j++){
		if($j!=0){ $query.=","; }
		if($data_n[$j] === null)
			$query .= "NULL";
		else if($data_n[$j] === "NOW")
			$query.= "NOW()";
		else
			$query.= "'".mysqli_real_escape_string($Connection, $data_n[$j])."'";
	}
	$query.=")";

	try {
		mysqli_query($Connection, $query);
		return true;
	} catch (\Throwable $th) {
		//throw $th;
		print_r($th);
		die();
	}

}

function selectSomeSQL($tabla,$where, $w_param, $order = "" ){
	global $Connection;
	$query = "SELECT * FROM  ".$tabla." WHERE";
	$lenght = count($w_param);
	for ($i=0; $i < $lenght; $i++) { 
		if($i == $lenght - 1){
			$query .= " ".$where ." = '" . $w_param[$i] ."'";
		}else{
			$query .= " ".$where . " = '" . $w_param[$i] . "' OR";
		}
	}
	
	if($order != ""){
		$query.=" ORDER BY ".$order."";
	}
	$consulta = mysqli_query($Connection, $query);
	$arr=array();
	while($row = mysqli_fetch_array($consulta)){
		$arr[] = $row;
	}
	return $arr;
	
}
function selectSQL($tabla,$where=array(),$order="", $buscar=array(), $cusWhere = ""){
	global $Connection;
	$keys = array_keys($where);
	$values = array_values($where);
	$keys_b = array_keys($buscar);
	$values_b = array_values($buscar);
	$order_busqueda=false;
	if(count($keys_b)!=0){
		if(is_array($values_b[0]))
		{
			$query = "SELECT * FROM `".$tabla."` ";
			for($i=0;$i<count($keys_b);$i++){
				if($i==0){ 
					$query.=" WHERE ("; }else{ $query.=" OR "; 
				}
				foreach($values_b[$i] as $key => $value){
					if($key!=0){ $query.=" OR "; }
					$str =$keys_b[$i]." LIKE '%".mysqli_real_escape_string($Connection, $value)."%'";
					$query.="".$str."";
				}
			}
			$query.=") ";
		}else
		{

			$order_busqueda=true;
			$trozos=explode(" ",$values_b[0]); 
			$numero=count($trozos); 
				if($numero<2){
				$query = "SELECT * FROM `".$tabla."` ";
				for($i=0;$i<count($keys_b);$i++){
					if($i==0){ $query.=" WHERE ("; }else{ $query.=" OR "; }
					$str =$keys_b[$i]." LIKE '%".mysqli_real_escape_string($Connection, $values_b[$i])."%'";
					$query.="".$str."";
				}
					$query.=")";
				}else{
				for($x=0;$x<count($trozos);$x++){
					$trozos[$x]="+".$trozos[$x];
				}
				$valuu=implode(" ",$trozos);
				$query = "SELECT * ,MATCH ( ";
				for($i=0;$i<count($keys_b);$i++){
					if($i!=0)$query.= ", ".$keys_b[$i]; else $query.= $keys_b[$i];
				}
				$query.=") AGAINST ( '".mysqli_real_escape_string($Connection, $valuu)."' IN BOOLEAN MODE ) AS Score FROM `".$tabla."` WHERE MATCH ( ";
				for($i=0;$i<count($keys_b);$i++){
					if($i!=0)$query.= ", ".$keys_b[$i]; else $query.= $keys_b[$i];
				}
				$query.=" ) AGAINST ( '".mysqli_real_escape_string($Connection, $valuu)."' IN BOOLEAN MODE ) ";
				}
		}
	}else{
		$query = "SELECT * FROM `".$tabla."` ";
	}
	for($i=0;$i<count($keys);$i++){
		if(count($keys_b)!=0){
			$query.= " AND "; 
		}else{
			if($i==0){ $query.=" WHERE "; }else{ $query.=" AND "; }
		}
		switch(substr($values[$i], -1)){
		case "%": $str = "`".$keys[$i]."` LIKE '%".mysqli_real_escape_string($Connection, substr($values[$i], 0, -1))."%'"; break;
		case ">": $str =$keys[$i]." > '".mysqli_real_escape_string($Connection, substr($values[$i], 0, -1))."'"; break;
		case "<": $str =$keys[$i]." < '".mysqli_real_escape_string($Connection, substr($values[$i], 0, -1))."'"; break;
		case "^": $var = explode(',',substr($values[$i], 0, -1)); $str ="(".$keys[$i].">='".mysqli_real_escape_string($Connection, $var[0])."' && ".$keys[$i]."<='".mysqli_real_escape_string($Connection, $var[1])."')"; break;
		default:
			$p = substr($values[$i], -2);
			switch(substr($values[$i], -2)){
				case ">=": $str =$keys[$i]." >= '".mysqli_real_escape_string($Connection, substr($values[$i], 0, -2))."'";  break;
				case "<=": $str =$keys[$i]." <= '".mysqli_real_escape_string($Connection, substr($values[$i], 0, -2))."'"; break;
				case "!=": $str =$keys[$i]." != '".mysqli_real_escape_string($Connection, substr($values[$i], 0, -2))."'"; break;
				default: $str = "`". $keys[$i]."` = '".mysqli_real_escape_string($Connection, $values[$i])."'";
			}
		}
		$query.="".$str."";
	}
	if($cusWhere!=""){
		$query = "SELECT * FROM `".$tabla."` WHERE $cusWhere";
	}

	if($order_busqueda and $numero>1){
		if($order!=""){
		$parts=explode("LIMIT",$order);
		if(count($parts)>1)
		$query.=" ORDER BY Score DESC LIMIT ".$parts[1];
		else
		$query.=" ORDER BY Score DESC";
		}
	}elseif($order!=""){
		$query.=" ORDER BY ".$order."";
	}
	try {
		//code...
		$consulta = mysqli_query($Connection, $query);
	} catch (\Throwable $th) {
		//throw $th;
		console_log($query);
		return array();
	}
	$arr=array();
	while($row = mysqli_fetch_array($consulta)){
		$arr[] = $row;
	}
	return $arr;
}

function selectAllSQL($tabla, $where){
	global $Connection;
	$keys = array_keys($where);
	$values = array_values($where);
	$query = "SELECT * FROM `".$tabla."` ";
	$query.=" WHERE ". $keys[0]. " = " .$values[0];
	$consulta = mysqli_query($Connection, $query);
	$arr=array();
	while($row = mysqli_fetch_assoc($consulta)){
		$arr[] = $row;
	}
	return $arr[0];
}

function countSQL($tabla,$where=array(),$order="",$buscar=array()){
	global $Connection;
	$keys = array_keys($where);
	$values = array_values($where);
	$keys_b = array_keys($buscar);
	$values_b = array_values($buscar);
	if(count($keys_b)!=0){
		if(is_array($values_b[0]))
		{
			$query = "SELECT * FROM `".$tabla."` ";
			for($i=0;$i<count($keys_b);$i++){
				if($i==0){ 
					$query.=" WHERE ("; }else{ $query.=" OR "; 
				}
				foreach($values_b[$i] as $key => $value){
					if($key!=0){ $query.=" OR "; }
					$str =$keys_b[$i]." LIKE '%".mysqli_real_escape_string($Connection, $value)."%'";
					$query.="".$str."";
				}
			}
			$query.=") ";
		}else
		{

			$order_busqueda=true;
			$trozos=explode(" ",$values_b[0]); 
			$numero=count($trozos); 
				if($numero<2){
				$query = "SELECT * FROM `".$tabla."` ";
				for($i=0;$i<count($keys_b);$i++){
					if($i==0){ $query.=" WHERE ("; }else{ $query.=" OR "; }
					$str =$keys_b[$i]." LIKE '%".mysqli_real_escape_string($Connection, $values_b[$i])."%'";
					$query.="".$str."";
				}
					$query.=")";
				}else{
				for($x=0;$x<count($trozos);$x++){
					$trozos[$x]="+".$trozos[$x];
				}
				$valuu=implode(" ",$trozos);
				$query = "SELECT * ,MATCH ( ";
				for($i=0;$i<count($keys_b);$i++){
					if($i!=0)$query.= ", ".$keys_b[$i]; else $query.= $keys_b[$i];
				}
				$query.=") AGAINST ( '".mysqli_real_escape_string($Connection, $valuu)."' IN BOOLEAN MODE ) AS Score FROM `".$tabla."` WHERE MATCH ( ";
				for($i=0;$i<count($keys_b);$i++){
					if($i!=0)$query.= ", ".$keys_b[$i]; else $query.= $keys_b[$i];
				}
				$query.=" ) AGAINST ( '".mysqli_real_escape_string($Connection, $valuu)."' IN BOOLEAN MODE ) ";
				}
		}
	}else{
		$query = "SELECT * FROM `".$tabla."` ";
	}
	for($i=0;$i<count($keys);$i++){
		if(count($keys_b)!=0){
			$query.= " AND "; 
		}else{
			if($i==0){ $query.=" WHERE "; }else{ $query.=" AND "; }
		}
		switch(substr($values[$i], -1)){
		case "%": $str = "`". $keys[$i]."` LIKE '".mysqli_real_escape_string($Connection, $values[$i])."'"; break;
		case ">": $str ="`". $keys[$i]."` > '".mysqli_real_escape_string($Connection, substr($values[$i], 0, -1))."'"; break;
		case "<": $str ="`". $keys[$i]."` < '".mysqli_real_escape_string($Connection, substr($values[$i], 0, -1))."'"; break;
		case "^": $var = explode(',',substr($values[$i], 0, -1)); $str ="(`". $keys[$i]."` >='".mysqli_real_escape_string($Connection, $var[0])."' && `". $keys[$i]."` <='".mysqli_real_escape_string($Connection, $var[1])."')"; break;
		default:
			switch(substr($values[$i], -2)){
				case ">=": $str = "`". $keys[$i]."` >= '".mysqli_real_escape_string($Connection, substr($values[$i], 0, -2))."'";  break;
				case "<=": $str = "`". $keys[$i]."` <= '".mysqli_real_escape_string($Connection, substr($values[$i], 0, -2))."'"; break;
				case "!=": $str = "`". $keys[$i]."` != '".mysqli_real_escape_string($Connection, substr($values[$i], 0, -2))."'"; break;
				default: $str = "`". $keys[$i]."` = '".mysqli_real_escape_string($Connection, $values[$i])."'";
			}
		}
		$query.="".$str."";
	}

	if($order!=""){ $query.=" ORDER BY ".$order.""; }
	$consulta = mysqli_query($Connection, $query) or die(mysqli_error($Connection));
	return mysqli_num_rows($consulta);
}
function updateSQL($tabla,$datos=array(),$where=array()){
	global $Connection;
	$keys_w = array_keys($where);
	$values_w = array_values($where);

	$keys = array_keys($datos);
	$values = array_values($datos);
	foreach($datos as $indice => $valor){ 
		$datos_n[] = $valor; 
	}
	$query = "UPDATE `".$tabla."` SET ";
	for($i=0;$i<count($keys);$i++){
		if($i!=0){ $query.=", "; }
		if($values[$i] === null)
			$query.="`".$keys[$i]."` = NULL";
		else if($values[$i] === "NOW")
			$query.="`".$keys[$i]."` = NOW()";
		else
			$query.="`".$keys[$i]."` = '".mysqli_real_escape_string($Connection, $values[$i])."'";
	}
	for($i=0;$i<count($keys_w);$i++){
		if($i==0){ $query.=" WHERE "; }else{ $query.=" AND "; }
		$query.="".$keys_w[$i]." = '".mysqli_real_escape_string($Connection, $values_w[$i])."'";
	}
	try {
		mysqli_query($Connection, $query);
		return true; 
	} catch (\Throwable $th) {
		//throw $th;
		print_r($th);
		return false;
	}
}
function deleteSQL($tabla,$where=array()){
	global $Connection;
	$keys_w = array_keys($where);
	$values_w = array_values($where);
	$query = "DELETE FROM `".$tabla."` ";
	for($i=0;$i<count($keys_w);$i++){
		if($i==0){ $query.=" WHERE "; }else{ $query.=" AND "; }
		$query.="".$keys_w[$i]." = '".mysqli_real_escape_string($Connection, $values_w[$i])."'";
	}
	if(mysqli_query($Connection, $query)){ return true; }else{ return false; }
}
function lastIdSQL(){
	global $Connection;
	return mysqli_insert_id($Connection);
}
function deleteSQLtable($t){
	global $Connection;
	mysqli_query($Connection, "DROP table `$t`");
}

function rawQuerySQL($query) 
{
	global $Connection;
	try {
		$consulta = mysqli_query($Connection, $query);

		$arr=array();
		while($row = mysqli_fetch_assoc($consulta)){
			$arr[] = $row;
		}
		return $arr;
	} catch (\Throwable $th) {
		return null;
	}
}
