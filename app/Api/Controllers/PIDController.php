<?php

namespace App\Api\Controllers;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\UI\Controller\IController;
use App\Repository\PointOfSaleRepository;
use DateTime;
use Exception;

/**
 * @Path("/")
 */
class PIDController implements IController
{
    public function __construct(private readonly PointOfSaleRepository $repository) {}

    /**
     * @Path("/")
     * @Method("GET")
     * @return array<int, PointOfSale>
     */
    public function index(ApiRequest $request, ApiResponse $response): array
    {
        $date = $request->getQueryParam('date', null);
        try {
            $date = $date ? new DateTime($date) : new DateTime();
        }
        catch (Exception $e) {
            return ['error' => 'Neplatný datum a čas'];
        }

        return $this->repository->findByDateTime($date);
    }
}
