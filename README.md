# OhMyShares plugin SDK

A plugin is a small composer package that adds OhMyShares new features.
It lives in its own git repository; admins install it from the *Plugins* page by URL or from the store.

# How to create plugins

## 1. Create the package

```
my-plugin/
├── composer.json
└── src/
    └── Plugin.php
```

`composer.json`:

```json
{
  "name": "acme/my-plugin",
  "description": "Short description shown in the store",
  "version": "1.0.0",
  "license": "MIT",
  "autoload": {
    "psr-4": {"Acme\\MyPlugin\\": "src/"}
  },
  "require": {
    "php": "^8.1",
    "ohmyshares/plugin": "dev-main"
  },
  "extra": {
    "ohmyshares": {
      "class": "Acme\\MyPlugin\\Plugin",
      "title": "My Plugin",
      "config": {
        "api_key": {"type": "string", "label": "API key", "required": true, "secret": true}
      }
    }
  }
}
```

- `name` — must be a valid composer package name; it becomes the plugin id.
- `version` — shown to admins and used to detect upgrades. Bump it on every release.
- `extra.ohmyshares.class` — plugin class. Optional: without it `<first psr-4 namespace>Plugin` is used.
- `extra.ohmyshares.title` — store title. Optional, defaults to the repository name.
- `extra.ohmyshares.config` — settings form rendered for admins (see below). Optional.

Dependencies are installed with `composer install --no-dev` on the host, so keep them minimal.

## 2. Implement the plugin class

Extend `OhMyShares\Plugin\BasePlugin` and override only the hooks you need:

```php
<?php
namespace Acme\MyPlugin;

use DateTimeInterface;
use OhMyShares\Plugin\BasePlugin;
use OhMyShares\Plugin\Dtos\History;
use OhMyShares\Plugin\Dtos\Ticker;
use OhMyShares\Plugin\Enums\AssetType;

class Plugin extends BasePlugin
{
    private MyApiClient $client;

    public function title(): string
    {
        return 'My Plugin';
    }

    public function afterLoad(): void
    {
        $this->client = new MyApiClient((string)$this->getConfig('api_key'));
    }

    public function onHistoryRequest(Ticker $ticker, ?DateTimeInterface $dateStart = null, ?DateTimeInterface $dateEnd = null): void
    {
        foreach ($this->client->candles($ticker->ticker, $dateStart, $dateEnd) as $candle) {
            $history = new History;
            $history->ticker = $ticker->ticker;
            $history->assetType = AssetType::Shares;
            $history->date = $candle->date;   // YYYY-MM-DD
            $history->open = $candle->open;
            $history->close = $candle->close;
            $history->high = $candle->high;
            $history->low = $candle->low;
            $history->volume = $candle->volume;
            $this->getHost()->addHistory($history);
        }
    }
}
```

### Hooks

All hooks are optional — `BasePlugin` provides empty implementations, so override only what you need. The host is attached via `setHost()` before any hook is called.

| Hook                                                                                   | When                                                                                                                                                                                                                                       |
|----------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `afterInstall()`                                                                       | Once, after the plugin is installed or upgraded. Initial setup: imports, default configuration, etc.                                                                                                                                       |
| `beforeUninstall()`                                                                    | Before the plugin is uninstalled and its directory is deleted. Clean up data and resources created by the plugin.                                                                                                                          |
| `afterLoad()`                                                                          | Every time the plugin is loaded. Read config and create clients here, **not in the constructor** — the host is not attached yet.                                                                                                           |
| `onHistoryRequest(Ticker $ticker, ?DateTimeInterface $start, ?DateTimeInterface $end)` | When OhMyShares needs price history for a ticker. Fetch data from your source and save it with `addHistory()`. `null` dates mean an unbounded period.                                                                                      |
| `onPeriodic()`                                                                         | Hourly, for background work: data synchronization, state checks, etc.                                                                                                                                                                      |
| `onTradeAdded(Trade $trade)`                                                           | When a user adds a trade to a portfolio. Receives a `Dtos\Trade` snapshot (type, ticker, asset, cost/shares/sum/fee, currency, timestamp, notes, portfolio uid). Auto-created money/fee records bound to a trade do not trigger this hook. |
| `onConfigSaved(array $config)`                                                         | After the admin saves the plugin config form. Re-read settings and rebuild clients here.                                                                                                                                                   |

The plugin also reports metadata through two required methods: `title()` (name shown in the UI) and `version()` (e.g. `"1.2.0"`).

### Host API

`$this->getHost()` returns an `OhMyShares\Plugin\HostInterface` — the API OhMyShares exposes to plugins:

- `addTicker(Dtos\Ticker $ticker): bool` — create or update a ticker; returns `true` on success.
- `addHistory(Dtos\History $history): bool` — upsert one day of prices/dividends; returns `true` on success.
- `getConfig(string $key): mixed` — value entered by the admin in the plugin config form, or `null` if not set (shortcut: `$this->getConfig($key)`).
- `getTicker(string $ticker): ?Dtos\Ticker` — find a known ticker by symbol or ISIN; returns `null` if unknown (shortcut: `$this->getTicker($ticker)`).
- `getHistory(string $ticker, Enums\AssetType $assetType, ?DateTimeInterface $start = null, ?DateTimeInterface $end = null): array` — price history already stored in OhMyShares, as `Dtos\History[]` ordered by date; `null` dates mean an unbounded period (shortcut: `$this->getHistory(...)`).
- `log(string $message, string $level = 'info'): void` — write to the OhMyShares log; entries are prefixed with the plugin package name. Levels: `info`, `warning`, `error` (shortcut: `$this->log(...)`).

Only fill the `History` fields you know; `null` fields do not overwrite existing values.

### Config schema

Each key in `extra.ohmyshares.config` describes one form field:

```json
"field_name": {
  "type": "string | number | boolean",
  "label": "Human readable label",
  "description": "Optional hint",
  "required": true,
  "secret": true,
  "default": "optional default value"
}
```

`secret` renders a password input. `required` fields are validated on save.
If no schema is declared, admins can still edit the config as raw JSON.

## 3. Test locally

Point an OhMyShares instance to your working copy: on the *Plugins* page paste the absolute path
to the plugin directory (e.g. `/var/www/plugins/my-plugin`) into *Repository URL*. Commit your changes,
then press *Upgrade* on the plugin card to pull them in.

Exceptions thrown from hooks are caught and written to the OhMyShares log; a plugin that fails
to load is shown with an error message on the *Plugins* page.

## 4. Publish

1. Push the repository to GitHub (or any git hosting with https access).
2. Add the **`ohmyshares-plugin`** topic to the GitHub repository. That's it — the plugin appears
   in every OhMyShares store within an hour (admins can press *Refresh* to see it immediately).
3. Tag releases (`git tag v1.0.0`). Admins can pin a version by installing `https://github.com/acme/my-plugin#v1.0.0`.

Not on GitHub? Ask an OhMyShares admin to add your `registry.json` to `PLUGINS_REGISTRIES`, or share the
repository URL for manual installation:

```json
{
  "plugins": [
    {
      "name": "acme/my-plugin",
      "title": "My Plugin",
      "description": "Short description",
      "url": "https://gitlab.com/acme/my-plugin",
      "version": "1.0.0"
    }
  ]
}
```

## Checklist

- [ ] `composer.json` has `name`, `version`, `autoload.psr-4` and `extra.ohmyshares.class`
- [ ] class extends `BasePlugin`, config is read in `afterLoad()`
- [ ] `composer install --no-dev` succeeds from a clean clone
- [ ] repository has the `ohmyshares-plugin` topic and a release tag
