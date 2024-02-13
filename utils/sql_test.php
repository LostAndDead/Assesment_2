<?php

require "../utils/sql.php";

echo "Tasks:<br>";

echo json_encode(getTasks()) . "<br>";

echo "<br>Users:<br>";

echo json_encode(getAllUsers()) . "<br>";

echo "<br>Admin:<br>";

echo json_encode(getUser("admin@admin.com")) . "<br>";

?>