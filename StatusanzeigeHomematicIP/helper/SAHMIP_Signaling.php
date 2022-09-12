<?php

/**
 * @project       _Statusanzeige/StatusanzeigeHomematicIP
 * @file          SAHMIP_Signaling.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection PhpSwitchStatementWitSingleBranchInspection */
/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

declare(strict_types=1);

trait SAHMIP_Signaling
{
    /**
     * Sets the color and brightness for a light unit.
     *
     * @param int $LightUnit
     * 0 =  Upper light unit
     * 1 =  Lower light unit
     *
     * @param int $Color
     * 0    = black / off
     * 1    = blue
     * 2    = green
     * 3    = turquoise
     * 4    = red
     * 5    = violet
     * 6    = yellow
     * 7    = white
     *
     * @param int $Brightness
     * @return bool
     * @throws Exception
     */
    public function SetDeviceSignaling(int $LightUnit, int $Color, int $Brightness): bool
    {
        $this->SendDebug(__FUNCTION__, 'wird  ausgeführt', 0);
        $this->SendDebug(__FUNCTION__, 'Leuchteinheit: ' . $LightUnit, 0);
        $this->SendDebug(__FUNCTION__, 'Farbe: ' . $Color, 0);
        $this->SendDebug(__FUNCTION__, 'Helligkeit: ' . $Brightness, 0);
        if ($this->CheckMaintenance()) {
            return false;
        }
        $result = true;
        $color = $this->SetColor($LightUnit, $Color);
        $brightness = $this->SetBrightness($LightUnit, $Brightness);
        if (!$color || !$brightness) {
            $result = false;
        }
        return $result;
    }

    /**
     * Updates the light units.
     *
     * @throws Exception
     */
    public function UpdateLightUnits(): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        if ($this->CheckMaintenance()) {
            return;
        }
        //Upper light unit
        if ($this->ValidateTriggerList(0)) {
            $this->UpdateUpperLightUnit();
        } else {
            $this->UpdateColorFromDeviceColor(0);
            $this->UpdateBrightnessFromDeviceLevel(0);
        }
        //Lower light unit
        if ($this->ValidateTriggerList(1)) {
            $this->UpdateLowerLightUnit();
        } else {
            $this->UpdateColorFromDeviceColor(1);
            $this->UpdateBrightnessFromDeviceLevel(1);
        }
    }

    /**
     * Updates the color from the actual device color.
     *
     * @param int $LightUnit
     * 0 =  Upper light unit
     * 1 =  Lower light unit
     *
     * @throws Exception
     */
    public function UpdateColorFromDeviceColor(int $LightUnit): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $this->SendDebug(__FUNCTION__, 'Leuchteinheit: ' . $LightUnit, 0);
        $deviceColorName = 'UpperLightUnitDeviceColor';
        $lightUnitColor = 'UpperLightUnitColor';
        if ($LightUnit == 1) {
            $deviceColorName = 'LowerLightUnitDeviceColor';
            $lightUnitColor = 'LowerLightUnitColor';
        }
        $id = $this->ReadPropertyInteger($deviceColorName);
        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
            $this->SendDebug(__FUNCTION__, 'Variable ID: ' . $id, 0);
            $color = GetValueInteger($id);
            $this->SendDebug(__FUNCTION__, 'Farbe: ' . $color, 0);
            $this->SetValue($lightUnitColor, $color);
        }
    }

    /**
     * Updates the brightness from the actual device level.
     *
     * @param int $LightUnit
     * 0 =  Upper light unit
     * 1 =  Lower light unit
     *
     * @throws Exception
     */
    public function UpdateBrightnessFromDeviceLevel(int $LightUnit): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $this->SendDebug(__FUNCTION__, 'Leuchteinheit: ' . $LightUnit, 0);
        $deviceBrightnessName = 'UpperLightUnitDeviceBrightness';
        $lightUnitBrightness = 'UpperLightUnitBrightness';
        if ($LightUnit == 1) {
            $deviceBrightnessName = 'LowerLightUnitDeviceBrightness';
            $lightUnitBrightness = 'LowerLightUnitBrightness';
        }
        $id = $this->ReadPropertyInteger($deviceBrightnessName);
        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
            $this->SendDebug(__FUNCTION__, 'Variable ID: ' . $id, 0);
            $brightness = GetValueFloat($id) * 100;
            $this->SendDebug(__FUNCTION__, 'Helligkeit: ' . $brightness, 0);
            $this->SetValue($lightUnitBrightness, $brightness);
        }
    }

    #################### Private

    /**
     * Updates the color and the brightness from trigger list for the upper light unit.
     *
     * @throws Exception
     */
    public function UpdateUpperLightUnit(): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        if ($this->CheckMaintenance()) {
            return;
        }
        $this->CheckTriggerConditions(0);
    }

    /**
     * Updates the color and the brightness from trigger list for the lower light unit.
     *
     * @throws Exception
     */
    public function UpdateLowerLightUnit(): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        if ($this->CheckMaintenance()) {
            return;
        }
        $this->CheckTriggerConditions(1);
    }

    /**
     * Sets the device color of a light unit.
     *
     * @param int $LightUnit
     * 0 =  Upper light unit
     * 1 =  Lower light unit
     *
     * @param int $Color
     * 0    = black / off
     * 1    = blue
     * 2    = green
     * 3    = turquoise
     * 4    = red
     * 5    = violet
     * 6    = yellow
     * 7    = white
     *
     * @return bool
     * @throws Exception
     */
    private function SetColor(int $LightUnit, int $Color): bool
    {
        $this->SendDebug(__FUNCTION__, 'wird  ausgeführt', 0);
        $this->SendDebug(__FUNCTION__, 'Leuchteinheit: ' . $LightUnit, 0);
        $this->SendDebug(__FUNCTION__, 'Farbe: ' . $Color, 0);
        $result = false;
        //Upper light unit
        if ($LightUnit == 0) {
            $this->UpdateColorFromDeviceColor(0);
            $actualColor = $this->GetValue('UpperLightUnitColor');
            $this->SetValue('UpperLightUnitColor', $Color);
            if ($actualColor == $Color) {
                $this->SendDebug(__FUNCTION__, 'Es wird bereits der gleiche Farbwert angezeigt!', 0);
                return true;
            }
            $id = $this->ReadPropertyInteger('UpperLightUnit');
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                switch ($this->ReadPropertyInteger('UpperLightUnitDeviceType')) {
                    case 1: //HmIP-BSL Channel 8
                    case 2: //HmIP-MP3P Channel 6
                        $commandControl = $this->ReadPropertyInteger('CommandControl');
                        if ($commandControl > 1 && @IPS_ObjectExists($commandControl)) {
                            $commands = [];
                            $commands[] = '@HM_WriteValueInteger(' . $id . ", 'COLOR', '" . $Color . "');";
                            $this->SendDebug(__FUNCTION__, 'Befehle: ' . json_encode(json_encode($commands)), 0);
                            $scriptText = self::ABLAUFSTEUERUNG_MODULE_PREFIX . '_ExecuteCommands(' . $commandControl . ', ' . json_encode(json_encode($commands)) . ');';
                            $this->SendDebug(__FUNCTION__, 'Ablaufsteuerung: ' . $scriptText, 0);
                            $result = @IPS_RunScriptText($scriptText);
                        } else {
                            $this->SendDebug(__FUNCTION__, 'Befehl: @HM_WriteValueInteger(' . $id . ", 'COLOR', " . $Color . ');', 0);
                            IPS_Sleep($this->ReadPropertyInteger('UpperLightUnitSwitchingDelay'));
                            $result = @HM_WriteValueInteger($id, 'COLOR', $Color);
                            if (!$result) {
                                IPS_Sleep($this->ReadPropertyInteger('UpperLightUnitSwitchingDelay'));
                                $result = @HM_WriteValueInteger($id, 'COLOR', $Color);
                            }
                        }
                        if ($result) {
                            $this->SendDebug(__FUNCTION__, 'Der Farbwert ' . $Color . ' wurde für die obere Leuchteinheit ID ' . $id . ' eingestellt.', 0);
                        } else {
                            $this->SendDebug(__FUNCTION__, 'Abbruch, der Farbwert ' . $Color . ' konnte für die obere Leuchteinheit ID ' . $id . ' nicht eingestellt werden!', 0);
                            //Revert color
                            $this->SetValue('UpperLightUnitColor', $actualColor);
                        }
                        break;
                }
            }
        }
        //Lower light unit
        if ($LightUnit == 1) {
            $this->UpdateColorFromDeviceColor(1);
            $actualColor = $this->GetValue('LowerLightUnitColor');
            if ($actualColor == $Color) {
                $this->SendDebug(__FUNCTION__, 'Es wird bereits der gleiche Farbwert angezeigt!', 0);
                return true;
            }
            $this->SetValue('LowerLightUnitColor', $Color);
            $id = $this->ReadPropertyInteger('LowerLightUnit');
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                switch ($this->ReadPropertyInteger('LowerLightUnitDeviceType')) {
                    case 1: //HmIP-BSL Channel 12
                        $commandControl = $this->ReadPropertyInteger('CommandControl');
                        if ($commandControl > 1 && @IPS_ObjectExists($commandControl)) {
                            $commands = [];
                            $commands[] = '@HM_WriteValueInteger(' . $id . ", 'COLOR', '" . $Color . "');";
                            $this->SendDebug(__FUNCTION__, 'Befehle: ' . json_encode(json_encode($commands)), 0);
                            $scriptText = self::ABLAUFSTEUERUNG_MODULE_PREFIX . '_ExecuteCommands(' . $commandControl . ', ' . json_encode(json_encode($commands)) . ');';
                            $this->SendDebug(__FUNCTION__, 'Ablaufsteuerung: ' . $scriptText, 0);
                            $result = @IPS_RunScriptText($scriptText);
                        } else {
                            $this->SendDebug(__FUNCTION__, 'Befehl: @HM_WriteValueInteger(' . $id . ", 'COLOR', " . $Color . ');', 0);
                            IPS_Sleep($this->ReadPropertyInteger('LowerLightUnitSwitchingDelay'));
                            $result = @HM_WriteValueInteger($id, 'COLOR', $Color);
                            if (!$result) {
                                IPS_Sleep($this->ReadPropertyInteger('LowerLightUnitSwitchingDelay'));
                                $result = @HM_WriteValueInteger($id, 'LEVEL', $Color);
                            }
                        }
                        if ($result) {
                            $this->SendDebug(__FUNCTION__, 'Der Farbwert ' . $Color . ' wurde für die untere Leuchteinheit ID ' . $id . ' eingestellt.', 0);
                        } else {
                            $this->SendDebug(__FUNCTION__, 'Abbruch, der Farbwert ' . $Color . ' konnte für die untere Leuchteinheit ID ' . $id . ' nicht eingestellt werden!', 0);
                            //Revert color
                            $this->SetValue('LowerLightUnitBrightness', $actualColor);
                        }
                        break;
                }
            }
        }
        return $result;
    }

    /**
     * Sets the device brightness of a light unit.
     *
     * @param int $LightUnit
     * 0 =  Upper light unit
     * 1 =  Lower light unit
     *
     * @param int $Brightness
     * @return bool
     * @throws Exception
     */
    private function SetBrightness(int $LightUnit, int $Brightness): bool
    {
        $this->SendDebug(__FUNCTION__, 'wird  ausgeführt', 0);
        $this->SendDebug(__FUNCTION__, 'Leuchteinheit: ' . $LightUnit, 0);
        $this->SendDebug(__FUNCTION__, 'Helligkeit: ' . $Brightness, 0);
        $result = false;
        //Upper light unit
        if ($LightUnit == 0) {
            $this->UpdateBrightnessFromDeviceLevel(0);
            $actualBrightness = $this->GetValue('UpperLightUnitBrightness');
            $this->SetValue('UpperLightUnitBrightness', $Brightness);
            if ($actualBrightness == $Brightness) {
                $this->SendDebug(__FUNCTION__, 'Es wird bereits die gleiche Helligkeit verwendet!', 0);
                return true;
            }
            $deviceBrightness = $this->GetValue('UpperLightUnitBrightness') / 100;
            $id = $this->ReadPropertyInteger('UpperLightUnit');
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                switch ($this->ReadPropertyInteger('UpperLightUnitDeviceType')) {
                    case 1: //HmIP-BSL Channel 8
                    case 2: //HmIP-MP3P Channel 6
                        $commandControl = $this->ReadPropertyInteger('CommandControl');
                        if ($commandControl > 1 && @IPS_ObjectExists($commandControl)) {
                            $commands = [];
                            $commands[] = '@HM_WriteValueFloat(' . $id . ", 'LEVEL', '" . $deviceBrightness . "');";
                            $this->SendDebug(__FUNCTION__, 'Befehle: ' . json_encode(json_encode($commands)), 0);
                            $scriptText = self::ABLAUFSTEUERUNG_MODULE_PREFIX . '_ExecuteCommands(' . $commandControl . ', ' . json_encode(json_encode($commands)) . ');';
                            $this->SendDebug(__FUNCTION__, 'Ablaufsteuerung: ' . $scriptText, 0);
                            $result = @IPS_RunScriptText($scriptText);
                        } else {
                            $this->SendDebug(__FUNCTION__, 'Befehl: @HM_WriteValueFloat(' . $id . ", 'LEVEL', " . $deviceBrightness . ');', 0);
                            IPS_Sleep($this->ReadPropertyInteger('UpperLightUnitSwitchingDelay'));
                            $result = @HM_WriteValueFloat($id, 'LEVEL', $deviceBrightness);
                            if (!$result) {
                                IPS_Sleep($this->ReadPropertyInteger('UpperLightUnitSwitchingDelay'));
                                $result = @HM_WriteValueFloat($id, 'LEVEL', $deviceBrightness);
                            }
                        }
                        if ($result) {
                            $this->SendDebug(__FUNCTION__, 'Der Helligkeitswert ' . $deviceBrightness . ' wurde für die obere Leuchteinheit ID ' . $id . ' eingestellt.', 0);
                        } else {
                            $this->SendDebug(__FUNCTION__, 'Abbruch, der Helligkeitswert ' . $deviceBrightness . ' konnte für die obere Leuchteinheit ID ' . $id . ' nicht eingestellt werden!', 0);
                            //Revert brightness
                            $this->SetValue('UpperLightUnitBrightness', $actualBrightness);
                        }
                       break;
                }
            }
        }
        //Lower light unit
        if ($LightUnit == 1) {
            $this->UpdateBrightnessFromDeviceLevel(1);
            $actualBrightness = $this->GetValue('LowerLightUnitBrightness');
            $this->SetValue('LowerLightUnitBrightness', $Brightness);
            if ($actualBrightness == $Brightness) {
                $this->SendDebug(__FUNCTION__, 'Es wird bereits die gleiche Helligkeit verwendet!', 0);
                return true;
            }
            $deviceBrightness = $this->GetValue('LowerLightUnitBrightness') / 100;
            $id = $this->ReadPropertyInteger('LowerLightUnit');
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                switch ($this->ReadPropertyInteger('LowerLightUnitDeviceType')) {
                    case 1: //HmIP-BSL Channel 12
                        $commandControl = $this->ReadPropertyInteger('CommandControl');
                        if ($commandControl > 1 && @IPS_ObjectExists($commandControl)) {
                            $commands = [];
                            $commands[] = '@HM_WriteValueFloat(' . $id . ", 'LEVEL', '" . $deviceBrightness . "');";
                            $this->SendDebug(__FUNCTION__, 'Befehle: ' . json_encode(json_encode($commands)), 0);
                            $scriptText = self::ABLAUFSTEUERUNG_MODULE_PREFIX . '_ExecuteCommands(' . $commandControl . ', ' . json_encode(json_encode($commands)) . ');';
                            $this->SendDebug(__FUNCTION__, 'Ablaufsteuerung: ' . $scriptText, 0);
                            $result = @IPS_RunScriptText($scriptText);
                        } else {
                            $this->SendDebug(__FUNCTION__, 'Befehl: @HM_WriteValueFloat(' . $id . ", 'LEVEL', " . $deviceBrightness . ');', 0);
                            IPS_Sleep($this->ReadPropertyInteger('LowerLightUnitSwitchingDelay'));
                            $result = @HM_WriteValueFloat($id, 'LEVEL', $deviceBrightness);
                            if (!$result) {
                                IPS_Sleep($this->ReadPropertyInteger('LowerLightUnitSwitchingDelay'));
                                $result = @HM_WriteValueFloat($id, 'LEVEL', $deviceBrightness);
                            }
                        }
                        if ($result) {
                            $this->SendDebug(__FUNCTION__, 'Der Helligkeitswert ' . $deviceBrightness . ' wurde für die untere Leuchteinheit ID ' . $id . ' eingestellt.', 0);
                        } else {
                            $this->SendDebug(__FUNCTION__, 'Abbruch, der Helligkeitswert ' . $deviceBrightness . ' konnte für die untere Leuchteinheit ID ' . $id . ' nicht eingestellt werden!', 0);
                            //Revert brightness
                            $this->SetValue('LowerLightUnitBrightness', $actualBrightness);
                        }
                        break;
                }
            }
        }
        return $result;
    }
}