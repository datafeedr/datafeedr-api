=== Datafeedr API ===

Contributors: datafeedr.com
Tags: import csv, import datafeed, data feed, datafeed, import affiliate products
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 7.4
Requires at least: 3.8
Tested up to: 6.7
Stable tag: 1.3.25

Connect to the Datafeedr API.

== Description ==

> **Important**

> The *Datafeedr API* plugin requires that you have an active Datafeedr API Product subscription. [Purchase Subscription](https://datafeedr.me/pricing).

The Datafeedr API provides access to our database of affiliate products. We have aggregated over 950,000,000 products from over [27,000+ merchants and 35+ affiliate networks](https://datafeedr.me/networks). We have indexed and normalized the product data making it easy for you to search for and find products you want to promote on your website.

The *Datafeedr API* plugin currently integrates with the following plugins:

* [Datafeedr WooCommerce Importer](https://wordpress.org/plugins/datafeedr-woocommerce-importer/)
* [Datafeedr Product Sets](https://wordpress.org/plugins/datafeedr-product-sets/)
* [Datafeedr Comparison Sets](https://wordpress.org/plugins/datafeedr-comparison-sets/)

The *Datafeedr API* plugin provides the interface to connect to the Datafeedr API and perform the following tasks:

* Configure your API connection settings.
* Select affiliate networks you are a member of.
* Select merchants who have approved you.
* Add your affiliate network affiliate IDs.
* Import/export your selection of affiliate networks and/or merchants.
* View your API account usage.

The *Datafeedr API* plugin was built to be extended. The *Datafeedr API* plugin contains its own functions that third party developers can use to connect to the Datafeedr API, make search requests or display an 'advanced search' form. We encourage other developers to build on top of the *Datafeedr API* plugin.

Additionally, we have written plugins that integrate the *Datafeedr API* plugin with WooCommerce.

*For personal-use only. Please contact us if you have any questions.*

**Requirements**

* PHP 7.4 or greater
* MySQL version 5.6 or greater
* [WordPress memory limit of 256 MB or greater](https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP)
* PHP's `CURL` enabled
* WordPress Cron enabled
* [HTTPS support](https://wordpress.org/news/2016/12/moving-toward-ssl/)

== Installation ==

This section describes how to install and configure the plugin:

1. Upload the `datafeedr-api` folder to the `/wp-content/plugins/` directory.
1. Activate the *Datafeedr API* plugin through the 'Plugins' menu in WordPress.
1. Enter your Datafeedr API keys here: WordPress Admin Area > Datafeedr API > Configuration
1. Select the affiliate networks you are a member of here: WordPress Admin Area > Datafeedr API > Networks
1. Select the merchants who have approved you here: WordPress Admin Area > Datafeedr API > Merchants

== Frequently Asked Questions ==

= Where can I get help?  =

Our support area can be found here: [https://datafeedrapi.helpscoutdocs.com/](https://datafeedrapi.helpscoutdocs.com/?utm_campaign=dfrapiplugin&utm_medium=referral&utm_source=wporg). This support area is open to everyone.

== Screenshots ==

1. API key configuration
2. Network selection
3. Merchant selection
4. Account usage overview

== Changelog ==

= 1.3.25 - 2025/02/12 =
* Updated API Version selection to "stable" and "beta" and disabled the "beta" option as it's not currently available.

= 1.3.24 - 2025/02/10 =
* Fixed drop down for Merchant Limit search filter

= 1.3.23 - 2025/02/10 =
* Added support for PriceRunner

= 1.3.21 - 2024/12/17 =
* Added support for GiddyUp network

= 1.3.20 - 2024/11/08 =
* Added support for API version r6 (beta)
* Fixed dynamically calling class properties error message
* Added support for new networks
* Fixed sorting of networks on networks page
* Updated "tested up to" version

= 1.3.19 - 2024/07/05 =
* Fixed "tested up to" version

= 1.3.18 - 2024/07/05 =
* Updated "tested up to" value

= 1.3.17 - 2024/02/29 =
* Updated "tested up to" value

= 1.3.16 - 2023/11/10 =
* Updated "tested up to" value

= 1.3.15 - 2023/05/02 =
* Added public property to `Dfrapi_Account` class.

= 1.3.14 - 2023/05/01 =
* Prevent merchants.js from loading in Customizer view.

= 1.3.13 - 2023/04/26 =
* Added "Stock Quantity" as a search option to the Product Sets search form.

= 1.3.12 - 2023/03/27 =
* Increase network and merchant caching time from one day to one month.

= 1.3.11 - 2023/02/16 =
* Removed outdated "Date Created" sort by options from search form.

= 1.3.10 - 2023/02/08 =
* White space and spelling fixes.

= 1.3.9 - 2023/01/06 =
* Removed `array_multisort()` from Effiliation and Partnerize admin notices because of "inconsistent array sizes" errors.

= 1.3.8 - 2022/11/28 =
* Added support for TimeOne affiliate network.
* Added support for Partnerize Sweden.
* Fixed bug where `Dfrapi_SearchForm::ajaxHandler()` method was returning all merchants when `$value` was empty.

= 1.3.7 - 2022/10/28 =
* Updated "tested up to" values.

= 1.3.6 - 2022/10/21 =
* Added PHP url host to options array in DatafeedrApi constructor to provide better usage analytics.

= 1.3.5 - 2022/08/23 =
* Added support for Partnerize France.
* Added support for Partnerize Denmark.

= 1.3.4 - 2022/07/15 =
* Added SKU (`sku`) as a searchable field in the Product Sets search form.

= 1.3.3 - 2022/07/11 =
* Updated readme.
* Update links to new member's website.

= 1.3.2 - 2022/05/09 =
* Added support for new affiliate network: [Addrevenue](https://addrevenue.io/).

= 1.3.1 - 2022/03/21 =
* Added `dfrapi_get_fields_from_product()` function.
* Added `dfrapi_get_install_plugin_url()` function.
* Added `dfrapi_get_activate_plugin_url()` function.
* Added `dfrapi_plugin_is_installed()` function.
* Added `dfrapi_parse_plugin_path()` function.
* Replaced some conditionals with ternary operator.

= 1.3.0 - 2022/02/16 =
* General code refactor and file rearranging.
* Updated API usage over 90%  notice.
* Updated Missing affiliate IDs notice.
* Updated No networks selected notice.
* Updated No merchants selected notice.
* Added User account data helper functions.
* Removed all functionality from Env file.
* Moved functions and actions into appropriate files.
* Fixed undefined index related to "adservice_mid".
* Removed old, unused action.
* Prevented the selection of Belboon, Effiliation and Partnerize networks if required keys were missing.
* Moved the searchform require to all pages, not just admin.
* Added helper functions for group IDs.
* Added new function that gets affiliate ID by network ID.

= 1.2.17 - 2022/02/10 =
* Fixed bug related to `DFRAPI_DOMAIN` missing.

= 1.2.16 - 2022/02/10 =
* Replaced all occurrences of `DFRAPI_DOMAIN` with `'datafeedr-api'`.
* Replaced references to ``'datafeedr-api/datafeedr-api.php'` with `DFRAPI_BASENAME`.
* Removed `datafeedr-api-v2.php` file and related `Wuwei` code (which wasn't doing anything).
* Moved all code from `datafeedr-api-v1.php` to the main `datafeedr-api.php` file.
* Removed the `zanox_client.php` library and some code related to Zanox.
* Updated Requirements in the readme file.
* Added `Requires PHP: 7.4` to readme.txt header and plugin header.
* Added new `register_activation_hook` to ensure that the Datafeedr API plugin cannot be activated at the Network level on Multisite installs.
* Moved WordPress Version check to `register_activation_hook` instead of a regular action.

= 1.2.15 - 2022/01/25 =
* Fixed bug where `get_option( 'dfrapi_transient_whitelist', [] )` didn't always return an array.

= 1.2.14 - 2022/01/12 =
* Made "matches" the default option in the search drop down menu instead of "contains". issues/668

= 1.2.13 - 2021/12/15 =
* Added new `admin_notices` api.
* Added new error notice if 1,000 or more merchants are selected.
* Added some new helper functions related to merchants.
* Rewrote some of the existing `admin_notices`

= 1.2.12 - 2021/11/29 =
* Added new `getQueryScore()` method to `DatafeedrSearchRequestBase` class.
* Added `score` to API `$response` array.
* Removed [Add All] button from list of Merchants on WordPress Admin Area > Datafeedr API > Merchants page to prevent thousands of merchants being selected inadvertently.
* Updated URLs to Docs and Contact page.
* Fixed missing action links on WordPress Admin Area > Plugins page.

= 1.2.11 - 2021/11/17 =
* Added new "Product ID" Product Set search filter. Now you can search by a specific Product ID.
* Added new "Barcode" Product Set search filter. Now you can search products by EAN, UPC or GTIN.
* Added new "Has Barcode" Product Set search filter. Now you can include or exclude products based on whether or not they have a Barcode (ie. UPC, EAN or GTIN).

= 1.2.10 - 2021/10/14 =
* Added support for a new Nordic-oriented affiliate network: [Adservice](https://www.adservice.com/)

= 1.2.9 - 2021/10/03 =
* Fixed bug where "missing affiliate IDs" error appeared for Partnerize Spain.
* Fixed bug where "missing affiliate IDs" error appeared for Partnerize Ireland.

= 1.2.8 - 2021/07/07 =
* Added support for GoAffPro affiliate network.
* Added support for Digital Advisor affiliate network.

= 1.2.7 - 2021/04/21 =
* Fixed bug where products ending with "." resulted in image ending with double "..".
* Fixed bug where product names which were very long were causing image imports to fail.

= 1.2.6 - 2021/03/23 =
* Fixed bug where image URL file names greater than 255 characters were returning a "failed to open stream: File name too long" resulting in a "mime_type_indeterminable" error. Now temporary file names are truncated at 200 characters. This is only related to temporary file names, not the final file name.

= 1.2.5 - 2021/03/19 =
* Added new "condition" field to Product Set search form.

= 1.2.4 - 2021/03/11 =
* Added `source` and `merchant` as possible query params.

= 1.2.3 - 2021/03/05 =
* Added new In Stock filter to Product Set Search form. :)

= 1.2.2 - 2021/03/03 =
* Complete rewrite of image importer.
* Added support for Jetpack Photon library when importing 'difficult' images and when Jetpack is active.

= 1.2.1 - 2021/02/18 =
* Remove `@` from `dfrapi_api_get_query_param()` function.
* Fixed issue where `dfrapi_request_effiliation_affiliate_ids()` returns WP_Error and was not handled properly.

= 1.2.0 - 2021/02/15 =
* Added new [ActionScheduler](https://wordpress.org/plugins/action-scheduler/) wrapper functions.
* Added more image verification checks to `Datafeedr_Image_Importer::media_sideload_image()` function.

= 1.1.5 - 2021/02/01 =
* Added new Size and Gender search filters to the Product Sets search form.

= 1.1.4 - 2021/01/29 =
* Added Site Health Info

= 1.1.3 - 2021/01/22 =
* Added support for Profitshare

= 1.1.2 - 2021/01/20 =
* Added support for Ukrainian Hryvnia (UAH) currency
* Added "Test API Connection" tool to Tools page.

= 1.1.1 - 2021/01/14 =
* Added `Dfrapi_Cron` class.

= 1.1.0 - 2021/01/11 =
* Added warning to The Affiliate Gateway affiliate ID input area on Networks page if SID is not set.
* Added new `Dfrapi_Price` class.
* Added new `Dfrapi_Currency` class.
* Added helper function to instantiate `Dfrapi_Price` class.
* Added helper function to instantiate `Dfrapi_Currency` class.
* Added more string helper functions.
* Added Material as a searchable field in the Searchform.

= 1.0.125 - 2020/12/29 =
* Prevent Belboon affiliate ID from being entered before Adspace ID is entered.
* Added a price.php file but not active yet.

= 1.0.124 - 2020/12/28 =
* Added support for new style of Belboon links.
* Fixed jQuery handlers like `.click()`, `.keyup()`, `.change()`, `.submit()` to use `.on()` or `.trigger()` methods instead.

= 1.0.123 - 2020/12/15 =
* Added support for Admitad.

= 1.0.122 - 2020/12/01 =
* Fixed issue where `ref_url` was requested for Amazon products.

= 1.0.121 - 2020/11/27 =
* Added product ID as a sort parameter in the search form.

= 1.0.120 - 2020/11/03 =
* Added support for additional Amazon locales
* Removed Zanox Brazil from available option

= 1.0.119 - 2020/10/28 =
* Added link to doc article for 2Performant.

= 1.0.118 - 2020/10/27 =
* Added new network 2Performant (Romania)

= 1.0.117 - 2020/10/12 =
* Fixed some broken network icons.

= 1.0.116 - 2020/10/12 =
* Skipped

= 1.0.115 - 2020/09/10 =
* Optimized unapproved_effiliation_merchants_exist() query.

= 1.0.114 - 2020/09/10 =
* Fixed issue with Effiliation affiliate ID loading.

= 1.0.113 - 2020/07/29 =
* Added support for HUF and RUB currencies.

= 1.0.112 - 2020/04/29 =
* Replacing network group names with group IDs.

= 1.0.111 - 2020/04/19 =
* Fixed bug with Awin and TheAffiliateGateway on Networks page.

= 1.0.110 - 2020/03/30 =
* Fixed link to Rakuten doc.

= 1.0.109 - 2020/03/26 =
* Added support for ADCELL.

= 1.0.108 - 2020/03/11 =
* Added support for Amazon NL.

= 1.0.107 - 2020/02/24 =
* Update Datafeedr API Library to support new [Amazon Product Advertising API 5.0](https://webservices.amazon.com/paapi5/documentation/).
* Requires an HTTPS connection to use Amazon.
* Added color field to search form.

= 1.0.106 - 2019/11/20 =
* Updated Helpscout beacon

= 1.0.105 - 2019/11/12 =
* Updated readme.

= 1.0.104 - 2019/10/15 =
* Added Merchant Limit filter to Search Form.

= 1.0.103 - 2019/08/06 =
* Updated Networks page to support new Partnerize networks.

= 1.0.102 - 2019/05/24 =
* Provided support for The Affiliate Gateway SID field.

= 1.0.101 - 2019/05/21 =
* Added support for The Affiliate Gateway network.
* Added new "_owner_datafeedr" meta key for all images imported via the `datafeedr_import_image()` function. This can include a meta_value of the plugin doing the importing as well.

= 1.0.100 - 2019/05/06 =
* Updated readme.

= 1.0.99 - 2019/04/23 =
* Updated readme.

= 1.0.98 - 2019/04/11 =
* Changed `TRUE` to `true` in `searchform.php` render() function.
* Added a width/height check in the image importer. Handles failed imports like Patagonia images.

= 1.0.97 - 2019/03/23 =
* Fixed missing FlexOffers logo.

= 1.0.96 - 2019/03/22 =
* Made plugin ready for upcoming FlexOffers support.

= 1.0.95 - 2019/02/19 =
* Updated readme.

= 1.0.94 - 2019/02/11 =
* Added Awin API token field so that only approved Awin programs are listed under Awin Merchants on this page: WordPress Admin Area > Datafeedr API > Merchants > Awin [Country]

= 1.0.93 - 2018/12/19 =
* Added support for Australia and Turkey for Amazon API queries.

= 1.0.92 - 2018/12/07 =
* Updated readme.

= 1.0.91 - 2018/07/31 =
* Added `tid` option for Partnerize on Networks page.

= 1.0.90 - 2018/07/30 =
* Changed more "Performance Horizon" references to "Partnerize".

= 1.0.89 - 2018/07/20 =
* Added support for Partnerize.

= 1.0.88 - 2018/07/16 =
* Added new Partnerize logos.
* Deleted old PJN logos.

= 1.0.87 - 2018/07/13 =
* Changed height of network and merchant pop-up modal.

= 1.0.85 - 2018/05/07 =
* Fixed tiny bug in Beacon code.

= 1.0.84 - 2018/05/07 =
* Added HelpScout Beacon to Datafeedr-specific pages for in-app documentation and support.

= 1.0.83 - 2018/04/11 =
* Removed old affiliate network icons and logos.

= 1.0.82 - 2018/04/11 =
* Fixed bug where images could not be imported if their file name was too long. Set limit to 100 characters.

= 1.0.81 - 2018/03/29 =
* Added support for Effiliation affiliate network.

= 1.0.80 - 2018/02/20 =
* Fixed form `action` on networks page.

= 1.0.79 - 2018/02/08 =
* Set API `retry` attempts to 3 if connection to API timesout. (#15364)

= 1.0.78 - 2018/01/19 =
* Added `dfrapi_string_starts_with()` helper function.

= 1.0.77 - 2018/01/17 =
* Updated "Tested up to" value.
* Added README.md for GitHub.

= 1.0.76 - 2018/01/10 =
* Fixed bug related to new dependency class.

= 1.0.75 - 2018/01/10 =
* Made more changes to the `Datafeedr_Plugin_Dependency` class + doc blocks.

= 1.0.74 - 2018/01/09 =
* Changed return values of `Datafeedr_Image_Importer::import()`.
* Removed some public methods from `Datafeedr_Image_Importer`.
* Added new `Datafeedr_Plugin_Dependency` class. Still needs doc blocks.

= 1.0.73 - 2018/01/08 =
* Changed protected method to public.

= 1.0.72 - 2018/01/08 =
* Changed @return doc of `datafeedr_import_image()` function.
* Added new `Datafeedr_Timer` trait.
* Added new methods and filters to `Datafeedr_Image_Importer` class.

= 1.0.71 - 2018/01/05 =
* Added new `Datafeedr_Image_Importer` class which will eventually replace all image importing functionality.
* Added new `datafeedr_import_image()` helper function.

= 1.0.70 - 2017/12/12 =
* Optimized check for incorrect Zanox & PH affiliate IDs.

= 1.0.69 - 2017/12/02 =
* Fixed errors thrown in `dfrapi_api_get_zanox_zmid()` and `dfrapi_api_get_ph_camref()` functions.
* Fixed issue where PerformanceHorizon errors were not being displayed as an admin_notice.

= 1.0.68 - 2017/12/01 =
* Optimize WordPress Admin Area pages so Networks and Merchants aren't queried from cache on every page load.

= 1.0.67 - 2017/11/29 =
* Cleaned up some of the admin nags.

= 1.0.66 - 2017/11/29 =
* Added support for Performance Horizon affiliate network. (#15361)

= 1.0.65 - 2017/11/18 =
* Updated `timeout` to `60` in `DatafeedrApi` options array. (#15382)

= 1.0.64 - 2017/11/18 =
* Updated CSS.

= 1.0.63 - 2017/11/17 =
* Updated instantiation of the `DatafeedrApi` class to accept an `$options` array instead of 3 parameters. (#15251)

= 1.0.62 - 2017/10/25 =
* Optimized query to check if unapproved Zanox merchants exist.
* Added ability to see unapproved Zanox merchants in admin notice.

= 1.0.61 - 2017/10/23 =
* Updated `DatafeedrApi` class with proper docs standard WP code formatting.

= 1.0.60 - 2017/10/18 =
* Added new `dfrapi_get_amazon_associate_tag()` function.
* Added Amazon support to the `dfrapi_url()` function where previously it was handled by child plugins. (#15201)

= 1.0.59 - 2017/10/10 =
* Updated TradeTracker network icons. (#15178)

= 1.0.58 - 2017/10/06 =
* Updated Documentation URLs.

= 1.0.57 - 2017/09/08 =
* Updated AffiliateWindow's name to Awin.

= 1.0.56 - 2017/05/01 =
* Replaced use of `number_format()` with `abs()` in API usage emailer script.

= 1.0.55 - 2017/03/06 =
* Updated Affiliate Window icons. (#14404)

= 1.0.54 - 2017/02/21 =
* Updated datafeedr.php API client. (#14372)
* Replaced all API calls with wp_remote_post(). (#14376)
* Removed "transport" option from WordPress Admin Area > Datafeedr API > Configuration > Datafeedr API Settings page. Replaced with wp_remote_post().

= 1.0.53 - 2017/02/12 =
* Added support for the Malaysian ringgit currency.

= 1.0.52 - 2017/01/21 =
* Fixed another bug introduced in 1.0.49

= 1.0.51 - 2017/01/21 =
* Removed option added in 1.0.49. It caused all sorts of issues.

= 1.0.50 - 2017/01/20 =
* Added support for a Tracking ID for Shopello.

= 1.0.49 - 2016/12/27 =
* Added new option to WordPress Admin Area > Datafeedr API > Configuration page to allow the API to be disabled without having to deactivate the Datafeedr API plugin.

= 1.0.48 - 2016/12/14 =
* Updated Snapdeal logos

= 1.0.47 - 2016/10/24 =
* Modified searchfilter.js to improve speed when searching for merchants on the Merchants page (#13955).

= 1.0.46 - 2016/09/07 =
* Remove Connexity from interface (#13803).

= 1.0.45 - 2016/09/02 =
* Replaced "Current Usage" section on Account page with link a link to the Members /api page.

= 1.0.44 - 2016/09/01 =
* Added support for connexity.com.
* Replaced old Shopello icons with new ones (#13589)

= 1.0.43 - 2016/06/27 =
* Removed 'database_rotation' message. (#13535)

= 1.0.42 - 2016/06/26 =
* Fixed CSS for 16x16 network icons. (#13534)

= 1.0.41 - 2016/06/20 =
* Updated CSS file for new networks.

= 1.0.40 - 2016/06/20 =
* Added support for Snapdeal (#13161)
* Added support for Shopello (#13258)

= 1.0.39 - 2016/05/24 =
* Added new `dfrapi_impression_url()` function. (#13237)

= 1.0.38 - 2016/04/10 =
* Modified search form help text.
* Added support for India, Brazil and Mexico for Amazon API queries.

= 1.0.37 - 2016/04/06 =
* Updated PepperJam logos.

= 1.0.36 - 2016/03/21 =
* Updated links to documentation on how to find your affiliate IDs on the Networks page.
* Updated links to https://members.datafeedr.com/ pages on the Account page.

= 1.0.35 - 2016/02/16 =
* Fixed more new links (see 1.0.34)

= 1.0.34 - 2016/02/16 =
* Changed links to Datafeedr's new support and members sites.

= 1.0.33 - 2016/02/03 =
* Added new function to get Amazon keys. This is related to DFRCS integration.

= 1.0.32 - 2016/02/03 =
* Added a "less than or equal to" and "greater than or equal to" option for the price field filters. (#12217)
* Added CSS to accommodate the new price field filters.
* Added compatibility/support for the upcoming DFRCS plugin. :)

= 1.0.31 - 2016/01/27 =
* Added support for FamilyBlend (previously Affiliate4You).

= 1.0.30 - 2015/10/20 =
* Added "Sort by Date Created Ascending" to API search form. (#11836)
* Added "Sort by Date Created Descending" to API search form. (#11836)
* Added "Sort by Final Price Ascending" to API search form. (#11836)
* Added "Sort by Final Price Descending" to API search form. (#11836)
* Added "Has Direct URL" filter to API search form. (#11836)

= 1.0.29 - 2015/09/21 =
* Added support for negative currency queries. (#11499)
* Added support for product ID queries (not exposed in search form). (#11499)
* Fixed "quirk" where individual products added to a Product Set would throw off pagination when those products were no longer available via the API. Now you can delete those products from the set and pagination works again.
* Updated the datafeedr.php library which fixed a small array issue. (#11346)
* Added new "noimage" image.

= 1.0.28 - 2015/08/12 =
* Added support for new affiliate network Optimise. (#11221)

= 1.0.27 - 2015/07/02 =
* Added new DatafeedrServerError class to the datafeedr.php library file. (#11199)

= 1.0.26 - 2015/06/30 =
* Removed DGM and replaced with APD affiliate network. Added APD network logos.

= 1.0.25 - 2015/04/29 =
* Added cache bypassing functions when getting transients. (#10866)

= 1.0.24 - 2015/04/06 =
* Added new icon to admin menu.
* Added new 128x128 and 256x256 plugin icons.
* Fixed broken URL to admin menu icons that have existed since the beginning of time.

= 1.0.23 =
* Fixed wrong error message when the merchant filter is added to the search form but no merchants are selected.

= 1.0.22 =
* Added support for Brazilian Real, Indian Rupee & Polish Złoty currency symbols.

= 1.0.21 =
* Updated Datafeedr API file.

= 1.0.20 =
* Just updating readme.

= 1.0.19 =
* Added FlipKart support.
* Added Amazon Local US support.

= 1.0.18 =
* Added MyCommerce to css file.
* Added link to docs for MyCommerce.

= 1.0.17 =
* Removed M4N from tags in readme.txt file.
* Changed RegNow to MyCommerce in readme file.
* Added MyCommerce icons.
* Added plugin icon for WordPress 4.0+.

= 1.0.16 =
* Fixed undefined 'tid' index.
* Changed the 'delete cached api data' tool from checkbox to ajax button.
* Display notice if a user has selected a Zanox merchant which has not approved their account. This prevents many extra API requests from being generated. (#9474)
* Removed p tags for nags.
* Added ajax.php file to handle... um... AJAX stuff.
* Add "___MISSING___" to Zanox URLs if affiliate ID is missing.

= 1.0.15 =
* Removed Commission Monster from list of supported affiliate networks.

= 1.0.14 =
* Fixed bug introduced by removing dfrapi_filter_affiliate_id filter in v1.0.12 related to Zanox.

= 1.0.13 =
* Removed BOL from list of supported affiliate networks.
* Changed WP header image.

= 1.0.12 =
* Removed dfrapi_filter_affiliate_id filter.
* Added ability to add tracking ID to outgoing affiliate links.

= 1.0.11 =
* Changed add_option to update_option in upgrade.php file.
* Updated the datafeedr.php API library to deal with 32-bit systems and product IDs.

= 1.0.10 =
* Added upgrade.php file to track version changes.
* Added dfrapi_get_total_products_in_db() function.

= 1.0.9 =
* Fixed issue where searches with duplicates excluded returned a higher 'found' count than really was there. (#8672)

= 1.0.8 =
* Added css and mapper link for docs for Double.net.

= 1.0.7 =
* Added logos for Double.net.

= 1.0.6 =
* Forgot to update version numbers in plugin file.

= 1.0.5 =
* Tweaked search form css.
* Added message for database rotation time between 8:00am and 8:20am GMT.
* Added search form help text.
* Changed some labels in the search form.
* Added support for Affiliator affiliate network.
* Updated Datafeedr API library.

= 1.0.4 =
* Tweaked search form css.
* Changes to a lot of help text on all pages.

= 1.0.3 =
* Changed <title> of Tools page.

= 1.0.2 =
* Edited nag boxes when API requests are 90% of max.
* Removed the 80% API usage email notice.
* Changed the text in the API usage emails.
* Converted emails sent from plain text to HTML.
* Fixed undefined indexes.
* Added "Free" plan to list of available plans.

= 1.0.1 =
* Added utm_campaign parameters to help tab links.

= 1.0.0 =
* Updated "Contributors" and "Author" fields to match WP.org username.
* Added support for AUD, DKK, SEK, NOK, CHF, NZD & TRY currency codes.

= 0.9.8 =
* Added support for Zanox.
* Fixed undefined indexes.

= 0.9.7 =
* Fixed issed related to using Sort by Relevance. #8439
* Fixed "support" links in help tab.
* Updated plugin information.

= 0.9.6 =
* Added if(!class_exists('...')) checks to the Datafeedr Api Client Library.

= 0.9.5 =
* Fixed undefined indexes.
* Added "static" to static methods to meet Strict Standards.

= 0.9.4 =
* Removed letters and characters from 'tagged' version.
* Updated "Tested up to" to 3.8.1

= 0.9-beta-3 =
* Initial release.

== Upgrade Notice ==

*None*

