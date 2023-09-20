<?php

namespace Cachet\Tests\Unit\DTOs\Components;

use Cachet\Data\Data;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

it('is immutable', function () {
    $dto = new class(
        name: 'My Data Object',
    ) extends Data {
        public function __construct(
            public readonly string $name,
        )
        {
        }
    };

    expect(fn() => $dto->name = 'foo')
        ->toThrow(\Error::class, 'Cannot modify readonly property Cachet\Data\Data@anonymous::$name');
});

it('can be built from an array', function () {
    class MyData extends Data
    {
        public function __construct(
            public readonly string $name,
        )
        {
        }
    }

    ;

    $dto = MyData::fromArray(data: $data = [
        'name' => 'My Data Object',
    ]);

    expect($dto)
        ->name->toBe($data['name']);
});

it('can be built from an JSON string', function () {
    class MyData extends Data
    {
        public function __construct(
            public readonly string $name,
        )
        {
        }
    }

    ;

    $dto = MyData::fromJson(json: '{"name": "My Data Object"}');

    expect($dto)
        ->name->toBe('My Data Object');
});

it('can be handle snake case array keys when being built from an array', function () {
    class MyData extends Data
    {
        public function __construct(
            public readonly string $name,
            public readonly string $snakeCaseTest,
        )
        {
        }
    }

    ;

    $dto = MyData::fromArray(data: $data = [
        'name' => 'My Data Object',
        'snake_case_test' => 'Foo',
    ]);

    expect($dto)
        ->name->toBe($data['name'])
        ->snakeCaseTest->toBe($data['snake_case_test']);
});

it('can be built from a request', function () {
    class MyData extends Data
    {
        public function __construct(
            public readonly string $name,
        )
        {
        }
    }

    ;

    $request = new FormRequest(request: $data = [
        'name' => 'My Data Object',
    ]);

    $request->setValidator(Validator::make($data, [
        'name' => ['required', 'string'],
    ]));

    $dto = MyData::fromRequest($request);

    expect($dto)
        ->name->toBe($data['name']);
});

it('can be cast to an array', function () {
    $dto = new class (
        name: 'My Data Object',
    ) extends Data {
        public function __construct(
            public readonly string $name,
        )
        {
        }
    };;

    expect($dto->toArray())
        ->toEqual([
            'name' => 'My Data Object',
        ]);
});

it('can be cast to a JSON string', function () {
    $dto = new class (
        name: 'My Data Object',
    ) extends Data {
        public function __construct(
            public readonly string $name,
        )
        {
        }
    };;

    expect($dto->jsonSerialize())
        ->toBeJson()
        ->toEqual('{"name":"My Data Object"}')
        ->and((string)$dto)
        ->toBeJson()
        ->toEqual('{"name":"My Data Object"}')
        ->and(strval($dto))
        ->toBeJson()
        ->toEqual('{"name":"My Data Object"}');
});
