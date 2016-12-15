echo off

REM Change directory to Laabs root 
cd /d "%~dp0..\.."

REM Create link to bundle public resources
mklink /d web\public\dependency\html ..\..\..\dependency\html\public

pause