# API Wrapper

This package is downgrade version of package [helium/api-wrapper](https://bitbucket.org/teamhelium/com.helium.php.api-wrapper/src/2.x/) compatible for laravel 6

## Introduction
API Wrapper is a system for quickly developing PHP wrapper clients around remote APIs. Powered by 
[Guzzle](https://docs.guzzlephp.org/en/stable/) under the hood, this library provides all of the necessary abstractions
to quickly configure API requests without the hassle of unnecessary boilerplate.

For a deep dive into the benefits of this library, checkout our
[Medium Article](https://teamhelium.medium.com/api-wrapper-library-bfeb163f472d).

All examples in this README use the JSONPlaceholder API, a free to use REST API for testing basic requests.

## Installation
To add this library into your project, run:
```
composer require helium/api-wrapper
```

## Usage
### Routes
#### Basic Routing
Routes can be created using the `Route` class. All route methods accept a unique name and a URL.
```
Route::get('post.all', 'https://jsonplaceholder.typicode.com/posts')
```
The unique name is used to reference this route for making requests (see [Requests](#Requests)).

#### Available Route Methods
You can declare routes manually, or using any HTTP verb:
```
Route::endpoint('GET', $name, $route);
Route::head($name, $route);
Route::get($name, $route);
Route::post($name, $route);
Route::put($name, $route);
Route::patch($name, $route);
Route::delete($name, $route);
Route::options($name, $route);
```

Each of the Route methods returns an instance of `Endpoint`, a class that represents all of the configuration options
for your route. You can apply additional configuration options such as Processors to an endpoint using its public member
methods.

#### Route Groups
To apply certain configuration options to a group of Routes, declare a Route Group. Route Groups can accept a base URL,
a single Processor or Group of Processors (see [Processors](#Processors)), and a callback containing Route declarations.
Route Groups can be nested, and all Routes declared within will receive all of the configuration options of all of its
parent Groups.
```
Route::group('https://jsonplaceholder.typicode.com', [], function() {
    Route::get('post.all', 'posts');
});
```

### Processors
#### Creating a Processor
Inspired by [Laravel Middleware](https://laravel.com/docs/master/middleware), the `Processor` class intercepts
Requests before and after they are executed. Processors can modify an outgoing Request before it is sent out, and can
inspect and modify Response objects before they are returned to you.

Processors are the preferred way of applying shared configuration options, such as Authorization headers, to outgoing
Requests (see [Requests](#Requests)).
```
class AuthorizationProcessor extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {
        $request->headers(['Authorization' => 'token']);

        return $next($request);
    }
}
```

To process the Response of a Request after it is sent, retrieve the Response object (see [Responses](#Responses)) 
returned by the `$next` callback.
```
class PostProcessor extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {
        $response = $next($request);
        
        //Do Something
        
        return $response;
    }
}
```

#### Applying Processors
You can apply Processors to Routes and Route Groups. Both the `Route::group` and the `$endpoint->processor` functions
accept either an array or a single Processor class name.
```
Route::group('https://jsonplaceholder.typicode.com', [GroupProcessor::class], function() {
    Route::get('post.all', 'posts')->processor(EndpointProcessor::class);
});
```

### Requests
#### Creating a Request
The `Request` class represents the complete configuration of an HTTP request before it is executed with Guzzle.
The best way to create a Request is with the static `route` method, which accepts the unique name you provided when
declaring the Route.
```
$request = Request::route('post.all');
```

#### Request Path Parameters
To set Request path parameters, you must declare the parameter on the Route using curly braces `{}`.
```
Route::get('post.get', 'posts/{id}');
```

When creating a Request, set the parameter value using the `pathParams` method.
```
$request->pathParams(['id' => 1]);
```

This request will resolve to the URL `https://jsonplaceholder.typicode.com/posts/1`.

#### Request Query Parameters
To set Request query (URL) parameters, use the `queryParams` method.
```
$request->queryParams(['userId' => 1]);
```

This request will resolve to the URL `https://jsonplaceholder.typicode.com/posts?userId=1`.

#### Request Body
The Request Body can be set using one of several methods, depending on your needs.
```
$request->body('body');
$request->json(['key' => 'value']);
$request->formParams(['key' => 'value']);
$request->multipart(['key' => 'value']);
```

#### Request Headers
To configure Request headers, use the `headers` method, which accepts an associative array of header names and values.
```
$request->headers(['Authorization' => 'token']);
```

#### Auth
To configure auth options as defined by Guzzle 
(see [Guzzle Documentation](https://docs.guzzlephp.org/en/stable/request-options.html#auth)) without manually setting 
the `Authorization` header, use the `auth` method.
```
$request->auth(['username', 'password']);
```

#### Options
To pass any additional configuration options to Guzzle, use the `options` method.
```
$request->options(['timeout' => 3.14]);
```

#### Guzzle Client
To use a custom Guzzle Client, pass your custom instance to the `Request::route` method. This is usually used for 
debugging and mocking Guzzle for tests.
```
Request::route('post.all', $client);
```

#### Send
After you are done configuring all of the requisite options on your Request, execute it with the `send` method to
receive a Response.
```
$response = $request->send();
```

#### Method Chaining
For ease of use, all of the previously mentioned Request methods can be chained into a single call.
```
$response = Request::route('post.get')
    ->pathParams(['id' => 1])
    ->headers(['Authorization' => 'token'])
    ...
    ->send();
```

### Responses
After you execute a Request, you will receive a `Response` object. This class implements 
`Psr\Http\Message\ResponseInterface`, the same as the Response objects returned directly from Guzzle
(see [Guzzle Documentation](https://docs.guzzlephp.org/en/stable/psr7.html?highlight=response#responses)), and in fact
forwards all interface method calls to an instance of the Guzzle Response object internally. However, this Response 
object contains a few additional methods for improved ease of use.

#### Get Contents
By default, Guzzle only returns response bodies as a stream. Although this can be cast to a
plain `string`, use the Response `getContents` method instead.
```
$body = $response->getContents();
```

#### JSON
To automatically handle decoding JSON string responses, use the `json` method.
```
$data = $response->json();
```

By default, this method decodes JSON strings into associative arrays, but to use `stdClass` instances instead, pass
`false` to the method.
```
$data = $response->json(false);
```

### Resources
Inspired by [Laravel's Eloquent](https://laravel.com/docs/master/eloquent) and the 
[Stripe PHP Library](https://github.com/stripe/stripe-php), the `Resource` class represents your API data as model
instances with basic ORM-like functionality.

Some APIs that don't fit or adhere to REST standards, Resources may be an unnecessary abstraction. However, for APIs
that deal in relational object data, Resources provide a lot of useful behavior.

#### Creating Resources
You can represent each of your API's models with a custom ApiResource class.
```
class Post extends Resource
{
    ...
}
```

#### Available Attribute Methods
Like Eloquent Models, Resources maintain an internal array of key/value pairs that represent the model data, and there
are a number of ways to interact with those attributes.
```
$post = new Post(['id' => 1]);

$post->id = 1;

$id = $post->id;

$post->setAttribute('id', 1);

$id = $post->getAttribute('id');

$post->mergeAttributes(['id' => 1]); //Merge the new attributes into the existing attributes.
                                     //For existing keys, overwrite old values with new, but do not clear other keys.
                                     
$post->setAttributes(['id' => 1]); //Same effect as mergeAttributes.

$post->setAttributes(['id' => 1], true); //Clear all existing attributes.
```

#### Casting Attributes
You can configure ApiResource classes to cast certain attributes to other types when that attribute value is set.

Available cast types include:
- `array`
- `bool`
- `collection` (see [Doctrine Collections](https://www.doctrine-project.org/projects/collections.html))
- `date` (see [Carbon](https://carbon.nesbot.com/docs/))
- `datetime` (see [Carbon](https://carbon.nesbot.com/docs/))
- `float`
- `int`
- `object`
- `string`
- `timestamp` (see [Carbon](https://carbon.nesbot.com/docs/))
- ApiResource classes (see [Casting Resources](#Casting-Resources))

Declare attribute cast types in your ApiResource class.
```
class Post extends Resource
{
    protected $casts = [
        'id' => 'int',
        ...
    ];
}
```

Note that declaring casts is not necessary if the remote API returns data in the correct type. Although it won't hurt
anything, you shouldn't bother with declaring cast types unless a transformation is necessary.

When a cast attribute is set, it's value is run through an internal transformation function that stores it internally
as the correct type. When you retrieve this object in the future, it will return as the stored type.

#### Casting Resources
To automatically cast nested objects as the correct ApiResource class, use the target classname as the cast type. When the
attribute is set using an associative array of data, it will automatically create an instance of the target Resource
class.
```
class Post extends Resource
{
    protected $casts = [
        'user' => 'User::class',
        ...
    ];
}
```

#### Default Operations
By default, this library provides implementations for the basic CRUD operations for your ApiResource classes. Include that
behavior in your class by implementing the appropriate Interface Contract and by using the provided Traits.
```
use NbsPhp\ApiWrapper\Resource\Contracts\All as AllContract;
use NbsPhp\ApiWrapper\Resource\Contracts\Create as CreateContract;
use NbsPhp\ApiWrapper\Resource\Contracts\Delete as DeleteContract;
use NbsPhp\ApiWrapper\Resource\Contracts\Get as GetContract;
use NbsPhp\ApiWrapper\Resource\Contracts\Update as UpdateContract;
use NbsPhp\ApiWrapper\Resource\Operations\All;
use NbsPhp\ApiWrapper\Resource\Operations\Create;
use NbsPhp\ApiWrapper\Resource\Operations\Delete;
use NbsPhp\ApiWrapper\Resource\Operations\Get;
use NbsPhp\ApiWrapper\Resource\Operations\Update;
use NbsPhp\ApiWrapper\Resource\Resource;

class Post extends ApiResource implements AllContract, CreateContract, DeleteContract, GetContract, UpdateContract
{
    use All, Create, Delete, Get, Update;
}
```

Under the hood, these operations map to the Request layer as explained above. To execute a query, call the available
operation methods.

```
$posts = Post::all();           //Queries post.all Route (usually GET)
                                //returns Collection of Post Resources

$post = Post::create([...]);    //Queries post.create Route (usually POST) using provided attributes
                                //returns instance of Post Resource

$post = new Post([...]);
$post->store();                 //Queries post.create Route (usually POST) using current attributes
                                //Fills $post with returned data
                                
Post::delete(1);                //Queries post.delete (usually DELETE) Route with id = 1

$post = new Post(['id' => 1]);
$post->destroy();               //Queries post.delete Route (usually DELETE) with id = 1

$post = Post::get(1);           //Queries post.get Route (usually GET) with id = 1

$post = new Post(['id' => 1]);
$post = $post->fresh();         //Queries post.get Route (usually GET) with id = 1
                                //Returns new instance of Post Resource
                                
$post = new Post(['id' => 1]);
$post->refresh();               //Queries post.get Route (usually GET) with id = 1
                                //Fills $post with returned data
                                
Post::update(1, [...]);         //Queries post.update Route (usually PUT/PATCH) with id = 1 and current attributes

$post = new Post(['id' => 1]);
$post->updateAttributes([...]); //Sets $post attribues using provided attributes
                                //Queries post.update Route (usually PUT/PATCH) with id = 1 and current attributes
                                //Fills $post with returned data
                                
$post = new Post(['id' => 1]);
$post->saveChanges();           //Queries post.update Route (usually PUT/PATCH) with id = 1 and current attributes
                                //Fills $post with returned data
```

#### Default Operation Routes
By default, each operation assumes a certain route name which includes the name of the model and the name of the
operation. For example, `Post::all` assumes a route named `post.all`. If you need to set a custom route name for any
of the default operations, set the associated property on your ApiResource class.
```
class Post extends ApiResource implements AllContract, CreateContract, DeleteContract, GetContract, UpdateContract
{
    use All, Create, Delete, Get, Update;
    
    protected $allRoute = 'customAllRoute';
    protected $createRoute = 'customCreateRoute';
    protected $deleteRoute = 'customDeleteRoute';
    protected $getRoute = 'customGetRoute';
    protected $updateRoute = 'customUpdateRoute';
}
```

#### Custom Operations
Of course, you can create your own custom Operations that meet the needs of your API. Simply define the method you need,
and call the `Request::route` method inside. Alternatively, if you need to override the behavior of the default
Operation implementations, you should implement the contract methods yourself, rather than pulling in the traits that
provide the default behavior.
