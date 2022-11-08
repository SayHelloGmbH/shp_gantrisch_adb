/*
|---------------------------------------------------------------
| parks.swiss API
| http://angebote.paerke.ch/
|---------------------------------------------------------------
*/



# ---
# About
# ---

This api provides functionality to import and show offers corresponding to your export configuration.
It is a PHP/MySQL api which runs on your own webserver and it is customizable to your own needs.



# ---
# Important notice
# ---
Never change the main api files.
Otherwise you will not be able to update to a newer version!



# ---
# First setup: install api
# ---

1. Download current version from https://angebote.paerke.ch/, page "export"
2. Upload the folder to a public directory on your webserver
3. Create new MySQL database
4. Import database/database.sql
5. Setup your configuration parameters (see below for more instructions)
6. Start first import of your offers, open http://YOUR-DOMAIN/path-to-api/scripts/cron.php
   If possible, start your import via CLI and increase php settings like max_execution_time
   or memory_limit to the maximum value.
7. Create your offer pages with your content management system or PHP environment
   (example.php shows how to use the api)
8. Create and call the cronjob to update offers permanently (see scripts/cron.sh)



# ---
# Upgrade/migrate api
# ---

1. Download latest api from https://angebote.paerke.ch/
2. Replace all api files except your config.php in your api folder
3. Execute the migration automatically, open: http://YOUR-DOMAIN/path-to-api/scripts/migrate.php
4. Check your log if migration was successfully.



# ---
# Configuration
# ---

Before you can use the api, you need to set a few configuration parameters.
Open /config.php
- Set the api Hash Key from your export configuration (https://angebote.paerke.ch/settings).
- Set your park id (you find your park id on https://angebote.paerke.ch/ > «My park»).
- Set the connection parameters for your MySQL database.
- Check all other settings and adapt it to your environment.



# ---
# Expand your custom view
# ---

The classes/ParksView.php file defines the view of the api overview and detail pages.
Take a look on their methods. If you want to overwrite methods or attributes, you can do this
with the custom/MyView.php file. You can find an example in custom/ParksSwissView.php.



# ---
# Templating
# ---

With the api templates, you are free to place the offer informations (placeholders)
in the offer detail page or in the filter.
Use the template/standard/ or create your own template with your own order.
You can find a customised example of www.parks.swiss in the template/parks.swiss folder.



# ---
# Plugins
# ---

Use your favored plugin scripts to show the most beautiful offer detail pages.
For instance, list and show all detail informations in an accordeon plugin as shown on www.parks.swiss.
Take a look at the template/parks.swiss/detail.tpl file.
