# Emani Template for Joomla 6

An unofficial community-maintained migration of the Joomla51 **Emani** site template for native use with Joomla 6.

> [!IMPORTANT]
> This repository contains **the template only**. It does not include Joomla51 modules, a quickstart package, demo installation, demo content, sample data, or commercial support.

The companion modules are being migrated and tested separately. They will be published one by one in their own repositories and as separate installable packages when they are ready.

## Compatibility

- Joomla 6.x
- PHP 8.3 or newer recommended
- Designed to run without the Joomla **Behaviour – Backward Compatibility** plugin
- Current template version: **2.1.0**
- Update path intended for an existing Joomla 5 Emani template installation as well as installation on Joomla 6

The current migration has been tested on Joomla 6.1.2 with PHP 8.3. A successful test on one environment does not guarantee compatibility with every hosting configuration, third-party extension, override, or customized Emani installation.

## Installation and updates

1. Create a complete backup of the website and database.
2. Test the package on a staging copy first.
3. Download the Joomla installation ZIP from the repository's **Releases** section. Do not use GitHub's automatically generated “Source code” archive as a Joomla installation package.
4. Install the ZIP through **System → Install → Extensions**.
5. When updating an existing Emani installation, do not uninstall the template first if template styles and assignments must be preserved.
6. Clear Joomla and browser caches, then verify the frontend, template styles, overrides, menus, modules, browser console, and server logs.

This template package does not install, update, or remove any Joomla51 companion module.

## Modules and demo content

No modules are bundled with this repository. In particular, modules such as Layer Slideshow, News, Icons, Image Hover, Inline Icons, Numbers, and Call to Action are separate projects and must be installed independently.

No demo site, quickstart package, database dump, customer configuration, or sample content is included.

## Backward Compatibility plugin

The migration is intended to run natively on Joomla 6 without the **Behaviour – Backward Compatibility** plugin. Other installed extensions may still require that plugin; disabling it is therefore a site-wide decision and should be tested carefully.

## Documentation

Technical migration notes and the completed work packages are available in [`docs/joomla-6-migration.md`](docs/joomla-6-migration.md).

## Disclaimer

**Download, installation, updates, and use are entirely at your own risk.**

This software is provided “as is”, without warranty of any kind. The maintainers are not liable for data loss, downtime, security incidents, incompatibilities, lost revenue, or any other direct or indirect damage. Always keep a tested backup and validate the package on a staging system before using it in production.

## Third-party rights and trademarks

Emani and the original template assets originate from Joomla51. This repository is an independent migration project and is not affiliated with or endorsed by Joomla51 or the Joomla Project.

No additional license grant for third-party code, fonts, images, design assets, trademarks, or other materials is implied by this repository. Users are responsible for confirming that they hold all rights and licenses required for their intended use and redistribution.

Joomla! is a registered trademark of Open Source Matters, Inc.
