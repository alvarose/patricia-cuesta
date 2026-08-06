<?php

namespace App\Domain\Patients\Patient\DTO;

final readonly class PatientFilterParams
{
    public function __construct(
        public string $search = '',
        public string $status = 'all',
    ) {}

    public function hasSearch(): bool
    {
        return $this->search !== '';
    }

    public function hasStatus(): bool
    {
        return $this->status !== '' && $this->status !== 'all';
    }
}
