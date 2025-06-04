# SilverCommerce Google Reviews Optin

Adds a custom snippet to the TagManager module that generates a Google Review opt in survey on the order complete page of a SilverCommerce site

If you find this module helpful, please consider supporting our work:

[![GitHub Sponsor](https://img.shields.io/github/sponsors/Dean-Forest-Tech?label=Sponsor&logo=GitHub)](https://github.com/sponsors/Dean-Forest-Tech)

## Install

Install via composer using:

    composer require "dft/silvercommerce-google-reviews-optin"

## Setup

Setup is as follows:

1. You must add a delivery lead time to each postage rate you have. **If you do not do this, the tag will not load***.
2. As with other `TagManager` modules, add a new "Google Reviews Opt In" and then add your Google Analytics ID.

## Usage

Once you add this tag and set your Google Analytics ID, this module will then automatically add a Google reviews opt in servay to the order completion page of the SilverCommerce checkout process.
