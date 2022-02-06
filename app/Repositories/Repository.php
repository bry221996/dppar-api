<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class Repository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function list(int $page_size = 10)
    {
        return $this->model->paginate($page_size);   
    }

    public function create(array $data)
    {
        return $this->model->create($data);   
    }

    public function update($entity, array $data)
    {
       return $entity->update($data);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function delete($entity)
    {
        return $entity->delete();
    }
}
