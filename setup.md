---
layout: page
title: Setup
permalink: /setup/
---

This guide will help you set up your own instance of the MobileMiles server and UI. It is roughly based on [this tutorial](https://gorails.com/deploy/ubuntu/14.04) and should take 1 - 2 hours to complete, depending on your level of expertise.

What you'll need
----------------

I'll be using [Ubuntu 14.04 LTS server](http://www.ubuntu.com/download/server) for this tutorial. Use whatever works best for you, and if you have any suggestions, shoot them to me at [{{ site.email }}](mailto:{{ site.email }}).

You will need the following packages installed:

- [git](http://www.git-scm.com/)
- [Node.js](http://nodejs.org/) (0.10.x recommended)
- [Ruby](https://www.ruby-lang.org/en/) (2.x recommended)
- Ruby's [bundler gem](http://bundler.io/)
- [Nginx](http://nginx.org/)
- [Phusion Passenger](https://www.phusionpassenger.com/)
- [PostgreSQL](http://www.postgresql.org/) (9.3.x recommended)

Install dependencies
--------------------

On Ubuntu systems, you can install these dependencies with `apt-get`. Let's start by making sure everything is up to date:

    sudo apt-get update

### Git

Install git. This will be used to grab source code from GitHub.

    sudo apt-get install git

### Node.js

Now install Node.js its package manager (npm). This will be used to build the UI source code:

    sudo apt-get install nodejs npm

### Ruby

Next, install [rvm](https://rvm.io/), a version manager for Ruby and default to using Ruby 2.1.2. This will be used to run the back-end code:

    \curl -sSL https://get.rvm.io | bash
    source ~/.rvm/scripts/rvm
    echo "source ~/.rvm/scripts/rvm" >> ~/.bashrc
    
    rvm install 2.1.2
    rvm use 2.1.2 --default

### Nginx and Phusion Passenger

Next we need to install Nginx and Phusion Passenger to actually serve the MobileMiles front- and back-end code. The following steps were mostly taken from [here](https://www.phusionpassenger.com/documentation/Users%20guide%20Nginx.html#install_on_debian_ubuntu).

Start by storing the `apt-key` needed for fetching Phusion Passenger:

    sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 561F9B9CAC40B2F7
    sudo apt-get install apt-transport-https ca-certificates

Then create a new file `/etc/apt/sources.list.d/passenger.list` and add:

    deb https://oss-binaries.phusionpassenger.com/apt/passenger trusty main

Set permissions and update `apt-get` again:

    sudo chown root: /etc/apt/sources.list.d/passenger.list
    sudo chmod 600 /etc/apt/sources.list.d/passenger.list
    sudo apt-get update

Now install Phusion Passenger and Nginx

    sudo apt-get install nginx-extras passenger

Uncomment the `passenger_root` and `passenger_ruby` lines from `/etc/nginx/nginx.conf`. (Note: `passenger_ruby` should be set to the output of `which ruby`)

    passenger_root /usr/lib/ruby/vendor_ruby/phusion_passenger/locations.ini;
    passenger_ruby /home/[username]/.rvm/wrappers/ruby-2.1.2/ruby;

Start it up!

    sudo service nginx restart

Navigate your server's IP address in a browser. You should see a message that says "Welcome to nginx!".

### PostgreSQL

Install Postges. This is the database where all MobileMiles data gets stored.

    sudo apt-get install postgresql postgresql-contrib libpq-dev


Download the source code
------------------------

Start by making a top-level directory for all MobileMiles source code.

    sudo mkdir /opt/mobilemiles

### Server

Download the server code into a convenient location. I'll be using `/opt/mobilemiles/server`.

    sudo git clone https://github.com/zourtney/mobilemiles.git /opt/mobilemiles/server

Install dependent gems. This may take a few minutes.

    cd /opt/mobilemiles/server
    bundle install

Next, generate this instance's `SECRET_KEY_BASE` by running:

    rake secret

Persist it by adding the following line to `~/.rvm/environments/ruby-2.1.2`.

    export SECRET_KEY_BASE='[output of rake secret]'

Edit `/etc/nginx/sites-available/default`, adding the following lines that host the server code:

    server {
      listen 8000;
      server_name mobilemiles.local;
      root /opt/mobilemiles/server/public;
      passenger_enabled on;
    }

Reboot.



References
----------

- https://gorails.com/deploy/ubuntu/14.04
- https://www.digitalocean.com/community/tutorials/how-to-install-and-use-postgresql-on-ubuntu-14-04
- https://rvm.io/integration/passenger
- https://www.phusionpassenger.com/documentation/Users%20guide%20Nginx.html
- https://groups.google.com/forum/#!topic/rubyversionmanager/LF2DxnpSlQU