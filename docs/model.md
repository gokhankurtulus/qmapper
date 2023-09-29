# Model

## Creating a Model

When creating a model, you need to specify `$table` and `$indexKey`. It is optional to specify `$driver` and `$cache`.
The model should inherit from the `Model` class.

* $table: Represents the name of the table in the database.
* $indexKey: Represents the name of the index associated with the table.
* (optional) $driver: Represents the name of the driver used for connecting to the database. This field is optional and
  can be omitted if the default driver is used.
* (optional) $cache: Represents whether the [cache](https://github.com/gokhankurtulus/jsoncache) mechanism is enabled or
  disabled for the model. This field is optional and can be omitted if caching is
  not required or handled differently. The cache mechanism helps improve performance by storing frequently accessed data
  in memory for quicker retrieval.

```php
use QMapper\Core\Model;

class User extends Model
{
    protected static string $table = 'users';
    protected static string $indexKey = '_id';
    
    protected static ?DatabaseDriver $driver = DatabaseDriver::MongoDB;
    protected static bool $cache = true;
    
    protected static function schema(): array
    {
        return [
            (new Field())->index(static::getIndexKey(), static::getDriver()),
            (new Field())->uuid()->unique()->searchable()->identifier(),
            (new Field())->varChar('username')->length([20])->min(6)->max(16)->required()->unique()->searchable()->identifier(),
            (new Field())->varChar('name')->length([50])->required()->searchable(),
            (new Field())->varChar('lastname')->length([50])->required()->searchable(),
            (new Field())->email('email')->required()->unique()->searchable(),
            (new Field())->text('password')->hidden()->match('repassword'),
            (new Field())->text('repassword')->hidden()->nostore(),
            (new Field())->varChar('country_code')->length([5])->max(5)->nullable(),
            (new Field())->varChar('phone')->length([14])->max(14)->nullable(),
            (new Field())->date('birthday')->nullable(),
            (new Field())->enum('role')->length(['1', '2', '3'])->default(1),
            (new Field())->enum('status')->length(['1', '2', '3'])->default(1),
            (new Field())->enum('verification_status')->length(['0', '1', '2', '3'])->default(0),
            (new Field())->bigint('license')->related(License::class, License::getIndexKey())->nullable(),
            (new Field())->createdAt(),
            (new Field())->updatedAt()
        ];
    }
}
```

## Working With Models

### Methods

#### Attribute Methods

```php
/**
 * @return DatabaseDriver|null
 */
final public static function getDriver(): ?DatabaseDriver

/**
 * @param array $fields
 * @param bool $raiseError
 * @return bool
 * @throws AttributeException
 */
public function validate(array $fields, bool $raiseError = false): bool

/**
 * Returns true if instance has different values from given fields
 * @param array $fields
 * @param bool $returnDiff
 * @return array|bool
 */
public function hasDiff(array $fields, bool $returnDiff = false): array|bool

/**
 * Returns true if instance has index value
 * @return bool
 */
public function hasIndex(): bool

/**
 * Returns true if instance has properties
 * @return bool
 */
public function isEmpty(): bool

/**
 * Remove hidden fields from properties
 * @return array
 */
public function toArray(): array

/**
 * @param int $options
 * @return string
 * @throws ModelException
 */
public function toJson(int $options = JSON_FORCE_OBJECT): string

/**
 * Returns table name of entity
 * @return string
 */
public static function getTable(): string

/**
 * Returns index key of entity
 * @return string
 */
public static function getIndexKey(): string

/**
 * Returns cache status of entity
 * @return bool
 */
public static function isCached(): bool

/**
 * Returns entity's index value
 * @return mixed
 */
public function getIndexValue(): mixed

/**
 * Check for $table and $indexKey are specified.
 * @return bool
 */
public function checkNecessaryModelProperties(): bool

/**
 * Returns fields.
 * @return Field[] array
 */
public function getFields(): array

/**
 * Returns field if exists, otherwise returns null.
 * @param string $key
 * @return Field|null
 */
public function getField(string $key): ?Field

/**
 * Returns true if field exist, otherwise returns false.
 * @param string $key
 * @return bool
 */
public function isFieldExist(string $key): bool

/**
 * Assigns properties and field value from given array or object.
 * @param array|object $entityValues
 * @return void
 */
public function assignProperties(array|object $entityValues = []): void

/**
 * Returns properties.
 * @return array
 */
public function getProperties(): array

/**
 * Returns property value.
 * @param string $key
 * @return mixed
 */
public function getProperty(string $key): mixed

/**
 * Set property value.
 * @param string $key
 * @param mixed $value
 * @return void
 */
public function setProperty(string $key, mixed $value): void

/**
 * Returns true if property exist, otherwise returns false.
 * @param string $key
 * @return bool
 */
public function isPropertyExist(string $key): bool

/**
 * Change properties to allow tree view from fields related model,
 * and can be reachable in properties.
 * @param bool $hideHiddenFields If true sets property value as array from related model,
 * but object can't be reachable.
 * @return $this
 */
public function tree(bool $hideHiddenFields = false): static

/**
 * Returns all flags as array.
 * @return array
 */
public function getFieldFlags(): array

/**
 * Returns unique fields.
 * @return array
 */
public function getUniques(): array

/**
 * Returns required fields.
 * @return array
 */
public function getRequireds(): array

/**
 * Returns nullable fields.
 * @return array
 */
public function getNullables(): array

/**
 * Returns hidden fields.
 * @return array
 */
public function getHiddens(): array

/**
 * Returns searchable fields.
 * @return array
 */
public function getSearchables(): array

/**
 * Returns uuid fields.
 * @return array
 */
public function getUuids(): array

/**
 * Returns relation fields.
 * @return array
 */
public function getRelations(): array
```

#### Interaction Methods

```php
/**
 * @return static|null
 */
final public static function getInstance(): ?static

/**
 * @param array ...$where
 * @return static
 */
final public static function where(array ...$where): static

/**
 * @param array ...$orWhere
 * @return static
 */
final public static function orWhere(array ...$orWhere): static

/**
 * @param array ...$sort
 * @return static
 */
final public static function order(array ...$sort): static

/**
 * @param int|null $limit
 * @param int|null $offset
 * @return static
 * @throws ModelException
 */
final public static function limit(?int $limit, ?int $offset = 0): static

/**
 * @param array $fields
 * @return static
 */
final public static function select(array $fields = ['*']): static

/**
 * @param string ...$where
 * @return static
 * @throws \JsonCache\Exceptions\CacheException
 * @throws ModelException
 */
final public static function find(string ...$where): static

/**
 * @param int|null $limit
 * @return Interactions|array
 * @throws \JsonCache\Exceptions\CacheException|ModelException
 */
final public static function get(?int $limit = null): static|array

/**
 * @param array $fields
 * @return mixed|null
 */
final public static function create(array $fields = []): mixed

/**
 * @param array $fields
 * @return int
 */
final public function update(array $fields = []): int

/**
 * @param array $fields
 * @return static
 */
final public static function updateMany(array $fields = []): static

/**
 * @return int
 */
final public static function set(): int

/**
 * @return int
 */
final public function delete(): int

/**
 * @return static
 */
final public static function deleteMany(): static

/**
 * @return int
 */
final public static function remove(): int
```

### Creating Objects

You can create a new object using the `create` method. The method accepts an array for fields.

```php
$fields = [
    'name' => 'John',
    'lastname' => 'Doe',
    'age' => 30
];

$user = User::create($fields); // Returns last inserted id if success
```

### Retrieving Objects

To retrieve a single object, you can use the `find` method. It accepts three parameters and returns the object, or empty
object if the object does not exist.

Parameter pairs;

* find($value) - Search for model's indexKey = $value
* find($key, $value) - Search for $key = $value
* find($key, $operator, $value) - Search for $key (>, >=, =, !=, <, <=) $value

```php
$user = User::find(123); // Get the user for primary key is '123'
// Or you can search for specific keys
$user = User::find('username', 'johndoe');
$user = User::find('status', '>=', 1);
```

There are several ways to get objects. You can build queries by chaining the methods.

The `select`,`where`,`orWhere` methods are starter methods;

The `get(1)` method returns a single object, while `get()` or `get(int $limit)` returns an array of objects. If there is
no result returns an empty object.

Here are a few examples for retrieving objects.

```php
// Retrieving all records
$users = User::get();
$users = User::select()::get();
$users = User::where()::get();

// Retrieving first records
$firstRecord = User::get(1);
$firstRecord = User::where(['status', '=', 1])::get(1);

// Retrieving first records id where status = 1
User::select(['id'])::where(['status', '=', '1'])::get(1);

// Retrieving name, lastname where created_at > 2023-01-01
User::select(['name', 'lastname'])::where(['created_at', '>', '2023-01-01'])::get();

// This will look like; where role = 1 OR status = 1
$users = User::where(['role', '=', 1])
    ::orWhere(['status', '=', 1])
    ::get();
    
// When you give multiple arrays to "where" and "orWhere" methods they automatically groups
// This will look like; where (role = 1 AND verification_status = 1)
$users = User::where(['role', '=', 1], ['verification_status', '=', 1])::get();

// This will look like; where (role = 1 AND verification_status = 1) AND created_at > 2023-01-01
$users = User::where(['role', '=', 1], ['verification_status', '=', 1])
    ::where(['created_at', '>', '2023-01-01'])
    ::get();
    
// This will look like; where (role = 1 AND verification_status = 1) OR (status = 1 AND verification_status = 1)
$users = User::where(['role', '=', 1], ['verification_status', '=', 1])
    ::orWhere(['status', '=', 1], ['verification_status', '=', 1])
    ::get();
```

### Updating Objects

The `update` and `updateMany` methods can be using for updating objects. `updateMany` method accepts an array as
parameters.

`update` and `set` methods executes the query and returns the affected record count.

```php
$user = User::find('1');
$result = $user->update(['status' => '1']);

// This will look like; SET status = 1 WHERE status != 1
$result = User::updateMany(['status' => '1'])::where(['status', '!=', '1'])::set();
// This will look like; SET status = 1 WHERE status != 1 OR id = 1
$result = User::updateMany(['status' => '1'])::where(['status', '!=', '1'])::orWhere(['id', '=', '1'])::set();
```

### Deleting Objects

To delete objects, you can use `delete` and `deleteMany` method.

`delete` and `remove` methods executes the query and returns the affected record count.

```php
$user = User::find(1);
$result = $user->delete();

// This will look like; where created at < 2023-01-01 or deleted = 1
$user = User::deleteMany()::where(['created_at', '<', '2023-01-01'])::orwhere(['deleted','=','1'])::remove();
```

## More Examples

```php
$fields = [
    'name' => 'John',
    'lastname' => 'Doe'
];
$user = User::find(1);
```

Assume that each example on below will continue after this part.

### isEmpty()

```php
/* Returns true if $user's properties are empty, otherwise false.*/
if ($user->isEmpty())
    throw new \Exception('User not found');
```

### hasDiff()

```php
/* Returns true if there is differences between $fields and $user's properties, otherwise false.*/
if (!$user->hasDiff($fields))
    throw new \Exception('All columns up to date.');
    
/* Returns keys of the differences from $fields if exists, otherwise false.*/
$hasDiff = $user->hasDiff($fields, true);
if ($hasDiff) {
    var_dump($hasDiff);
} else {
    throw new \Exception('All columns up to date.');
}
```

### validate()

```php
/* Returns true if fields are validated, otherwise false.*/
if (!$user->validate($fields))
    throw new \Exception('Fields not validated.');

/* Returns AttributeException if one of the field's validation fails, otherwise true.*/
try {
    if ($user->validate($fields, true)){
        //...
    }
} catch (\QMapper\Exceptions\AttributeException|\Exception $ex) {
    echo $ex->getMessage();
}
```

### tree()

See `bigint('license')` field at [Creating a Model](#creating-a-model) for relation specifying example.

```php
/* Changes property values to related models if the relation specified,
so related model can be reachable in $user.*/
$userTree = $user->tree();
/* Sets property value as array from related model, but object can't be reachable.*/
$userTree = $user->tree(true);
```

### Example Update Flow

```php
if ($user->isEmpty())
    throw new \Exception('User not found');
    
if (!$user->hasDiff($fields))
    throw new \Exception('All columns up to date.');

if (!$user->validate($fields))
    throw new \Exception('Fields not validated.');
    
$result = $user->update($fields);
if (!$result)
    throw new \Exception('Nothing was updated.');

echo 'User updated.';
```