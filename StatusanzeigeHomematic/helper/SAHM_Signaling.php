<?php

/**
 * @project       _Statusanzeige/StatusanzeigeHomematic
 * @file          SAHM_Signaling.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection PhpUndefinedFunctionInspection */
/** @noinspection DuplicatedCode */

declare(strict_types=1);

trait SAHM_Signaling
{
    /**
     * Toggles the signalling.
     *
     * @param bool $State
     * false =  Off
     * true =   On
     *
     * @param bool $OverrideMaintenance
     * false =  Check maintenance
     * true =   Always switch state
     *
     * @param bool $CheckDeviceState
     * false =  Don't check device state
     * true =   Check device state
     *
     * @return bool
     * false =  An error occurred
     * true =   Successful
     *
     * @throws Exception
     */
    public function ToggleSignalling(bool $State, bool $OverrideMaintenance, bool $CheckDeviceState): bool
    {
        //Off
        if (!$State) {
            $result = $this->SetSignalling(false, $OverrideMaintenance, $CheckDeviceState);
            IPS_Sleep(100);
            $this->SetInvertedSignalling(true, $OverrideMaintenance, $CheckDeviceState);
        }
        //On
        else {
            $this->SetInvertedSignalling(false, $OverrideMaintenance, $CheckDeviceState);
            IPS_Sleep(100);
            $result = $this->SetSignalling(true, $OverrideMaintenance, $CheckDeviceState);
        }
        return $result;
    }

    /**
     * Updates the state.
     *
     * @return void
     * @throws Exception
     */
    public function UpdateState(): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        if ($this->CheckMaintenance()) {
            return;
        }
        $variables = json_decode($this->ReadPropertyString('TriggerList'), true);
        foreach ($variables as $variable) {
            if (!$variable['Use']) {
                continue;
            }
            $execute = true;
            //Check primary condition
            if (!IPS_IsConditionPassing($variable['PrimaryCondition'])) {
                $execute = false;
            }
            //Check secondary condition
            if (!IPS_IsConditionPassing($variable['SecondaryCondition'])) {
                $execute = false;
            }
            if (!$execute) {
                $this->SendDebug(__FUNCTION__, 'Abbruch, die Bedingungen wurden nicht erfüllt!', 0);
            } else {
                $this->SendDebug(__FUNCTION__, 'Die Bedingungen wurden erfüllt.', 0);
                //Signalling
                switch ($variable['Signalling']) {
                    case 0: //Off
                        $this->ToggleSignalling(false, false, false);
                        break;

                    case 1: //On
                        $this->ToggleSignalling(true, false, false);
                        break;

                }
            }
        }
    }

    ########## Private

    /**
     * Sets the signalling.
     *
     * @param bool $State
     * false =  Off
     * true =   On
     *
     * @param bool $OverrideMaintenance
     * false =  Check maintenance
     * true =   Always switch state
     *
     * @param bool $CheckDeviceState
     * false =  Don't check device state
     * true =   Check device state
     *
     * @return bool
     * false =  An error occurred
     * true =   Successful
     *
     * @throws Exception
     */
    private function SetSignalling(bool $State, bool $OverrideMaintenance, bool $CheckDeviceState): bool
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $statusText = 'Aus';
        $value = 'false';
        if ($State) {
            $statusText = 'An';
            $value = 'true';
        }
        $this->SendDebug(__FUNCTION__, 'Status: ' . $statusText, 0);
        if (!$OverrideMaintenance) {
            if ($this->CheckMaintenance()) {
                return false;
            }
        }
        $result = false;
        $id = $this->ReadPropertyInteger('SignallingDeviceInstance');
        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
            $result = true;
            $actualValue = $this->GetValue('Signalling');
            $this->SetValue('Signalling', $State);
            if ($CheckDeviceState) {
                $deviceState = $this->ReadPropertyInteger('SignallingDeviceState');
                if ($deviceState > 1 && @IPS_ObjectExists($deviceState)) {
                    if (GetValue($deviceState) == $State) {
                        $this->SendDebug(__FUNCTION__, 'Es wird bereits der gleiche Status angezeigt!', 0);
                        return true;
                    }
                }
            }
            //HM-LC-Sw4-WM
            if ($this->ReadPropertyInteger('SignallingDeviceType') == 1) {
                $commandControl = $this->ReadPropertyInteger('CommandControl');
                if ($commandControl > 1 && @IPS_ObjectExists($commandControl)) { //0 = main category, 1 = none
                    $commands = [];
                    $commands[] = '@HM_WriteValueBoolean(' . $id . ", 'STATE', " . $value . ');';
                    $this->SendDebug(__FUNCTION__, 'Befehl: ' . json_encode(json_encode($commands)), 0);
                    $scriptText = self::ABLAUFSTEUERUNG_MODULE_PREFIX . '_ExecuteCommands(' . $commandControl . ', ' . json_encode(json_encode($commands)) . ');';
                    $this->SendDebug(__FUNCTION__, 'Ablaufsteuerung: ' . $scriptText, 0);
                    $result = @IPS_RunScriptText($scriptText);
                } else {
                    IPS_Sleep($this->ReadPropertyInteger('SignallingDelay'));
                    $parameter = @HM_WriteValueBoolean($id, 'STATE', $State);
                    if (!$parameter) {
                        $this->SendDebug(__FUNCTION__, 'Bei der Signalisierung ist ein Fehler aufgetreten!', 0);
                        $this->SendDebug(__FUNCTION__, 'Der Schaltvorgang wird wiederholt.', 0);
                        IPS_Sleep($this->ReadPropertyInteger('SignallingDelay'));
                        $parameter = @HM_WriteValueBoolean($id, 'STATE', $State);
                        if (!$parameter) {
                            $result = false;
                        }
                    }
                }
                if (!$result) {
                    $this->SetValue('Signalling', $actualValue);
                    $this->SendDebug(__FUNCTION__, 'Die Signalisierung konnte für die Statusanzeige ID ' . $id . ' nicht erfolgreich geschaltet werden!', 0);
                    $this->LogMessage('ID ' . $this->InstanceID . ', ' . __FUNCTION__ . ', die invertierte Signalisierung konnte für die Statusanzeige ID  . ' . $id . ' nicht erfolgreich geschaltet werden!', KL_ERROR);
                }
            }
        }
        return $result;
    }

    /**
     * Sets the inverted signalling.
     *
     * @param bool $State
     * false =  Off
     * true =   On
     *
     * @param bool $OverrideMaintenance
     * false =  Check maintenance
     * true =   Always switch state
     *
     * @param bool $CheckDeviceState
     * false =  Don't check device state
     * true =   Check device state
     *
     * @return bool
     * false =  An error occurred
     * true =   Successful
     *
     * @throws Exception
     */
    private function SetInvertedSignalling(bool $State, bool $OverrideMaintenance, bool $CheckDeviceState): bool
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $statusText = 'Aus';
        $value = 'false';
        if ($State) {
            $statusText = 'An';
            $value = 'true';
        }
        $this->SendDebug(__FUNCTION__, 'Status: ' . $statusText, 0);
        if (!$OverrideMaintenance) {
            if ($this->CheckMaintenance()) {
                return false;
            }
        }
        $result = false;
        $id = $this->ReadPropertyInteger('InvertedSignallingDeviceInstance');
        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
            if ($CheckDeviceState) {
                $deviceState = $this->ReadPropertyInteger('InvertedSignallingDeviceState');
                if ($deviceState > 1 && @IPS_ObjectExists($deviceState)) {
                    if (GetValue($deviceState) == $State) {
                        $this->SendDebug(__FUNCTION__, 'Es wird bereits der gleiche Status angezeigt!', 0);
                        return true;
                    }
                }
            }
            $result = true;
            //HM-LC-Sw4-WM
            if ($this->ReadPropertyInteger('InvertedSignallingDeviceType') == 1) {
                $commandControl = $this->ReadPropertyInteger('CommandControl');
                if ($commandControl > 1 && @IPS_ObjectExists($commandControl)) { //0 = main category, 1 = none
                    $commands = [];
                    $commands[] = '@HM_WriteValueBoolean(' . $id . ", 'STATE', " . $value . ');';
                    $this->SendDebug(__FUNCTION__, 'Befehl: ' . json_encode(json_encode($commands)), 0);
                    $scriptText = self::ABLAUFSTEUERUNG_MODULE_PREFIX . '_ExecuteCommands(' . $commandControl . ', ' . json_encode(json_encode($commands)) . ');';
                    $this->SendDebug(__FUNCTION__, 'Ablaufsteuerung: ' . $scriptText, 0);
                    $result = @IPS_RunScriptText($scriptText);
                } else {
                    IPS_Sleep($this->ReadPropertyInteger('InvertedSignallingDelay'));
                    $parameter = @HM_WriteValueBoolean($id, 'STATE', $State);
                    if (!$parameter) {
                        $this->SendDebug(__FUNCTION__, 'Bei der invertierten Signalisierung ist ein Fehler aufgetreten!', 0);
                        $this->SendDebug(__FUNCTION__, 'Der Schaltvorgang wird wiederholt.', 0);
                        IPS_Sleep($this->ReadPropertyInteger('InvertedSignallingDelay'));
                        $parameter = @HM_WriteValueBoolean($id, 'STATE', $State);
                        if (!$parameter) {
                            $result = false;
                        }
                    }
                }
                if (!$result) {
                    $this->SendDebug(__FUNCTION__, 'Die invertierte Signalisierung konnte für die Statusanzeige ID ' . $id . ' nicht erfolgreich geschaltet werden!', 0);
                    $this->LogMessage('ID ' . $this->InstanceID . ', ' . __FUNCTION__ . ', die invertierte Signalisierung konnte für die Statusanzeige ID . ' . $id . ' nicht erfolgreich geschaltet werden!', KL_ERROR);
                }
            }
        }
        return $result;
    }
}