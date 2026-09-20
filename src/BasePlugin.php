<?php
namespace OhMyShares\Plugin;

use DateTimeInterface;
use OhMyShares\Plugin\Dtos\History;
use OhMyShares\Plugin\Dtos\Ticker;
use OhMyShares\Plugin\Dtos\Trade;
use OhMyShares\Plugin\Enums\AssetType;

class BasePlugin implements PluginInterface
{
    private mixed $_host = null;

    public function setHost(HostInterface $host): void
    {
        $this->_host = $host;
    }

    public function getHost(): HostInterface
    {
        return $this->_host;
    }

    public function getConfig($key): mixed
    {
        return $this->getHost()->getConfig($key);
    }

    public function getTicker(string $ticker): ?Ticker
    {
        return $this->getHost()->getTicker($ticker);
    }

    /**
     * @return History[]
     */
    public function getHistory(string $ticker, AssetType $assetType,
                               ?DateTimeInterface $dateStart = null,
                               ?DateTimeInterface $dateEnd = null): array
    {
        return $this->getHost()->getHistory($ticker, $assetType, $dateStart, $dateEnd);
    }

    public function log(string $message, string $level = 'info'): void
    {
        $this->getHost()->log($message, $level);
    }

    public function title(): string
    {
        return 'My Plugin';
    }

    public function version(): string
    {
        return '0.0.1';
    }

    public function afterInstall(): void
    {
    }

    public function beforeUninstall(): void
    {
    }

    public function afterLoad(): void
    {
    }

    public function onHistoryRequest(Ticker $ticker,
                                     ?DateTimeInterface $dateStart = null,
                                     ?DateTimeInterface $dateEnd = null): void
    {
    }

    public function onPeriodic(): void
    {
    }

    public function onTradeAdded(Trade $trade): void
    {
    }

    public function onConfigSaved(array $config): void
    {
    }
}
