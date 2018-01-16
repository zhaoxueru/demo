<?php

/**
 * Crypt Extension TEST Script
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

use \ext\keygen, \ext\crypt;

class test_crypt
{
    public static $key = [
        'run' => []
    ];

    public static function run(): void
    {
        $string = 'Some Test TEXT~ 我爱我家！';

        //Absolute path of "openssl.cnf" on your hard drive
        //crypt::$ssl_cnf = 'Your SSL Config file path ("openssl.cnf") / 你本地的 OpenSSL 配置文件 "openssl.cnf" 路径';

        $key = keygen::create();
        var_dump($key);
        echo PHP_EOL;

        $a = crypt::encrypt($string, $key);
        var_dump($a);

        $b = crypt::decrypt($a, $key);
        var_dump($b);
        echo PHP_EOL;

        $rk = crypt::rsa_keys();
        var_dump($rk);
        echo PHP_EOL;

        $ra = crypt::rsa_encrypt($string, $rk['public']);
        var_dump($ra);

        $rb = crypt::rsa_decrypt($ra, $rk['private']);
        var_dump($rb);
        echo PHP_EOL;

        $ra = crypt::rsa_encrypt($string, $rk['private']);
        var_dump($ra);

        $rb = crypt::rsa_decrypt($ra, $rk['public']);
        var_dump($rb);
        echo PHP_EOL;

        $sna = crypt::sign($string);
        var_dump($sna);

        $snb = crypt::verify($sna);
        var_dump($snb);
        echo PHP_EOL;

        $sra = crypt::sign($string, $rk['public']);
        var_dump($sra);

        $srb = crypt::verify($sra, $rk['private']);
        var_dump($srb);
        echo PHP_EOL;

        $sra = crypt::sign($string, $rk['private']);
        var_dump($sra);

        $srb = crypt::verify($sra, $rk['public']);
        var_dump($srb);
        echo PHP_EOL;

        $pwd = 'some text';

        $pwd_hash = crypt::hash_pwd($pwd, $key);
        var_dump($pwd_hash);

        $pwd_chk = crypt::check_pwd($pwd, $key, $pwd_hash);
        var_dump($pwd_chk);

        $pwd_chk = crypt::check_pwd('wrong text', $key, $pwd_hash);
        var_dump($pwd_chk);
    }
}