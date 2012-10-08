Freshrr 1.1
===========

This scriptlet does one thing: automatically refreshes a web page the instant that file(s) are altered. This can be especially useful when tweaking CSS or developing for the mobile web.

It should go without saying that this plugin is only intended for use during development, and should **never be deployed on a production site**, as the server would grind to a halt very quickly. You've been warned.

Requires [jQuery][] or [Zepto][].



Usage
-----

1. Upload freshrr.php to the root level of your site.

2. Add this script tag to your `<head>`. Subsitute the file names you wish to track, separated by commas.

		<script type="text/javascript" src="/freshrr.php?files=myfile.php,path/to/otherfile.js"></script>

3. ~~Make her open the box.~~ You're done! When you save the file(s), the website will refresh.


Arguments
---------

Add additional arguments to the URL query string: `freshrr.php?files=myfile.php&longpoll=0&ms=500&max=10`

- **files**:	Comma-separated list of paths to files, relative to freshrr.php.
- **longpoll**:	Boolean to indicate a long-lived request. This gives the best performance.
- **ms**:		Milliseconds between polls for updates (min 100; defaults to 250 for longpoll, and 1000 for ajax).
- **max**:		Stop polling after this many minutes. Prevents server kersplosion when you fall asleep at your desk.

To temporarily disable, add "?refresh=0" to the URL bar, or call `freshrr(false);` from your Javascript.


Fiercely Asked Questions
------------------------

**Q: How do I handle files with unusual characters in the name, such as a comma or ampersand?**

A: The doctor says, "don't do that".

**Q: Can I check a whole directory at once?**

A: Yes, but only one level down. Recursively checking sub-directories would be very expensive to the server.

**Q: How come you pollute the global namespace in Javascript?**

A: Cry me a river, hippie.


Version History
---------------

### v1.1 ###
 * Support for long-polling
 * Refactor

### v1.0 ###
 * Initial release


Contact
-------
Luke Dennis  
http://lukifer.net  
[@lkfr][]



[jQuery]: http://jquery.com
[Zepto]: http://zeptojs.com
[@lkfr]: http://twitter.com/lkfr