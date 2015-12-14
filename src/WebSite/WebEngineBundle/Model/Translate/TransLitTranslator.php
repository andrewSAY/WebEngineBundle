<?php
/**
 * Created by PhpStorm.
 * User: Сапрыкин А_Ю
 * Date: 15.10.15
 * Time: 16:56
 */

namespace WebSite\WebEngineBundle\Model\Translate;


class TransLitTranslator
{
    private $cyrillicPattern = array('а', 'б', 'в', 'г', 'д', 'e', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у',
        'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ь', 'э', 'ы', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У',
        'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ь', 'Э', 'Ы', 'Ю', 'Я', ' ');
    private $latinPattern = array('a', 'b', 'v', 'g', 'd', 'e', 'jo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u',
        'f', 'h', 'ts', 'ch', 'sh', 'sht', '', '`', 'je', 'ji', 'yu', 'ya', 'A', 'B', 'V', 'G', 'D', 'E', 'Jo', 'Zh',
        'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U',
        'F', 'H', 'Ts', 'Ch', 'Sh', 'Sht', '', '`', 'Je', 'Ji', 'Yu', 'Ya', '_');

    function __construct()
    {
    }

    function translateToLatin($string)
    {
        return str_replace($this->cyrillicPattern, $this->latinPattern, $string);
    }

    function translateToCyrillic($string)
    {
        return str_replace($this->latinPattern, $this->cyrillicPattern, $string);
    }

} 