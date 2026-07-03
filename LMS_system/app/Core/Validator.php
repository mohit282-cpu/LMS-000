<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    public static function make(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $fieldRules = is_array($fieldRules) ? $fieldRules : explode('|', (string) $fieldRules);

            foreach ($fieldRules as $rule) {
                $parameters = [];

                if (str_contains((string) $rule, ':')) {
                    [$rule, $parameterString] = explode(':', (string) $rule, 2);
                    $parameters = explode(',', $parameterString);
                }

                if ($rule === 'nullable' && ($value === null || $value === '')) {
                    break;
                }

                if ($rule === 'required' && ($value === null || trim((string) $value) === '')) {
                    $errors[$field][] = self::label($field) . ' is required.';
                }

                if ($rule === 'email' && $value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                    $errors[$field][] = self::label($field) . ' must be a valid email address.';
                }

                if ($rule === 'min' && strlen((string) $value) < (int) ($parameters[0] ?? 0)) {
                    $errors[$field][] = self::label($field) . ' must be at least ' . (int) $parameters[0] . ' characters.';
                }

                if ($rule === 'max' && strlen((string) $value) > (int) ($parameters[0] ?? PHP_INT_MAX)) {
                    $errors[$field][] = self::label($field) . ' may not be greater than ' . (int) $parameters[0] . ' characters.';
                }

                if ($rule === 'integer' && $value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $errors[$field][] = self::label($field) . ' must be an integer.';
                }

                if ($rule === 'numeric' && $value !== null && $value !== '' && ! is_numeric($value)) {
                    $errors[$field][] = self::label($field) . ' must be numeric.';
                }

                if ($rule === 'date' && $value !== null && $value !== '' && strtotime((string) $value) === false) {
                    $errors[$field][] = self::label($field) . ' must be a valid date.';
                }

                if ($rule === 'in' && $value !== null && $value !== '' && ! in_array((string) $value, $parameters, true)) {
                    $errors[$field][] = self::label($field) . ' has an invalid value.';
                }
            }
        }

        return $errors;
    }

    private static function label(string $field): string
    {
        return ucfirst(str_replace('_', ' ', $field));
    }
}

