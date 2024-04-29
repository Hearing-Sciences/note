# Using Vagrant to provide a test server

Rather than setting up Apache or Nginx manually to create a server, you can deploy and provision a disposable server as a virtual machine on your PC. It's a great deal easier because all of the configuration is done for you.

You will need the following:

* Admin privileges on your computer
* At least 8GB RAM. Most Windows or Linux PCs and most Apply computers sold in the last ten years should have this.
* About 1GB free hard disk space (if you don't have this, you probably need a new hard disk)
* Oracle VirtualBox
  * Available from [https://www.virtualbox.org/wiki/Downloads](https://www.virtualbox.org/wiki/Downloads)
  * There are versions for Windows, Linux and macOS hosts. The macOS version is for Intel CPUs but may work with the newer M-series CPUs. There is a developer preview for ARM-based Macs available on the [https://www.virtualbox.org/wiki/Testbuilds](Test builds page) which may work but may also have issues.
* The latest version of Vagrant
  * Available from [https://www.vagrantup.com/downloads](https://www.vagrantup.com/downloads)
  * Get the *Amd64* version for Windows or the ARM64 version for macOS.
* A zip download of the NOTE files
  * Available from [https://github.com/Hearing-Sciences/note/archive/refs/heads/master.zip](https://github.com/Hearing-Sciences/note/archive/refs/heads/master.zip)
  * If you don't have access to the GitHub site, ask someone to provide the zip archive.

## Installation on Windows

**I only have access to a Windows PC so cannot provide instructions for Linux or macOS installations.** If the situation changes I will update this page.

First, install VirtualBox. Accept all of the defaults and install everything offered. You might be asked to restart your PC. Don't do it yet; wait until Vagrant has also been installed.

(There is an *extras* package available for VirtualBox. It os offered through a personal use and evaluation licence (PUEL) but is **not** required.)

Next, install Vagrant. Again, accept all of the defaults and reboot when asked. Rebooting should add vagrant to the command line which will be vital later on, so don't skip the reboot.

Once you are logged back into your PC, create a folder on C: called NOTE. This must be at the root of the C: drive i.e. not in any other folders.

(You can use any other folder for your installation of NOTE but please remember to refer to your chosen folder in later steps.)

Unzip the contents of the NOTE zip archive to C:\NOTE. Don't copy the  *master* folder itself, just the contents. When you're done you should see the following files:

* C:\NOTE\index.php
* C:\NOTE\utils.php
* c:\NOTE\server-config
* etc.

Open up the command prompt as an administrator. To do this, click the start button, type in *cmd* and look for an option *Run as administrator* on the right.

Run the following commands:

```cmd
cd c:\NOTE\server-config
vagrant up
```

These two commands change the working directory to the *server-config* directory and then start Vagrant.

Vagrant does the following:

* Initialises itself
* Downloads a Ubuntu Linux server image from the Vagrant website. This may take several minutes depending on your internet connection.
* Starts the server using Virtualbox
* Updates the server packages
* Installs extra packages:
  * git
  * apache2
  * php
  * sqlite
  * php-sqlite3
* Copies the Apache configuration file to the server
* Enables the Apache rewrite module
* (Re)starts the Apache web server

At this point you should be able to access the website using a browser and the link [http://localhost:5000](http://localhost:5000).

## Shared folders

The server itself is difficult to access. This is intentional: we want it to be disposable and just get on with things. NOTE uses ```C:\NOTE\modules``` as the place to look for surveys so create them there. The modules folder is 'outside' the server and shared, so it's protected from the server being shut down or deleted.

## Starting up, shutting down, deleting

In the future you can start the server using the command prompt as an administrator:

```cmd
cd c:\NOTE\server-config
vagrant up
```

The command ```vagrant halt``` shuts down the server. ```vagrant destroy``` deletes the server completely (but not the shared folders).

## Things that could go wrong

### Failure to start after an upgrade

If you are upgrading the version of the server you might see messages like this:

```txt
VirtualBox error:

VBoxManage.exe: error: Could not rename the directory 'C:\Users\<username>\VirtualBox VMs\ubuntu-jammy-22.04-cloudimg-20230317_1679477845950_29553' to 'C:\Users\<username>\VirtualBox VMs\NOTE-SERVER' to save the settings file (VERR_ALREADY_EXISTS)
VBoxManage.exe: error: Details: code E_FAIL (0x80004005), component SessionMachine, interface IMachine, callee IUnknown
VBoxManage.exe: error: Context: "SaveSettings()" at line 3641 of file VBoxManageModifyVM.cpp
```

The solution is to delete the folder `C:\Users\<username>\VirtualBox VMs\NOTE-SERVER` and re-run `vagrant up`.

## Advanced - sharing other folders and cloud synchronisation

You might not want to keep your only copy of surveys on the C:\ drive in case something happens to your computer. If you want to put them somewhere else, here's what to do. This is advanced configuration so expect a bit of trial and error. I'm going to assume that you will want to store them in OneDrive and sync them to your PC but any cloud provider should be fine.

* Create a folder on OneDrive called *modules* and sync it to your PC. Look for the folder in Windows Explorer and make a note of the location.
* Look in *C:\NOTE\server-config* and open the file called *vagrantfile* in Notepad or Notepad++ (or text editor of your choice).
* Look for the following lines:

```ruby
  config.vm.synced_folder "C:/NOTE", "/var/www/html/note/",
    owner: "www-data", group: "www-data", mount_options: ["dmode=775,fmode=664"]
```

* Copy these two lines and paste them immediately after. Then change them to the following:

```ruby
  config.vm.synced_folder "<The modules folder you created earlier>", "/var/www/html/note/modules",
    owner: "www-data", group: "www-data", mount_options: ["dmode=775,fmode=664"]
```

* There are two changes:
  * change *C:\NOTE* to the folder you created earlier.
  * **Very important:** replace any backslashes with either forward slashes or double-backslashes (i.e. change \ to / or \\\\)
  * change */var/www/html/note* to */var/www/html/note/modules*

So the updated section should now look something like this:

```ruby
  config.vm.synced_folder "C:/NOTE", "/var/www/html/note/",
    owner: "www-data", group: "www-data", mount_options: ["dmode=775,fmode=664"]
  config.vm.synced_folder "<The modules folder you created earlier using forward slashes or double-backslashes>", "/var/www/html/note/modules",
    owner: "www-data", group: "www-data", mount_options: ["dmode=775,fmode=664"]
```

With your changes saved, go back to the command prompt and type in:

```ruby
cd c:\NOTE\server-config
vagrant reload
```

This should reload the server and update the shared folders.

## Advanced - talking to the server

Although Vagrant runs in a 'black box' configuration it is possible to log into the server for tasks such as checking error logs. The command ```vagrant ssh``` connects to the server and presents a command line interface. Root-level operations are possible using *sudo* and no password is required. The ```exit``` command leaves the server environment and returns to the Windows command line.
