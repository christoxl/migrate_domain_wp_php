<?php
/******
* Name:   migrate.php
* Desc:   Make the updates in db to change the domain of a Wordpress site
* Author: Christopher Perez <christoxl@gmail.com>
* Notes:  Delete this script from your server after finish the migration
*         or it will create a BIG security risk
******/

// Browser output in plain text
header('Content-Type: text/plain');

// Database Information
$db_name = '';
$db_user = '';
$db_password = '';
$db_host = 'localhost';  // Change if your database is in another host

// Set your old and new domain names
$new_domain = '';
$old_domain = '';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
printf("Connected successfully\n");

// Udates to execute
$sql_update = array();
$sql_update[0] = "UPDATE wp_options "
            . "SET option_value = replace(option_value, ?, ?) "
            . "WHERE option_name = 'home' OR option_name = 'siteurl';";
$sql_update[1] = "UPDATE wp_posts SET guid = replace(guid, ?, ?);";
$sql_update[2] = "UPDATE wp_posts SET post_content = replace(post_content, ?, ?);"; 
$sql_update[3] = "UPDATE wp_postmeta SET meta_value = replace(meta_value, ?, ?);";

// Prepare the updates and execute them
for ($i=0; $i < 4; $i++) { 
    make_update($sql_update[$i], $conn, $old_domain, $new_domain);
}

// Close connection
$conn->close();

// Function to execute the updates
function make_update($sql_update, $conn, $old_domain, $new_domain){
    if ($sql_update = $conn->prepare($sql_update)) {
        $sql_update->bind_param('ss', $old_domain, $new_domain);
        $sql_update->execute();
        printf("Rows affected: %d\n", $sql_update->affected_rows);
        $sql_update->close();
    }
}

?>