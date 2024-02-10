#!/bin/bash

# Stop and remove the Nix daemon services
sudo launchctl unload /Library/LaunchDaemons/org.nixos.nix-daemon.plist
sudo rm /Library/LaunchDaemons/org.nixos.nix-daemon.plist
sudo launchctl unload /Library/LaunchDaemons/org.nixos.darwin-store.plist
sudo rm /Library/LaunchDaemons/org.nixos.darwin-store.plist

# Remove the nixbld group and the _nixbuildN users
sudo dscl . -delete /Groups/nixbld
for u in $(sudo dscl . -list /Users | grep _nixbld); do sudo dscl . -delete /Users/$u; done

# Edit fstab using sudo vifs to remove the line mounting the Nix Store volume
sudo vifs -d

# Edit /etc/synthetic.conf to remove the nix line
sudo sed -i '' '/nix/d' /etc/synthetic.conf

# Remove the files Nix added to your system
sudo rm -rf /etc/nix /var/root/.nix-profile /var/root/.nix-defexpr /var/root/.nix-channels ~/.nix-profile ~/.nix-defexpr ~/.nix-channels

# Remove the Nix Store volume
sudo diskutil apfs deleteVolume /nix

echo "Nix has been uninstalled successfully."

