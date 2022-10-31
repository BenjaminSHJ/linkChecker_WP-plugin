# linkChecker_WP-plugin

Linkchecker docs 
Linkchecker består af 3 elementer.
- API (linkChecker)
- Client med Tabel (linkChecker_client)
- WP plugin (Som henter den table som linkCheck_client laver) 

Api delen er lavet med Express api, Samt Node js, som er en server side application. 
Og vil kræve at den server eller computer, som den startes på skal have Node js installeret.

Start API (linkChecker)
hent mappen ned fra Github, og åben mappe med VSC(Visual studio code)
Start med NPM, åben din terminal i VSC og kør kommando "npm run start"
den vil så åbne på http://localhost:8000/


API er lavet med Node.js, Express og Puppeteer samt Cheerio


Start client (linkChecker_client)
Den er lavet med Vue js, og er lidt anderledes, men ellers samme princip. 
Hent mappen ned fra github, og åben med VSC(Visual studio code), derefter brug denne kommando i din terminal
"npm run start", den vil så åbne på http://localhost:3000/

Linkchecker Table hænger sammen med WP plugin, hvor tabel bliver indlæst i Iframe via af WP plugin.
Client delen med Tabel UI, hedder linkChecker_client på Github. 

Brug af løsningen kræver abn. på 
http://api.scraperapi.com?api_key=9f437b17052fd30355e9e01be96dcf64&url=

Scraperapi er en proxy service til webscraping, og som bliver kaldt med den url som skal scrapes.

Brug af løsningen kræver kendskab til VUEJS, NodeJS, samt Javascript og PHP til WordPress plugin

Links
https://code.visualstudio.com/
https://nodejs.org/en/

Dependencies docs

Puppeteer
https://pptr.dev/

Express 
https://expressjs.com/

VUE JS
https://vuejs.org/
