<?php
namespace App\Repositories;

abstract class BaseRepository
{

    protected $model;

    public function __construct()
    {
        $this->model = app()->make($this->model());
    }

    abstract public function model();
    abstract public function search(array $parameters);

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function get()
    {
        return $this->model->get();
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        if ($model = $this->find($id))
        {
            return $model->update($attributes) !== false;
        }
        else
        {
            return false;
        }

    }

    public function delete($id)
    {
        if ($model = $this->find($id))
        {
            return $model->delete();
        }
        else
        {
            return false;
        }
    }

    public function paginate($perPage, $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->model = $this->model->where($column, $operator, $value, $boolean);
        return $this;
    }

    public function take($number)
    {

        $this->model = $this->model->limit($number);
        return $this;
    }

    public function latest()
    {
        $this->orderBy('created_at', 'desc');
        return $this;
    }

    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);
        return $this;
    }

    public function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $this->model = $this->model->whereBetween($column, $values, $boolean = 'and', $not = false);
        return $this;
    }

    public function with($relations)
    {
        $this->model = $this->model->with($relations);
        return $this;
    }

    public function withTrashed(){
        $this->model = $this->model->withTrashed();
        return $this;
    }

    public function whereNotNull($param){
        $this->model = $this->model->whereNotNull($param);
        return $this;
    }

    public function restore(){
        $this->model = $this->model->restore();
        return $this;
    }

    public function count(){
        return $this->model->count();
    }

    public function formatDate($date){
        return date('d/m/Y',strtotime($date));
    }

    public function formatDate2($date){
        $d = date('d',strtotime($date));
        $bulan = (int)date('m',strtotime($date));
        $m = ucfirst(Bulan::where('id',$bulan)->first()->nama_bulan);
        $y = date('Y',strtotime($date));
        return $d." ".$m." ".$y;
    }

    public function formatDate3($date){
        $bulan = (int)date('m',strtotime($date));
        $m = ucfirst(Bulan::where('id',$bulan)->first()->nama_bulan);
        $y = date('Y',strtotime($date));
        return $m." ".$y;
    }

}
