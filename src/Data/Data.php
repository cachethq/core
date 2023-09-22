<?php

namespace Cachet\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

abstract class Data implements Arrayable, \ArrayAccess, \JsonSerializable, \Stringable
{
    /**
     * Create a new data object from a form request.
     */
    final public static function fromRequest(FormRequest $request): static
    {
        return static::fromArray(array_filter($request->validated()));
    }

    /**
     * Create a new data object from an array.
     */
    public static function fromArray(array $data): static
    {
        return new static(
            ...collect($data)->mapWithKeys(
                fn ($value, $key) => [
                    Str::of($key)->camel()->toString() => $value,
                ]
            )
                ->all()
        );
    }

    /**
     * Create a new data object from a JSON string.
     */
    public static function fromJson(string $json): static
    {
        return static::fromArray(
            json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR),
        );
    }

    /**
     * Determine if a given index exists on the data object.
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->toArray()[$offset]);
    }

    /**
     * Retrieve the value of the given index.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset];
    }

    /**
     * Set the value of the given index.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $klass = static::class;

        throw new \Error("Cannot modify readonly property $klass::\${$offset}", code: 2);
    }

    /**
     * Remove the given index from the data object.
     */
    public function offsetUnset(mixed $offset): void
    {
        $klass = static::class;

        throw new \Error("Cannot modify readonly property $klass::\${$offset}", code: 2);
    }

    /**
     * Get the array representation of the data object.
     */
    final public function toArray(): array
    {
        $properties = (new \ReflectionClass($this))->getProperties(
            \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_READONLY
        );

        return collect($properties)->mapWithKeys(
            fn (\ReflectionProperty $property) => [
                Str::of($property->getName())->snake()->toString() => $property->getValue($this),
            ]
        )->all();
    }

    /**
     * Get the data to be serialized into JSON.
     */
    public function jsonSerialize(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * JSON serialize the object into a string.
     */
    public function __toString(): string
    {
        return $this->jsonSerialize();
    }
}
