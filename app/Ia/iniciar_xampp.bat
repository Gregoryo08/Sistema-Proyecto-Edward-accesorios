@echo off
cd /d "%~dp0"
title IA Edward - Modulo XAMPP

python --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ============================================================
    echo [ERROR] Python no esta instalado o no se agrego al PATH.
    echo Descargalo desde https://www.python.org/
    echo IMPORTANTE: Marca la casilla "Add Python to PATH" al instalar.
    echo ============================================================
    pause
    exit /b
)

echo Verificando e instalando librerias requeridas...
pip install numpy scikit-fuzzy flask flask-cors mysql-connector-python

echo.
echo Iniciando IA Edward en entorno XAMPP...
set DB_PORT=3306
set DB_PASS=
python ia_edward.py
pause