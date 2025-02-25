# "People data" Gutenberg Block

## Test task
Create block that would display information about people from Wiki articles.

## Result
- Plugin allows importing data about people in format "name" - "wiki url" from file people.csv
- Then plugin sets up a cron job to load 30 images and texts every 5 minutes
- If execution timeouts or fails, it will repeat from where it left off last time
- In block editor there will be new "People Block" available
