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

use \ext\crypt;

class test
{
    public static $key = [
        'crypt' => []
    ];

    private static function chk_equal(string $name, array $data): void
    {
        echo $name . ': ' . ($data[0] === $data[1] ? 'PASSED!' : 'Failed! ' . (string)$data[0] . ' !== ' . (string)$data[1]);
        echo PHP_EOL;
    }

    public static function crypt(): void
    {
        echo 'Crypt Test Starts:';
        echo PHP_EOL;
        echo 'Make sure to set the right path of "openssl.cnf" in "cfg.php"';
        echo PHP_EOL;
        echo 'You can provide your own "keygen" class script in "cfg.php"';
        echo PHP_EOL;
        echo PHP_EOL;

        $string = (string)mt_rand();

        $aes_key = forward_static_call([crypt::$keygen, 'create']);

        $enc = crypt::encrypt($string, $aes_key);
        $dec = crypt::decrypt($enc, $aes_key);
        self::chk_equal('encrypt/decrypt', [$string, $dec]);


        $rsa_key = crypt::rsa_keys();

        $enc = crypt::rsa_encrypt($string, $rsa_key['public']);
        $dec = crypt::rsa_decrypt($enc, $rsa_key['private']);
        self::chk_equal('rsa_encrypt(pub)/rsa_decrypt(pri)', [$string, $dec]);

        $enc = crypt::rsa_encrypt($string, $rsa_key['private']);
        $dec = crypt::rsa_decrypt($enc, $rsa_key['public']);
        self::chk_equal('rsa_encrypt(pri)/rsa_decrypt(pub)', [$string, $dec]);


        $enc = crypt::sign($string);
        $dec = crypt::verify($enc);
        self::chk_equal('sign/verify', [$string, $dec]);

        $enc = crypt::sign($string, $rsa_key['public']);
        $dec = crypt::verify($enc, $rsa_key['private']);
        self::chk_equal('sign(pub)/verify(pri)', [$string, $dec]);

        $enc = crypt::sign($string, $rsa_key['private']);
        $dec = crypt::verify($enc, $rsa_key['public']);
        self::chk_equal('sign(pri)/verify(pub)', [$string, $dec]);

        $hash = crypt::hash_pwd($string, $aes_key);
        $pwd_chk = crypt::check_pwd($string, $aes_key, $hash);
        self::chk_equal('hash_pwd/check_pwd', [$pwd_chk, true]);
    }
}