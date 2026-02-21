<?php
namespace App\Utilities;

class Validator {
    private $errors = [];

    public function validate($data, $rules) {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return [
            'valid' => empty($this->errors),
            'errors' => $this->errors
        ];
    }

    private function applyRule($field, $value, $rule) {
        if (strpos($rule, ':') !== false) {
            [$ruleName, $param] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $param = null;
        }

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
                }
                break;
            case 'min':
                if (strlen($value) < (int)$param) {
                    $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . " must be at least {$param} characters";
                }
                break;
            case 'max':
                if (strlen($value) > (int)$param) {
                    $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . " must not exceed {$param} characters";
                }
                break;
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' must be a valid email';
                }
                break;
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' must be a number';
                }
                break;
            case 'optional':
                // Skip validation if field is empty
                break;
        }
    }
}

