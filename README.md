# Prerequires

- PHP 8
- Composer 2

# Installation

- Clone the project

`git clone git@github.com:sapiet/symfony-oc-jobs-watcher.git oc-jobs-watcher`

- Install dependencies

`composer install`

- Create and configure `.env.local` file according to `.env.local.recipe` and check [Google security for sending emails with app passwords](https://myaccount.google.com/signinoptions/two-step-verification)

- Generate Mysql database

`bin/console doctrine:database:create`

- Apply database migrations

`bin/console doctrine:migrations:migrate -n`

# Run

- Add a crontab to watch and notify new jobs

```
* * * * * /path/to/php /path/to/oc-jobs-watcher/bin/console app:watch && /path/to/php /path/to/oc-jobs-watcher/bin/console app:notify
```
