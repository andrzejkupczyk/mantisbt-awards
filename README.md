# MantisBT Awards

![PHP requirement](https://img.shields.io/packagist/php-v/webgarden/mantisbt-awards?logo=php&style=flat-square)
![GitHub tag (latest SemVer)](https://img.shields.io/github/v/tag/andrzejkupczyk/mantisbt-awards?sort=semver&style=flat-square)
[![GitHub license](https://img.shields.io/github/license/andrzejkupczyk/mantisbt-awards?style=flat-square)](https://github.com/andrzejkupczyk/mantisbt-awards/blob/main/LICENSE "License")

> An awarded emoji tells a thousand words.

Emoji awards plugin for [Mantis Bug Tracker](https://www.mantisbt.org/).
Allows users to quickly give and receive feedback.

Emojis can be awarded on comments (known as bugnotes or activities), 
but more "awardable" objects will be supported in the near future.

If you find this plugin useful, feel free to try [my other plugins](https://github.com/search?q=user%3Aandrzejkupczyk+topic%3Amantisbt-plugin) as well.

## Installation

MantisBT Awards is packaged with [Composer](https://getcomposer.org/) 
and uses [composer installers](https://github.com/composer/installers) library 
to install the plugin in the `plugins/Awards` directory:

`composer require webgarden/mantisbt-awards`

### Old school alternative

If you prefer to avoid modifying the original MantisBT `composer.json` file, 
you can follow these steps:
- download or clone the repository and place it under the MantisBT plugins folder
- rename the folder to Awards
- cd into plugin's folder and run `composer install --no-dev`

## Screenshots

![Bugnotes](https://user-images.githubusercontent.com/11018286/182019499-145b4319-f39d-4ee5-86c4-e2ae97e80964.png)

## Translations

Supported languages are: :gb: :poland:
