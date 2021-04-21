<?php

namespace Api\Controllers;

use Api\Entities\Car;
use Api\Enum\CommandInputEnum;
use Api\Enum\StatusEnum;
use Api\Messages\CarMessages;
use Api\Messages\RaceMessages;
use Api\Messages\RacingDriverMessages;
use Api\Repository\CarRepository;
use Api\Validations\CarValidator;
use Core\Controller\ControllerInterface;
use Core\Controller\ControllerResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\ORMException;

final class CarController implements ControllerInterface
{
    /**
     * @param int|null $id
     * @return \Core\Controller\ControllerResponse
     *
     * @throws \Exception
     */
    public function index(?int $id = null): ControllerResponse
    {
        if ($id !== 0 && $id !== null) {
            $car = (new CarRepository())->findById($id);
            $formattedCar = CarMessages::showCar($car);

            return (new ControllerResponse(StatusEnum::OK, $formattedCar));
        }

        $dataCars = (new CarRepository())->findAll();

        CarValidator::thereAreCars($dataCars);

        return (new ControllerResponse(StatusEnum::OK, 'Cars list', $dataCars));
    }

    /**
     * @param array $requestBody
     * @return ControllerResponse
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function create(array $requestBody): ControllerResponse
    {
        $car = new Car();

        $car->setRacingDriver($requestBody['racing_driver'] ?? null);
        $car->setBrand($requestBody['brand'] ?? null);
        $car->setModel($requestBody['model'] ?? null);
        $car->setColor($requestBody['color'] ?? null);
        $car->setYear($requestBody['year'] ?? null);

        try {
            (new CarRepository())->create($car);
        } catch (UniqueConstraintViolationException $e) {
            throw new \Exception(
                RacingDriverMessages::errorMessage_RacingDriverAlreadyExists(),
                StatusEnum::BAD_REQUEST
            );
        }

        return (new ControllerResponse(StatusEnum::CREATED, 'Car was created'));
    }

    /**
     * @param int $id
     * @param array $requestArguments
     * @return ControllerResponse
     */
    public function update(int $id, array $requestArguments): ControllerResponse
    {
        return (new ControllerResponse(StatusEnum::NOT_FOUND, 'Car cannot be upgraded'));
    }

    /**
     * @param int $id
     * @return ControllerResponse
     *
     * @throws ORMException
     */
    public function delete(int $id): ControllerResponse
    {
        $carRepository = new CarRepository();

        $car = $carRepository->findById($id);

        $carRepository->delete($car);

        $this->ifExistsCarsThenSetPosition();
        return (new ControllerResponse(StatusEnum::OK, 'Car has been deleted'));
    }

    /**
     * @throws ORMException
     */
    private function ifExistsCarsThenSetPosition(): void
    {
        $dataCars = (new CarRepository())->findAll();

        if (count($dataCars) > 0) {
            $this->setPosition($dataCars);
        }
    }

    /**
     * @param array $dataCars
     * @return array
     *
     * @throws ORMException
     */
    public function setPosition(array $dataCars): array
    {
        $position = 1;

        /** @var Car $dataCar */
        foreach ($dataCars as $dataCar) {
            $dataCar->setPosition($position);

            (new CarRepository())->update($dataCar);

            $position++;
        }

        return $dataCars;
    }
}
