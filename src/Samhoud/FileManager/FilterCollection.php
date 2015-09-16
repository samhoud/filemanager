<?php


namespace Samhoud\FileManager;


use Illuminate\Support\Collection;
use Samhoud\FileManager\Exceptions\InvalidFilterException;
use Samhoud\FileManager\Filters\Contracts\FilterInterface;

class FilterCollection
{
    private $filters;

    public function __construct($filters = [])
    {
        if (count($filters) > 0) {
            $this->validateFilters($filters);
        }
        $this->filters = new Collection($filters);

    }

    public function all()
    {
        return $this->filters->all();
    }

    public function add(FilterInterface $filter)
    {
        $this->filters->push($filter);
    }

    protected function validFilter($filter)
    {
        if (!$filter instanceof FilterInterface) {
            throw new InvalidFilterException('Invalid file filter');
        }
    }

    private function validateFilters($filters)
    {
        foreach ($filters as $filter) {
            $this->validFilter($filter);
        }
    }
}