travlr v1.3
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
       	 * @return - TrvaleWrapper object, Traversable
       	 */
       	public function get_arriving_trains($station);

       	/*
       	 * Function to get all departing trains from station X
       	 * @scope public
       	 * @param string - station name
       	 * @return - TrvaleWrapper object, Traversable
       	 */
       	public function get_departing_trains($station);

	}

```

##Hur använder man detta API?

Ladda ner filerna eller klona repositoriet.

Instansiera ett objekt av travl (konstruktor tar ett frivilligt INT tal för antal sekunder att cacha svaren, om inget anges kommer det att cachas i 3600 sekunder).

Två metoder ingår i det publika API:et.

>__construct				( opt int:cache-ttl )

>get_arriving_trains (	string:station	) =>		TravelWrapper Object, Implemeterar Iterator interface, är traversable

>get_departing_trains	(	string:station	) =>		TravelWrapper Object, Implemeterar Iterator interface, är traversable

##Travel objektet

TravelWrapper objektet som returneras innehåller en samling med Travel objekt, Wrapperns Travel samlingen kan itereras och varje Travel objekt har följande API/Interface

```php

	public function get_date_and_time() {}

  public function get_destination() {}

  public function get_origin() {}

```


##Exempel på användning:
```php

	//Require travlr.php
  require_once( 'travlr.php' );

  //Instantiate
  $my_travlr = new Travlr();

  //Make a call
  $travels = $my_travlr->get_arriving_trains( 'Kalmar' );

  //Work with the result
  foreach ( $travels as $travel ) {
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
	$travels = $my_travlr->get_arriving_trains( 'HumbugCity' );

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
