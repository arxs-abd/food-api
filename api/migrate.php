<?php 
namespace Api;

require_once '../controller/food.php';

use Controller\Food;

$foodController = new Food();
$foodController->migrate();
?>