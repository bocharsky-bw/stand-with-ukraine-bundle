# StandWithUkraineBundle

Speak English? See [README.md](README.md)

TODO Translate the text below into Russian

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

Как установить и использовать этот бандл? Дальше смотри [README.md](README.md#installation)
