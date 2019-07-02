<?php

namespace Controllers;

use Models\ModelRace;
use Traits\TraitGetData;
use View\View;

class ControllerRace
{
    use TraitGetData;

    public function startRace()
    {
        if (empty($this->dataCars)) {
            View::errorMessageNeedAddCars();
            exit;
        }

        if (count($this->dataCars) == 1) {
            View::errorMessageOneCar();
            exit;
        }

        foreach ($this->dataCars as $car) {
            if (empty($car['Posicao'])) {
                View::errorMessageNeedDefinePosition();
                exit;
            }
        }

        if ($this->dataRace['Start'] == true) {
            View::errorMessageStartAgain();
            exit;
        }

        $start = [
            "Start" => true
        ];

        ModelRace::startRace($start);

        if ($this->godMode['Status'] == false) {
            View::successMessageStartRace();
        }
    }

    public function finishRace()
    {
        if ($this->dataRace['Start'] == true) {

            if ($this->godMode['Status'] == false) {
                View::podium($this->dataCars);
            }

            $start = [
                "Start" => false
            ];

            ModelRace::setRace($start);

        } else {
            View::errorMessageNeedStart();
        }
    }

    public function overtake($win)
    {
        if ($this->dataRace['Start'] == true) {
            foreach ($this->dataCars as $key => $car) {
                if ($car['Piloto'] == $win) {
                    if ($car['Posicao'] == 1) {
                        View::errorMessageOvertakingFirsPlace($car);
                        exit;
                    }
                    $anterior = $key - 1;
                    $this->dataCars[$key]['Posicao'] -= 1;
                    $this->dataCars[$anterior]['Posicao'] += 1;
                    $lost = $this->dataCars[$anterior];
                }
            }

            if (!isset($anterior)) {
                View::errorMessageNotFoundPilot();
                exit;
            }

            $this->report[] = $win . " ultrapassou " . $lost['Piloto'] . "!" . PHP_EOL;

            $carsOrdered = ControllerRace::orderCars($this->dataCars);
            ModelRace::overtake($carsOrdered, $this->report);

            if ($this->godMode['Status'] == false) {
                View::successMessageOvertaking($win, $lost);
            }

        } else {
            View::errorMessageNeedStart();
            exit;
        }
    }

    public static function orderCars($cars)
    {
        $sortArray = array();
        foreach ($cars as $car) {
            foreach ($car as $key => $value) {
                if (!isset($sortArray[$key])) {
                    $sortArray[$key] = array();
                }
                $sortArray[$key][] = $value;
            }
        }
        $orderby = "Posicao";
        array_multisort($sortArray[$orderby], SORT_ASC, $cars);

        return $cars;
    }

    public function getReport()
    {
        View::logo();

        if (!empty($this->report)) {
            foreach ($this->report as $item) {
                View::report($item);
            }
        } else {
            View::errorMessageEmptyReport();
        }
    }
}
