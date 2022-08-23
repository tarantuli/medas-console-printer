<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Printer;

use Medas\Console\Formats\{BgColor, Color, Format, Style};
use Medas\ServiceManager\Attributes\Service;

// https://misc.flogisoft.com/bash/tip_colors_and_formatting
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

    public function getCode(Format $format): string
    {
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
            BgColor::White => self::WHITE_BG,

            default => throw new Exceptions\UnknownFormatException($format),
        };
    }
}
