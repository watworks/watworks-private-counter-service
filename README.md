# Private Service 1 #

> Making infinite loops run faster since 2089!

This service is a PHP app based on the `silex` microframework.  It exposes a tiny api for incrementing numbers.  It's web scale.

## Developing ##

Developers should only need 3 things installed on their local system to work on the project: `docker`, `docker-compose`, and `make`.

A `makefile` serves as the frontend to most of the commands you might need in a dev environment.  It will connect to the `app` container started from `docker-compose up`, and run dev commands from within the container.  Use of the `makefile` isn't required, it's just there for convenience.  If you prefer to use `docker-compose` directly, feel free.

To get started, run the following commands:

    make dev-up
    make deps-install
    make test

Then edit your code and run your tests, and when you want to shut everything down:

    make dev-down