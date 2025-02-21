<?php

namespace App\Repository\Interfaces;

interface UserInterface {
  public function filterByName($query, $data, $field);
  public function filterByEmail($query, $data, $field);
}