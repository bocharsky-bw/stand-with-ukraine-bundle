# StandWithUkraineBundle

На русском? Смотри [README.ru.md](README.ru.md)

*This bundle helps to block content for Russian-speaking users.* **Why?** Good question!
First of all, because almost all Russian media are not independent anymore and spread
*fake* news to their users about what is happening in the world and even in their own country.

The initial idea of this bundle is to push Russian-speaking people to *think*!
Everybody has a *choice*. You can choose different sources in different languages
to read the news from many other independent media around the world, don't limit
yourself with Russian language and Russian media only.

## Features

Some features this bundle could help you with:

- **Display "StandWithUkraine" banner** to show that your website stands united with the people
  of Ukraine.
- **Block content** for users who have the **preferred language** in `Accept-Language` request
  header set to `ru`. Basically, affects people who read most of the content in Russian language.
  Users would be able to access the content only after changing their preferred language to
  any other language.
- **Block content** for users who are trying to **get access from Russian IP addresses**, i.e.
  accessing the content from Russia. Users would be able to access the content only after
  connecting via a [VPN](https://en.wikipedia.org/wiki/Virtual_private_network) client
  choosing a location different from Russia region there. It makes things less convenient
  probably, but if you're using a good VPN client - you get better security, especially if
  you're connecting from public Wi-Fi spots or do not trust your internet provider.

## Installation

Install with [Composer](https://getcomposer.org/):

```bash
$ composer require bw/stand-with-ukraine-bundle
```

Then, enable the bundle if you don't use Symfony Flex:

```php
// config/bundles.php

return [
    // ...
    BW\StandWithUkraineBundle\StandWithUkraineBundle::class => ['all' => true],
];
```

And activate the event subscriber:

```yaml
# config/services.yaml

services:
    # ...
    BW\StandWithUkraineBundle\EventSubscriber\BannerSubscriber: ~
    BW\StandWithUkraineBundle\EventSubscriber\AcceptLanguageSubscriber: ~
    BW\StandWithUkraineBundle\EventSubscriber\BlockCountrySubscriber: ~
    BW\StandWithUkraineBundle\Twig\AppExtension: ~
    BW\StandWithUkraineBundle\Twig\AppRuntime: ~
```

## Usage

For testing purposes, you can easily simulate some request data to test things manually in
an easy way. To overwrite country code, use `swu_country_code` query parameter, e.g:
https://127.0.0.1:8000/?swu_country_code=ru . Also, you can overwrite preferred language
as well, use `swu_preferred_lang` query parameter, e.g: https://127.0.0.1:8000/?swu_preferred_lang=ru .

## Configuration

```yaml
stand_with_ukraine:
    banner:
        enabled: true
        # TODO Idea to add left/right positions and render small banner there
        position: top # Possible options: "top" ro "bottom". Set to "top" by default  
        target_url: /stand-with-ukraine # Wrap the banner with a link to the given URL
        brand_name: Symfony Demo # The current HTTP host by default
    access:
        block_language: true
        block_country: true
        message_as_link: true # Defaults to "true"
        censorship: true # Defaults to "true"
        polite: true
```

That's it, thanks for using or sharing it! And issues or PRs are always welcome. 
