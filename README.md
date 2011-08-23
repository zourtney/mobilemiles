#MobileMiles (1.0b1)
**Version:** 1.0 beta1

**Author:** zourtney@randomland.net

**Date:** first committed 2011-07-26

##Overview
[MobileMiles](http://github.com/zourtney/mobilemiles) is simple web app for tracking fuel mileage and other statistics straight from your mobile device. Every time you fill up, simply enter the number of gallons pumped and the unit price, tap submit and you're set! Visit the official live version at [gas.randomland.net](http://gas.randomland.net).

##Privacy
This web app uses the [Google Data API](http://code.google.com/apis/gdata/) to communicate with a relatively simple speadsheet stored in _your_ Google Docs account. This means you'll never have to worry about _your_ data being stored on _our_ servers. The hosting server is used solely to serve the PHP interface and act as an intermediary to Google Docs via the [Zend Gdata](http://framework.zend.com/download/gdata) library. Future version may attempt to cut the cord entirely.

##Ok, what does it do for me?
MobileMiles provides a simple HTML interface to the fill-up spreadsheet stored in your Google Docs account. All calculations are performed here. Thus, the spreadsheet can be viewed and altered independently of the MobileMiles web app. The following stats are viewable from the web interface:

* fuel mileage
* trip distance
* fill-up location
* fill-up date and time
* fill-up notes

The "stats" sheet gives you a historical look, calculating these at 30- and 60-day intervals as well as an "all-time" column. These advanced stats are calculated, but not currently displayed:

* pump-price accuracy
* days between fill-ups
* cost per day
* cost per mile

The entry form will autofill most of the form for you with "guesses", saving you time at the pump. The autofilled fields are:

* date and time of fill-up
* "favorite" location (most frequently occuring text value)
* odometer reading estimate (based on last mileage + average trip distance)
* price-per-gallon estimate (uses last fill-up's value)

##License
The source code is licensed under the Apache 2.0 license. You are free to use the code in any manner you see fit. However, if you find this application or its code useful, I'd love to hear about it at zourtney@randomland.net!

##Setup
If you want to use the [live version](http://gas.randomland.net), no setup is required. However, if you wish to run your own instance, you will need the following:

* Web server with PHP (LAMP stack is my preferred method)
* [Zend Framework](http://framework.zend.com/download/current/) ([Gdata API](http://framework.zend.com/download/webservices) is sufficient for everything *except* OAuth integration)
* Create `scripts/oauth_secret.php` with the following `define`s, filling in the secret information in `[]` brackets:
  * `define('OAUTH_COOKIE_EXPIRATION', 60 * 60 * 24 * 30); /* 30 days */`
  * `define('OAUTH_CONSUMER_KEY', '[your oauth url]');`
  * `define('OAUTH_SECRET', '[your oauth secret]');`

##The future (and the present)
I plan to keep this project active so long as I am buying gasoline (read: indefinitely). There are a number of improvements I would like to explore, including geo-location, native app development, and/or HTML5 app deployment. The code is currently "beta". I am testing it in real life and things will inevitably break. Please submit all suggestions and bug reports on [github](http://github.com/zourtney/mobilemiles/issues) or to me at zourtney@randomland.net. Enjoy!