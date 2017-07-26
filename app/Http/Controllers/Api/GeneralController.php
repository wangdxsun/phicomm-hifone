<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Models\Emotion;

class GeneralController extends ApiController
{
    /**
     * Ping endpoint allows API consumers to check the version.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping()
    {
        return $this->item('Pong!');
    }

    public function exception()
    {
        throw new \Exception('myException', 444);
    }

    public static function emotion()
    {
        $emotions = Emotion::all(['emotion','url']);
        return  $emotions;
    }
}
