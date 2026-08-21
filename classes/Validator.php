<?php
/**
 * Kelas Validator (Validasi & Sanitasi Data Input)
 * Memenuhi Langkah Kerja 2 (Security: Input Validation & Sanitization) & Langkah Kerja 8 (OOP)
 */

class Validator {
    private array $errors = [];
    private array $data = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * Memeriksa apakah suatu field wajib diisi
     */
    public function required(string $field, string $customName = ''): self {
        $name = $customName ?: $field;
        if (!isset($this->data[$field]) || trim((string)$this->data[$field]) === '') {
            $this->errors[$field][] = "Field {$name} wajib diisi.";
        }
        return $this;
    }

    /**
     * Memeriksa apakah nilai bertipe angka/numerik dan >= 0
     */
    public function numericNonNegative(string $field, string $customName = ''): self {
        $name = $customName ?: $field;
        if (isset($this->data[$field]) && trim((string)$this->data[$field]) !== '') {
            if (!is_numeric($this->data[$field]) || (float)$this->data[$field] < 0) {
                $this->errors[$field][] = "Field {$name} harus berupa angka positif atau nol.";
            }
        }
        return $this;
    }

    /**
     * Memeriksa panjang minimal string
     */
    public function minLength(string $field, int $min, string $customName = ''): self {
        $name = $customName ?: $field;
        if (isset($this->data[$field]) && mb_strlen(trim((string)$this->data[$field])) < $min) {
            $this->errors[$field][] = "Field {$name} minimal harus {$min} karakter.";
        }
        return $this;
    }

    /**
     * Memeriksa apakah input valid tanpa ada error
     */
    public function isValid(): bool {
        return empty($this->errors);
    }

    /**
     * Mengambil seluruh pesan error dalam format associative array
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * Mengambil error pertama untuk ditampilkan pada form
     */
    public function getFirstError(): ?string {
        if (!empty($this->errors)) {
            $firstField = array_key_first($this->errors);
            return $this->errors[$firstField][0] ?? null;
        }
        return null;
    }
}
