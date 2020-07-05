cls
set COLLECTORS_ENV=DEV
del collectors_test.db
del public\upload\*.jpg
cmd /C php initDB.php
cmd /C vendor\bin\phpunit tests
cmd /C php seedDB.php
