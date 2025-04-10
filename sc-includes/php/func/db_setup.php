<?php
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

 class db_setup {

	var $error;
 	// param $check bool: echo the sql statements instead of writing them into dbase

 	// Constructor
 	function db_setup($host = false, $user = false, $pass = false, $database = false) {
 		$this->host = $host;
 		$this->database = $database;
 		$this->user = $user;
 		$this->pass = $pass;
 		$this->SqlArchive = "sc-includes/php/func/script_tables.sql";
 	}

 	// Connnect
 	function dbConnect() {
 		$this->con = @mysqli_connect($this->host, $this->user, $this->pass);
 		if (!$this->con) {
 			$this->error = "<b>Error (dbConnect): " . mysqli_error($this->con) . "</b>";
 		}
 	}

 	// Select dbase
 	function select_db() {
 		$result = @mysqli_select_db($this->con, $this->database);
 		if (!$result) {
 			$this->error = "<b>Error (select_db): " . mysqli_error($this->con) . "</b>";
 		}
 	}

 	// Import Data
 	function import() {

 		// Connect if $host is set, else we're using the active connection (if any) !
 		if ($this->host) {
 			$this->dbConnect();
 			if ($this->error)
 				return;
 		}

 		// Select dbase if $database is set, can be set via sql as well
 		if ($this->database) {
 			$this->select_db();
 			if ($this->error)
 				return;
 		}
		
 		// For existing connections $this->con is false...
 		if ($this->con !== false || $this->check) {

 			// To avoid problems we're reading line by line ...
 			$lines = file($this->SqlArchive, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
 			$buffer = '';
 			foreach ($lines as $line) {
 				// Skip lines containing EOL only
 				if (($line = trim($line)) == '')
 					continue;

 				// skipping SQL comments
 				if (substr(ltrim($line), 0, 2) == '--')
 					continue;

 				// An SQL statement could span over multiple lines ...
 				if (substr($line, -1) != ';') {
 					// Add to buffer
 					$buffer .= $line;
 					// Next line
 					continue;
 				} else
 					if ($buffer) {
 						$line = $buffer . $line;
 						// Ok, reset the buffer
 						$buffer = '';
 					}

 				// strip the trailing ;
 				$line = substr($line, 0, -1);

 				// Write the data
				$result = mysqli_query($this->con,$line);

 				if (!$result and !$this->check) {
 					$this->error = "<b>Error (mysqli_query): " . mysqli_error($this->con) . "</b>";
 					return;
 				}
 			}
 		}
 	}
 }
?>