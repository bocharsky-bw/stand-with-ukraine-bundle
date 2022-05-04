# StandWithUkraineBundle

[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://vshymanskyy.github.io/StandWithUkraine/)

[![CI](https://github.com/bocharsky-bw/stand-with-ukraine-bundle/actions/workflows/ci.yaml/badge.svg?branch=main)](https://github.com/bocharsky-bw/stand-with-ukraine-bundle/actions/workflows/ci.yaml)

На русском? Смотри [README.ru.md](README.ru.md)

*This bundle provides a built-in StandWithUkraine banner for your Symfony application and
has some features to block content for Russian-speaking users.* **Why?** Great question!
There is a war in Ukraine right now. [You can read more about it here](https://stand-with-ukraine.pp.ua/ToRussianPeople.html#-to-people-of-russia).

The initial idea of this bundle is to *push* Russian-speaking people to *think*!
Almost all Russian media are not independent anymore and spread *fake* news
to their users about what is happening in the world and even in their own country.
Everybody has a *choice*. You can choose different sources in different languages
to read the news from many other independent and trusted media around the world,
don't limit yourself with Russian language and Russian media only.

## Features

Some features included in this bundle:

- **Display "StandWithUkraine" banner** to show that your website stands united with the people
  of Ukraine.
  ![Example of StandWithUkraine banner](https://github.com/bocharsky-bw/stand-with-ukraine-bundle/blob/main/docs/images/banner.png)
- **Block content** for users who have the **preferred language** in `Accept-Language` request
  header set to `ru`. Basically, affects people who read most of the content in Russian language.
  Users would be able to access the content only after changing their preferred language to
  any other language. They still can keep Russian language, but as a secondary one.
  ![Example of access denied page](https://github.com/bocharsky-bw/stand-with-ukraine-bundle/blob/main/docs/images/access-denied.png)
- **Block content** for users who are trying to **get access from Russian IP addresses**, i.e.
  accessing the content from Russia. Users would be able to access the content only after
  connecting via a [VPN](https://en.wikipedia.org/wiki/Virtual_private_network) client
  choosing a location different from Russia region there. It makes things less convenient
  probably, but if you're using a good VPN client - you get better security, especially if
  you're connecting from public Wi-Fi spots or do not trust your internet provider.
  ![Example of access denied page](https://github.com/bocharsky-bw/stand-with-ukraine-bundle/blob/main/docs/images/access-denied.png)

## Demo

Want to see this bundle in action before installing it? Check a little demo:

- https://stand-with-ukraine-bundle.herokuapp.com/
- https://stand-with-ukraine-bundle.herokuapp.com/en/?swu_overwrite_country_code_ru=yes
- https://stand-with-ukraine-bundle.herokuapp.com/en/?swu_overwrite_preferred_lang_ru=yes

Thanks to [Heroku](https://heroku.com/) for hosting it ❤️

## Installation

Install with [Composer](https://getcomposer.org/):

```bash
$ symfony composer require bw/stand-with-ukraine-bundle
```

Then, enable the bundle if you don't use Symfony Flex:

```php
// config/bundles.php

return [
    // ...
    BW\StandWithUkraineBundle\StandWithUkraineBundle::class => ['all' => true],
];
```

And then activate/deactivate event subscribers in its configuration.

## Configuration

Configuration is optional, all options have defaults. But if you want to change it - create
a config file `config/services/stand_with_ukraine.yaml` and tweak it. Below you can find
the full configuration example with the defaults values:

```yaml
# config/packages/stand_with_ukraine.yaml

stand_with_ukraine:
    banner:
        enabled:              true

        # Possition of the banner: "top" or "bottom"
        position:             top

        # Wrap the banner with a link to the given URL
        target_url:           null

        # Will be shown in the banner, HTTP host by default
        brand_name:           null
    ban_language:
        enabled:              true
        use_links:            true
    ban_country:
        enabled:              true
        use_links:            true
```

Or you can see it in your terminal, just run:

```bash
$ symfony console config:dump-reference stand_with_ukraine
```

## Testing

For testing purposes, you can easily simulate bad requests to test things manually
on your website in an easy way. To overwrite the actual country code with `ru`, use
`swu_overwrite_country_code_ru` query parameter, i.e:

[https://127.0.0.1:8000/?swu_overwrite_country_code_ru=yes](https://127.0.0.1:8000/?swu_overwrite_country_code_ru=yes)

Also, you can overwrite preferred language with `ru` as well, use
`swu_overwrite_preferred_lang_ru` query parameter, i.e:

[https://127.0.0.1:8000/?swu_overwrite_preferred_lang_ru=yes](https://127.0.0.1:8000/?swu_overwrite_preferred_lang_ru=yes)

The system will thinks that you're sending requests from a Russian IP address or
with Russian preferred language correspondingly and behave accordingly your configuration.

## That's it!

Thanks for using this bundle! Feel free to create an issue or send a PR if you have any
ideas how to improve it. And if you like it - please, share!  
