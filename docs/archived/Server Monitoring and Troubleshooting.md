# Server Monitoring and Troubleshooting

## Checking Status

### CPU

You can see details of the CPU by typing

```cat /proc/cpuinfo```

At the time of writing the NOTE server contains 1 (virtual) CPU core.

You can check the load on the CPU by typing

```uptime```

The three numbers at the end of the report show the average load for the past 1, 5 and 15 minutes. They shouldn't be used as an exact measure are are not percentages. The numbers represent the equivalent number of CPU cores that are or have been loaded. So a value of 1.00 would represent the equivalent of one fully loaded CPU core.

If you would like to see a live view of usage type in

```top```

This shows a GUI of processor usage by application. Type ```q``` to exit.

### Memory

You can see the status of the memory by typing

```free -m```

You should see a report of the amount of available and used memory and swap space. The amount of buffered memory in use can be ignored; Linux will assign buffers to try to make use of spare memory.

### Disk space

To see the amount of free disk space type

```df -h```

The ```-h``` option presents the values in 'human readable' format with K, M and G representing KB, MB and GB.

To see the amount of space used by a particular folder type

```du -hs <name of folder>```

The ```-s``` requests a summary and ```-h``` again requests a human readable format.

## Troubleshooting

So what should you do when things go wrong?

### Check the logs

Have a look at the logs ```/var/log/nginx/error.log``` and ```/var/log/nginx/access.log``` and see if there are errors relating to the problem. You can use the following command to see the end of the log and follow it as it is updated:

```sudo tail -f /var/log/nginx/error.log```

You will need to ```sudo``` to root to read the log files.

### Check CPU activity

If the CPU activity is abnormally high then it might be that the server is overloaded or that a process has failed. ```uptime``` will quickly reveal usage and ```top``` may show an offending process.

## Check memory usage

If a process has developed a memory leak then it might be consuming all of the available memory. ```free -m``` should show the amount of memory available and ```top``` should show if a process is consuming everything.

## Check Disk space

If the root or log partitions fill then Linux systems fail. ```df -h``` should show that immediately - look out for ```/``` and ```/var``` (where the log files are stored). If they fill you might have to delete some unnecessary files to get things working again.

A full disk is often a symptom of another problem, such as a process running wild and spewing information into the log. You can list individual log files with

```sudo ls -lh /var/log/*.log*```

An unexpectedly large file is usually a clue that a process has gone rogue.

## Restarting services

Always bear in mind that **restarting processes may disrupt running surveys and restarting the process may not be enough to restore them.** To restart Nginx you can use

```sudo service nginx restart```

and to restart PHP you can use

```sudo service php-fpm restart```

Follow these commands with the word ```status``` rather than ```restart``` to see the service status and confirm that they are running.

If the service fails to restart it might have to be killed. Look for the *process ID* of the service by typing

```ps -ef | grep <service>```

Where ```<service>``` is the service you want to check (probably *nginx* or *php-fpm*). This might reveal lots of entries: kill the lowest numbered service with

```kill <number>```

Where ```<number>``` is the process ID. If that fails, ```kill -9 <number``` will terminate the process with extreme prejudice. Then you can restart the service.
