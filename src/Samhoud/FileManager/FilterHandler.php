<?php


namespace Samhoud\FileManager;

use Intervention\Image\Image;
use Samhoud\FileManager\Filters\Contracts\FileFilterInterface;
use Samhoud\FileManager\Filters\Contracts\FilterInterface;
use Samhoud\FileManager\Filters\Contracts\ImageFilterInterface;


/**
 * Class FileEditHandler
 * @package Samhoud\FileManager
 */
class FilterHandler
{
    /**
     * @var FilterCollection
     */
    private $filters;

    /**
     * @var int
     */
    protected $edits = 0;

    /**
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = new FilterCollection($filters);
    }

    /**
     * @return array
     */
    public function filters()
    {
        return $this->filters->all();
    }

    /**
     * @param FilterInterface $filter
     */
    public function add(FilterInterface $filter){
        $this->filters->add($filter);
    }

    /**
     * @param Contracts\File $file
     * @return Contracts\File|Image
     */
    public function handle($file)
    {
        if (count($this->filters()) == 0) {
            return $file;
        }

        foreach ($this->filters() as $filter) {

            /** @var FileFilterInterface|ImageFilterInterface $filter */
            $file = $filter->applyFilter($file);
            $this->edits++;
        }
        return $file;
    }


    /**
     * @return int
     */
    public function edits()
    {
        return $this->edits;
    }
}