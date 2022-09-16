<?php

declare(strict_types=1);

include_once __DIR__ . '/stubs/Validator.php';

class StatusanzeigeValidationTest extends TestCaseSymconValidation
{
    public function testValidateLibrary(): void
    {
        $this->validateLibrary(__DIR__ . '/..');
    }

    public function testValidateModule_Statusanzeige(): void
    {
        $this->validateModule(__DIR__ . '/../Statusanzeige');
    }

    public function testValidateModule_StatusanzeigeHomematic(): void
    {
        $this->validateModule(__DIR__ . '/../StatusanzeigeHomematic');
    }

    public function testValidateModule_StatusanzeigeHomematicIP(): void
    {
        $this->validateModule(__DIR__ . '/../StatusanzeigeHomematicIP');
    }
}