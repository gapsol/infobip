# URL shortener for Infobip
## Functional description
There is a URL address form reserved for shortened addresses - http(s)://your.domain/?id=#
where # is a string of numbers

**Function 1** - registering of valid URLs and providing short alternatives

using URL *http(s)://your.domain/* browser displays UI with the shortened form and simple list of statistics

The shortener form contains:
- **URL** input field for entering original (long) address
- **shorten URL** button firing the backend script, which
  - checks, if entered URL is valid
  - if not valid - error message appears under the button
  - if valid - checks, if entered URL is already registered
  - if registered - shortened URL appears in the *Shortened URL* input field
  - if not registered - generates new shortened URL and it appears in the *Shortened URL* input field
  - any registered pair is recorded as a new item of the *shorten.json* file in *data* directory
- **Shortened URL** input filed for displaying of the shortened URL if it exists or was generated
- **copy to clipboard** button helping user to copy shortened URL to the computer clipboard
- by clicking this button user gets information about result of the copy function:
  - *Nothing to copy!* if the Shortened input field is empty
  - *URL copied to clipboard!* if the copy function is successful
  - *Copying to clipboard failed!* if the copy function fails (it happen e.g. in case of nonsecure *http://* page)
  - in case of failed copying user has to copy shortened URL manually

**Function 2** - transcription of shortened URL to registered original URL and redirecting browser

using URL *http(s)://your.domain/?id=#* (where # is a string of numbers) the browser
- either redirects to its pair = registered URL (backend script checks if pair exists)
- or displays an error message informing, that the short URL has no pair = no registered URL to display

**Function 3** - providing statistics of using main function and subfunctions

any backend action use is counted and these counts are registered in *stats.json* file in *data* directory

Simple statistics are displayed as a part of UI (Function 1)

## Technical description
**Frontend**
- HTML + CSS + JavaScript
- placed in *root* and *fe* directories

**Backend**
- PHP (developed and tested on version 5.6.40)
- script placed in *be* directory

**Data**
- JSON files (shorten.json, stats.json)
- placed in *data* directory
- *data* directory and JSON files are created during the first use of appropriated parts of the backend script

## Server settings
- use any webserver with PHP 5+ support
- configure server for using *your.domain*
- set cloned repository directory as *your.domain* root directory
