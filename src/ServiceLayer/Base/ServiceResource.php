<?php

namespace App\ServiceLayer\Base;

use App\ServiceLayer\Base\ServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceResource implements ServiceInterface
{
  protected $repository;

  /**
   *
   * @param string $uuid
   * @return null|object
   */
  public function show(string $uuid): ?object
  {
    return $this->repository->findByUuid($uuid);
  }

  /**
   *
   * @param array $data
   * @return Collection|LengthAwarePaginator
   */
  public function index(array $data): Collection|LengthAwarePaginator
  {
    return $this->repository->getData($data);
  }

  /**
   *
   * @param mixed $data
   * @return object
   */
  public function store($data): object
  {
    return $this->repository->store($data, $this->repository->getModel());
  }

  /**
   *
   * @param string $uuid
   * @param array $data
   * @return object
   */
  public function update(string $uuid, array $data): object
  {
    return $this->repository->update($uuid, $data);
  }

  /**
   *
   * @param string $uuid
   * @return bool
   */
  public function delete(string $uuid): bool
  {
    return $this->repository->delete($uuid);
  }
}