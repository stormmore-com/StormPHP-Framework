<?php /**
 * TODO
 *
 * dokonczyc tworze obj bindable (fluent object)
 *
 * Validator datetime
 *
 * Refaktor Request
 *  - creater dla cli i htttp
 *  - usunac typowanie requestów (to szalone jak sie zastanowic)
 *  - zrobic kolekcje osobna dla get/post i z takimi samymi interfejsami i dac ten interfesj do klasy request (getParameter, get(...))
 *  - dodac funckje getBool, getInt, getFloat, getDateTime
 * - to object tak by moze tylko uzywac fluent class
 *
 *
 *   response cache
 *
 *  Gate - TentativeType To moze pomoc. w iterable zmienilem typ z interfejsu i zero problemu
 *
 *   uruchamienia z cli
 *    urla (kontrolera)
 *     -> get
 *     -> post file
 *    uruchaminie tasków
 *   uruchamiene endpotionow z z testów jednostkowych poprzez cli
 *
 *  update Readme
 *   Support for cli tasks (define tasks and run it from cli)
 *   Support for running controller endpoints from cli
 *   Support e2e tests with php cli (test your entire backend stack from phpunit easilly)
 *   Response cache. Event driven cache. EDC
 *
 *   konfiguracja
 *    - klasa konfiguracja czytajacy z plikow php jak i plików innych
 *    - format 'poziom1.poziom2.poziom3' etc.
 *    - rozne instacje klas
 *    - plik konfiguracyjny srodowisko
 *    - plik konfiguracja name: value z mozlwiocia multiline po 3x"
 *
 *   obsługa meilów
 *
 *  - tlumaczenia iterałów
 *
 *  - testy i tak bede potrzebne
 *
 *  dac mozlwiosc printownia na output bo jet ob_get i nie mozna debugowac za pomoca echo
 * *  enviro
 * *  - debug umozlwiosc tpo
 * *  - prodt
 * *  - development
 *
 *   StormQueries
 *   - konfiguracja DateTimeów (czy ma zapisywac do utc czy nie) (konfiguracja jak ma wygladac, ew. na jaki timezone ma tlumaczyc domysl)
 *   - zrobic te zapytania bez funkcji execute na najwyzszym poziomie
 *
 *
 *  refaktoro
 *   -> przejrzec katalog i klasa po klasie
 *   -> strukture frameworka
 *   -> pousuwac te helpery
 */


