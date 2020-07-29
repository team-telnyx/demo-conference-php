<div align="center">

# Telnyx PHP Conferencing

![Telnyx](logo-dark.png)

A small sample app that covers basic conferences with Telnyx's Call Control API.

</div>

## Documentation & Tutorial

The full documentation and tutorial is available on [developers.telnyx.com](https://developers.telnyx.com/docs/v2/call-control/tutorials/conferencing-demo?lang=php&utm_source=referral&utm_medium=github_referral&utm_campaign=cross-site-link)

## Pre-Reqs

You will need to set up:

* [Telnyx Account](https://telnyx.com/sign-up?utm_source=referral&utm_medium=github_referral&utm_campaign=cross-site-link)
* [Telnyx Phone Number](https://portal.telnyx.com/#/app/numbers/my-numbers?utm_source=referral&utm_medium=github_referral&utm_campaign=cross-site-link) enabled with:
  * [Telnyx Call Control Application](https://portal.telnyx.com/#/app/call-control/applications?utm_source=referral&utm_medium=github_referral&utm_campaign=cross-site-link)
  * [Telnyx Outbound Voice Profile](https://portal.telnyx.com/#/app/outbound-profiles?utm_source=referral&utm_medium=github_referral&utm_campaign=cross-site-link)
* [PHP](https://www.php.net/) installed with [Composer](https://getcomposer.org/)
* Ability to receive webhooks (with something like [ngrok](https://developers.telnyx.com/docs/v2/development/ngrok?utm_source=referral&utm_medium=github_referral&utm_campaign=cross-site-link))

## Usage

The following environmental variables need to be set

| Variable            | Description                                                                                                                                              |
|:--------------------|:---------------------------------------------------------------------------------------------------------------------------------------------------------|
| `TELNYX_API_KEY`    | Your [Telnyx API Key](https://portal.telnyx.com/#/app/api-keys?utm_source=referral&utm_medium=github_referral&utm_campaign=cross-site-link)              |
| `TELNYX_PUBLIC_KEY` | Your [Telnyx Public Key](https://portal.telnyx.com/#/app/account/public-key?utm_source=referral&utm_medium=github_referral&utm_campaign=cross-site-link) |

### .env file

This app uses the excellent [phpenv](https://github.com/vlucas/phpdotenv) package to manage environment variables.

Make a copy of [`.env.sample`](./.env.sample) and save as `.env` and update the variables to match your creds.

```
TELNYX_API_KEY=
TELNYX_PUBLIC_KEY=
```

### Callback URLs For Telnyx Call Control Applications

| Callback Type          | URL                                   |
|:-----------------------|:--------------------------------------|
| Inbound Voice Callback | `{ngrok-url}/Callbacks/Voice/Inbound` |

### Install

Run the following commands to get started

```
git clone https://github.com/team-telnyx/demo-conference-php.git
composer install
```

### Ngrok

This application uses PHP's built in server serving on port 8000. Be sure to route inbound requests to port 8000

```
./ngrok http 8000
```

> Terminal should look _something_ like

```
ngrok by @inconshreveable                                                                                                                               (Ctrl+C to quit)

Session Status                online
Account                       Little Bobby Tables (Plan: Free)
Version                       2.3.35
Region                        United States (us)
Web Interface                 http://127.0.0.1:4040
Forwarding                    http://your-url.ngrok.io -> http://localhost:8000
Forwarding                    https://your-url.ngrok.io -> http://localhost:8000

Connections                   ttl     opn     rt1     rt5     p50     p90
                              0       0       0.00    0.00    0.00    0.00
```

At this point you can point your call control application to generated ngrok URL + path  (Example: `http://{your-url}.ngrok.io/Callbacks/Voice/Inbound`).

### Run

Run the app with the PHP servering on port 8000

```
php -S localhost:8000 -t public
```

### Call your number

You can now call your Telnyx phone number and get dropped into a conference. Any subsequent calls will be added to the existing conference. When the final member leaves the conference, the conference is ended and the next inbound caller will be dropped into a new conference.



