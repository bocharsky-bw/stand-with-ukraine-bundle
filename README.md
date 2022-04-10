# StandWithUkraineBundle

На русском? Смотри [README.ru.md](README.ru.md)

This bundle help to block content for Russian-speaking users. Why? Good question!
First of all, because almost all Russian media are nit independent anymore and
spread *fake* news to their users about what is happening in the world and even
in their own country.

The initial idea of this bundle is to push Russian-speaking people to *think*!
Everybody has a choice. You can choose different sources in different languages
to know the news from many other independent media around the world, don't limit
yourself with Russian language and Russian media only.

## Features
- Show "StandWithUkraine" banner to show that your website stands united with the people
  of Ukraine.
- Block content for users who have the main language in `Accept-Language` request header
  set to `ru`. Basically, all people who read most of the content in Russian language.
- Block content for users who are trying to get access from Russian IP addresses, i.e.
  accessing the content from Russia. Yes, they still may access content connecting via
  a VPN app, but at least it makes things less convenient.

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
    BW\StandWithUkraineBundle\EventSubscriber\AcceptLanguageSubscriber: ~
```

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
        polite: true
        censorship: true
        
```
