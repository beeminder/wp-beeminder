Beeminder Ping
==============

A super simple plugin to ping Beeminder whenever a post is published on a
WordPress blog. It can do the following:

* Send a single value (1) to a goal -- Use this if you have a goal such as "post
  5 times a week"
* Send the wordcount to a goal -- Use this for goals like "write 1,000 words a
  week.

That's pretty much all there is to it. 


Installation & Setup
--------------------

`beeminder-ping` should work out of the box. Just make sure the library
`beeminder-api` is also downloaded.

Once the plugin is installed and activated, head over to "Beeminder Ping" in the
WordPress settings menu. You'll need to enter your username and Beeminder auth
token before you can do anything fun. You can obtain your username by logging in
to Beeminder and visiting the following url:
https://www.beeminder.com/api/v1/auth_token.json

After setting your credentials, you can set up two separate actions: one for
pinging Beeminder, and one for sending a word count. The word count may not be
100% accurate, but it should do the trick.


Credits
-------

[Beeminder](https://www.beeminder.com/) is pretty neat. You should use it :)
