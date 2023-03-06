

Synology Download Station Monitor (Albert Frutos Gonzalo - albertfg89@gmail.com)
================================================================================

*********************************************************************************************************************
* PHP code that uses Synology Download Station API to monitor the different parameters of each download.            *
* Uses CSS to improve the GUI and HTML 5 for progress bar.                                                          *
* Uses Javascript to show at which time was the page loaded and automatically refreshes every 3 minutes by default. *
*                                                                                                                   *
* The PHP code and CSS style have been developed by (and are property of) Albert Frutos.                            *
* Sabadell (Barcelona), November 2013.                                                                              *
* Feel free to use this, but please mention the original developer of the code and CSS style.                       *
*********************************************************************************************************************

You should set your DiskStation IP, port, user and pass in index.php (see comments in file for more information).

The PHP page retrieves and shows/uses:

- Download ID (not shown, but compulsory to be used to get download details)
- File name
- Size of file (in GB)
- Downloaded file amount (in GB)
- Uploaded file amount (in GB)
- Download speed (in MB/s)
- Upload speed (in MB/s)
- Status (uses icons included)
- Total number of downloads/uploads in Download Station
- Total download and upload speeds

Also calculates:

- Progress (shows in progress bar, uses HTML 5).
- Ratio (Uploaded/Downloaded, really useful for private trackers)
- ETA (Estimated Time for Arrival, the time until the download completes)

Other parameters the PHP can retrieve but doesn't use due to their low utility (for most people). They can be used by uncommenting the corresponding lines.

- Leechers Connected
- Seeders Connected
- Total Peers
- Priority
- User who is downloading the file
- Type of download (bt, http, emule...)


Synology Download Station Web API (V3) can be found at: http://download.synology.com/download/other/Synology_Download_Station_Official_API_V3.pdf


*********************************************************************************************************************
* The PHP code and CSS style have been developed by (and are property of) Albert Frutos.                            *
* Sabadell (Barcelona), November 2013.                                                                              *
* Feel free to use this, but please mention the original developer of the code and CSS style.                       *
*********************************************************************************************************************
