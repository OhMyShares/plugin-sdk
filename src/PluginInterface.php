<?php
namespace OhMyShares\Plugin;

use DateTimeInterface;
use OhMyShares\Plugin\Dtos\Ticker;
use OhMyShares\Plugin\Dtos\Trade;

interface PluginInterface
{
    /**
     * Sets the host instance that provides the plugin with access to the system API.
     *
     * Called by the system when loading the plugin, before other lifecycle methods.
     *
     * @param HostInterface $host The host instance.
     */
    public function setHost(HostInterface $host);

    /**
     * Returns the host instance associated with the plugin.
     *
     * @return HostInterface The host instance.
     */
    public function getHost(): HostInterface;

    /**
     * Returns the plugin title displayed in the system UI.
     *
     * @return string The plugin title.
     */
    public function title(): string;

    /**
     * Returns the plugin version (e.g. "1.2.0").
     *
     * @return string The plugin version.
     */
    public function version(): string;

    /**
     * Called after the plugin is installed into the system.
     *
     * Suitable for initial setup: creating tables, setting default
     * configuration, etc.
     */
    public function afterInstall(): void;

    /**
     * Called before the plugin is uninstalled from the system.
     *
     * Suitable for cleaning up data and resources created by the plugin.
     */
    public function beforeUninstall(): void;

    /**
     * Called after the plugin is loaded by the system on every startup.
     *
     * At this point you can perform initialization that does not require
     * installation.
     */
    public function afterLoad(): void;

    /**
     * Called when historical data for a ticker is requested.
     *
     * The plugin may fetch data from an external source and save it
     * via {@see HostInterface::addHistory()}.
     *
     * @param Ticker $ticker The ticker for which history was requested.
     * @param DateTimeInterface|null $dateStart Start of the period (null — no lower bound).
     * @param DateTimeInterface|null $dateEnd End of the period (null — no upper bound).
     */
    public function onHistoryRequest(Ticker $ticker,
                                     ?DateTimeInterface $dateStart = null,
                                     ?DateTimeInterface $dateEnd = null): void;

    /**
     * Called periodically on the system's schedule.
     *
     * Suitable for background tasks: data synchronization, state checks, etc.
     */
    public function onPeriodic(): void;

    /**
     * Called when a new trade is added to the system.
     *
     * @param Trade $trade The added trade.
     */
    public function onTradeAdded(Trade $trade): void;

    /**
     * Called after the plugin configuration is saved.
     *
     * @param array $config The saved configuration values (key => value).
     */
    public function onConfigSaved(array $config): void;
}
