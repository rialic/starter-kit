<?php


namespace App\Repository\Base;

use App\Repository\RepositoryExceptions\FilterByMethodNotDefined;
use App\Repository\RepositoryExceptions\EntityNotDefined;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DBRepository implements DBRepositoryInterface
{
    protected $model;
    protected $params;

    public function __construct()
    {
        $this->model = $this->getModel();
    }

    /**
     *
     * @return object
     * @throws EntityNotDefined
     * @throws BindingResolutionException
     */
    public function getModel(): object
    {
        if (!method_exists($this, 'model')) {
            throw new EntityNotDefined();
        }
        return app($this->model());
    }

    /**
     *
     * @return int
     */
    public function count(): int
    {
        return $this->model::count();
    }

    /**
     *
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->model::all();
    }

    /**
     *
     * @param int $id
     * @return object
     */
    public function findById(int $id): ?object
    {
        return $this->model::where($this->model->getKeyName(), $id)->first();
    }

    /**
     *
     * @param int $id
     * @return object
     */
    public function findByIdOrNew(int $id): object
    {
        return $this->model::firstOrNew([$this->model->getKeyName() => $id]);
    }

    /**
     *
     * @param string $uuid
     * @return null|object
     */
    public function findByUuid(string $uuid): ?object
    {
        return $this->model::where('uuid', $uuid)->first();
    }

    /**
     *
     * @param string $uuid
     * @return object
     */
    public function findByUuidOrNew(string $uuid): object
    {
        return $this->model::firstOrNew(['uuid' => $uuid]);
    }

    /**
     *
     * @param array $params
     * @return object
     */
    public function getFirstData(array $params = []): ?object
    {
        return $this->query($params)->first();
    }

    /**
     *
     * @param array $params
     * @return object
     */
    public function getFirstDataOrNew(array $params = []): object
    {
        return $this->query($params)->first() ?? $this->model;
    }

    /**
     *
     * @param array $data
     * @param object|null $model
     * @return object
     * @throws EntityNotDefined
     * @throws BindingResolutionException
     */
    public function store(array $data, object $model = null): object
    {
        $model = $model ?? $this->getModel();
        $model->fill($data);
        $model->save();

        return $model;
    }

    /**
     *
     * @param string $uuid
     * @param array $data
     * @return object
     * @throws EntityNotDefined
     * @throws BindingResolutionException
     */
    public function update(string $uuid, array $data): ?object
    {
        $model = $this->findByUuid($uuid);

        return $this->store($data, $model);
    }

    /**
     *
     * @param string $uuid
     * @param bool $softDelete
     * @return bool
     */
    public function delete(string $uuid, bool $softDelete = true): bool
    {
        $model = $this->findByUuid($uuid);
        return ($softDelete) ? $model->delete() : $model->forceDelete();
    }

    /**
     * Query data with or without pagination
     * @param array $params
     * @return Collection|LengthAwarePaginator
     */
    public function getData(array $params = []): Collection|LengthAwarePaginator
    {
        $isPaginable = Arr::get($params, 'page');
        $limit = Arr::get($params, 'limit') ?: -1;
        $model = $this->query($params);

        if ($isPaginable) {
            return ($limit !== -1) ? $model->paginate($limit)->onEachSide(1) : $model->paginate()->onEachSide(1);
        }

        return ($limit !== -1) ? $model->limit($limit)->get() : $model->get();
    }

    /**
     *
     * @param array $params
     * @return Builder
     */
    public function query(array $params = []): Builder
    {
        $model = $this->model;
        $model = $this->filter($model, $params);
        $model = $this->sort($model, $params);

        return $model;
    }

    /**
     *
     * @param object $model
     * @param array $params
     * @return object
     */
    private function filter(object $model, array $params): object
    {
        $this->params = $params;
        $params = collect($params);
        $params = $params->filter(fn($value, $key) => $key !== 'page' && $key !== 'limit' && $key !== 'orderBy' && $key !== 'direction');

        if ($params->isEmpty()) {
            return $model;
        }

        return $params->reduce(function ($accModel, $value, $field) {
            $method = 'filterBy' . Str::studly($field);

            // Check if any method has name like filterByMethod
            if (method_exists($this, $method)) {
                $accModel = $this->{$method}($accModel, $value, $field);

                return $accModel;
            }

            throw new FilterByMethodNotDefined();
        }, $model);
    }

    /**
     *
     * @param object $model
     * @param array $params
     * @return Builder
     */
    private function sort(object $model, array $params): Builder
    {
        $isSortable = Arr::get($params, 'orderBy');

        if ($isSortable) {
            $orderBy = Arr::get($params, 'orderBy');
            $direction = Arr::get($params, 'direction');
            $method = 'orderBy' . Str::studly($orderBy);

            // Check for a custom orderByField method
            if (method_exists($this, $method)) {
                $model = $this->{$method}($model, $direction ?? 'desc');
            } else {
                $model = $model->orderBy($orderBy, $direction ?? 'desc');
            }

            return $model;
        }

        return $model->orderBy('created_at', 'desc');
    }
}
