<?php

namespace App\Http\Controllers;

use App\Eloquents\DrinkType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DrinkTypeController extends Controller
{
    /* @var DrinkType */
    private $drinkType;
    /** @var int */
    private $pagination;

    /**
     * @param DrinkType $drinkType
     */
    public function __construct(DrinkType $drinkType)
    {
        $this->drinkType = $drinkType;
        $this->pagination = env('API_PAGINATION');
    }

    /**
     * @return LengthAwarePaginator
     */
    public function list(): LengthAwarePaginator
    {
        return $this->drinkType->paginate($this->pagination);
    }
}
