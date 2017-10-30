REM echo off

REM Change directory to Laabs root 
cd /d "%~dp0..\..\.."

REM Create link to bundle public resources
mkdir web\public\dependency
mklink /d web\public\dependency\html ..\..\..\dependency\html\public

pause