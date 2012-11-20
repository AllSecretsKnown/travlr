travlr
======
Användningsområde:
======

Ett PHP API som kapslar in och ger möjligheten att hämta reseinformation (tågtider) från Trafikverket via api.tagtider.net. <br />
Enkelt och smidigt sätt att få tillgång till alla aktuella avgångar och ankomster på valfri station. <br />
Information hämtas via http://tagtider.net/ som i sin tur hämtar från Trafikverket.
Antalet tillgängliga stationer begränsas av vad som finns tillgängligt via api.tagtider.net, tillgängliga stationer finns dokumenterade här: <br />


Vilken funktionalitet finns?
======
Hämta aktuella tider för samtliga ankommande tåg till station x <br />
Hämta aktuella tider för samtliga avgående tåg från station x

Vilka andra API:er används/beroenden finns?
======
cURL för att hämta data från api.tagtider.net <br />
PHP APC används för att cacha data för att minska antalet requests.

Varför och hur använder man detta API?
======
Som användare av Travlr kan man på ett enkelt sätt få ut aktuella information om resor till och från en specifik station. <br />
Konstruktor tar ett frivilligt INT tal för antal sekunder att cacha svaren, om inget anges kommer det att cachas i 3600 sekunder. <br />
Två metoder ingår i det publika API:et. <br />
Se API dokumentation nedan. <br />

Hur får man tillgång till API:et?
======
API:et finns publikt på <br />
https://github.com/AllSecretsKnown/travlr <br />
https://github.com/AllSecretsKnown/travlr.git

Användning:
======
require_once('travlr.php'); <br />

$my_travlr = new Travlr(); //Alt. new Travlr(5000); Där tiden att chacha svaret anges i sekunder, om inget anges cachas det i 3600 sekunder. <br />
$departures = $my_travlr->what_goes_around('Kalmar c'); <br />
$arrivals = $my_travlr->what_comes_around('Kalmar c'); <br />

Hur sker felhantering?
======
Samtliga publika metoder returnerar tom array om ingen data finns att presentera, eller om den eftersökta stationen saknas. <br />
Interna undantag hanteras av Travlr och array returneras vid eventuella fel. (Tom array om infomation saknas, och array med felmeddelande om anslutningen inte kunde etableras)<br />
Om Tågtider APIet inte svara returneras:
array( 'Error' => 'Could not connect to remote API' );


API:
======
Metod:			 | 			Argument: | 						Response: <br />

__construct				( opt int:cache-ttl )  <br />

whats_comes_around (	string:station	) =>		array( arriving_date&time => string:origin ) <br />

whats_goes_around	(	string:station	) =>		array( departuredate&time => string:destination ) <br />

Exempel på svar:
======
$arrivals = $my_travlr->what_comes_around('Kalmar c'); <br />
var_dump($arrivals); <br />

array (size=16)<br />
  '2012-11-20 10:00:00' => string 'GÃ¶teborg,Alvesta,VÃ¤xjÃ¶' (length=25)<br />
  '2012-11-20 10:51:00' => string 'KÃ¶penhamn,MalmÃ¶' (length=17)<br />
  '2012-11-20 11:37:00' => string 'LinkÃ¶ping,Vimmerby,Hultsfred' (length=29)<br />
  '2012-11-20 12:58:00' => string 'KÃ¶penhamn,MalmÃ¶' (length=17)<br />
  '2012-11-20 13:35:00' => string 'LinkÃ¶ping,Vimmerby,Hultsfred' (length=29)<br />
  '2012-11-20 14:01:00' => string 'KÃ¶penhamn,MalmÃ¶' (length=17)<br />
  '2012-11-20 14:54:00' => string 'KÃ¶penhamn,MalmÃ¶' (length=17)<br />
  '2012-11-20 15:08:00' => string 'GÃ¶teborg,Alvesta,VÃ¤xjÃ¶' (length=25)<br />
  '2012-11-20 15:34:00' => string 'LinkÃ¶ping,Vimmerby,Hultsfred' (length=29)<br />
  '2012-11-20 16:29:00' => string 'KÃ¶penhamn,MalmÃ¶' (length=17)<br />
  '2012-11-20 17:00:00' => string 'KÃ¶penhamn,MalmÃ¶' (length=17)<br />
  '2012-11-20 17:34:00' => string 'LinkÃ¶ping,Vimmerby,Hultsfred' (length=29)<br />
  '2012-11-20 18:00:00' => string 'KÃ¶penhamn,MalmÃ¶' (length=17)<br />
  '2012-11-20 18:55:00' => string 'KÃ¶penhamn,MalmÃ¶' (length=17)<br />
  '2012-11-20 19:34:00' => string 'LinkÃ¶ping,Vimmerby,Hultsfred' (length=29)<br />
  '2012-11-20 20:50:00' => string 'KÃ¶penhamn,MalmÃ¶' (length=17)<br />