# SS Video Playlist

This example code was used on a SilverStripe Youtube playlist site. 

The playlist was managed using DataObjects rather than a YouTube playlist.

**Note: No longer fully functional as YT API 2.0 is now deprecated**

The basics were:

- Create new Video records from front-end without interrupting playing video
- Import a list videos from a text file (youtube ID's)
- Interact with YouTube API to gather data about each track, storing it in the DB and presenting it on the front-end
- Load random videos, scroll to load more
- pause playing video if a new video is started
- skip to a random unplayed video on the page once a video ends

This project was just for a bit of fun.
