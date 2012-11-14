travlr
======

Ett PHP API som kapslar in och ger möjligheten att hämta reseinformation (tågtider) från Trafikverket via api.tagtider.net. <br />
Enkelt och smidigt sätt att få tillgång till alla aktuella avgångar och ankomster på valfri station.

Användningsområde:
======

Få ut snabb information om aktuella tågtider och förbindelser från samtliga tågoperatörer på just din ort.
Information hämtas via http://tagtider.net/ som i sin tur hämtar från Trafikverket.


Vilken funktionalitet finns?
======
Hämta aktuella tider för samtliga ankommande tåg till station x <br />
Hämta aktuella tider för samtliga avgående tåg från station x

Vilka andra API:er används/beroenden finns?
======
cURL för att hämta data från api.tagtider.net <br />
PHP APC används för att cacha data för att minska antalet requests.

Varför och hur använder man detta api?
======
Som användare av Travlr kan man på ett enkelt sätt få ut aktuella information om resor till och från en specifik station. <br />
Konstruktor tar ett frivilligt INT tal för antal sekunder att cacha svaren, om inget anges kommer det att cachas i 3600 sekunder. <br />
Två metoder ingår i det publika API:et. <br />
Se API dokumentation nedan. <br />

Hur får man tillgång till api´et? Hur installerar man det?
======
API:et finns publikt på <br />
https://github.com/AllSecretsKnown/travlr <br />
https://github.com/AllSecretsKnown/travlr.git

Användning:
======
require_once('travlr.php'); <br />

$my_travlr = new Travlr(); //Alt. new Travlr(5000); with custom cache-TTL <br />
$departures = $my_travlr->what_goes_around('Kalmar c'); <br />
$arrivals = $my_travlr->what_comes_around('Kalmar c'); <br />

Hur sker felhantering?
======
Samtliga publika metoder returnerar tom array om ingen data finns att presentera. <br />
Interna undantag hanteras av Travlr och tom array returneras vid eventuella fel. <br />


API:
======
Metod:			 | 			Argument: | 						Response: <br />

__construct				( opt int:cache-ttl )  <br />

whats_comes_around (	string:station	) =>		array(arriving_date&time => string:origin) <br />

whats_goes_around	(	string:station	) =>		array(departuredate&time => string:destination)