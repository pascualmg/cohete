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
  if [ -f /etc/bash.bashrc.backup-before-nix ]
  then
      echo "El archivo /etc/bash.bashrc.backup-before-nix existe."
      read -p "¿Deseas eliminar este archivo? [y/N] " respuesta
      if [[ $respuesta =~ ^[Yy]$ ]]
      then
          sudo rm -f /etc/bash.bashrc.backup-before-nix
          echo "El archivo /etc/bash.bashrc.backup-before-nix ha sido eliminado."
      else
          echo "Abortando la instalación de Nix."
          exit 1
      fi
  fi
    echo "Nix no está instalado. Iniciando la instalación..."
  	curl -L https://nixos.org/nix/install | sh -s -- --daemon
    echo "Instalación de Nix completada. Activando características experimentales..."
    echo "experimental-features : nix-command flakes" >> ~/.config/nix/nix.conf
    echo "Características experimentales activadas."
fi