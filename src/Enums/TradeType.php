<?php
namespace OhMyShares\Plugin\Enums;

use OhMyShares\Plugin\Traits\EnumToArray;

enum TradeType: int
{
    use EnumToArray;

    case Buy = 1;
    case Sell = 2;
    case Dividend = 3;
    case Fee = 4;
    case Coupon = 5;
    case Taxes = 6;
    case Credit = 7;
    case Debit = 8;
}
