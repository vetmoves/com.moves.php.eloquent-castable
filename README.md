# Castable
## Introduction
The purpose of this library is to provide overrides to the default Laravel/Eloquent behaviors for querying model
instances from your app database in order to cast query results from concrete parent models to the appropriate child
model classes.

For the short explanation, skip to [TL;DR](#tldr).

### In-Depth Explanation
By default, Eloquent hydrates instances of the same class used to initiate the query, like this:
```
$users = User::all();

// $users is a Collection of User class objects
```
For 99% of use cases, that makes perfect sense. Why would you want it any other way?

One of the four main concepts of Object Oriented Programming is *inheritance*, meaning that a child class can 
automatically *inherit* the functions and properties of the parent class that it extends. In Laravel, one of the best 
examples of this is how your model classes inherit from `Illuminate\Database\Eloquent\Model` to receive all of the base
Model functionality (like querying).

Another one of the four main concepts which is closely tied to inheritance is *polymorphism*, which is the idea that 
anywhere where an instance of the initial parent class is accepted, so too are instances of that parent's children 
classes.

Let's say you have a `pets` table in your database, and an associated `Pet` model class. As you might expect, 
`Pet::all()` returns a collection of `Pet` model instances. `Pet` has an attribute field `type` for specifying the
species: cat, dog, ferret, etc. But what if now, you decide you need to provide custom functionality for each type of
pet? 

You could litter the base `Pet` class with `if`/`else` or `switch` statements, but as you add more and more types
of pets, your code is going to get very messy and hard to follow. 

You could follow the OOP principle of inheritance and create `Dog`, `Cat`, and `Ferret` subclasses of `Pet`, but there 
are problems with that as well...

By default, Eloquent expects the table name to match the model class name, so you're
now forced to separate your `pets` table into many separate tables. If you tend to do a lot, or really any querying on
the collection of ALL pets, regardless of type, this could become a major inconvenience.

If you're clever, you know that you can set the `$table` property on the model class to specify the table name instead
of using the default value which matches the class name. However, in this case, a query like `Dog:all()` would still
return ALL of the pets in the table, not just those where the `type` is `dog`. Ok, so you add a
[global scope](https://laravel.com/docs/8.x/eloquent#global-scopes) to each of the subclasses to filter by type, and
everything works as expected.

But there's still one major drawback, and it's that querying `Pet::all()` doesn't return a mix of `Dog`, `Cat`, and
`Ferret` instances, it returns a Collection of plain old `Pet` instances, which means you're losing out on all of the
custom functionality in each of the child classes.

And now we come to the purpose of this library: to cast Parent model class instances to the appropriate child class,
automatically...

### TL;DR
In short, given a parent Model `Pet` and subclasses `Dog`, `Cat`, and `Ferret`, this library allows you to query 
`Pet::all()` and receive a collection which is a mix of `Dog`, `Cat`, and `Ferret` instances. This means that when 
you iterate over the returned collection, each instance is of the appropriate child class type, so you have access to 
all of the child class functionality.

Global scopes are also automatically applied to filter queries directly on the child classes.

Finally, the table name is automatically assumed to always match the parent class name. As in Laravel, you can always
override this at the parent class or the child class level by setting the `$table` property.

## Installation
To add this library into your project, run:
```
composer require moves/eloquent-castable
```

## Usage
To continue with the example above, implement your parent class by implementing `ICastable` and using the `TCastable`
trait:
```
use Illuminate\Database\Eloquent\Model;
use Moves\Eloquent\Castable\Contracts\ICastable;
use Moves\Eloquent\Castable\Traits\TCastable;

class Pet extends Model Implements ICastable
{
    use TCastable;
    
    public function speak(): string
    {
        return 'squeak';
    }
}
```

Next, create your child classes by extending your parent class:
```
class Dog extends Pet
{
    public function speak(): string
    {
        return 'woof';
    }
}
```
```
class Cat extends Pet
{
    public function speak(): string
    {
        return 'meow';
    }
}
```
```
class Ferret extends Pet
{    
    public function speak(): string
    {
        return 'eek eek eek';
    }
}
```

And that's it!

You can now query the parent class and receive a collection of child instances:
```
$pets = Pet::all();
// Example results:
// [0] => instance of Dog
// [1] => instance of Cat
// [2] => instance of Ferret

foreach ($pets as $pet) {
    $pet->speak();
}
// Expected output:
// 'woof'
// 'meow'
// 'eek eek eek'
```

You can also query each pet individually, and you will only receive instances of that type, with other types from the
same table being filtered out:
```
$dogs = Dog::all();
// Example results:
// [0] => instance of Dog
// [1] => instance of Dog
// [2] => instance of Dog

foreacch ($dogs as $dog) {
    $dog->speak();
}
// Expected output:
// 'woof'
// 'woof'
// 'woof'
```
