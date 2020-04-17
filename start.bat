@echo off

:: Vérifier qu'une commande a été founie
IF "%1"=="" (
    start "" cmd /c "echo Vous devez fournir une commande a executer (ex: "start.bat docker-run-dev").&echo(&pause"
    exit
)

:: Chemins connus vers vcvarsall, selon les différentes versions de Visual Studio
set VS_2019_FILES[0]=C:\Program Files (x86)\Microsoft Visual Studio\2019\BuildTools\VC\Auxiliary\Build\vcvarsall.bat
set VS_2019_FILES[1]=C:\Program Files (x86)\Microsoft Visual Studio\2019\Community\VC\Auxiliary\Build\vcvarsall.bat
set VS_2019_FILES[2]=C:\Program Files (x86)\Microsoft Visual Studio 14.0\VC\vcvarsall.bat
set VS_2019_FILES[3]=C:\Program Files (x86)\Microsoft Visual Studio 12.0\VC\vcvarsall.bat

:: Trouver un chemin valide
set VS_2019 = "0"
for /F "tokens=2 delims==" %%s in ('set VS_2019_FILES[') do (
    if exist %%s (
        echo %%s
        call "%%s" x86_amd64
        call nmake.exe /F NMakefile %1
        exit
    )
)

start "" cmd /c "echo Les outils de developpement de Visual Studio sont introuvables.&echo(&pause"
exit