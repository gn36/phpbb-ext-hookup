# phpBB 3.2 Extension - Hookup
This Extension adds the possibility of scheduling meetings with multiple people to your topics. Use this for finding the right date for a meeting very easy or for getting feedback on availability of users on certain dates.

This Extension is intended to be fully backwards compatible to the existing phpBB 3.0.x Mod "Hookup" from the phpBB.de Team for Upgrades, but may include additional features.

Current features:

* Adds a Date / User Matrix to the top of the topics.
* Permissions can be set per forum.
* Users and groups can be invited and receive an email on invitation. When listed, users also receive an email when dates are added. Reminders are reset when the user visits the topic.
* Self-Invitation is supported, allowing users to enter themselves into the planner list, as long as they have access to the topic and are permitted to view the hookup. This is intended for large groups that would otherwise extend the list too much and can be set individually for each hookup.
* User Options include yes, no, and maybe.
* Once a final date is found, the date can be auto-prepended to the topic title and users can be informed by email. The list is hidden and instead a large notice on the final date is displayed above the topic. Final date can be changed if desired.
* Text options are supported
* Compatible to upgrades from phpBB 3.0.x Hookup Mod
* Optional automatic resets and added dates once a week to allow use for weekly meetings.
* Potentially add active dates to the calendars provided by other extensions. I am open to suggestions which extension may be suitable. This will only be done if the extension can still be used without this. Currently, there is a separate extension that enters hookup active dates into hjws calendar extension (hjw/calendar).

Planned features:

* Add a reply post to the topic once the final date is found.


## Installation

### Using the source code from github

Clone into ext/gn36/hookup:

    git clone https://github.com/gn36/phpbb-ext-hookup ext/gn36/hookup

Install the missing dependencies:

	cd ext/gn36/hookup
	php composer.phar install
	
Go to "ACP" > "Customise" > "Extensions" and enable the "Hookup" extension.

### Using a complete install package

Download a complete install package from the latest 3.2.x release and unzip it into ext/gn36/hookup.

Go to "ACP" > "Customise" > "Extensions" and enable the "Hookup" extension.

## Development

If you find a bug, please report it on https://github.com/gn36/phpbb-ext-hookup

You can also suggest new features by filing an issue. Feel free to send a pull request for any existing feature request, but please add a note to the issue that you are doing so. If possible, please also add corresponding unit tests to your PR.

If you wish to contribute a translation, feel free to send a pull request. Your translation will be automatically checked using a translation validator.

## Automated Testing

We use automated unit tests including functional tests to prevent regressions. Check out our travis build below:

master: [![Build Status](https://travis-ci.org/gn36/phpbb-ext-hookup.png?branch=master)](http://travis-ci.org/gn36/phpbb-ext-hookup)

## License

[GPLv2](license.txt)
