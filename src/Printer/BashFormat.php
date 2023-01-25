<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Printer;

use Medas\Console\Formats\{BgColor, Color, Format, HexBgColor, HexColor, Style};
use Medas\ConsolePrinter\Printer\Exceptions\UnknownFormatException;
use Medas\ServiceManager\Attributes\Service;

/**
 * @link https://misc.flogisoft.com/bash/tip_colors_and_formatting
 * @link https://robotmoon.com/256-colors/#table-of-color-codes
 */
#[Service]
class BashFormat
{
    // Formatting
    public const BOLD = '1';
    public const DIM = '2';
    public const UNDERLINED = '4';
    public const BLINK = '5';
    public const INVERT = '7';
    public const HIDDEN = '8';

    // Foreground colors
    public const DEFAULT = '39';
    public const BLACK = '30';
    public const RED = '31';
    public const GREEN = '32';
    public const YELLOW = '33';
    public const BLUE = '34';
    public const MAGENTA = '35';
    public const CYAN = '36';
    public const LIGHT_GRAY = '37';
    public const GRAY = '90';
    public const LIGHT_RED = '91';
    public const LIGHT_GREEN = '92';
    public const LIGHT_YELLOW = '93';
    public const LIGHT_BLUE = '94';
    public const LIGHT_MAGENTA = '95';
    public const LIGHT_CYAN = '96';
    public const WHITE = '97';

    // Background colors
    public const DEFAULT_BG = '49';
    public const BLACK_BG = '40';
    public const RED_BG = '41';
    public const GREEN_BG = '42';
    public const YELLOW_BG = '43';
    public const BLUE_BG = '44';
    public const MAGENTA_BG = '45';
    public const CYAN_BG = '46';
    public const LIGHT_GRAY_BG = '47';
    public const GRAY_BG = '100';
    public const LIGHT_RED_BG = '101';
    public const LIGHT_GREEN_BG = '102';
    public const LIGHT_YELLOW_BG = '103';
    public const LIGHT_BLUE_BG = '104';
    public const LIGHT_MAGENTA_BG = '105';
    public const LIGHT_CYAN_BG = '106';
    public const WHITE_BG = '107';

    // 256 color map prefixes
    public const COLOR256 = '38;5;';
    public const COLOR256_BG = '48;5;';

    public const XTERM256_CODES = [
        '#000000' => '0',
        '#800000' => '1',
        '#008000' => '2',
        '#808000' => '3',
        '#000080' => '4',
        '#800080' => '5',
        '#008080' => '6',
        '#c0c0c0' => '7',
        '#808080' => '8',
        '#ff0000' => '9',
        '#00ff00' => '10',
        '#ffff00' => '11',
        '#0000ff' => '12',
        '#ff00ff' => '13',
        '#00ffff' => '14',
        '#ffffff' => '15',
        '#00005f' => '17',
        '#000087' => '18',
        '#0000af' => '19',
        '#0000d7' => '20',
        '#005f00' => '22',
        '#005f5f' => '23',
        '#005f87' => '24',
        '#005faf' => '25',
        '#005fd7' => '26',
        '#005fff' => '27',
        '#008700' => '28',
        '#00875f' => '29',
        '#008787' => '30',
        '#0087af' => '31',
        '#0087d7' => '32',
        '#0087ff' => '33',
        '#00af00' => '34',
        '#00af5f' => '35',
        '#00af87' => '36',
        '#00afaf' => '37',
        '#00afd7' => '38',
        '#00afff' => '39',
        '#00d700' => '40',
        '#00d75f' => '41',
        '#00d787' => '42',
        '#00d7af' => '43',
        '#00d7d7' => '44',
        '#00d7ff' => '45',
        '#00ff5f' => '47',
        '#00ff87' => '48',
        '#00ffaf' => '49',
        '#00ffd7' => '50',
        '#5f0000' => '52',
        '#5f005f' => '53',
        '#5f0087' => '54',
        '#5f00af' => '55',
        '#5f00d7' => '56',
        '#5f00ff' => '57',
        '#5f5f00' => '58',
        '#5f5f5f' => '59',
        '#5f5f87' => '60',
        '#5f5faf' => '61',
        '#5f5fd7' => '62',
        '#5f5fff' => '63',
        '#5f8700' => '64',
        '#5f875f' => '65',
        '#5f8787' => '66',
        '#5f87af' => '67',
        '#5f87d7' => '68',
        '#5f87ff' => '69',
        '#5faf00' => '70',
        '#5faf5f' => '71',
        '#5faf87' => '72',
        '#5fafaf' => '73',
        '#5fafd7' => '74',
        '#5fafff' => '75',
        '#5fd700' => '76',
        '#5fd75f' => '77',
        '#5fd787' => '78',
        '#5fd7af' => '79',
        '#5fd7d7' => '80',
        '#5fd7ff' => '81',
        '#5fff00' => '82',
        '#5fff5f' => '83',
        '#5fff87' => '84',
        '#5fffaf' => '85',
        '#5fffd7' => '86',
        '#5fffff' => '87',
        '#870000' => '88',
        '#87005f' => '89',
        '#870087' => '90',
        '#8700af' => '91',
        '#8700d7' => '92',
        '#8700ff' => '93',
        '#875f00' => '94',
        '#875f5f' => '95',
        '#875f87' => '96',
        '#875faf' => '97',
        '#875fd7' => '98',
        '#875fff' => '99',
        '#878700' => '100',
        '#87875f' => '101',
        '#878787' => '102',
        '#8787af' => '103',
        '#8787d7' => '104',
        '#8787ff' => '105',
        '#87af00' => '106',
        '#87af5f' => '107',
        '#87af87' => '108',
        '#87afaf' => '109',
        '#87afd7' => '110',
        '#87afff' => '111',
        '#87d700' => '112',
        '#87d75f' => '113',
        '#87d787' => '114',
        '#87d7af' => '115',
        '#87d7d7' => '116',
        '#87d7ff' => '117',
        '#87ff00' => '118',
        '#87ff5f' => '119',
        '#87ff87' => '120',
        '#87ffaf' => '121',
        '#87ffd7' => '122',
        '#87ffff' => '123',
        '#af0000' => '124',
        '#af005f' => '125',
        '#af0087' => '126',
        '#af00af' => '127',
        '#af00d7' => '128',
        '#af00ff' => '129',
        '#af5f00' => '130',
        '#af5f5f' => '131',
        '#af5f87' => '132',
        '#af5faf' => '133',
        '#af5fd7' => '134',
        '#af5fff' => '135',
        '#af8700' => '136',
        '#af875f' => '137',
        '#af8787' => '138',
        '#af87af' => '139',
        '#af87d7' => '140',
        '#af87ff' => '141',
        '#afaf00' => '142',
        '#afaf5f' => '143',
        '#afaf87' => '144',
        '#afafaf' => '145',
        '#afafd7' => '146',
        '#afafff' => '147',
        '#afd700' => '148',
        '#afd75f' => '149',
        '#afd787' => '150',
        '#afd7af' => '151',
        '#afd7d7' => '152',
        '#afd7ff' => '153',
        '#afff00' => '154',
        '#afff5f' => '155',
        '#afff87' => '156',
        '#afffaf' => '157',
        '#afffd7' => '158',
        '#afffff' => '159',
        '#d70000' => '160',
        '#d7005f' => '161',
        '#d70087' => '162',
        '#d700af' => '163',
        '#d700d7' => '164',
        '#d700ff' => '165',
        '#d75f00' => '166',
        '#d75f5f' => '167',
        '#d75f87' => '168',
        '#d75faf' => '169',
        '#d75fd7' => '170',
        '#d75fff' => '171',
        '#d78700' => '172',
        '#d7875f' => '173',
        '#d78787' => '174',
        '#d787af' => '175',
        '#d787d7' => '176',
        '#d787ff' => '177',
        '#d7af00' => '178',
        '#d7af5f' => '179',
        '#d7af87' => '180',
        '#d7afaf' => '181',
        '#d7afd7' => '182',
        '#d7afff' => '183',
        '#d7d700' => '184',
        '#d7d75f' => '185',
        '#d7d787' => '186',
        '#d7d7af' => '187',
        '#d7d7d7' => '188',
        '#d7d7ff' => '189',
        '#d7ff00' => '190',
        '#d7ff5f' => '191',
        '#d7ff87' => '192',
        '#d7ffaf' => '193',
        '#d7ffd7' => '194',
        '#d7ffff' => '195',
        '#ff005f' => '197',
        '#ff0087' => '198',
        '#ff00af' => '199',
        '#ff00d7' => '200',
        '#ff5f00' => '202',
        '#ff5f5f' => '203',
        '#ff5f87' => '204',
        '#ff5faf' => '205',
        '#ff5fd7' => '206',
        '#ff5fff' => '207',
        '#ff8700' => '208',
        '#ff875f' => '209',
        '#ff8787' => '210',
        '#ff87af' => '211',
        '#ff87d7' => '212',
        '#ff87ff' => '213',
        '#ffaf00' => '214',
        '#ffaf5f' => '215',
        '#ffaf87' => '216',
        '#ffafaf' => '217',
        '#ffafd7' => '218',
        '#ffafff' => '219',
        '#ffd700' => '220',
        '#ffd75f' => '221',
        '#ffd787' => '222',
        '#ffd7af' => '223',
        '#ffd7d7' => '224',
        '#ffd7ff' => '225',
        '#ffff5f' => '227',
        '#ffff87' => '228',
        '#ffffaf' => '229',
        '#ffffd7' => '230',
        '#080808' => '232',
        '#121212' => '233',
        '#1c1c1c' => '234',
        '#262626' => '235',
        '#303030' => '236',
        '#3a3a3a' => '237',
        '#444444' => '238',
        '#4e4e4e' => '239',
        '#585858' => '240',
        '#606060' => '241',
        '#666666' => '242',
        '#767676' => '243',
        '#8a8a8a' => '245',
        '#949494' => '246',
        '#9e9e9e' => '247',
        '#a8a8a8' => '248',
        '#b2b2b2' => '249',
        '#bcbcbc' => '250',
        '#c6c6c6' => '251',
        '#d0d0d0' => '252',
        '#dadada' => '253',
        '#e4e4e4' => '254',
        '#eeeeee' => '255',
    ];

    public function getCode(Format $format): string
    {
        if ($format instanceof HexColor) {
            if (!array_key_exists($format->color(), self::XTERM256_CODES)) {
                throw new UnknownFormatException($format);
            }

            $prefix = ($format instanceof HexBgColor) ? self::COLOR256_BG : self::COLOR256;

            return $prefix . self::XTERM256_CODES[$format->color()];
        }

        return match ($format) {
            Style::Bold => self::BOLD,
            Style::Dim => self::DIM,
            Style::Underlined => self::UNDERLINED,

            Color::Default => self::DEFAULT,
            Color::Black => self::BLACK,
            Color::Red => self::RED,
            Color::Green => self::GREEN,
            Color::Yellow => self::YELLOW,
            Color::Blue => self::BLUE,
            Color::Magenta => self::MAGENTA,
            Color::Cyan => self::CYAN,
            Color::LightGray => self::LIGHT_GRAY,
            Color::LightGreen => self::LIGHT_GREEN,
            Color::LightYellow => self::LIGHT_YELLOW,
            Color::LightBlue => self::LIGHT_BLUE,
            Color::LightMagenta => self::LIGHT_MAGENTA,
            Color::LightCyan => self::LIGHT_CYAN,
            Color::LightRed => self::LIGHT_RED,
            Color::White => self::WHITE,

            BgColor::Default => self::DEFAULT_BG,
            BgColor::Black => self::BLACK_BG,
            BgColor::Red => self::RED_BG,
            BgColor::Green => self::GREEN_BG,
            BgColor::Yellow => self::YELLOW_BG,
            BgColor::Blue => self::BLUE_BG,
            BgColor::Magenta => self::MAGENTA_BG,
            BgColor::Cyan => self::CYAN_BG,
            BgColor::LightGray => self::LIGHT_GRAY_BG,
            BgColor::LightGreen => self::LIGHT_GREEN_BG,
            BgColor::LightYellow => self::LIGHT_YELLOW_BG,
            BgColor::LightBlue => self::LIGHT_BLUE_BG,
            BgColor::LightMagenta => self::LIGHT_MAGENTA_BG,
            BgColor::LightCyan => self::LIGHT_CYAN_BG,
            BgColor::LightRed => self::LIGHT_RED_BG,
            BgColor::White => self::WHITE_BG,

            default => throw new Exceptions\UnknownFormatException($format),
        };
    }
}
