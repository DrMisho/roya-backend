<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class Service
{

    /**
     * @var
     */
    protected $model;
    protected bool $count = false;

    /**
     * @param array $array
     * @return mixed
     */
    abstract protected function queryResult(array $array);

    /**
     * Get List
     *
     * @param array $array
     * @return Collection
     */
    public function getList(array $array = []): Collection
    {
        
        if(key_exists('order_by', $array))
        {
            return $this->queryResult($array)->orderBy($array['order_by'], $array['order_dir']?? 'asc')->get();
        }
        return $this->queryResult($array)->get();
    }

    /**
     * @param $id
     * @param $slug
     * @return ?Model
     */
    public function getSingle($id, $slug = ''): ?Model
    {
        if ($id ?? false) {
            return $this->queryResult(['id' => $id])->firstOrFail();
        } else {
            if (!empty($slug)) {
                return $this->queryResult(['slug' => $slug])->firstOrFail();
            }
        }
    }

    /**
     * @param array $array
     * @return mixed
     */
    public function getSingleByQuery(array $array = [])
    {
        return $this->queryResult($array)->firstOrFail();
    }

    /**
     * @param $query
     * @return int
     */
    public function getCount(array $query = []): int
    {
        if (array_key_exists('page', $query)) {
            unset($query['page']);
        }

        if (array_key_exists('limit', $query)) {
            unset($query['limit']);
        }

        if (array_key_exists('order_by', $query)) {
            unset($query['order_by']);
        }
        $this->count = true;
        return $this->queryResult($query)->get()->count();
    }

    /**
     * @param array $form
     * @return Model
     */
    public function store(array $form): Model
    {
        return $this->model->create($form)->refresh();
    }

    /**
     * @param $model
     * @param $file
     * @param string $owner_type
     * @param int|null $owner_id
     * @return void
     */
    public function addMedia(&$model, $file, string $owner_type = '', int $owner_id = null): void
    {
        if(blank($owner_type))
            $model
                ->addMedia($file)
                ->toMediaCollection(mediaType($file->getClientMimeType()));
        else
            $model
                ->addMedia($file)
                ->withCustomProperties(['owner_type' => $owner_type, 'owner_id' => $owner_id])
                ->toMediaCollection(mediaType($file->getClientMimeType()));
    }

    /**
     * @param $model
     * @param $file
     * @param string $owner_type
     * @param int $owner_id
     * @return void
     */
    public function editMedia(&$model, $file, string $owner_type = '', int $owner_id = null): void
    {
        $this->deleteMedia($model, mediaType($file->getClientMimeType()));

        $this->addMedia($model, $file, $owner_type, $owner_id);

    }

    /**
     * @param $model
     * @param string $collection
     * @return void
     */
    public function deleteMedia(&$model, string $collection): void
    {
        if(! empty($model->getMedia($collection)))
            foreach ($model->getMedia($collection) as $media)
                $model->deleteMedia($media);
    }

    /**
     * Edit Model
     *
     * @param array $form
     * @param [type] $model
     * @return Model
     */
    public function edit(array $form, &$model): Model
    {
        $model->update($form);
        return $model->refresh();
    }

    /**
     * Delete Model
     *
     * @param [type] $model
     * @return boolean
     */
    public function delete(&$model): bool
    {
        return $model->delete();
    }

    /**
     * Delete By Query
     *
     * @param [type] $query
     * @param boolean $force
     * @return boolean
     */
    public function deleteByQuery($query, $force = false): bool
    {
        if ($force) {
            return $this->queryResult($query)->forceDelete();
        } else {
            return $this->queryResult($query)->delete();
        }

    }

}
