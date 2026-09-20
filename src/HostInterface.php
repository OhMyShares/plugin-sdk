<?php
namespace OhMyShares\Plugin;

use DateTimeInterface;
use OhMyShares\Plugin\Dtos\History;
use OhMyShares\Plugin\Dtos\Ticker;
use OhMyShares\Plugin\Enums\AssetType;

interface HostInterface
{
    /**
     * Adds a ticker to the system.
     *
     * @param Ticker $ticker The ticker to add.
     * @return bool true if the ticker was added successfully, false otherwise.
     */
    public function addTicker(Ticker $ticker): bool;

    /**
     * Adds historical data to the system.
     *
     * @param History $history The historical data record.
     * @return bool true if the data was added successfully, false otherwise.
     */
    public function addHistory(History $history): bool;

    /**
     * Returns a plugin configuration value.
     *
     * @param string $key The configuration key.
     * @return mixed The configuration value, or null if not set.
     */
    public function getConfig($key): mixed;

    /**
     * Returns a ticker by its string identifier.
     *
     * @param string $ticker The ticker symbol (e.g. "AAPL").
     * @return Ticker|null The ticker, or null if it does not exist.
     */
    public function getTicker(string $ticker): ?Ticker;

    /**
     * Returns historical data for a ticker over the given period.
     *
     * @param string $ticker The ticker symbol.
     * @param AssetType $assetType The asset type.
     * @param DateTimeInterface|null $dateStart Start of the period (null — from the very beginning).
     * @param DateTimeInterface|null $dateEnd End of the period (null — up to now).
     * @return History[] The list of historical data records.
     */
    public function getHistory(string $ticker, AssetType $assetType,
                               ?DateTimeInterface $dateStart = null,
                               ?DateTimeInterface $dateEnd = null): array;

    /**
     * Writes a message to the system log.
     *
     * @param string $message The message text.
     * @param string $level The severity level (e.g. "info", "warning", "error").
     */
    public function log(string $message, string $level = 'info'): void;
}
