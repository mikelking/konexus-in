Ok if you are reading this then you are either very bored or extremely curious. Basically 
there are a number of issues with these plugins and the mostly revolve around the way that
Wordpress handles page reloads and the inability to receive inbound connections to the 
sandbox that this was developed on. 

The latter inhibited the use of the call back methodology that one would typically employ 
to access an OAuth Provider. This is not to say that you can not connect to OAuth from a
sandbox it's just that if the OAuth provider expects to talk back automatically to you via
a call back url and you are not publicly accessible then it will fail. There are only two 
ways around this. The preferred method is to use a nonce or PIN based approach, however;
thanks to Wordpress's insistent reload of page data that proved futile because the OAuth
request tokens are regenerated on each page load, thus invalidating the nonce your receive
from the provider upon use.

The only other method is to build a gateway process that negotiates the OAuth binding or
authorization on your behalf and returns the appropriate token pair for long term storage.
This is where the story becomes extremely complex in that the server I am using for a
bridge lacks the PECL OAuth and to install it is not a walk in the park (yeah Apple Server).
In light of the aforementioned limitations I opted for the google oauth module which worked
nicely with the Mac OS X Server version of Apache2. 

So where does that leave us well, well with a certain level of disconnect between the oauth
tokens stored in Wordpress and the real world. What this means is if you revoke permission
for the app (a.k.a plugin) to use your linkedin account the app has no way of knowing this. 
Ugly things will happen. Most notably the widget in the side bar will cease to output 
anything but the title and your logs will fill up with fatal unknown oauth object errors.

That being said the picture URL and last headline you saved in your profile will continue 
to work regardless because they are being publicly referenced. In addition, entities that 
rely on the public profile url in the LinkedIn contact method will also continue to function
properly.

On the list for the future is to make the plugin work with either the pecl or google oauth 
class libraries. I honestly don't know how viable it is but that is a worthy goal as the two 
are NOT compatible. Also on the list is to clean this place up this was a fast dev effort and 
I know I left some cruft laying around. In either case it should make for some amusing reading.

