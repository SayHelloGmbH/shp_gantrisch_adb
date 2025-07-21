# API – Netzwerk Schweizer Pärke


## Requirements
- PHP >= 7.4 and <= 8.4  
- MariaDB or MySQL database  
- Javascript libraries: 
  - parks.min.css
  - parks.min.js
  - jQuery v3.7.1 (will be deprecated in next API version)
  - jQuery UI v1.13.2 (will be deprecated in next API version)


## Whats this API for?
This API provides functionality to import and display offers according to your export configuration. 
It is a PHP/MySQL API that runs on your own web server and can be customised according to your needs.


## Start
Create an export configuration on https://angebote.paerke.ch/en/settings and get your hash key and park user id.


## ⚠ Important note
**Important:** Never change the core API files. Otherwise you will not be able to update to a newer version!


## Install API
1. Download the latest version from https://angebote.paerke.ch/en/settings
2. Upload the folder to a public directory on your web server
3. Create a new MySQL database
4. Import database/database.sql
5. Set up your configuration parameters in config.php file (see below for further instructions)
6. Speed up your PHP – Increase at least max_execution_time and memory_limit if possible
7. Start the first import of your offers, open [url-to-api]/scripts/cron.php on CLI 
7. Create your offer pages with your content management system or PHP environment (example.php shows how you can use the API)
8. Create and call the cronjob to permanently update offers (see scripts/cron.sh)


## Upgrade the API
1. Download the latest API from https://angebote.paerke.ch/en/settings
2. Follow the instructions by version


## Configuration
Before you can use the API, you need to set some configuration parameters.
Open /config.php:
- Set the API hash key from your export configuration (https://angebote.paerke.ch/settings)
- Set your park ID (you can find your park ID at https://angebote.paerke.ch/ > «My park»)
- Set the connection parameters for your MySQL database
- Check all other settings and adapt them to your environment


## Expand your customised view
The classes/ParksView.php file defines the view of the API overview and detail pages.
Take a look at their methods. If you want to override methods or attributes, you can do this
with the custom/MyView.php file. You can find an example in custom/ParksSwissView.php.


## Templating
With the API templates you can place the offer information (placeholders) freely in the offer details page or in the filter.
Use template/standard/ or create your own template with your own order.
You can find a customised example from www.parks.swiss in the template/parks.swiss folder.


## Plugins
Use your favourite plugin scripts to display the most beautiful offer detail pages.
For example, list and display all the detailed information in an accordion plugin as shown on www.parks.swiss.
Take a look at the template/parks.swiss/detail.tpl file.


## Contact
For more information, please visit [parks.swiss](https://www.parks.swiss).


> **Netzwerk Schweizer Pärke**  
> Monbijoustrasse 61, CH-3007 Bern  
> +41 (0)31 381 10 71  
> [info@parks.swiss](info@parks.swiss)