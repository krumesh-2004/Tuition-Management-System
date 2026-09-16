<?php
/**
 * Validator
 * Small reusable helper for form validation and input sanitisation.
 */
class Validator
{
    private array $errors = [];

    public function required(array $data, array $fields): self
    {
        foreach ($fields as $field => $label) {
            if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                $this->errors[$field] = "{$label} is required.";
            }
        }
        return $this;
    }

    public function email(array $data, string $field, string $label = 'Email'): self
    {
        if (!empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$label} must be a valid email address.";
        }
        return $this;
    }

    public function minLength(array $data, string $field, int $length, string $label): self
    {
        if (!empty($data[$field]) && strlen($data[$field]) < $length) {
            $this->errors[$field] = "{$label} must be at least {$length} characters.";
        }
        return $this;
    }

    public function numeric(array $data, string $field, string $label): self
    {
        if (isset($data[$field]) && $data[$field] !== '' && !is_numeric($data[$field])) {
            $this->errors[$field] = "{$label} must be a number.";
        }
        return $this;
    }

    public function passwordsMatch(array $data, string $field1, string $field2): self
    {
        if (($data[$field1] ?? '') !== ($data[$field2] ?? '')) {
            $this->errors[$field2] = "Passwords do not match.";
        }
        return $this;
    }

    public function fails(): bool { return !empty($this->errors); }
    public function errors(): array { return $this->errors; }
    public function first(): ?string { return $this->errors ? array_values($this->errors)[0] : null; }

    public static function clean(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}
