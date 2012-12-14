travlr v1.2
======
##Användningsområde:

Ett PHP API som kapslar in och ger möjligheten att hämta reseinformation (tågtider) från Trafikverket via api.tagtider.net.

Enkelt och smidigt sätt att få tillgång till alla aktuella avgångar och ankomster på valfri station.

Information hämtas via http://tagtider.net/ som i sin tur hämtar från Trafikverket.

Antalet tillgängliga stationer begränsas av vad som finns tillgängligt via api.tagtider.net, tillgängliga stationer ökar i takt med vidareutveckling av api.tagtider.net <br />


##Vilken funktionalitet finns?

>Hämta aktuella tider för samtliga ankommande tåg till station x

>Hämta aktuella tider för samtliga avgående tåg från station x

##Vilka andra API:er används/beroenden finns?

>cURL för att hämta data från api.tagtider.net
>>http://curl.haxx.se/

>PHP APC används för att cacha data för att minska antalet requests.
>>http://php.net/manual/en/book.apc.php

##Hur får man tillgång till API:et?

API:et finns publikt på:

>https://github.com/AllSecretsKnown/travlr

##Hur ser interfacet ut?

```php

	interface iTravlr{

		/*
    	 * Function to get all Arriving trains to statin X
    	 * @scope public
    	 * @param string - Station name
    	 * @return - TravelWrapper object, Traversable
    	 */
    	public function what_comes_around($station);

    	/*
    	 * Function to get all departing trains from station X
    	 * @scope public
    	 * @param string - station name
    	 * @return - TravelWrapper object, Traversable
    	 */
    	public function what_goes_around($station);

	}

```

##Hur använder man detta API?

Ladda ner filerna eller klona repositoriet.

Instansiera ett objekt av travl (konstruktor tar ett frivilligt INT tal för antal sekunder att cacha svaren, om inget anges kommer det att cachas i 3600 sekunder).

Två metoder ingår i det publika API:et.

>__construct				( opt int:cache-ttl )

>whats_comes_around (	string:station	) =>		TravelWrapper Object, Implemeterar Iterator interface, är traversable

>whats_goes_around	(	string:station	) =>		TravelWrapper Object, Implemeterar Iterator interface, är traversable


##Exempel på användning:
```php

	//Require travlr.php
  require_once( 'travlr.php' );

  //Instantiate
  $my_travlr = new Travlr();

  //Make a call
  $travel_wrapper = $my_travlr->what_comes_around( 'Kalmar c' );

  //Work with the result, Implements Iterator interface, is traversable
  foreach ( $travel_wrapper as $travel ) {
  	echo $travel->get_date_and_time() . ' ' . $travel->get_destination() . ' -> ' . $travel->get_origin() . '<br>';
  }

	//Output
		2012-12-13 12:55:00 Kalmar c -> Köpenhamn,Malmö
		2012-12-13 13:35:00 Kalmar c -> Linköping,Rimforsa,Kisa
		2012-12-13 14:02:00 Kalmar c -> Köpenhamn,Malmö
		2012-12-13 14:56:00 Kalmar c -> Köpenhamn,Malmö
		2012-12-13 15:08:00 Kalmar c -> GÃ¶teborg,BorÃ¥s,VÃ¤xjÃ¶
		2012-12-13 15:34:00 Kalmar c -> Linköping,Rimforsa,Kisa
		2012-12-13 16:30:00 Kalmar c -> Köpenhamn,Malmö
		2012-12-13 17:05:00 Kalmar c -> Köpenhamn,Malmö
		2012-12-13 17:34:00 Kalmar c -> Linköping,Tannefors,Rimforsa
		2012-12-13 18:00:00 Kalmar c -> Köpenhamn,Malmö
		2012-12-13 19:00:00 Kalmar c -> Köpenhamn,Malmö
		2012-12-13 19:34:00 Kalmar c -> Linköping,Tannefors,Rimforsa
		2012-12-13 20:10:00 Kalmar c -> Göteborg,Borås,Växjö
		2012-12-13 21:00:00 Kalmar c -> Köpenhamn,Malmö
		2012-12-13 21:28:00 Kalmar c -> Linköping,Tannefors,Rimforsa
		2012-12-13 22:05:00 Kalmar c -> Göteborg,Borås,Växjö
		2012-12-13 22:55:00 Kalmar c -> Köpenhamn,Malmö

```

##Hur sker felhantering?

Samtliga publika metoder returnerar ett Wrapper object med en error property returneras om ingen data finns att presentera, eller om den eftersökta stationen saknas.

```php

	//Require travlr.php
  require_once( 'travlr.php' );

  //Instantiate
  $my_travlr = new Travlr();

  //Make a call
  $travel_wrapper = $my_travlr->what_comes_around( 'Kalmar c' );

  echo $travel_wrapper->get_error_message();

  //Result
  Cant find station

	//The wrapper object
  object(TravelWrapper)[2]
    private 'position' => int 0
    private 'travels' =>
      array (size=0)
        empty
    private 'error' => string 'Cant find station' (length=17)

	public function get_error_message() {}

```

Interna undantag hanteras av Travlr och ett Wrapper object med en error property returneras vid eventuella fel.

Om Tågtider APIet inte svara returneras:

```php

	object(TravelWrapper)[2]
    private 'position' => int 0
    private 'travels' =>
      array (size=0)
        empty
    private 'error' => string 'Could not connect to remote API' (length=31)

    public function get_error_message() {}
```
