# Demo Drupal 10 Site

## Server Requirements

* PHP >= 8.1 
* [Composer](https://getcomposer.org/)
* nodejs >= 18.20.4 LTS 

## Installation for local development

This project has been configured for local development using DDEV and Composer

### Prerequisites

Ensure you have the following installed on your **local** development machine.

* [DDEV](https://ddev.com/)
* [PHP >= 8.1](http://php.net/manual/en/install.php)
* [Install composer globally](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx) on your host machine.


### Local Development Environment Setup

Ensure you have ddev setup on your machine
* https://ddev.com/get-started/

Clone the repo:

```
git clone git@github.com:demo/demo.git && cd demo
```

Install the project with Composer:

```bash
composer install --no-autoloader --ignore-platform-reqs
```

> Note if you are on Windows, or if your host does not meet the same requirements as the project, you may have to use
> `composer install --ignore-platform-reqs --no-autoloader`.

Start ddev:

```bash
ddev start
````

You can run drush for the local environment using the ddev CLI tool.

```bash
ddev drush <command>
```

You can import the database using the ddev cli tool to talk to platform.sh.

```bash
git checkout production
ddev pull platform
```

If you have a tar.gz file from Pantheon (old host), you can run the following command to import that file:

```bash
zcat db.sql.gz | mysql -u demo -p demo
```

You'll want to get a copy of the settings.local.php file from another dev on the project so  the site will load with db and domain settings properly. Put it into:

```bash
<project_root>/web/sites/default
```

```bash
composer install
ddev drush cim -y
ddev drush cr
```

Set up the private directory:

We use the `private/` directory which is outside the `web/` directory for storing sensitive information. The files in
the `private/` directory are not stored in git for security reasons. See the project's local dev setup doc for more
information on populating this directory. The `private/` directory should be set up before the next step of running the
setup script.

#### ERP Integrations

Additional configuration is required to setup an SSH Tunnel to the ERP API server. To setup the connection
first connect to the VPN, and then run the following command to setup the link to the DDS server.

```bash
ddev dds-tunnel
```
Secret settings changes `settings.secret.php` are needed to successfully connect. Please ask the project's lead
developer for the Dev API Port configuration settings.

Once the proxy is setup, you should be able to launch the web ui at https://demo.com:4443/Help.

#### Debug Issues

1. Add has salt in settings.php file can be grabbed from proj channel
2. To remove https redirection add - $config['domain.record.demo_com']['scheme'] = 'http';
3. Install Content Security Policy headers disabled Chrome plugin to load css files with https scheme




## Environments

Unleashed has the following server environments configured:

| Environment        | URL                        | Server    | Name |
| ------------------ |----------------------------| --------- | -------------------------------- |
| **Development**    | <https://demo.dev.com>     | `dev-web-01` | `development`                    |
| **Staging**        | <https://demo.staging.com> | `stg-web-01` | `staging`                        |
| **Production**     | <https://demo.com>         | `prd-*-*` | `production`                     |

## FAQ

### Should I commit the contrib modules I download?

Composer recommends **no**. They provide [argumentation against but also
workrounds if a project decides to do it anyway](https://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md).

### How can I apply patches to downloaded modules?

If you need to apply patches (depending on the project being modified, a pull
request is often a better solution), you can do so with the
[composer-patches](https://github.com/cweagans/composer-patches) plugin.

To add a patch to drupal module foobar insert the patches section in the extra
section of composer.json:
```json
"extra": {
    "patches": {
        "drupal/foobar": {
            "Patch description": "URL to patch"
        }
    }
}
```

## Local Testing

All tests can be run locally using `composer test`.  This will execute the following testing systems:

 - PHPCS
 - PHPUnit
 - PHPUnit
 - Behat
 - Gulp tests

## Additional Information

#### Apache Solr
When using Drupal Search API, the required configuration for the Solr Connector is as follows:
- Server Name: {ANY SERVER NAME}
- Backend: `Solr`
- Solr Connector: `Standard`
- HTTP Protocol: `http`
- Solr Host: (See the "Solr IP" column in the "Environments" table)
- Solr Port: `8983`
- Solr Path: `/`
- Solr Core: `default`

The rest can be left as-is, pending further configuration changes. Updates TBD.
