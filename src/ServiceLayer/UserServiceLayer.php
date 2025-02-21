<?php

namespace App\ServiceLayer;

use App\Repository\UserRepository;
use App\ServiceLayer\Base\ServiceResource;

class UserServiceLayer extends ServiceResource {

    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }
}