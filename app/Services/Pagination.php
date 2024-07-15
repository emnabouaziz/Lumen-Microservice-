<?php
namespace App\Services;
class Pagination
{
    public static function paginate($items, $per_page)
    {
        if ($per_page == "all") {
            $items = $items->get();
            $per_page = ($items->count() == 0) ? 1 : $items->count();
            $items = PaginationGenerator::paginate($items,$per_page);
        }else{
            $items = $items->paginate($per_page)->withQueryString();
        }
        return $items ;
    }
}