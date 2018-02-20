<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12/22/2017
 * Time: 3:26 PM
 */
require_once 'SQLHelper.php';
$helper = new SQLHelper('localhost', 'BadgerDev', 'password', 'flooddb', 3306);
$helper->prepare("SELECT id FROM access WHERE ip = ?");
$helper->bindParams("s", "127.0.0.1");
if($helper->execute()) {
    echo "There were " . $helper->num_rows . " rows testing value ID <br />";
    while ($row = $helper->get_both_array_results()) {
        echo 'The id is: ' . $row['id'];
    }
    echo "<br /> <br /> <br />";
    echo "SQLObj test: ";
    echo 'The id is ' . $helper->get_sql_obj()->id;
}
