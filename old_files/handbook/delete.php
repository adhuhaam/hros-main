<?php
include 'db.php';

$id = (int)$_GET['id'];
$type = $_GET['type'];

switch ($type) {
    case 'section':
        $conn->query("DELETE FROM handbook_sections WHERE id = $id");
        break;
    case 'subsection':
        $conn->query("DELETE FROM handbook_subsections WHERE id = $id");
        break;
    case 'detail':
        $conn->query("DELETE FROM handbook_details WHERE id = $id");
        break;
    default:
        exit("Invalid type");
}

header("Location: index.php");
exit;
