# Using Vagrant to provide a test server

Rather than setting up Apache or Nginx manually to create a server, you can deploy and provision a disposable server as a virtual machine on your PC. It's a great deal easier because all of the configuration is done for you.

You will need the following:

* Admin privileges on your computer
* At least 8GB RAM. Most Windows or Linux PCs and most Apple computers sold in the last ten years should have this.
* About 1GB free hard disk space (if you don't have this, you probably need a new hard disk)
* Oracle VirtualBox
  * Available from [https://www.virtualbox.org/wiki/Downloads](https://www.virtualbox.org/wiki/Downloads)
  * There are versions for Windows, Linux and macOS hosts. The macOS version is for Intel CPUs but may work with the newer M-series CPUs. There is a developer preview for ARM-based Macs available on the [Test builds page](https://www.virtualbox.org/wiki/Testbuilds/) which may work but may also have issues.
* The latest version of Vagrant
  * Available from [https://www.vagrantup.com/downloads](https://www.vagrantup.com/downloads)
  * Get the *Amd64* version for Windows or the ARM64 version for macOS.
* A zip download of the NOTE files
  * Available from [https://github.com/Hearing-Sciences/note/archive/refs/heads/master.zip](https://github.com/Hearing-Sciences/note/archive/refs/heads/master.zip)
  * If you don't have access to the GitHub site, ask someone to provide the zip archive.

## Installation on Windows

**Oracle have changed the licensing restrictions for the VirtualBox Extension Pack. DO NOT USE the extension pack in *any* organisation (commercial, educational, governmental etc.).**

**I only have access to a Windows PC so cannot provide instructions for Linux or macOS installations.** If the situation changes I will update this page.

### Install VirtualBox

Accept all of the defaults and install everything offered. You might be asked to restart your PC. Don't do it yet; wait until Vagrant has also been installed.

(There is an *extras* package available for VirtualBox. It os offered through a personal use and evaluation licence (PUEL) but is **not** required. **DO NOT** install it.)

### Install Vagrant

Again, accept all of the defaults and reboot when asked. Rebooting should add vagrant to the command line which will be vital later on, so don't skip the reboot.

### Create folders and files

Once you are logged back into your PC, create a folder on C: called NOTE. This must be at the root of the C: drive i.e. not in any other folders.

(You can use any other folder for your installation of NOTE but please remember to refer to your chosen folder in later steps.)

Unzip the contents of the NOTE zip archive to C:\NOTE. Don't copy the  *master* folder itself, just the contents. When you're done you should see the following files:

* C:\NOTE\index.php
* C:\NOTE\utils.php
* C:\NOTE\server-config
* C:\NOTE\server-config\vagrantfile
* etc.

### Vagrantfile

The file *vagrantfile* contains all of the configuration Vagrant needs to set up the server. This includes virtual server 'hardware' configuration, how the server is networked to your PC and beyond, the initial 'box' to run and any software configuration required once the virtual machine has started.

The current *vagrantfile* configures the following:

* A Ubuntu 22.04 virtual server
* 768MB RAM
* 2 CPUs
* The virtual network port 80 (www) is mapped to localhost port 5000 (see below)

The version of Ubuntu should be supported until 2027. From that date an alternative box would need to be found if NOTE was still needed (and if Ubuntu and Vagrant still exist).

If you require more performance or just want to experiment, you can change the values for *v.memory* and *v.server*. Make sure you have enough real hardware to support the configuration.

### Running - *Vagrant up*

Open up the command prompt as an administrator. To do this, click the start button, type in *cmd* and look for an option *Run as administrator* on the right.

Run the following commands:

```cmd
cd c:\NOTE\server-config
vagrant up
```

These two commands change the working directory to the *server-config* directory and then start Vagrant.

It will take significantly longer to start Vagrant the first time because everything must be set up. Vagrant does the following:

* Initialises itself
* Downloads a Ubuntu Linux server image from the Vagrant website. This may take several minutes depending on your internet connection.
* Starts the server using Virtualbox
* Removes Apache (if it is installed)
* Updates the server packages
* Removes any redundant packages
* Installs extra packages:
  * nginx
  * php (using FPM)
  * php-sqlite3
* Copies the Nginx configuration file to the server
* (Re)starts the Nginx web server

Later when you start Vagrant it only needs to complete the following:

* Initialises itself
* Starts the server using Virtualbox

### Accessing the server

The configuration file maps the virtual network port 80 to the localhost port 5000. Once Vagrant has started you should be able to access the virtual server's website by using a web browser to visit [http://localhost:5000](http://localhost:5000).

## Shared folders

The server itself is difficult to access. This is intentional: we want it to be disposable and just get on with things. NOTE uses ```C:\NOTE\modules``` as the place to look for surveys so create them there. The modules folder is 'outside' the server and shared, so it's protected from the server being shut down or deleted.

If you want to share a different folder, look for the following line in the *vagrantfile*:

```ruby
config.vm.synced_folder "C:/NOTE", "/var/www/html/note/",
```

Change the first value ```"C:/NOTE"``` to something else. Make sure all of your NOTE and server configuration files are in the folder you specify. A subdirectory called *modules* must contain the survey folders.

If you do decide to use a different folder bear in mind that this will affect the instructions on this page (e.g. the *cd* command just below).

## Starting up, shutting down, deleting

In the future you can start the server using the command prompt as an administrator:

```cmd
cd c:\NOTE\server-config
vagrant up
```

Here's what you should expect to see. This is the second running of *vagrant up* so there are messages reporting that the server as already been provisioned.

```log
Bringing machine 'default' up with 'virtualbox' provider...
==> default: Checking if box 'ubuntu/jammy64' version '20241002.0.0' is up to date...
==> default: Resuming suspended VM...
==> default: Booting VM...
==> default: Waiting for machine to boot. This may take a few minutes...
    default: SSH address: 127.0.0.1:2222
    default: SSH username: vagrant
    default: SSH auth method: private key
==> default: Machine booted and ready!
==> default: Machine already provisioned. Run `vagrant provision` or use the `--provision`
==> default: flag to force provisioning. Provisioners marked to run always will still run.
```

You can check that the VM is running by typing ```vagrant status```:

```cmd
C:\NOTE\server-config>vagrant status
Current machine states:

default                   running (virtualbox)

The VM is running. To stop this VM, you can run `vagrant halt` to
shut it down forcefully, or you can run `vagrant suspend` to simply
suspend the virtual machine. In either case, to restart it again,
simply run `vagrant up`.
```

The command ```vagrant halt``` shuts down the server. ```vagrant destroy``` deletes the server completely (but not the shared folders).

## Things that could go wrong

### Timeouts waiting for SSH

You might see the following when Vagrant starts:

```txt
default: SSH address: 127.0.0.1:2222
    default: SSH username: vagrant
    default: SSH auth method: private key
Timed out while waiting for the machine to boot. This means that
Vagrant was unable to communicate with the guest machine within
the configured ("config.vm.boot_timeout" value) time period.
```

This problem has a number of causes and possible solutions, and sometimes after the timeout the server functions normally. One possible fix is already present in the vagrantfile: updating the VirtualBox setting to make sure the virtual ethernet is set to 'cable connected.'

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

```cmd
cd c:\NOTE\server-config
vagrant reload
```

This should reload the server and update the shared folders.

## Advanced - talking to the server

Although Vagrant runs in a 'black box' configuration it is possible to log into the server for tasks such as checking error logs. The command ```vagrant ssh``` connects to the server and presents a command line interface. Root-level operations are possible using *sudo* and no password is required. The ```exit``` command leaves the server environment and returns to the Windows command line.
