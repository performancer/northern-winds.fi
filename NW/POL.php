<?php
declare(strict_types=1);

//////////////////////////////////////////////////////////////////////////////////////////////////////////
// DISCLAIMER: This code is from a third party and were provided by NW. I do not claim any ownership.
//
// Credits to MuadDib
// MuadDib@lostsoulsshard.org
// http://www.lostsoulsshard.org
//////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace NW;

use NW\Config\Enviroment;

class POL
{
    public function serverStatus(string $hostname, int $port): bool
    {
        return @fsockopen($hostname, $port, $errno, $errorstr, 2) !== false;
    }

    public function createAccount(string $account, string $password, string $email, string $culprit): bool
    {
        if ($connection = @fsockopen(
            Enviroment::$config['game_server_host'],
            Enviroment::$config['game_server_port'],
            $errno,
            $errorstr,
            10
        )) {
            fwrite($connection, $this->packpolarray([$account, $password, '99', $email, $culprit]) . "\n");
            stream_set_timeout($connection, 3);
            $data = $this->unpackpolvar(@fgets($connection, 4096));
            fclose($connection);

            if ($data !== 1) {
                echo 'Tilin luonti epäonnistui, välitä tieto kehittäjille.<br>';

                if (isset($data)) {
                    echo $data . '<br><br>';
                } else {
                    echo 'Palvelimelta ei saapunut vastausta, mahdollisesti yhteyksissä häiriötä.<br><br>';
                }

                return false;
            }

            return true;
        }

        return false;
    }

    // Unpacks a standard string.
    // IE: sHello World! is Hello World!
    private function unpackpolstr($var): string
    {
        return substr_replace((string)$var, '', 0, 1);
    }

    // Unpacks a single array string from POL to PHP.
    // IE: "s12:Hello World!" becomes "Hello World!"
    private function unpackpolarraystr($var)
    {
        $pos = strpos($var, ":");
        if (!$pos) {
            return substr_replace((string)$var, "", 0, 1);
        }

        return substr_replace((string)$var, "", 0, $pos);
    }

    // Unpacks a POL Integer for PHP.
    // IE: "i1" becomes an Integer of 1
    private function unpackpolint($var)
    {
        $i = preg_replace("/[^0-9.]/", "", substr_replace("$var", "", 0, 1));
        $ix = preg_replace("/\.[0-9]*$/", "", $i);
        return (int)$ix;
    }

    // Unpacks a string in pol format that uses
    // a delimiter(keyword). The delimiter is used to seperate
    // entries and create an array of the elements.
    // IE: "entry1;entry2;entry3", with ; as the keyword
    // (spaces work too of course), becomes an array of
    // {entry1, entry2, entry3}. Or in PHP terms,
    // { 1 -> entry1, 2 -> entry2, 3 -> entry3 }
    private function unpackpolstrkeyword($var, $keyword)
    {
        $strfix = explode($keyword, substr_replace("$var", "", 0, 1));
        return ($strfix);
    }

    // If you do not want to use the unpackpolarraystr and
    // unpackpolint seperately all the time or if the information
    // could be int or str, can use this function to auto convert
    // convert it for you as needed.
    private function unpackpolvar($var)
    {
        if ($var[0] === "i") {
            return $this->unpackpolint($var);
        }

        return $this->unpackpolarraystr($var);
    }

    // Unacks a POL array into a PHP format.
    // IE: a2:S6:entry1S6:entry2 becomes
    // {1 -> entry1, 2 -> entry2 } or also
    // known as { entry1, entry2 } (how seen in POL).
    // ONLY WORKS on a single state array. This
    // Does NOT handle array/dicts/structs inside of
    // of an array.
    private function unpackpolarray($polsarray)
    {
        $polarray = substr_replace($polsarray, "", 0, 1);
        $pos = strpos($polarray, ":");

        $polarray = substr_replace("$polarray", "", 0, ($pos + 1));
        $phparray = array();
        $chkit = 0;
        while ($chkit == 0) {
            if (strpos($polarray, "i") === 0) {
                /* Remove the "i" from it. */
                $polarray = substr_replace($polarray, "", 0, 1);
                $intstr = "";
                $intstrcnt = 0;
                for ($i = 0, $iMax = strlen($polarray); $i < $iMax; $i++) {
                    if (($polarray[$i] === "i") || ($polarray[$i] === "S")) {
                        break;
                    }
                    $intstr .= $polarray[$i];
                    $intstrcnt++;
                }
                $j = preg_replace("/[^0-9.]/", "", $intstrcnt);
                $j = preg_replace("/\.[0-9]*$/", "", $j);
                $j = (int)$j;
                $tmppol = preg_replace("/[^0-9.]/", "", $intstr);
                $tmppol = preg_replace("/\.[0-9]*$/", "", $tmppol);
                $tmppol = (int)$tmppol;
                $phparray[] = $tmppol;
                $polarray = substr_replace("$polarray", "", 0, $j);
                if (!strlen($polarray)) {
                    $chkit = 1;
                }
            } else {
                $polarray = substr_replace($polarray, "", 0, 1);
                $pos = strpos($polarray, ":");
                $strcnt = "";
                for ($i = 0; $i < $pos; $i++) {
                    $strcnt .= $polarray[$i];
                }
                $j = preg_replace("/[^0-9.]/", "", $strcnt);
                $j = preg_replace("/\.[0-9]*$/", "", $j);
                $j = (int)$j;
                $polarray = substr_replace("$polarray", "", 0, ($pos + 1));
                $tmppol = substr("$polarray", 0, $j);
                $phparray[] = $tmppol;
                $polarray = substr_replace("$polarray", "", 0, $j);
                if (!strlen($polarray)) {
                    $chkit = 1;
                }
            }
        }

        return $phparray;
    }

    // Packs a string to POL format for strings inside
    // an array.
    // IE: "a1:S3:123"  <-- creates the S3:123 part from "123"
    private function packpolarraystr($str)
    {
        return "S" . strlen($str) . ":$str";
    }

    // Packs an Integer passed to the function into
    // POL Packed format.
    // IE: 1 becomes i1
    private function packpolint($int)
    {
        return "i$int";
    }

    private function packpolvar($var)
    {
        if (is_int($var)) {
            return $this->packpolint($var);
        }

        return $this->packpolarraystr($var);
    }

    private function packpolarray($array) : string
    {
        $polarray = "a".count($array).":";

        foreach ($array as $elem) {
            $polarray .= $this->packpolvar($elem);
        }

        return $polarray;
    }
}