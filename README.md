# Agebotsdatenbank Naturpark Gantrisch

## Description

Plugin für die Ausgabe der Angebote auf der Website vom Naturpark Gantrisch.

## Usage

Install and use as a normal plugin.

## API

The code in the _vendor/parks_api_ subfolder is provided by Schweizer Pärke. Download the current
version [here](https://angebote.paerke.ch/de/settings).

When the plugin is first installed, you will need to manually create the database tables using the
SQL script in _vendor/parks_api/database/database.sql_. You can use WP CLI for this: `wp db import database.sql`.
Then call the API script in the browser (see **Manual Update** below.)

### Version

The current API version is 16. (Installed 15.9.2022)

### Data synchronisation

The cron file _shp_gantrisch_adb/vendor/parks_api/scripts/cron.php_ is called by a custom CRON
task (which is registered when the plugin is activated). This is forcibly set to run at 1 a.m.
every day. The script synchronises the custom database table content with the version from the remote
API.

### Configuration

-   Set the required API hash and the Park ID under _Settings » Angebotsdatenbank_ in WordPress.
-   Get the API hash from the [Export](https://angebote.paerke.ch/de/settings) page at Schweizer Pärke.
-   Get the Park ID from the [Mein Park](https://angebote.paerke.ch/de/settings) page at Schweizer Pärke.

### Manual update

The API synchronisation script can be run manually by calling the URL **DOMAIN**/wp-content/plugins/shp_gantrisch_adb/vendor/parks_api/scripts/cron.php
directly in the browser

## Changelog

### 0.1.0

-   Initial release version with API cron task implemented.

### 0.0.0

-   Initial development version.

## Contributors

-   Mark Howells-Mead (mark@sayhello.ch)

## License

Use this code freely, widely and for free. Provision of this code provides and implies no guarantee.

Please respect the GPL v3 licence, which is available via http://www.gnu.org/licenses/gpl-3.0.html
