<?php /**
 * TODO
 *
 *  fluent api ktory waliduje objekt wtedy bedzie mozna wywalic z klasy request te rozne rzeczy ew. przekazac walidatora
 *  fluent zwraca array  z opisem ruli
 * w request magic get moze byc potrzebne
 *
 *
 *  - formularze
 *  - walidacja (ładowanie walidatorów frameworka po nazwie moze byc problematyczne w tej konfiguracji)
 *
 *   response cache
 *
 *   obsługa meilów
 *
 *  - tlumaczenia iterałów
 *  - refaktoryzacja tego co nastanie po tych testach i zmianach
 */


$validator = new Validator();
$validator->for('fieled')->min(5)-max(4)->required();
$validator->for('image')->image()->required();
$validator->for('num')->range(4,5);
$validator->for('values')->inArray(['1', '2', '3'])->required();
$validator->for('agree')->is(true, message: 'value should be true');
$validator->for('email')->email()->required(message: 'validator.email_is_required');
$validator->for('phone')->phone()->required(message: 'validator.phone_is_required');
$validator->for('url')->validator(UserValidator::class);
$validator->for('test')->validator(function($value){
$validator->validate($object);
});
