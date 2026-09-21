<?php
namespace OhMyShares\Plugin\Dtos;

use OhMyShares\Plugin\Enums\AssetType;

class History
{
    public string $ticker;
    public ?string $exchange = null; // null — exchange resolved by the host from the ticker
    public string $date; // YYYY-MM-DD
    public AssetType $assetType;
    public ?float $open = null;
    public ?float $close = null;
    public ?float $high = null;
    public ?float $low = null;
    public ?float $volume = null;
    public ?float $dividends = null;
}