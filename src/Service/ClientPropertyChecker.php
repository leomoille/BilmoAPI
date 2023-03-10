<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ClientPropertyChecker
{
    public function control($owner, $userToCheck)
    {
        if ($owner !== $userToCheck) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
    }
}
