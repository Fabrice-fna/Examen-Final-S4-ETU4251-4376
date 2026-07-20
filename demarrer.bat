@echo off
title MobileMoney - Demarrage
echo =====================================================
echo   MobileMoney - Simulation d'un operateur mobile money
echo =====================================================
echo.

where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERREUR] PHP n'a pas ete trouve dans le PATH Windows.
    echo.
    echo Installez PHP ^(par exemple via XAMPP : https://www.apachefriends.org/^)
    echo puis ajoutez le dossier PHP a votre variable d'environnement PATH.
    echo Voir LISEZMOI.txt pour plus de details.
    echo.
    pause
    exit /b 1
)

if not exist writable\database.db (
    echo Initialisation de la base de donnees SQLite ...
    php -r "$pdo = new PDO('sqlite:writable/database.db'); $sql = file_get_contents('base.sql'); $pdo->exec($sql); echo 'Base de donnees creee avec succes.' . PHP_EOL;"
    echo.
)

echo Choisissez la methode de demarrage :
echo.
echo   1 - Serveur PHP integre ^(php -S^) sur le port 8080
echo   2 - CodeIgniter Spark ^(php spark serve^) sur le port 8080
echo   3 - Serveur PHP integre sur le port 8000
echo.
set /p choix="Votre choix (1, 2 ou 3) : "

if "%choix%"=="2" (
    echo.
    echo Demarrage avec php spark serve sur http://127.0.0.1:8080
    echo.
    echo   - Espace client   : http://127.0.0.1:8080/
    echo   - Espace operateur: http://127.0.0.1:8080/admin  ^(admin / admin123^)
    echo.
    echo Laissez cette fenetre ouverte. Fermez-la pour arreter le serveur.
    echo.
    php spark serve
    pause
    exit /b
)

if "%choix%"=="3" (
    echo.
    echo Demarrage du serveur PHP integre sur http://127.0.0.1:8000
    echo.
    echo   - Espace client   : http://127.0.0.1:8000/
    echo   - Espace operateur: http://127.0.0.1:8000/admin  ^(admin / admin123^)
    echo.
    echo Laissez cette fenetre ouverte. Fermez-la pour arreter le serveur.
    echo.
    echo [INFO] N'oubliez pas de modifier app.baseURL dans .env ou app/Config/App.php
    echo.
    php -S 127.0.0.1:8000 -t public
    pause
    exit /b
)

echo.
echo Demarrage du serveur PHP integre sur http://127.0.0.1:8080
echo.
echo   - Espace client   : http://127.0.0.1:8080/
echo   - Espace operateur: http://127.0.0.1:8080/admin  ^(admin / admin123^)
echo.
echo Laissez cette fenetre ouverte. Fermez-la pour arreter le serveur.
echo.

php -S 127.0.0.1:8080 -t public
pause
