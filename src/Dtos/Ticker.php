<?php
namespace OhMyShares\Plugin\Dtos;

use OhMyShares\Plugin\Enums\TickerType;

class Ticker
{
    public string $ticker;
    public string $name;
    public ?string $isin = null;
    public string $exchange;
    public ?string $country = null;
    public string $currency;
    public TickerType $type;
    public ?string $nominal = null;
    public ?string $coupon_percent = null;
    public ?string $info_url = null;
    public ?string $disclosure_url = null;
}