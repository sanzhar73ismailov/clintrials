@ECHO OFF
rem all tests run
rem vendor\bin\phpunit.bat  --testdox

rem vendor\bin\phpunit.bat --testdox  tests\DdlExecutorTest.php
rem vendor\bin\phpunit.bat --testdox  tests\TableValidatorTest.php
rem vendor\bin\phpunit.bat --testdox  tests\DdlValidateTest.php
vendor\bin\phpunit.bat --testdox tests\CreateBackupTest.php