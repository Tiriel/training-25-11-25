# SensioLabs Master Training
## Training project installation

* Clone the repository using `git clone https://github.com/Tiriel/training-17-11-25.git`
* `cd training-17-11-25` to get into the newly created folder
* `symfony composer install` to install the dependencies
* `symfony console secret:set ADMIN_PWD` to create the missing env var, then enter the desired value
* `symfony console doctrine:database:create` to initialize the database
* `symfony console doctrine:migrations:migrate -n` to set up the base schema
* `symfony console doctrine:fixtures:load -n` to load initial data
* `symfony console importmap:install` to install web assets
* `symfony serve` (on mac and linux, add  `-d` for daemon mode) then browse the application
