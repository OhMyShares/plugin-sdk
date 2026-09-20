<?php
namespace OhMyShares\Plugin\Enums;

use OhMyShares\Plugin\Traits\EnumToArray;

enum AssetType: int
{
    use EnumToArray;

    case Shares = 1;
    case Bonds = 2;
    case Money = 10;
    case Crypto = 11;
    case Custom = 20;
}