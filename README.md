# PHP Storm Framework &#9889;

Stworzony z miłością by dostarczać radość i zadowolenia z każdym pisanym fragmentem Twojej wymarzonej aplikacji.\
By zacząc potrzebujesz ok. 3 minut. Storm został zaprojektowany tak by zapewnić niski próg wejścia i przejrzystość API by stał się intuicyjny. Najprostrza aplikacja składa się z 4 linijek kodu.\
Jednocześnie oferuje bogactwo funkcjnalności zachowując przy tym minimalizm składni i rozmiaru. Całość ma 100KB.

- Built-in dependency injection container
- Built-in middleware support
- **Built-in CQS (command query separation)**
- Built-in event dispatcher 
- Built-in multilanguage support
- Built-in authentication and authorization system
- Built-in logger
- Built-in class autoloader with scanning source code (detect automatically controllers)
- Built-in path alias system ('@templates/homepage.php' refers to /your/project/dir/templates/homepage.php)
- Built-in mature view system using pure PHP, views can control content of layout (adding css/jss scripts or changing title
- Build-in validation
- Build-in forms
- Build-in mailing (i18n, smtp client)
- Support cli tasks (define tasks and run it from cli)
- Support for running controller from cli
- Support e2e tests with php cli (test automatically your entire backend stack from phpunit easilly)
- Support error customization
- Support middleware (put your code to pipeline before and after request is handled)
- Support Docker out of the box
- Support PHP8 and greater
- Works with StormQueries


## Hello World

```php
require 'vendor/autoload.php'
$app = App::create();
$app->addRoute('/', function() { return "hello world";});
$app->run();
```

## Przykładowa aplikacja
Dodać opis i link do StormWord

## Quick start

### Instalacja
Istnieją dwa sposoby instalacji Storm. Przy pomocy Composera lub sciągnięcie paczki ZIP z kodem i dołaczeniem pliku autoload z frameworka.

#### Composer
By zainstalować przy pomocy Composera użyj komendy
```
composer require stormmore/framework
```
w pliku `index.php` dodaj plik autoload
```php
require '../vendor/autoload.php';
```

#### Standalone
Jest to wydajniejsze rozwiązanie niż paczka Composera.\
Sciągnij paczkę ZIP, rozpakuj katalog `src` i dodaj plik autoload z katalogu `src`

```php
require 'YOUR/PATH/TO/STORM/src/autoload.php';
```

## Pierwsza aplikacja 
Link do tutorialu tworzenia aplikacji od A do Z. 

## Dokumentacja
Designed to be intuitive as scratching your nose but there is some things you should know.

### Zanim zaczniesz 

Zapewne spotkałeś się już z jakimś frameworkiem MVC w PHP ale ten posiada kilka unikalnych cech o których powinieneś wiedzieć. 


**Skanowanie plików**\
Storm skanuje pliki aplikacji w celu znalezienie interesujących go klas i zapisuje je.

Nie robi tego kiedy środowisko uruchomieniowe jest zdefiniowane jako produkcja. Na produkcję należy dostarczyć plik `.cache` wraz
z kodem. 

> [!TIP]  
> Na środowiskach deweloperskich w przypadku nie odnalezienia klasy bądź routingu zanim zostanie wyrzuconyh bład Storm 
> skanuje pliki.\
> W ten sposób możesz płynnie dodawać klasy i zmieniać nazwy bez potrzeby usuwanie ręcznie pliku. 


**Ładowanie klas**\
Storm w pierwsej kolejnści sprobuje załadować Twoje klasy na podstawie użytego namespacu.\
W przypadku
```php
use Infrastructure\Images\Resize
```
Sprobuje załadować klasę `Resize` z pliku w katalogu `src/infrastructure/images/resize.php`.\
Jęśli nie znajdzie klasy sprobuje wyszukać klasy `Infrastructure\Images\Resize` w pliku `.cache` z przeskanowanymi klasami.

**Routing**\
Przez zastowanie skanowania plików PHP nie trzeba rejestrować w kodzie kontrolerów. Są one odnajdywane przez atrybut `Routing`

**Middleware**\
Cały framework składa się z łańcucha middlewarów wywoływanych jeden po drugim od początku żądania aż do odpowedzi.\
Istnieje kilka predefiniowanych middleware które dodaje obsługę ustawien itp.\
Możesz dodać własny middleware i przerwać wykonywanie w dowolnych momencie i zwrócić własna odpowiedż np. kiedy użytkownik jest niautoryzowany.

**Aliasy**\
Storm korzysta z wewnętrznego systemu aliasów. Alias zaczyna się od znaku `@` i tak możemy zdefiniować scięzkę do szablonów 
```php
$app->addMiddleware(AliasMiddleware::class, [
    '@templates' => "@/src/templates"
]);
```
W definicjach sciężek `@` odnosi się do katalogu projektu.\
W ten sposób można łatwo utworzyć wiele szablonów aplikacji.

**Konfiguracja aplikacji**\
Nie jest wymagana ale jeśli zamierzasz zbudować coś większego napewno będziesz chciał dodać własną.\
W przypadku nie podania wszystkie odnoszą się do katalogu w którym jest wykonywany skrypt.
```php
$app = App::create(directories: [
'project' => '../', //project directory, alias `@` refers it
'source' => '../src', // source directory, it will be scanned for controllers, tasks, etc. 
'cache' => '../.cache', // file with written cache file
'logs' => '../.logs' // log directory
]);
```

**Przykładowa struktura katalogów**\
```
my_app_made_with_love 
├── .cache/
├── .logs/
├── public_html/
│   ├── index.php
│   ├── app.css
│   └── app.js
├── src/
│   ├── MyController.php
│   ├── Database.php
│   ├── templates/ 
│   │   └── view.php
└── README.md
```

`.cache` katalog z plikami cache\
`.logs` logi aplikacji\
`public_html` katalog do udostępnienia przez serwer na zewnątrz. Prócz `index.php` nie powinno tu być żadnych innych plików `php`


## Obsługa żądań

Obsługa żadań odbywa się poprzez definiowanie metody w klasie `app` lub za pomoca kontrolera. Klasy obsługując dane urle.

### Funkcja
```php
$app->addRoute('/', function() { return "hello world";});
```

### Kontroler
```php
namespace src\App;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;
use Stormmore\Framework\Mvc\View\View;

#[Controller]
readonly class HomepageController
{
    #[Route("/")]
    public function index(): View
    {
        return view("@templates/homepage");
    }
}
```
Kontroler powinien być w dowolnym katalogu lub podkatalogu `src` i być oznaczy za pomocą atrybutu `Controller`.\
Url definiuje atrybut `Route`.

#### Wstrzykiwanie zależności
```php
#[Controller]
readonly class HomepageController
{
    public function __construct(private Request $request) {}
    
    #[Route("/")]
    public function index(): View
    {
        return view("@templates/homepage");
    }
}
```
By wstrzyknać zalezność należy ja zdefiniować w konstruktorze kontrolera i zostanie utworzona samoistnie.

#### Typ żadania 
```php
#[Route("/")]
#[Get]
public function index(): View
{
    return view("@templates/homepage");
}
```
Typ obsługiwanego żądania definiuje za pomoca atrybutów `Get`, `Post`, `Put`, `Patch`, `Delete`.

#### Parametry żadania 
```php
#[Route("/article")]
public function article(string $title, int $id): View
{
    return view("@templates/homepage");
}
```
Obsługiwany url `/article?title=awesome-phg-tool&id=1`

##### Path parameters
```php
#[Route("/article/:title/:id")]
public function article(string $phrase, int $id)
{
    ...
}
```
Obsługiwany url `/article/awesome-php-tool/1`

### Odpowedzi kontrolera
Prócz widoku kontroler może zwrócić 

#### Redirect
```php
#[Route('/signout')]
public function signout(): Redirect
{
    return redirect('/');
}
```
Przekierowanie pod `/`
```php
#[Route('/finish-order')]
public function finishOrder(): Redirect
{
    return back('/');
}
```
Przekierowanie na poprzednią stronę lub pod podany adres jeśli nie ma nagłówka `Referer`

#### Obiekt który jest zwracany jako JSON
```php
#[Route("/get-product")]
public function getProduct(): object
{
    return new Product();
}
```

#### Wartość (liczba, string)
```php
#[Route("/hello-world")]
public function getValue(): mixed
{
    return "hello-world"
}
```

### Żądanie


### Views

Thaks to alias system you can easilly change templates

