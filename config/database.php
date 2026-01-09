<?php
/*
 * --------------------------------------------------------------------------
 * DATABASE CONFIGURATION
 * --------------------------------------------------------------------------
 *
 * Please update the following constants with your database credentials.
 *
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for the blog */
define('DB_NAME', 'rafli_1070_uas');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'rafli001');

/** MySQL hostname */
define('DB_HOST', 'db');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');


/*
 * --------------------------------------------------------------------------
 * ESTABLISH DATABASE CONNECTION
 * --------------------------------------------------------------------------
 *
 * This part of the script attempts to connect to the MySQL database
 * using the credentials you provided above.
 *
 */

// Attempt to connect to MySQL database
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    // If connection fails, stop the script and display an error.
    // In a production environment, you might want to log this error
    // instead of displaying it to the user.
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}

// Set the character set
$mysqli->set_charset(DB_CHARSET);

?>
