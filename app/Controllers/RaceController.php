<?php

namespace Controllers;

use Helper\Validation;
use Models\Race;
use Views\View;

class RaceController
{
    const PARAM_PILOT = 2;

    public function startRace(string $statusRace, array $cars): array
    {
        Validation::raceAlreadyStarted($statusRace);
        Validation::carsExists($cars);
        Validation::existsMoreOneCar($cars);
        Validation::positionsAreSet($cars);

        return ['Start' => 'on'];
    }

    public function finishRace(string $statusRace, array $cars): array
    {
        Validation::raceNotStarted($statusRace);

        return ['Start' => 'off'];
    }

    public function overtake(array $input, string $statusRace, array $dataCars, array $reports): array
    {
        if (!$input[self::PARAM_PILOT]) {
            View::errorMessageOvertakeNull();
        }

        Validation::raceNotStarted($statusRace);
        $lost = null;

        foreach ($dataCars as $key => $car) {
            if ($car['Piloto'] == $input[self::PARAM_PILOT]) {
                Validation::carIsTheFirst($car);

                $carLost = $key - 1;
                $dataCars[$key]['Posicao'] -= 1;
                $dataCars[$carLost]['Posicao'] += 1;
                $lost = $dataCars[$carLost];
            }
        }

        $reports[] = $input[self::PARAM_PILOT] . " ultrapassou " . $lost['Piloto'] . "!" . PHP_EOL;
        $carsOrdered = RaceController::orderCars($dataCars);

        Race::setReports($reports);
        View::successMessageOvertaking($input[self::PARAM_PILOT], $lost['Piloto']);

        return $carsOrdered;
    }

    public static function orderCars(array $cars): array
    {
        sort($cars);

        $sortArray = array();
        foreach ($cars as $car) {
            foreach ($car as $key => $value) {
                if (!isset($sortArray[$key])) {
                    $sortArray[$key] = array();
                }
                $sortArray[$key][] = $value;
            }
        }

        array_multisort($sortArray['Posicao'], SORT_ASC, $cars);

        return $cars;
    }

    public function getReport(array $reports): void
    {
        Validation::existsReports($reports);

        foreach ($reports as $report) {
            View::report($report);
        }
    }
}
