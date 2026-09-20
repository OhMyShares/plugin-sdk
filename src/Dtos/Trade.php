<?php
namespace OhMyShares\Plugin\Dtos;

use OhMyShares\Plugin\Enums\AssetType;
use OhMyShares\Plugin\Enums\TradeType;

class Trade
{
    public TradeType $type;
    public string $ticker;
    public AssetType $assetType;
    public ?float $cost = null;
    public ?float $shares = null;
    public float $sum;
    public float $fee = 0.0;
    public ?string $currency = null;
    public int $timestamp;
    public ?string $notes = null;
    public string $portfolioUid;
}
