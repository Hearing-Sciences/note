# Requirements

It must be borne in mind that the hardware requirements will be driven by the needs of the surveys rather than by NOTE itself. Paying attention to NOTE's demands alone could result in a server that cannot run complex surveys.

## Hardware

### Server

Hardware requirements are modest. NOTE can be installed and run on a Raspberry Pi 3b so any physical or virtual server would be suitable if it has:

- 1 CPU core
- 4GB RAM
- 2GB disk space
- Gigabit network connection

### Local development

For local development it is best to meet the specification above **in addition** to whatever is required by the host operating system.

## Software

### Server

It is assumed that a server will be running a version of Linux although other operating systems would be fine. A server setup would require:

- A web server that can support:
  - PHP 8+
  - URL rewriting
  - HTTPS
  - Access security by folder and IP address
  - In reality all web servers are likely to support the list above. These instructions assume Nginx will be used but Apache, Lighttpd, IIS etc. could be made to work.
- PHP-SQLite plugin

### Local development

Survey developers can either install a web server and PHP as local applications or run the entire setup within a virtual machine. Guides for setting up both within Windows are provided in this guide. 



