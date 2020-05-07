# piHoleDNSAdmin
pi-hole admin page that allows the user to manage a local Domain Name Server.

# Installation
Steps
1. Install pi-hole: [One-Step Automated Install](https://github.com/pi-hole/pi-hole/#one-step-automated-install) 
2. Setup pi-hole to a Lan DNS server: [HOWTO: Using pi-hole as LAN DNS server](https://discourse.pi-hole.net/t/howto-using-pi-hole-as-lan-dns-server/533)
3. Clone this repository. Login to the Raspberry Pi with sudo rights and run: 
```
  $ git clone https://github.com/stuartsimpson/piHoleDNSAdmin.git
```
4. run the setup script. This script will copy the neccesary files from the project to the pi-hole/AdminLTE location (/var/www/html/admin).:
```
  $ cd piHoleDNSAdmin
  $ sudo sh ./deploy.sh
```

5. edit the /var/www/htlm/admin/scripts/ip-hole/php/header.php file and add the following code after the <!-- Network --> section
```php
                <!-- Local DNS -->
                <li<?php if($scriptname === "localDNS.php"){ ?> class="active"<?php } ?>>
                    <a href="localDNS.php">
                          <i class="fa fa-network-wired"></i> <span>Local DNS</span>
                    </a>
                </li>
  ```
  after the edit it should look like this:
  ```php
                <!-- Network -->
                <li<?php if($scriptname === "network.php"){ ?> class="active"<?php } ?>>
                    <a href="network.php">
                        <i class="fa fa-network-wired"></i> <span>Network</span>
                    </a>
                </li>
                <!-- Local DNS -->
                <li<?php if($scriptname === "localDNS.php"){ ?> class="active"<?php } ?>>
                    <a href="localDNS.php">
                          <i class="fa fa-network-wired"></i> <span>Local DNS</span>
                    </a>
                </li>
```
6. change the permissions on the /etc/ip-hole/<domain>.list file configured in Step 2. At the time of creating this I had not come up with a better solution than changing the permissions of the file.  If anyone has a good way to fix this please let me know by logging an issue.
```
  sudo chmod 666 /etc/pi-hole/<domain>.list
```
  
