<?
$title = 'PHP-Calendar 0.5';
$header = 'PHP-Calendar';
$bgcolor = '#336699';
$textcolor = 'blue';
$headercolor = 'white';
$headerbgcolor = 'gray';
$tablebgcolor = '#CCCCCC';
$futurecolor = 'white';
$pastcolor = '#CCDDFF';
$nonecolor = 'silver';
$bordercolor = '#339999';
include ("header.php");

if(empty($action)) {
    echo "<form method=\"GET\" action=\"install.php\">
<table class=\"display\">
  <tr>
    <td>MySQL hostname:</td>
    <td><input type=\"text\" name=\"my_hostname\"></td>
  </tr>
  <tr>
    <td>Database name:</td>
    <td><input type=\"text\" name=\"my_database\"></td>
  </tr>
  <tr>
    <td>Table name:</td>
    <td><input type=\"text\" name=\"my_tablename\"></td>
  </tr>
  <tr>
    <td>Username:</td>
    <td><input type=\"text\" name=\"my_username\"></td>
  </tr>
  <tr>
    <td>Password:</td>
    <td><input type=\"password\" name=\"my_passwd\"></td>
  </tr>
  <tr>
    <td>MySQL admin user:</td>
    <td><input type=\"text\" name=\"admin_username\"></td>
  <tr>
    <td>MySQL admin password:</td>
    <td><input type=\"password\" name=\"admin_passwd\"></td>
  </tr>
  <tr>
    <td colspan=\"2\"><input name=\"action\" type=\"submit\" value=\"Install\"></td>
  </tr>
</table>
</form>";
} else {
    $fp = fopen("config.php", "w")
        or die("Couldn't open config file");

    $fstring = "<?php
\$mysql_hostname = '$my_hostname';
\$mysql_username = '$my_username';
\$mysql_password = '$my_passwd';
\$mysql_database = '$my_database';
\$mysql_tablename = '$my_tablename';
\$title = 'PHP-Calendar 0.5';
\$header = 'PHP-Calendar';
\$bgcolor = '#336699';
\$textcolor = 'blue';
\$headercolor = 'white';
\$headerbgcolor = 'gray';
\$tablebgcolor = '#CCCCCC';
\$futurecolor = 'white';
\$pastcolor = '#CCDDFF';
\$nonecolor = 'silver';
\$bordercolor = '#339999';
?>";

    fwrite($fp, $fstring)
        or die("could not write to file");
    fclose($fp);

    $database = mysql_connect($my_hostname, $admin_username, $admin_passwd)
        or die("Could not connect to server");
    mysql_select_db("mysql")
        or die("could not select mysql");

    $query = "REPLACE INTO user (host, user, password)
    VALUES (
        '$my_hostname',
        '$my_username',
        password('$my_passwd')
    );";
    $result = mysql_query($query)
        or die("Could not add user");

    $query = "REPLACE INTO db (host, db, user, select_priv, insert_priv,
                 update_priv, delete_priv, create_priv, drop_priv)
    VALUES (
        '$my_hostname',
        '$my_database',
        '$my_username',
        'Y', 'Y', 'Y', 'Y',
        'Y', 'Y'
    );";
    $result = mysql_query($query)
        or die("Could not change privileges"); 
    
    mysql_create_db($my_database)
        or die("Could not create database");
    
    mysql_select_db($my_database)
        or die("Could not select $my_database");

    $query = "CREATE TABLE $my_tablename (
  id int(11) DEFAULT '0' NOT NULL auto_increment,
  username varchar(255),
  stamp datetime,
  duration datetime,
  eventtype int(4),
  subject varchar(255),
  description blob,
  PRIMARY KEY (id)
);";
    $result = mysql_query($query)
        or die("Could not create table");

    $query = "GRANT SELECT, INSERT, UPDATE, DELETE ON $my_tablename TO $my_username;";
    $result = mysql_query($query)
        or die("Could not grant");

    $query = "FLUSH PRIVILEGES;";
    $result = mysql_query($query)
        or die("Could not flush privileges");

    mysql_close($database);
}
include("footer.php");
?>