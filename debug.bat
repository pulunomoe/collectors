cls
set COLLECTORS_ENV=DEV
php initDB.php
php -S localhost:3000 -t public
