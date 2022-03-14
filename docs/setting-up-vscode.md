# Making Visual Studio Code work better with NOTE
Once you have NOTE installed locally, you're going to want to write some surveys! Any competent text editor can be used, but Visual Studio Code is recommended.
## Visual Studio Code
This can be downloaded and installed from here:
[Get Visual Studio Code](https://code.visualstudio.com/) 

The software installs into your user profile so no admin privileges should be required. Once installed, it's a good idea to set it up to work with PHP. I recommend the following steps:

* Get a zipped version of PHP from [The PHP for Windows Downloads Page](https://windows.php.net/download). Choose the zip file for the 'VS16 x64 Thread Safe' version.
* Once downloaded, unzip the contents into a folder (maybe C:\PHP). It will create a folder called *php-8.1.0-Win32-vs16-x64* (or whatever the latest version is). **Make a note of this path as you will need it later.**
* In Visual Studio Code, go to the plugins section and install *PHP Intelephense* (Yes, it really is called that; PHP's symbol/mascot is an elephant)
* You will need to provide the path to the PHP executable. Open the file:
* * %APPDATA%\Code\User\settings.json 
* (Create the file if necessary)
* and add the following:
* * `"php.validate.executablePath": "<path you made a note of earlier>\\php.exe",` 
* Include the double quotes and the comma. If you needed to create the file, enclose the line with {} braces.

The whole *settings.json* file might look like this:
```
{
    "php.validate.executablePath": "C:\\PHP\\php-8.1.0-Win32-vs16-x64\\php.exe",
}
```
That should be enough for Visual Studio Code to access PHP and for Intelephense to produce debugging information.