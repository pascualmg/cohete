#!/bin/bash
if ! command -v curl >/dev/null 2>&1
then
    echo "Curl no está instalado. Intentando instalar..."
    # Intenta instalar Curl
    sudo apt-get install curl
fi

# Verificar si Nix está instalado
if command -v nix >/dev/null 2>&1
then
    echo "Nix ya está instalado. No se realizará ninguna acción."
else
    echo "Nix no está instalado. Iniciando la instalación..."
  	curl -L https://nixos.org/nix/install | sh -s -- --daemon
    echo "Instalación de Nix completada. Activando características experimentales..."
    echo "experimental-features : nix-command flakes" >> ~/.config/nix/nix.conf
    echo "Características experimentales activadas."
fi