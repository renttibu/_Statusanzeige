<?php

/**
 * @project       _Statusanzeige/Statusanzeige
 * @file          SA_Signaling.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection PhpUndefinedFunctionInspection */
/** @noinspection DuplicatedCode */

declare(strict_types=1);

trait SA_Signaling
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
     * @param bool $CheckVariableState
     * false =  Don't check variable state
     * true =   Check variable state
     *
     * @return bool
     * false =  An error occurred
     * true =   Successful
     *
     * @throws Exception
     */
    public function ToggleSignalling(bool $State, bool $OverrideMaintenance, bool $CheckVariableState): bool
    {
        //Off
        if (!$State) {
            $result = $this->SetSignalling(false, $OverrideMaintenance, $CheckVariableState);
            IPS_Sleep(100);
            $this->SetInvertedSignalling(true, $OverrideMaintenance, $CheckVariableState);
        }
        //On
        else {
            $this->SetInvertedSignalling(false, $OverrideMaintenance, $CheckVariableState);
            IPS_Sleep(100);
            $result = $this->SetSignalling(true, $OverrideMaintenance, $CheckVariableState);
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
     * @param bool $CheckVariableState
     * false =  Don't check variable state
     * true =   Check variable state
     *
     * @return bool
     * false =  An error occurred
     * true =   Successful
     *
     * @throws Exception
     */
    private function SetSignalling(bool $State, bool $OverrideMaintenance, bool $CheckVariableState): bool
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
        $id = $this->ReadPropertyInteger('SignallingVariable');
        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
            $actualValue = $this->GetValue('Signalling');
            $this->SetValue('Signalling', $State);
            if ($CheckVariableState) {
                $variable = $this->ReadPropertyInteger('SignallingVariable');
                if ($variable > 1 && @IPS_ObjectExists($variable)) {
                    if (GetValue($variable) == $State) {
                        $this->SendDebug(__FUNCTION__, 'Es wird bereits der gleiche Status angezeigt!', 0);
                        return true;
                    }
                }
            }
            $commandControl = $this->ReadPropertyInteger('CommandControl');
            if ($commandControl > 1 && @IPS_ObjectExists($commandControl)) { //0 = main category, 1 = none
                $commands = [];
                $commands[] = '@RequestAction(' . $id . ', ' . $value . ');';
                $this->SendDebug(__FUNCTION__, 'Befehl: ' . json_encode(json_encode($commands)), 0);
                $scriptText = self::ABLAUFSTEUERUNG_MODULE_PREFIX . '_ExecuteCommands(' . $commandControl . ', ' . json_encode(json_encode($commands)) . ');';
                $this->SendDebug(__FUNCTION__, 'Ablaufsteuerung: ' . $scriptText, 0);
                $result = @IPS_RunScriptText($scriptText);
            } else {
                IPS_Sleep($this->ReadPropertyInteger('SignallingDelay'));
                @RequestAction($id, $State);
            }
            if (!$result) {
                //Revert
                $this->SetValue('Signalling', $actualValue);
                $this->SendDebug(__FUNCTION__, 'Die Signalisierung konnte für die Statusanzeige ID ' . $id . ' nicht erfolgreich geschaltet werden!', 0);
                $this->LogMessage('ID ' . $this->InstanceID . ', ' . __FUNCTION__ . ', die invertierte Signalisierung konnte für die Statusanzeige ID  . ' . $id . ' nicht erfolgreich geschaltet werden!', KL_ERROR);
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
     * @param bool $CheckVariableState
     * false =  Don't check variable state
     * true =   Check variable state
     *
     * @return bool
     * false =  An error occurred
     * true =   Successful
     *
     * @throws Exception
     */
    private function SetInvertedSignalling(bool $State, bool $OverrideMaintenance, bool $CheckVariableState): bool
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
        $id = $this->ReadPropertyInteger('InvertedSignallingVariable');
        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
            if ($CheckVariableState) {
                $variable = $this->ReadPropertyInteger('InvertedSignallingVariable');
                if ($variable > 1 && @IPS_ObjectExists($variable)) {
                    if (GetValue($variable) == $State) {
                        $this->SendDebug(__FUNCTION__, 'Es wird bereits der gleiche Status angezeigt!', 0);
                        return true;
                    }
                }
            }
            $commandControl = $this->ReadPropertyInteger('CommandControl');
            if ($commandControl > 1 && @IPS_ObjectExists($commandControl)) { //0 = main category, 1 = none
                $commands = [];
                $commands[] = '@RequestAction(' . $id . ', ' . $value . ');';
                $this->SendDebug(__FUNCTION__, 'Befehl: ' . json_encode(json_encode($commands)), 0);
                $scriptText = self::ABLAUFSTEUERUNG_MODULE_PREFIX . '_ExecuteCommands(' . $commandControl . ', ' . json_encode(json_encode($commands)) . ');';
                $this->SendDebug(__FUNCTION__, 'Ablaufsteuerung: ' . $scriptText, 0);
                $result = @IPS_RunScriptText($scriptText);
            } else {
                IPS_Sleep($this->ReadPropertyInteger('InvertedSignallingDelay'));
                @RequestAction($id, $State);
            }
            if (!$result) {
                $this->SendDebug(__FUNCTION__, 'Die Signalisierung konnte für die Statusanzeige ID ' . $id . ' nicht erfolgreich geschaltet werden!', 0);
                $this->LogMessage('ID ' . $this->InstanceID . ', ' . __FUNCTION__ . ', die invertierte Signalisierung konnte für die Statusanzeige ID  . ' . $id . ' nicht erfolgreich geschaltet werden!', KL_ERROR);
            }
        }
        return $result;
    }
}