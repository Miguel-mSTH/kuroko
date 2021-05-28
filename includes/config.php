<?php
/*
| Original Project
|--------------------------------------------------------------------------
| OWSA-INV V2
|--------------------------------------------------------------------------
| Author: Siamon Hasan
| Project Name: OSWA-INV
| Version: v2
| Official page: http://oswapp.com/
| facebook Page: https://www.facebook.com/oswapp
|
|
|***************************************************************
| This project was taken and carefully enhanced by
| 
| author: Yoel Monsalve
| mail:   yymonsalve@gmail.com
| web:    (futurely) www.yoelmonsalve.com
|

*/
define( 'DB_HOST', 'localhost' );			// Set database host
//define( 'DB_USER', 'root' );			// Set database user
//define( 'DB_PASS', 'root' );			// Set database password
define( 'DB_USER', 'root' );		// Set database user
define( 'DB_PASS', '' );				// Set database password
define( 'DB_NAME', 'almacen' );			// Set database name

session_write_close( );
session_start();
?>
