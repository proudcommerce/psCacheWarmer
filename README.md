psCacheWarmer
============

Automatically calling urls using the xml-sitemap, eg. for cache warming.
Free module for OXID eshop 6.

Features

	- set sitemap url in admin / or cli parameter
	- optional basic auth user/password
	- optional csv logfile

Installation

	composer require proudcommerce/cachewarmer

Usage

	php source/modules/pc/cachewarmer/bin/warmup.php [optional parameter s for shop-id, eg. warmup.php -s 2 -f sitemaps/categories.xml]

Parameters:

    -s: ShopId: -s 2
    -f: Path to separte Sitemap: -f sitemaps/categories.xml
	
Changelog

    2021-03-17  3.1.2   add new new parameter -f for separate File
    2020-09-15  3.1.1   readd ee shopurl fix (missing 3.0.1)
	2020-09-14  3.1.0   add own logger
    2020-08-17  3.0.0   cli only, some improvements
	2020-08-06  2.2.1   fix for OXID 6.2
	2019-07-19  2.2.0   Write Report in a file (PR #3)
	2019-07-17  2.1.0   add error 500 check (PR #2)
	2019-06-26  2.0.0   OXID eShop 6 (PR #1)
	2016-10-12  1.0.1   fix reading sitemap url with user/pass,fix checking sitemap object
	2016-08-25  1.0.0   module release for oxid 4.7, 4.8, 4.9, 4.10

License

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    

Copyright

	ProudCommerce { www.proudcommerce.com }
