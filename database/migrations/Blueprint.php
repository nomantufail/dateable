<?php
/**
 * Created by PhpStorm.
 * User: nomantufail
 * Date: 11/24/2016
 * Time: 5:36 PM
 */

namespace Migrations;


class Blueprint extends \Illuminate\Database\Schema\Blueprint
{
    public function addColumn($type, $name, array $parameters = [])
    {
        dd($this->getColumns());
    }
}