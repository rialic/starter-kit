<?php

namespace App\Repository\Base;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface DBRepositoryInterface
{
    public function count(): int;
    public function findAll(): Collection;
    public function findById(int $id): ?object;
    public function findByIdOrNew(int $id): object;
    public function findByUuid(string $uuid): ?object;
    public function findByUuidOrNew(string $uuid): object;
    public function getFirstData(array $params): ?object;
    public function getFirstDataOrNew(array $params = []): object;
    public function store(array $data, object $model = null): object;
    public function update(string $id, array $data): ?object;
    public function delete(string $uuid, bool $softDelete = true): bool;
    public function getData(array $params): Collection|LengthAwarePaginator;
}