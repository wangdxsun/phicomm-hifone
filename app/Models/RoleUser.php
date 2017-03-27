<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Models;

use Cache;
use DB;
use Hifone\Presenters\RolePresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;
use Venturecraft\Revisionable\RevisionableTrait;
use Zizaco\Entrust\EntrustRole;

class RoleUser extends Model
{
    use RevisionableTrait;

//    protected $table = 'permission_role';

}
