<?php

namespace Hifone\Http\Controllers\Web;

use Hifone\Exceptions\HifoneException;
use Hifone\Models\Emotion;

class GeneralController extends WebController
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
        throw new HifoneException('myException', 444);
    }

    public static function emotion()
    {
        $emotions = Emotion::all(['emotion','url']);
        foreach ($emotions as $emotion) {
            $emotion->url = request()->getSchemeAndHttpHost().$emotion->url;
        }
        return  $emotions;
    }
}
