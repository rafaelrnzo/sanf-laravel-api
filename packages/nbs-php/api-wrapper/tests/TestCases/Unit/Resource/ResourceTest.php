<?php

namespace Tests\TestCases\Unit\Resource;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Resource\Resources\BasicApiResource;

class ResourceTest extends TestCase
{
    /**
     * Expected behavior:
     * - Provided attributes are merged with default attributes.
     */
    public function testConstruct()
    {
        $data = [
            'abc' => 123
        ];
        $resource = new BasicApiResource($data);

        $this->assertCount(2, $resource->attributes);
        $this->assertArrayHasKey('id', $resource->attributes);
        $this->assertArrayHasKey('abc', $resource->attributes);
        $this->assertEquals($data['abc'], $resource->attributes['abc']);
    }

    /**
     * Expected Behavior:
     * - All dynamic attribute retrievals pass through to getAttribute function.
     */
    public function testGet()
    {
        $data = [
            'abc' => 123
        ];
        $resource = new BasicApiResource($data);
        $resource->didGetAttributes = [];

        $this->assertEquals($data['abc'], $resource->abc);
        $this->assertCount(1, $resource->didGetAttributes);
        $this->assertEquals('abc', $resource->didGetAttributes[0]);
    }

    /**
     * Expected Behavior:
     * - All dynamic attribute sets pass through to setAttribute function.
     */
    public function testSet()
    {
        $resource = new BasicApiResource();
        $resource->didSetAttributes = [];
        $resource->abc = 123;

        $this->assertEquals(123, $resource->attributes['abc']);
        $this->assertCount(1, $resource->didSetAttributes);
        $this->assertEquals('abc', $resource->didSetAttributes[0]);
    }

    /**
     * Expected Behavior:
     * - Determine if the specified key is in the attribute array.
     */
    public function testHasAttribute()
    {
        $resource = new BasicApiResource();
        $resource->attributes = [];

        $this->assertFalse($resource->hasAttribute('abc'));

        $resource->attributes = ['abc' => 123];
        $this->assertTrue($resource->hasAttribute('abc'));
    }

    /**
     * Expected Behavior:
     * - Value is passed through from attributes array
     * - Keys not in the attributes array return null value
     */
    public function testGetAttribute()
    {
        $resource = new BasicApiResource();

        $resource->attributes = ['string' => 'abc'];
        $this->assertEquals($resource->attributes['string'], $resource->getAttribute('string'));

        $resource->attributes = [];
        $this->assertNull($resource->getAttribute('unknown'));
    }

    /**
     * Expected Behavior:
     * - Keys in casts array are cast then passed through to attributes array
     * - Otherwise, value is passed through to attributes array
     * - Attributes added to dirty array
     */
    public function testSetAttribute()
    {
        $resource = new BasicApiResource();
        $resource->attributes = [];

        $resource->casts = ['int' => 'int'];
        $resource->setAttribute('int', '123');
        $this->assertArrayHasKey('int', $resource->attributes);
        $this->assertArrayHasKey('int', $resource->dirty);
        $this->assertIsInt($resource->attributes['int']);
        $this->assertIsInt($resource->dirty['int']);
        $this->assertEquals(123, $resource->attributes['int']);
        $this->assertEquals(123, $resource->dirty['int']);

        $resource->setAttribute('value', 123);
        $this->assertArrayHasKey('value', $resource->attributes);
        $this->assertArrayHasKey('value', $resource->dirty);
        $this->assertEquals(123, $resource->attributes['value']);
        $this->assertEquals(123, $resource->dirty['value']);
    }

    /**
     * Expected Behavior:
     * - If $clear is false, merge provided array into the attributes array
     * - If $clear is true, overwrite attributes array completely with the provided array
     * - Keys in casts array are cast then passed through to attributes array
     * - Attributes added to dirty array
     * - Returns $this
     */
    public function testSetAttributes()
    {
        $oldData = [
            'abc' => 123,
            'def' => 345
        ];
        $resource = new BasicApiResource();
        $resource->attributes = $oldData;
        $resource->casts = ['int' => 'int'];

        $newData = [
            'abc' => 0,
            'xyz' => 345,
            'int' => '123',
        ];

        $returnValue = $resource->setAttributes($newData);

        $this->assertEquals($resource, $returnValue);
        $this->assertCount(4, $resource->attributes);
        $this->assertArrayHasKey('abc', $resource->attributes);
        $this->assertEquals($newData['abc'], $resource->attributes['abc']);
        $this->assertArrayHasKey('def', $resource->attributes);
        $this->assertEquals($oldData['def'], $resource->attributes['def']);
        $this->assertArrayHasKey('xyz', $resource->attributes);
        $this->assertEquals($newData['xyz'], $resource->attributes['xyz']);
        $this->assertArrayHasKey('int', $resource->attributes);
        $this->assertIsInt($resource->attributes['int']);
        $this->assertEquals(123, $resource->attributes['int']);

        $this->assertCount(3, $resource->dirty);
        $this->assertArrayHasKey('abc', $resource->dirty);
        $this->assertEquals($newData['abc'], $resource->dirty['abc']);
        $this->assertArrayHasKey('xyz', $resource->dirty);
        $this->assertEquals($newData['xyz'], $resource->dirty['xyz']);
        $this->assertArrayHasKey('int', $resource->dirty);
        $this->assertIsInt($resource->dirty['int']);
        $this->assertEquals(123, $resource->dirty['int']);

        $resource->setAttributes($newData, true);

        $this->assertCount(3, $resource->attributes);
        $this->assertArrayHasKey('abc', $resource->attributes);
        $this->assertEquals($newData['abc'], $resource->attributes['abc']);
        $this->assertArrayHasKey('xyz', $resource->attributes);
        $this->assertEquals($newData['xyz'], $resource->attributes['xyz']);
        $this->assertArrayNotHasKey('def', $resource->attributes);
        $this->assertArrayHasKey('int', $resource->attributes);
        $this->assertIsInt($resource->attributes['int']);
        $this->assertEquals(123, $resource->attributes['int']);

        $this->assertCount(3, $resource->dirty);
        $this->assertArrayHasKey('abc', $resource->dirty);
        $this->assertEquals($newData['abc'], $resource->dirty['abc']);
        $this->assertArrayHasKey('xyz', $resource->dirty);
        $this->assertEquals($newData['xyz'], $resource->dirty['xyz']);
        $this->assertArrayHasKey('int', $resource->dirty);
        $this->assertIsInt($resource->dirty['int']);
        $this->assertEquals(123, $resource->dirty['int']);
    }

    /**
     * Expected Behavior:
     * - Data merged into attribute array
     * - Attributes added to dirty array
     * - Returns $this
     */
    public function testMergeAttributes()
    {
        $oldData = [
            'abc' => 123,
            'def' => 345
        ];
        $resource = new BasicApiResource();
        $resource->attributes = $oldData;

        $newData = [
            'abc' => 0,
            'xyz' => 345
        ];

        $returnValue = $resource->mergeAttributes($newData);

        $this->assertEquals($resource, $returnValue);
        $this->assertCount(3, $resource->attributes);
        $this->assertArrayHasKey('abc', $resource->attributes);
        $this->assertEquals($newData['abc'], $resource->attributes['abc']);
        $this->assertArrayHasKey('def', $resource->attributes);
        $this->assertEquals($oldData['def'], $resource->attributes['def']);
        $this->assertArrayHasKey('xyz', $resource->attributes);
        $this->assertEquals($newData['xyz'], $resource->attributes['xyz']);

        $this->assertCount(2, $resource->dirty);
        $this->assertArrayHasKey('abc', $resource->dirty);
        $this->assertEquals($newData['abc'], $resource->dirty['abc']);
        $this->assertArrayHasKey('xyz', $resource->dirty);
        $this->assertEquals($newData['xyz'], $resource->dirty['xyz']);
    }

    /**
     * Expected Behavior:
     * - Array of dirty attribues is passed through
     */
    public function testGetDirty()
    {
        $resource = new BasicApiResource();
        $data = [
            'abc' => 123
        ];
        $resource->dirty = $data;

        $this->assertEquals($data, $resource->getDirty());
    }

    /**
     * Expected Behavior:
     * - Returns whether key is in casts array
     */
    public function testCastsAttribute()
    {
        $resource = new BasicApiResource();

        $this->assertFalse($resource->castsAttribute('abc'));

        $resource->casts = ['abc' => 'int'];
        $this->assertTrue($resource->castsAttribute('abc'));
    }

    /**
     * Expected Behavior:
     * - Returns value from casts array
     */
    public function testGetAttributeCastType()
    {
        $resource = new BasicApiResource();

        $this->assertNull($resource->getAttributeCastType('abc'));

        $resource->casts = ['abc' => 'int'];
        $this->assertEquals('int', $resource->getAttributeCastType('abc'));
    }

    /**
     * Expected Behavior:
     * - Attributes are cast to the appropriate type when set
     */
    public function testCastAs()
    {
        $resource = new BasicApiResource();
        $resource->casts = [
            'bool' => 'bool',
            'collection' => 'collection',
            'date' => 'date',
            'datetime' => 'datetime',
            'timestamp' => 'datetime',
            'float' => 'float',
            'int' => 'int',
            'string' => 'string',
            'class' => BasicApiResource::class,
            'classCollection' => BasicApiResource::class,
            'other' => 'abc'
        ];
        $data = [
            'bool' => 'true',
            'collection' => [
                'abc',
                'def'
            ],
            'date' => 'Jan 01, 2000',
            'datetime' => 'Jan 01, 2000 8:00 AM',
            'timestamp' => 946684800,
            'float' => '1.2',
            'int' => '10',
            'string' => 10,
            'class' => [
                'abc' => 123
            ],
            'classCollection' => [
                [
                    'abc' => 123
                ],
                [
                    'abc' => 123
                ]
            ],
            'other' => 123
        ];
        $resource->setAttributes($data, true);

        $this->assertIsBool($resource->bool);
        $this->assertEquals(true, $resource->bool);

        $this->assertInstanceOf(Collection::class, $resource->collection);
        $this->assertEquals($data['collection'], $resource->collection->toArray());

        $this->assertInstanceOf(Carbon::class, $resource->date);
        $this->assertEquals(Carbon::create($data['date']), $resource->date);

        $this->assertInstanceOf(Carbon::class, $resource->datetime);
        $this->assertEquals(Carbon::create($data['datetime']), $resource->datetime);

        $this->assertInstanceOf(Carbon::class, $resource->timestamp);
        $this->assertEquals(Carbon::createFromTimestamp($data['timestamp']), $resource->timestamp);

        $this->assertIsFloat($resource->float);
        $this->assertEquals(1.2, $resource->float);

        $this->assertIsInt($resource->int);
        $this->assertEquals(10, $resource->int);

        $this->assertIsString($resource->string);
        $this->assertEquals('10', $resource->string);

        $this->assertInstanceOf(BasicApiResource::class, $resource->class);
        $this->assertArrayHasKey('abc', $resource->class->toArray());
        $this->assertEquals(123, $resource->class->toArray()['abc']);

        $this->assertInstanceOf(Collection::class, $resource->classCollection);
        $this->assertCount(2, $resource->classCollection);
        foreach($resource->classCollection as $class) {
            $this->assertInstanceOf(BasicApiResource::class, $class);
            $this->assertArrayHasKey('abc', $class->toArray());
            $this->assertEquals(123, $class->toArray()['abc']);
        }

        $this->assertNull($resource->other);
    }

    /**
     * Expected Behavior:
     * - Attributes are encoded to a JSON string
     */
    public function testToJson()
    {
        $resource = new BasicApiResource();
        $data = [
            'abc' => 123
        ];
        $resource->attributes = $data;

        $this->assertEquals(json_encode($data), $resource->toJson());
    }

    /**
     * Expected Behavior:
     * - JSON string is used to set attributes of a new instance
     */
    public function testFromJson()
    {
        $data = [
            'id' => 1,
            'abc' => 123
        ];
        $json = json_encode($data);

        $resource = BasicApiResource::fromJson($json);

        $this->assertInstanceOf(BasicApiResource::class, $resource);
        $this->assertEquals($data, $resource->attributes);
    }

    /**
     * Expected Behavior:
     * - idField on class is returned
     */
    public function testGetIdField()
    {
        $resource = new BasicApiResource();
        $this->assertEquals('id', $resource->getIdField());

        $resource->idField = 'primary_key';
        $this->assertEquals('primary_key', $resource->getIdField());
    }

    /**
     * Expected Behavior:
     * - Value of id field in attributes array is returned
     */
    public function testGetId()
    {
        $resource = new BasicApiResource([
            'id' => 123,
            'primary_key' => 456
        ]);

        $this->assertEquals(123, $resource->getId());

        $resource->idField = 'primary_key';
        $this->assertEquals(456, $resource->getId());
    }

    /**
     * Expected Behavior:
     * - Array of attributes is used to create a new instance
     */
    public function testCast()
    {
        $data = [
            'id' => 1,
            'abc' => 123
        ];
        $resource = BasicApiResource::cast($data);

        $this->assertInstanceOf(BasicApiResource::class, $resource);
        $this->assertEquals($data, $resource->attributes);
    }

    /**
     * Expected Behavior:
     * - Attributes array is returned
     * - Attributes are cast to array values if possible
     */
    public function testToArray()
    {
        $data = [
            'id' => 1,
            'collection' => [
                'abc' => 123
            ],
            'class' => [
                'id' => 2,
                'abc' => 123
            ],
            'string' => 'abc',
            'int' => 123
        ];

        $resource = new BasicApiResource();
        $resource->casts = [
            'collection' => 'collection',
            'class' => BasicApiResource::class
        ];
        $resource->setAttributes($data);

        $array = $resource->toArray();
        $this->assertEquals($data, $array);
    }
}