cd /d %~dp0
cd /d ../../user_config/
setlocal enabledelayedexpansion

for /f "tokens=*" %%a in ('type batch.config') do (
	set %%a
)
cd /d %~dp0
"%link_PHP_program%"  "..\php\batch_add_friends.php"
pause