<div align="center">

# Telnyx PHP Conferencing

![Telnyx](logo-dark.png)

A small sample app that covers basic conferences with Telnyx's Call Control API.

</div>

## Documentation & Tutorial

The full documentation and tutorial is available on [developers.telnyx.com](https://developers.telnyx.com/docs/v2/call-control/tutorials/conferencing-demo?lang=php)

## Pre-Reqs

You will need to set up:

* [Telnyx Account](https://telnyx.com/sign-up)
* [Telnyx Phone Number](https://portal.telnyx.com/#/app/numbers/my-numbers) enabled with:
  * [Telnyx Call Control Application](https://portal.telnyx.com/#/app/call-control/applications)
  * [Telnyx Outbound Voice Profile](https://portal.telnyx.com/#/app/outbound-profiles)
* [PHP](https://www.php.net/) installed with [Composer](https://getcomposer.org/)
* Ability to receive webhooks (with something like [ngrok](https://ngrok.com/))

## Usage

The following environmental variables need to be set

| Variable            | Description                                                                  |
|:--------------------|:-----------------------------------------------------------------------------|
| `TELNYX_API_KEY`    | Your [Telnyx API Key](https://portal.telnyx.com/#/app/api-keys)              |
| `TELNYX_PUBLIC_KEY` | Your [Telnyx Public Key](https://portal.telnyx.com/#/app/account/public-key) |

### .env file

This app uses the excellent [phpenv](https://github.com/vlucas/phpdotenv) package to manage environment variables.

Make a copy of [`.env.sample`](./.env.sample) and save as `.env` and update the variables to match your creds.

```
TELNYX_API_KEY=
TELNYX_PUBLIC_KEY=
```

### Callback URLs For Telnyx Call Control Applications

| Callback Type          | URL                        |
|:-----------------------|:---------------------------|
| Inbound Voice Callback | `/Callbacks/Voice/Inbound` |

### Install

Run the following commands to get started

```
composer require slim/slim:^4.0
composer require Telnyx/sdk
composer require slim/http
composer require slim/psr7
php -S localhost:8000 -t public
```


## What You Can Do

# Tutorial


## Code-along
