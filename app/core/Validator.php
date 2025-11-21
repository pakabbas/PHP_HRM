<?php

class Validator
{
    public static function required(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $field => $label) {
            if (empty($data[$field])) {
                $errors[$field] = "{$label} is required.";
            }
        }
        return $errors;
    }

    public static function email(?string $value, string $label): ?string
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return "{$label} is not a valid email.";
        }
        return null;
    }

    public static function date(?string $value, string $label): ?string
    {
        if ($value && !strtotime($value)) {
            return "{$label} must be a valid date.";
        }
        return null;
    }
}

