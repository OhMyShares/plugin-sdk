<?php
namespace OhMyShares\Plugin\Enums;

use OhMyShares\Plugin\Traits\EnumToArray;

enum TickerType: int
{
    use EnumToArray;

    case Stock = 1;
    case ETF = 2;
    case Future = 3;
    case Currency = 4;
    case Index = 5;
    case MutualFond = 6;
    case Bond = 7;
    case Crypto = 8;
}