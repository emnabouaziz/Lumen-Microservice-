<?php
namespace App\Services;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
class PaginationGenerator
{
    public static function paginate($items, $perPage =5 , $page = null, $options = [])
    {
            //Get current page form url e.g. &page=6
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            //Create a new Laravel collection from the array data
            $collection = new Collection($items);
            //Define how many items we want to be visible in each page
            //$perPage = 1;
            //Slice the collection to get the items to display in current page
            $currentPageResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();
            //Create our paginator and add it to the data array
            $data= new LengthAwarePaginator($currentPageResults, count($collection), $perPage);
            //Set base url for pagination links to follow e.g custom/url?page=6
            return $data->setPath(request()->url());
    }
}