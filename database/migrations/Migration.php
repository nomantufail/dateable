<?php
/**
 * Created by PhpStorm.
 * User: nomantufail
 * Date: 11/24/2016
 * Time: 5:10 PM
 */
namespace Migrations;

use Illuminate\Database\Migrations\Migration as L_Migration;
abstract class Migration extends L_Migration
{
    /**
     * @return array of table columns
     * */
    public abstract function fields();
}