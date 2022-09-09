<?php

/**
 * @project       _Statusanzeige/StatusanzeigeHomematicIP
 * @file          SAHMIP_Control.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection PhpUnused */

declare(strict_types=1);

trait SAHMIP_Control
{
    /**
     * Toggles the module inactive or active.
     *
     * @param bool $State
     * @throws Exception
     */
    public function ToggleActive(bool $State): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $statusText = 'Aus';
        if ($State) {
            $statusText = 'An';
        }
        $this->SendDebug(__FUNCTION__, 'Status: ' . $statusText, 0);
        $this->SetValue('Active', $State);
        //Inactive
        if (!$State) {
            //Set the values for the upper light unit from the automatic deactivation properties
            $this->WriteAttributeInteger('UpperLightUnitLastColor', $this->GetValue('UpperLightUnitColor'));
            if ($this->ReadPropertyBoolean('DeactivateUpperLightUnitChangeColor')) {
                $this->SetColor(0, $this->ReadPropertyInteger('DeactivationUpperLightUnitColor'));
            }
            $this->WriteAttributeInteger('UpperLightUnitLastBrightness', $this->GetValue('UpperLightUnitBrightness'));
            if ($this->ReadPropertyBoolean('DeactivateUpperLightUnitChangeBrightness')) {
                $this->SetBrightness(0, $this->ReadPropertyInteger('DeactivationUpperLightUnitBrightness'));
            }
            //Set the values for the lower light unit from the automatic deactivation properties
            $this->WriteAttributeInteger('LowerLightUnitLastColor', $this->GetValue('LowerLightUnitColor'));
            if ($this->ReadPropertyBoolean('DeactivateLowerLightUnitChangeColor')) {
                $this->SetColor(1, $this->ReadPropertyInteger('DeactivationLowerLightUnitColor'));
            }
            $this->WriteAttributeInteger('LowerLightUnitLastBrightness', $this->GetValue('LowerLightUnitBrightness'));
            if ($this->ReadPropertyBoolean('DeactivateLowerLightUnitChangeBrightness')) {
                $this->SetBrightness(1, $this->ReadPropertyInteger('DeactivationLowerLightUnitBrightness'));
            }
        }

        //Active
        else {
            //Upper light unit
            if ($this->ValidateTriggerList(0)) {
                $this->UpdateUpperLightUnit();
            } else {
                if ($this->ReadPropertyBoolean('ReactivateUpperLightUnitLastColor')) {
                    $this->SetColor(0, $this->ReadAttributeInteger('UpperLightUnitLastColor'));
                } else {
                    $this->UpdateColorFromDeviceColor(0);
                }
                if ($this->ReadPropertyBoolean('ReactivateUpperLightUnitLastBrightness')) {
                    $this->SetBrightness(0, $this->ReadAttributeInteger('UpperLightUnitLastBrightness'));
                } else {
                    $this->UpdateBrightnessFromDeviceLevel(0);
                }
            }
            //Lower light unit
            if ($this->ValidateTriggerList(1)) {
                $this->UpdateLowerLightUnit();
            } else {
                if ($this->ReadPropertyBoolean('ReactivateLowerLightUnitLastColor')) {
                    $this->SetColor(1, $this->ReadAttributeInteger('LowerLightUnitLastColor'));
                } else {
                    $this->UpdateColorFromDeviceColor(1);
                }
                if ($this->ReadPropertyBoolean('ReactivateLowerLightUnitLastBrightness')) {
                    $this->SetBrightness(1, $this->ReadAttributeInteger('LowerLightUnitLastBrightness'));
                } else {
                    $this->UpdateBrightnessFromDeviceLevel(1);
                }
            }
        }
    }

    /**
     * Starts the automatic deactivation.
     *
     * @throws Exception
     */
    public function StartAutomaticDeactivation(): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $this->ToggleActive(false);
        $this->SetAutomaticDeactivationTimer();
    }

    /**
     * Stops the automatic deactivation.
     *
     * @throws Exception
     */
    public function StopAutomaticDeactivation(): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $this->ToggleActive(true);
        $this->SetAutomaticDeactivationTimer();
    }

    #################### Private

    /**
     * Sets the timer for the automatic deactivation.
     * @throws Exception
     */
    private function SetAutomaticDeactivationTimer(): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $use = $this->ReadPropertyBoolean('UseAutomaticDeactivation');
        //Start
        $milliseconds = 0;
        if ($use) {
            $milliseconds = $this->GetInterval('AutomaticDeactivationStartTime');
        }
        $this->SetTimerInterval('StartAutomaticDeactivation', $milliseconds);
        //End
        $milliseconds = 0;
        if ($use) {
            $milliseconds = $this->GetInterval('AutomaticDeactivationEndTime');
        }
        $this->SetTimerInterval('StopAutomaticDeactivation', $milliseconds);
    }

    /**
     * Gets the interval for a timer.
     *
     * @param string $TimerName
     * @return int
     * @throws Exception
     */
    private function GetInterval(string $TimerName): int
    {
        $timer = json_decode($this->ReadPropertyString($TimerName));
        $now = time();
        $hour = $timer->hour;
        $minute = $timer->minute;
        $second = $timer->second;
        $definedTime = $hour . ':' . $minute . ':' . $second;
        if (time() >= strtotime($definedTime)) {
            $timestamp = mktime($hour, $minute, $second, (int) date('n'), (int) date('j') + 1, (int) date('Y'));
        } else {
            $timestamp = mktime($hour, $minute, $second, (int) date('n'), (int) date('j'), (int) date('Y'));
        }
        return ($timestamp - $now) * 1000;
    }

    /**
     * Checks the status of the automatic deactivation timer.
     *
     * @return bool
     * @throws Exception
     */
    private function CheckAutomaticDeactivationTimer(): bool
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        if (!$this->ReadPropertyBoolean('UseAutomaticDeactivation')) {
            return false;
        }
        $start = $this->GetTimerInterval('StartAutomaticDeactivation');
        $stop = $this->GetTimerInterval('StopAutomaticDeactivation');
        if ($start > $stop) {
            //Deactivation timer is active, must be toggled to inactive
            $this->ToggleActive(false);
            return true;
        } else {
            //Deactivation timer is inactive, must be toggled to active
            $this->ToggleActive(true);
            return false;
        }
    }
}