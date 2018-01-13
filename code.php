<?php

/**
 * Auth Code DEMO
 *
 * Author 秋水之冰 <27206617@qq.com>
 *
 * Copyright 2018 秋水之冰
 *
 * This file is part of NervSys.
 *
 * NervSys is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NervSys is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NervSys. If not, see <http://www.gnu.org/licenses/>.
 */

namespace demo;

use ext\authcode;
use core\ctr\router;

class code extends authcode
{
    public static $key = [
        'get' => [],
        'chk' => ['code', 'input']
    ];

    /**
     * Some properties
     */
    public static function init(): void
    {
        if (isset(router::$data['width'])) parent::$width = (int)router::$data['width'];
        if (isset(router::$data['height'])) parent::$height = (int)router::$data['height'];

        if (isset(router::$data['type'])) parent::$type = router::$data['type'];//Auth code type (any / num / calc / word)

        if (isset(router::$data['life'])) parent::$life = (int)router::$data['life'];
        if (isset(router::$data['count'])) parent::$count = (int)router::$data['count'];
    }

    /**
     * Check Auth Code & User Input
     *
     * @return bool
     */
    public static function chk(): bool
    {
        return parent::valid(router::$data['code'], router::$data['input']);
    }
}