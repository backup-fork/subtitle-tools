<?php

namespace App\Support\Facades;

use App\Subtitles\VobSub\VobSub2SrtFake;
use App\Subtitles\VobSub\VobSub2SrtInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static VobSub2SrtInterface get()
 * @method static VobSub2SrtInterface path($pathWithoutExtension)
 */
class VobSub2Srt extends Facade
{
    protected static function getFacadeAccessor()
    {
        return VobSub2SrtInterface::class;
    }

    /** @return VobSub2SrtFake */
    public static function fake()
    {
        static::swap(
            static::$app[VobSub2SrtFake::class]
        );

        return static::getFacadeRoot();
    }

    /** @return VobSub2SrtFake */
    public static function fakeExtracting()
    {
        return static::fake()->onlyFakeExtracting();
    }
}
