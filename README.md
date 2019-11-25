# Vertretungsplan

## Disclaimer

This project was created as part of a [BeLL](https://de.wikipedia.org/wiki/Besondere_Lernleistung) in 2017.
I put it here for the sake of enlightening others doing similar work. This is not maintained nor do I plan to in the near future.

## Installation

Follow the steps below to install the application.

## Dependencies

* `php7.1`
* `php7.1-mysql`
* `php7.1-mbstring`
* `php7.1-xml`


* `mysql-server`

## Dev Dependencies

* `php7.1-zip` or `unzip`
* `composer`
* `npm`
* `bower`
* `polymer-cli`

## PHP

Since the Debian sources do not contain PHP 7.1 we have to use a PPA or compile them by hand. 

```
sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
sudo sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
sudo apt update
```

After that we should be able to install the required PHP dependencies.

```
sudo apt install php7.1 php7.1-mysql php7.1-mbstring php 7.1-xml
```

## MySQL

Install MySQL from the official sources.

```
sudo apt install mysql-server
```

Create an empty table.

## Composer

To install composer globally run the following commands.

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
After running the installer you can run the following to move composer.phar to a directory that is in your path.

```
mv composer.phar /usr/local/bin/composer
```

Now just run ```composer``` in order to run Composer instead of ```php composer.phar```.

## NodeJS & npm

```
curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
sudo apt-get install -y nodejs
```

To prevent permission problems we have to change npm's default directory.

```
mkdir ~/.npm-global
npm config set prefix '~/.npm-global'
```

Now we have to add the new directory to our PATH by adding the following to ```~/.profile```.

```
PATH="$HOME/.npm-global/bin:$PATH"
```

Update the system variables by running ```source ~/.profile```.

## Bower & polymer-cli

The npm packages ```bower``` and ```polymer-cli``` are required to build the client application. Install them by running the following.
```
npm install -g bower
npm install -g polymer-cli
```

## Deployment

Follow the readme's inside `api` and schedule` to deploy the application.