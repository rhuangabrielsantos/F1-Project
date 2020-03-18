<?php

namespace Commands;

use Controllers\CarController;
use Helper\FormatEntry;
use Lib\StorageFactory;
use Models\Car;
use Views\View;

class DeleteCar implements Command
{
    private $dataCars;
    private $statusRace;
    private $input;
    private $storage;

    public function __construct(array $input, array $dataCars, string $statusRace, StorageFactory $storage)
    {
        $this->input = $input;
        $this->dataCars = $dataCars;
        $this->statusRace = $statusRace;
        $this->storage = $storage;
    }

    public function runCommand()
    {
        $returnedCars = (new CarController())->deleteCar(
            (new FormatEntry())->returnDeleteCar($this->input),
            $this->dataCars,
            $this->statusRace
        );

        (new Car())->setCars($this->storage, $returnedCars);
        (new View())->successMessageDeleteCar();
    }
}