<?php

namespace App\Core\Models\OrderCore;
use App\Core\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class BinderyOption extends BaseModel
{
    /**
     * Specify the DB connection to use.
     *
     * @string
     */
    protected $connection = 'order_core';

    /**
     * Specify the table to use.
     *
     * @var string
     */
    protected $table = 'bindery_option';

    /**
     * Allow mass assignment all, []
     *
     * @var array
     */
    protected $guarded = [];

}