<?php

require "vendor/autoload.php";

use Controllers\Car;

$car = new Car();
$car->showCars();

//$car->newCar('Rhuan', 'Ferrari', 'AA', 'Vermelho', '2019');
//$car->newCar('Eloah', 'Mercedes', 'X', 'Branco', '2019');
//$car->newCar('Gabriela', 'Red Bull', 'X', 'Amarelo', '2019');
//$car->newCar('Vandin', 'Renault', 'X', 'Preto', '2019');
//
//$car->setPosition();
//
//
//
//$car->startRace();
//
//$car->overtake('Eloah', 'Rhuan');
//$car->overtake('Gabriela', 'Rhuan');
//
//$car->finishRace();
//
//$car->report();
