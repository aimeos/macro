## PHP Macro

Easy to use PHP package for extending objects by custom methods at runtime.

```bash
composer req aimeos/macro
```

This package is for application, framework and library developers who want to
allow customizing the behavior of their code by their users.

## Why macros

Unlike other languages, PHP doesn't allow to extend objects at runtime and you
can only inject methods into classes at compile time using traits.

In applications, frameworks or libraries which are build for customization it's
necessary to add new or overwrite existing functionality to be able customize
its behavior. This is where dynamic macros are very handy because they can add
custom methods at runtime.

Using the PHP Macro package, you can also allow users to overwrite methods in
base classes without forcing your users to extend these classes.

## Allow customization

The result of existing methods can be modified if the original method checks
for an existing macro and use that instead its own implementation:

```php
// original code

class A {
    use Aimeos\Macro\Macroable;

    public function do() {
        $fcn = static::macro( 'concat' );
        return $fcn ? $fcn( [1, 2, 3] ) : join( ',', [1, 2, 3] );
    }
};
```

Now, you can add your custom `concat` macro that will be used instead:

```php
// user code

A::macro( 'concat', function( array $values ) {
   return implode( '-', $values );
} );

(new A)->do(); // now returns '1-2-3'
```

Thus, you can generate own output or pass a different result to subseqent methods
within the application.

## Access class properties

When macros are called in an object context, they can also access class properties:

```php
// original code

class A {
    use Aimeos\Macro\Macroable;
    private $name = 'A';
};
```

Here, the private property `$name` is available in the macro:

```php
// user code

A::macro( 'concat', function( array $values ) {
   return $this->name . ':' . implode( '-', $values );
} );

(new A)->concat( ['1', '2', '3'] ); // returns 'A:1-2-3'
```

The macro can use the property as input for creating the returned value.

## Use inherited macros

The PHP macro package also allows to inherit macros from parent classes. Then,
they can access class properties of the child class just like regular class
methods:

```php
// original code

class A {
    use Aimeos\Macro\Macroable;
    private $name = 'A';
};

class B extends A {
    private $name = 'B';
};
```

Macros added to the parent class will be available in child classes too:

```php
// user code

A::macro( 'concat', function( array $values ) {
   return $this->name . ':' . implode( '-', $values );
} );

(new B)->concat( ['1', '2', '3'] ); // returns 'B:1-2-3'
```

Class `B` extends from class `A` but provides a different `$name` property. The
macro inherited from class `A` will now use the property of class `B`.


## Overwrite inherited macros

It's also possible to overwrite macros inherited from parent classes as it's
possible with regular class methods:

```php
// original code

class A {
    use Aimeos\Macro\Macroable;

    public function do() {
        return static::macro( 'concat' )( [1, 2, 3] );
    }
};

class B extends A {};

class C extends A {};
```

Now you can add macros to the parent class and one of the child classes:

```php
// user code

A::macro( 'concat', function( array $values ) {
   return implode( ',', $values );
} );

C::macro( 'concat', function( array $values ) {
   return implode( '-', $values );
} );

(new B)->do(); // returns '1,2,3'

(new C)->do(); // returns '1-2-3'
```

This enables you to add special handling for single classes even if all other
classes still use the macro added to class `A`.

## Overwrite protected methods

Base classes often offer a set of methods that are used by the child classes.
In PHP, replacing the methods of a base class is impossible and thus, you have
to overwrite each child class with your own implementation.

To avoid that, the original method can use the `call()` method instead of calling
the method of the parent class directly:

```php
// original code

class A {
    use Aimeos\Macro\Macroable;

    protected function getName( $prefix ) {
        return $prefix . 'A';
    }
};

class B extends A {
    public function do() {
        return $this->call( 'getName', 'B-' );
    }
};
```

This will check if there's a macro `getName` available and will call that instead
of the `getName()` method:

```php
// user code

(new B)->do(); // returns 'B-A'

A::macro( 'getName', function( $prefix ) {
   return $this->getName( $prefix ) . '-123';
} );

(new B)->do(); // returns 'B-A-123'
```

The original `getName()` method can still be used in the macro.

## Reset macros

Sometimes, it may be necessary to remove macros from objects, especially when
running automated tests. You can unset a macro by using:

```php
class A {
    use Aimeos\Macro\Macroable;
};

// add macro
A::macro( 'test', function() {
   return 'test';
} );

// remove macro
A::unmacro( 'test' );
```
