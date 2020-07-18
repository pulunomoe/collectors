cls
set COLLECTORS_ENV=PROD
php initDB.php
php -S localhost:3000 -t public
