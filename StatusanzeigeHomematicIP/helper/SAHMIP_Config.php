<?php

/**
 * @project       _Statusanzeige/StatusanzeigeHomematicIP
 * @file          AZ_Config.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

declare(strict_types=1);

trait SAHMIP_Config
{
    /**
     * Reloads the configuration form.
     *
     * @return void
     */
    public function ReloadConfig(): void
    {
        $this->ReloadForm();
    }

    /**
     * Modifies a configuration button.
     *
     * @param string $Field
     * @param string $Caption
     * @param int $ObjectID
     * @return void
     */
    public function ModifyButton(string $Field, string $Caption, int $ObjectID): void
    {
        $state = false;
        if ($ObjectID > 1 && @IPS_ObjectExists($ObjectID)) { //0 = main category, 1 = none
            $state = true;
        }
        $this->UpdateFormField($Field, 'caption', $Caption);
        $this->UpdateFormField($Field, 'visible', $state);
        $this->UpdateFormField($Field, 'objectID', $ObjectID);
    }

    /**
     * Modifies a trigger list configuration button
     *
     * @param string $Field
     * @param string $Condition
     * @return void
     */
    public function ModifyTriggerListButton(string $Field, string $Condition): void
    {
        $id = 0;
        $state = false;
        //Get variable id
        $primaryCondition = json_decode($Condition, true);
        if (array_key_exists(0, $primaryCondition)) {
            if (array_key_exists(0, $primaryCondition[0]['rules']['variable'])) {
                $id = $primaryCondition[0]['rules']['variable'][0]['variableID'];
                if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                    $state = true;
                }
            }
        }
        $this->UpdateFormField($Field, 'caption', 'ID ' . $id . ' Bearbeiten');
        $this->UpdateFormField($Field, 'visible', $state);
        $this->UpdateFormField($Field, 'objectID', $id);
    }

    /**
     * Gets the configuration form.
     *
     * @return false|string
     * @throws Exception
     */
    public function GetConfigurationForm()
    {
        $form = [];

        ########## Elements

        //Info
        $form['elements'][0] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Info',
            'items'   => [
                [
                    'type'  => 'Image',
                    'image' => 'data:image/png;base64, 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'
                ],
                [
                    'type'    => 'Label',
                    'name'    => 'ModuleID',
                    'caption' => "ID:\t\t" . $this->InstanceID
                ],
                [
                    'type'    => 'Label',
                    'name'    => 'ModuleDesignation',
                    'caption' => "Modul:\t" . self::MODULE_NAME
                ],
                [
                    'type'    => 'Label',
                    'name'    => 'ModulePrefix',
                    'caption' => "Präfix:\t" . self::MODULE_PREFIX
                ],
                [
                    'type'    => 'Label',
                    'name'    => 'ModuleVersion',
                    'caption' => "Version:\t" . self::MODULE_VERSION
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'ValidationTextBox',
                    'name'    => 'Note',
                    'caption' => 'Notiz',
                    'width'   => '600px'
                ]
            ]
        ];

        //Functions
        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Funktionen',
            'items'   => [
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableActive',
                    'caption' => 'Aktiv (Schalter im WebFront)'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Obere Leuchteinheit',
                    'italic'  => true,
                    'bold'    => true
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableUpperLightUnitColor',
                    'caption' => 'Farbauswahl'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableUpperLightUnitBrightness',
                    'caption' => 'Helligkeit'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Untere Leuchteinheit',
                    'italic'  => true,
                    'bold'    => true
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableLowerLightUnitColor',
                    'caption' => 'Farbauswahl'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableLowerLightUnitBrightness',
                    'caption' => 'Helligkeit'
                ]
            ]
        ];

        ##### Upper light unit

        //Upper light unit instance
        $upperLightUnitDeviceInstance = $this->ReadPropertyInteger('UpperLightUnit');
        $enableUpperLightUnitDeviceInstanceButton = false;
        if ($upperLightUnitDeviceInstance > 1 && @IPS_ObjectExists($upperLightUnitDeviceInstance)) { //0 = main category, 1 = none
            $enableUpperLightUnitDeviceInstanceButton = true;
        }

        //Upper light unit color
        $upperLightUnitDeviceColorVariable = $this->ReadPropertyInteger('UpperLightUnitDeviceColor');
        $enableUpperLightUnitDeviceColorButton = false;
        if ($upperLightUnitDeviceColorVariable > 1 && @IPS_ObjectExists($upperLightUnitDeviceColorVariable)) { //0 = main category, 1 = none
            $enableUpperLightUnitDeviceColorButton = true;
        }

        //Upper light unit brightness
        $upperLightUnitDeviceBrightnessVariable = $this->ReadPropertyInteger('UpperLightUnitDeviceBrightness');
        $enableUpperLightUnitDeviceBrightnessButton = false;
        if ($upperLightUnitDeviceBrightnessVariable > 1 && @IPS_ObjectExists($upperLightUnitDeviceBrightnessVariable)) { //0 = main category, 1 = none
            $enableUpperLightUnitDeviceBrightnessButton = true;
        }

        //Upper light unit trigger list
        $triggerListValues = [];
        $variables = json_decode($this->ReadPropertyString('UpperLightUnitTriggerList'), true);
        foreach ($variables as $variable) {
            $rowColor = '#C0FFC0'; //light green
            if (!$variable['Use']) {
                $rowColor = '#DFDFDF'; //grey
            }
            //Primary condition
            if ($variable['PrimaryCondition'] != '') {
                $primaryCondition = json_decode($variable['PrimaryCondition'], true);
                if (array_key_exists(0, $primaryCondition)) {
                    if (array_key_exists(0, $primaryCondition[0]['rules']['variable'])) {
                        $id = $primaryCondition[0]['rules']['variable'][0]['variableID'];
                        if ($id <= 1 || !@IPS_ObjectExists($id)) { //0 = main category, 1 = none
                            $rowColor = '#FFC0C0'; //red
                        }
                    }
                }
            }
            //Secondary condition, multi
            if ($variable['SecondaryCondition'] != '') {
                $secondaryConditions = json_decode($variable['SecondaryCondition'], true);
                if (array_key_exists(0, $secondaryConditions)) {
                    if (array_key_exists('rules', $secondaryConditions[0])) {
                        $rules = $secondaryConditions[0]['rules']['variable'];
                        foreach ($rules as $rule) {
                            if (array_key_exists('variableID', $rule)) {
                                $id = $rule['variableID'];
                                if ($id <= 1 || !@IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                    $rowColor = '#FFC0C0'; //red
                                }
                            }
                        }
                    }
                }
            }
            $triggerListValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] =
            [
                'type'    => 'ExpansionPanel',
                'caption' => 'Obere Leuchteinheit',
                'items'   => [
                    [
                        'type'    => 'Label',
                        'caption' => 'Gerät',
                        'italic'  => true,
                        'bold'    => true
                    ],
                    [
                        'type'    => 'Select',
                        'name'    => 'UpperLightUnitDeviceType',
                        'caption' => 'Typ',
                        'options' => [
                            [
                                'caption' => 'Kein Gerät',
                                'value'   => 0
                            ],
                            [
                                'caption' => 'HmIP-BSL, Kanal 8',
                                'value'   => 1
                            ],
                            [
                                'caption' => 'HmIP-MP3P, Kanal 6',
                                'value'   => 2
                            ]
                        ]
                    ],

                    [
                        'type'  => 'RowLayout',
                        'items' => [
                            [
                                'type'     => 'SelectInstance',
                                'name'     => 'UpperLightUnit',
                                'caption'  => 'Instanz',
                                'width'    => '600px',
                                'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "UpperLightUnitDeviceInstanceConfigurationButton", "ID " . $UpperLightUnit . " Instanzkonfiguration", $UpperLightUnit);'
                            ],
                            [
                                'type'    => 'Label',
                                'caption' => ' '
                            ],
                            [
                                'type'     => 'OpenObjectButton',
                                'name'     => 'UpperLightUnitDeviceInstanceConfigurationButton',
                                'caption'  => 'ID ' . $upperLightUnitDeviceInstance . ' Instanzkonfiguration',
                                'visible'  => $enableUpperLightUnitDeviceInstanceButton,
                                'objectID' => $upperLightUnitDeviceInstance
                            ]
                        ]
                    ],
                    [
                        'type'  => 'RowLayout',
                        'items' => [
                            [
                                'type'     => 'SelectVariable',
                                'name'     => 'UpperLightUnitDeviceColor',
                                'caption'  => 'Variable COLOR (Farbe)',
                                'width'    => '600px',
                                'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "UpperLightUnitDeviceColorConfigurationButton", "ID " . $UpperLightUnitDeviceColor . " bearbeiten", $UpperLightUnitDeviceColor);'
                            ],
                            [
                                'type'    => 'Label',
                                'caption' => ' '
                            ],
                            [
                                'type'     => 'OpenObjectButton',
                                'name'     => 'UpperLightUnitDeviceColorConfigurationButton',
                                'caption'  => 'ID ' . $upperLightUnitDeviceColorVariable . ' bearbeiten',
                                'visible'  => $enableUpperLightUnitDeviceColorButton,
                                'objectID' => $upperLightUnitDeviceColorVariable
                            ]
                        ]
                    ],
                    [
                        'type'  => 'RowLayout',
                        'items' => [
                            [
                                'type'     => 'SelectVariable',
                                'name'     => 'UpperLightUnitDeviceBrightness',
                                'caption'  => 'Variable LEVEL (Helligkeit)',
                                'width'    => '600px',
                                'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "UpperLightUnitDeviceBrightnessConfigurationButton", "ID " . $UpperLightUnitDeviceBrightness . " bearbeiten", $UpperLightUnitDeviceBrightness);'
                            ],
                            [
                                'type'    => 'Label',
                                'caption' => ' '
                            ],
                            [
                                'type'     => 'OpenObjectButton',
                                'name'     => 'UpperLightUnitDeviceBrightnessConfigurationButton',
                                'caption'  => 'ID ' . $upperLightUnitDeviceBrightnessVariable . ' bearbeiten',
                                'visible'  => $enableUpperLightUnitDeviceBrightnessButton,
                                'objectID' => $upperLightUnitDeviceBrightnessVariable
                            ]
                        ]
                    ],
                    [
                        'type'    => 'NumberSpinner',
                        'name'    => 'UpperLightUnitSwitchingDelay',
                        'caption' => 'Schaltverzögerung',
                        'minimum' => 0,
                        'suffix'  => 'Millisekunden'
                    ],
                    [
                        'type'    => 'Label',
                        'caption' => ' '
                    ],
                    [
                        'type'    => 'Label',
                        'caption' => 'Auslöser',
                        'italic'  => true,
                        'bold'    => true
                    ],
                    [
                        'type'     => 'List',
                        'name'     => 'UpperLightUnitTriggerList',
                        'rowCount' => 10,
                        'add'      => true,
                        'delete'   => true,
                        'sort'     => [
                            'column'    => 'Priority',
                            'direction' => 'descending'
                        ],
                        'columns' => [
                            [
                                'caption' => 'Aktiviert',
                                'name'    => 'Use',
                                'width'   => '100px',
                                'add'     => true,
                                'edit'    => [
                                    'type' => 'CheckBox'
                                ]
                            ],
                            [
                                'caption' => 'Priorität',
                                'name'    => 'Priority',
                                'width'   => '150px',
                                'add'     => 1,
                                'edit'    => [
                                    'type'    => 'Select',
                                    'options' => [
                                        [
                                            'caption' => '1 - niedrig',
                                            'value'   => 1
                                        ],
                                        [
                                            'caption' => '2',
                                            'value'   => 2
                                        ],
                                        [
                                            'caption' => '3',
                                            'value'   => 3
                                        ],
                                        [
                                            'caption' => '4 - mittel',
                                            'value'   => 4
                                        ],
                                        [
                                            'caption' => '5 - mittel',
                                            'value'   => 5
                                        ],
                                        [
                                            'caption' => '6',
                                            'value'   => 6
                                        ],
                                        [
                                            'caption' => '7',
                                            'value'   => 7
                                        ],
                                        [
                                            'caption' => '8 - hoch',
                                            'value'   => 8
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'caption' => 'Bezeichnung',
                                'name'    => 'Designation',
                                'onClick' => self::MODULE_PREFIX . '_ModifyTriggerListButton($id, "UpperLightUnitTriggerListConfigurationButton", $UpperLightUnitTriggerList["PrimaryCondition"]);',
                                'width'   => '300px',
                                'add'     => '',
                                'edit'    => [
                                    'type' => 'ValidationTextBox'
                                ]
                            ],
                            [
                                'caption' => ' ',
                                'name'    => 'SpacerPrimaryCondition',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type' => 'Label'
                                ]
                            ],
                            [
                                'caption' => 'Bedingung:',
                                'name'    => 'LabelPrimaryCondition',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type'   => 'Label',
                                    'italic' => true,
                                    'bold'   => true
                                ]
                            ],
                            [
                                'caption' => ' ',
                                'name'    => 'PrimaryCondition',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type' => 'SelectCondition'
                                ]
                            ],
                            [
                                'caption' => ' ',
                                'name'    => 'SpacerSecondaryCondition',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type' => 'Label'
                                ]
                            ],
                            [
                                'caption' => 'Weitere Bedingung(en):',
                                'name'    => 'LabelSecondaryCondition',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type'   => 'Label',
                                    'italic' => true,
                                    'bold'   => true
                                ]
                            ],
                            [
                                'caption' => ' ',
                                'name'    => 'SecondaryCondition',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type'  => 'SelectCondition',
                                    'multi' => true
                                ]
                            ],
                            [
                                'caption' => ' ',
                                'name'    => 'SpacerSignaling',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type' => 'Label'
                                ]
                            ],
                            [
                                'caption' => 'Signalisierung:',
                                'name'    => 'LabelSignaling',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type'   => 'Label',
                                    'italic' => true,
                                    'bold'   => true
                                ]
                            ],
                            [
                                'caption' => 'Farbe',
                                'name'    => 'Color',
                                'width'   => '100px',
                                'add'     => 0,
                                'edit'    => [
                                    'type'    => 'Select',
                                    'options' => [
                                        [
                                            'caption' => '0 - Aus',
                                            'value'   => 0
                                        ],
                                        [
                                            'caption' => '1 - Blau',
                                            'value'   => 1
                                        ],
                                        [
                                            'caption' => '2 - Grün',
                                            'value'   => 2
                                        ],
                                        [
                                            'caption' => '3 - Türkis',
                                            'value'   => 3
                                        ],
                                        [
                                            'caption' => '4 - Rot',
                                            'value'   => 4
                                        ],
                                        [
                                            'caption' => '5 - Violett',
                                            'value'   => 5
                                        ],
                                        [
                                            'caption' => '6 - Gelb',
                                            'value'   => 6
                                        ],
                                        [
                                            'caption' => '7 - Weiß',
                                            'value'   => 7
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'caption' => 'Helligkeit',
                                'name'    => 'Brightness',
                                'width'   => '100px',
                                'add'     => 100,
                                'edit'    => [
                                    'type'    => 'NumberSpinner',
                                    'suffix'  => '%',
                                    'minimum' => 0,
                                    'maximum' => 100
                                ]
                            ]
                        ],
                        'values' => $triggerListValues
                    ],
                    [
                        'type'     => 'OpenObjectButton',
                        'name'     => 'UpperLightUnitTriggerListConfigurationButton',
                        'caption'  => 'Bearbeiten',
                        'visible'  => false,
                        'objectID' => 0
                    ],
                    [
                        'type'     => 'Label',
                        'name'     => 'UpperLightUnitTriggerListConfigurationButtonSpacer',
                        'caption'  => ' ',
                        'visible'  => false,
                        'objectID' => 0
                    ],
                    [
                        'type'    => 'CheckBox',
                        'name'    => 'UpdateLowerLightUnit',
                        'caption' => 'Untere Leuchteinheit aktualisieren',
                    ]
                ]
            ];

        ##### Lower light unit

        //Lower light unit instance
        $lowerLightUnitDeviceInstance = $this->ReadPropertyInteger('LowerLightUnit');
        $enableLowerLightUnitDeviceInstanceButton = false;
        if ($lowerLightUnitDeviceInstance > 1 && @IPS_ObjectExists($lowerLightUnitDeviceInstance)) { //0 = main category, 1 = none
            $enableLowerLightUnitDeviceInstanceButton = true;
        }

        //Lower light unit color
        $lowerLightUnitDeviceColorVariable = $this->ReadPropertyInteger('LowerLightUnitDeviceColor');
        $enableLowerLightUnitDeviceColorButton = false;
        if ($lowerLightUnitDeviceColorVariable > 1 && @IPS_ObjectExists($lowerLightUnitDeviceColorVariable)) { //0 = main category, 1 = none
            $enableLowerLightUnitDeviceColorButton = true;
        }

        //Lower light unit brightness
        $lowerLightUnitDeviceBrightnessVariable = $this->ReadPropertyInteger('LowerLightUnitDeviceBrightness');
        $enableLowerLightUnitDeviceBrightnessButton = false;
        if ($lowerLightUnitDeviceBrightnessVariable > 1 && @IPS_ObjectExists($lowerLightUnitDeviceBrightnessVariable)) { //0 = main category, 1 = none
            $enableLowerLightUnitDeviceBrightnessButton = true;
        }

        //Lower light unit trigger list
        $triggerListValues = [];
        $variables = json_decode($this->ReadPropertyString('LowerLightUnitTriggerList'), true);
        foreach ($variables as $variable) {
            $rowColor = '#C0FFC0'; //light green
            if (!$variable['Use']) {
                $rowColor = '#DFDFDF'; //grey
            }
            //Primary condition
            if ($variable['PrimaryCondition'] != '') {
                $primaryCondition = json_decode($variable['PrimaryCondition'], true);
                if (array_key_exists(0, $primaryCondition)) {
                    if (array_key_exists(0, $primaryCondition[0]['rules']['variable'])) {
                        $id = $primaryCondition[0]['rules']['variable'][0]['variableID'];
                        if ($id <= 1 || !@IPS_ObjectExists($id)) { //0 = main category, 1 = none
                            $rowColor = '#FFC0C0'; //red
                        }
                    }
                }
            }
            //Secondary condition, multi
            if ($variable['SecondaryCondition'] != '') {
                $secondaryConditions = json_decode($variable['SecondaryCondition'], true);
                if (array_key_exists(0, $secondaryConditions)) {
                    if (array_key_exists('rules', $secondaryConditions[0])) {
                        $rules = $secondaryConditions[0]['rules']['variable'];
                        foreach ($rules as $rule) {
                            if (array_key_exists('variableID', $rule)) {
                                $id = $rule['variableID'];
                                if ($id <= 1 || !@IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                    $rowColor = '#FFC0C0'; //red
                                }
                            }
                        }
                    }
                }
            }
            $triggerListValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] =
            [
                'type'    => 'ExpansionPanel',
                'caption' => 'Untere Leuchteinheit',
                'items'   => [
                    [
                        'type'    => 'Label',
                        'caption' => 'Gerät',
                        'italic'  => true,
                        'bold'    => true
                    ],
                    [
                        'type'    => 'Select',
                        'name'    => 'LowerLightUnitDeviceType',
                        'caption' => 'Typ',
                        'options' => [
                            [
                                'caption' => 'Kein Gerät',
                                'value'   => 0
                            ],
                            [
                                'caption' => 'HmIP-BSL, Kanal 12',
                                'value'   => 1
                            ]
                        ]
                    ],
                    [
                        'type'  => 'RowLayout',
                        'items' => [
                            [
                                'type'     => 'SelectInstance',
                                'name'     => 'LowerLightUnit',
                                'caption'  => 'Instanz',
                                'width'    => '600px',
                                'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "LowerLightUnitDeviceInstanceConfigurationButton", "ID " . $LowerLightUnit . " Instanzkonfiguration", $LowerLightUnit);'
                            ],
                            [
                                'type'    => 'Label',
                                'caption' => ' '
                            ],
                            [
                                'type'     => 'OpenObjectButton',
                                'name'     => 'LowerLightUnitDeviceInstanceConfigurationButton',
                                'caption'  => 'ID ' . $lowerLightUnitDeviceInstance . ' Instanzkonfiguration',
                                'visible'  => $enableLowerLightUnitDeviceInstanceButton,
                                'objectID' => $lowerLightUnitDeviceInstance
                            ]
                        ]
                    ],
                    [
                        'type'  => 'RowLayout',
                        'items' => [
                            [
                                'type'     => 'SelectVariable',
                                'name'     => 'LowerLightUnitDeviceColor',
                                'caption'  => 'Variable COLOR (Farbe)',
                                'width'    => '600px',
                                'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "LowerLightUnitDeviceColorConfigurationButton", "ID " . $LowerLightUnitDeviceColor . " bearbeiten", $LowerLightUnitDeviceColor);'
                            ],
                            [
                                'type'    => 'Label',
                                'caption' => ' '
                            ],
                            [
                                'type'     => 'OpenObjectButton',
                                'name'     => 'LowerLightUnitDeviceColorConfigurationButton',
                                'caption'  => 'ID ' . $lowerLightUnitDeviceColorVariable . ' bearbeiten',
                                'visible'  => $enableLowerLightUnitDeviceColorButton,
                                'objectID' => $lowerLightUnitDeviceColorVariable
                            ]
                        ]
                    ],
                    [
                        'type'  => 'RowLayout',
                        'items' => [
                            [
                                'type'     => 'SelectVariable',
                                'name'     => 'LowerLightUnitDeviceBrightness',
                                'caption'  => 'Variable LEVEL (Helligkeit)',
                                'width'    => '600px',
                                'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "LowerLightUnitDeviceBrightnessConfigurationButton", "ID " . $LowerLightUnitDeviceBrightness . " bearbeiten", $LowerLightUnitDeviceBrightness);'
                            ],
                            [
                                'type'    => 'Label',
                                'caption' => ' '
                            ],
                            [
                                'type'     => 'OpenObjectButton',
                                'name'     => 'LowerLightUnitDeviceBrightnessConfigurationButton',
                                'caption'  => 'ID ' . $lowerLightUnitDeviceBrightnessVariable . ' bearbeiten',
                                'visible'  => $enableLowerLightUnitDeviceBrightnessButton,
                                'objectID' => $lowerLightUnitDeviceBrightnessVariable
                            ]
                        ]
                    ],
                    [
                        'type'    => 'NumberSpinner',
                        'name'    => 'LowerLightUnitSwitchingDelay',
                        'caption' => 'Schaltverzögerung',
                        'minimum' => 0,
                        'suffix'  => 'Millisekunden'
                    ],
                    [
                        'type'    => 'Label',
                        'caption' => ' '
                    ],
                    [
                        'type'    => 'Label',
                        'caption' => 'Auslöser',
                        'italic'  => true,
                        'bold'    => true
                    ],
                    [
                        'type'     => 'List',
                        'name'     => 'LowerLightUnitTriggerList',
                        'rowCount' => 10,
                        'add'      => true,
                        'delete'   => true,
                        'sort'     => [
                            'column'    => 'Priority',
                            'direction' => 'descending'
                        ],
                        'columns' => [
                            [
                                'caption' => 'Aktiviert',
                                'name'    => 'Use',
                                'width'   => '100px',
                                'add'     => true,
                                'edit'    => [
                                    'type' => 'CheckBox'
                                ]
                            ],
                            [
                                'caption' => 'Priorität',
                                'name'    => 'Priority',
                                'width'   => '150px',
                                'add'     => 1,
                                'edit'    => [
                                    'type'    => 'Select',
                                    'options' => [
                                        [
                                            'caption' => '1 - niedrig',
                                            'value'   => 1
                                        ],
                                        [
                                            'caption' => '2',
                                            'value'   => 2
                                        ],
                                        [
                                            'caption' => '3',
                                            'value'   => 3
                                        ],
                                        [
                                            'caption' => '4 - mittel',
                                            'value'   => 4
                                        ],
                                        [
                                            'caption' => '5 - mittel',
                                            'value'   => 5
                                        ],
                                        [
                                            'caption' => '6',
                                            'value'   => 6
                                        ],
                                        [
                                            'caption' => '7',
                                            'value'   => 7
                                        ],
                                        [
                                            'caption' => '8 - hoch',
                                            'value'   => 8
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'caption' => 'Bezeichnung',
                                'name'    => 'Designation',
                                'onClick' => self::MODULE_PREFIX . '_ModifyTriggerListButton($id, "LowerLightUnitTriggerListConfigurationButton", $LowerLightUnitTriggerList["PrimaryCondition"]);',
                                'width'   => '300px',
                                'add'     => '',
                                'edit'    => [
                                    'type' => 'ValidationTextBox'
                                ]
                            ],
                            [
                                'caption' => ' ',
                                'name'    => 'SpacerPrimaryCondition',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type' => 'Label'
                                ]
                            ],
                            [
                                'caption' => 'Bedingung:',
                                'name'    => 'LabelPrimaryCondition',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type'   => 'Label',
                                    'italic' => true,
                                    'bold'   => true
                                ]
                            ],
                            [
                                'caption' => ' ',
                                'name'    => 'PrimaryCondition',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type' => 'SelectCondition'
                                ]
                            ],
                            [
                                'caption' => ' ',
                                'name'    => 'SpacerSecondaryCondition',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type' => 'Label'
                                ]
                            ],
                            [
                                'caption' => 'Weitere Bedingung(en):',
                                'name'    => 'LabelSecondaryCondition',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type'   => 'Label',
                                    'italic' => true,
                                    'bold'   => true
                                ]
                            ],
                            [
                                'caption' => ' ',
                                'name'    => 'SecondaryCondition',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type'  => 'SelectCondition',
                                    'multi' => true
                                ]
                            ],
                            [
                                'caption' => ' ',
                                'name'    => 'SpacerSignaling',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type' => 'Label'
                                ]
                            ],
                            [
                                'caption' => 'Signalisierung:',
                                'name'    => 'LabelSignaling',
                                'width'   => '200px',
                                'add'     => '',
                                'visible' => false,
                                'edit'    => [
                                    'type'   => 'Label',
                                    'italic' => true,
                                    'bold'   => true
                                ]
                            ],
                            [
                                'caption' => 'Farbe',
                                'name'    => 'Color',
                                'width'   => '100px',
                                'add'     => 0,
                                'edit'    => [
                                    'type'    => 'Select',
                                    'options' => [
                                        [
                                            'caption' => '0 - Aus',
                                            'value'   => 0
                                        ],
                                        [
                                            'caption' => '1 - Blau',
                                            'value'   => 1
                                        ],
                                        [
                                            'caption' => '2 - Grün',
                                            'value'   => 2
                                        ],
                                        [
                                            'caption' => '3 - Türkis',
                                            'value'   => 3
                                        ],
                                        [
                                            'caption' => '4 - Rot',
                                            'value'   => 4
                                        ],
                                        [
                                            'caption' => '5 - Violett',
                                            'value'   => 5
                                        ],
                                        [
                                            'caption' => '6 - Gelb',
                                            'value'   => 6
                                        ],
                                        [
                                            'caption' => '7 - Weiß',
                                            'value'   => 7
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'caption' => 'Helligkeit',
                                'name'    => 'Brightness',
                                'width'   => '100px',
                                'add'     => 100,
                                'edit'    => [
                                    'type'    => 'NumberSpinner',
                                    'suffix'  => '%',
                                    'minimum' => 0,
                                    'maximum' => 100
                                ]
                            ]
                        ],
                        'values' => $triggerListValues
                    ],
                    [
                        'type'     => 'OpenObjectButton',
                        'name'     => 'LowerLightUnitTriggerListConfigurationButton',
                        'caption'  => 'Bearbeiten',
                        'visible'  => false,
                        'objectID' => 0
                    ],
                    [
                        'type'     => 'Label',
                        'name'     => 'LowerLightUnitTriggerListConfigurationButtonSpacer',
                        'caption'  => ' ',
                        'visible'  => false,
                        'objectID' => 0
                    ],
                    [
                        'type'    => 'CheckBox',
                        'name'    => 'UpdateUpperLightUnit',
                        'caption' => 'Obere Leuchteinheit aktualisieren',

                    ]
                ]

            ];

        //Command control
        $id = $this->ReadPropertyInteger('CommandControl');
        $enableButton = false;
        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
            $enableButton = true;
        }

        $form['elements'][] =
            [
                'type'    => 'ExpansionPanel',
                'caption' => 'Ablaufsteuerung',
                'items'   => [
                    [
                        'type'  => 'RowLayout',
                        'items' => [
                            [
                                'type'     => 'SelectModule',
                                'name'     => 'CommandControl',
                                'caption'  => 'Instanz',
                                'moduleID' => self::ABLAUFSTEUERUNG_MODULE_GUID,
                                'width'    => '600px',
                                'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "CommandControlConfigurationButton", "ID " . $CommandControl . " Instanzkonfiguration", $CommandControl);'
                            ],
                            [
                                'type'    => 'Button',
                                'caption' => 'Neue Instanz erstellen',
                                'onClick' => self::MODULE_PREFIX . '_CreateCommandControlInstance($id);'
                            ],
                            [
                                'type'    => 'Label',
                                'caption' => ' '
                            ],
                            [
                                'type'     => 'OpenObjectButton',
                                'caption'  => 'ID ' . $id . ' Instanzkonfiguration',
                                'name'     => 'CommandControlConfigurationButton',
                                'visible'  => $enableButton,
                                'objectID' => $id
                            ]
                        ]
                    ]
                ]
            ];

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Deaktivierung',
            'items'   => [
                [
                    'type'    => 'Label',
                    'caption' => 'Deaktivierung',
                    'italic'  => true,
                    'bold'    => true
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Nachfolgende Funktionen werden bei Deaktivierung verwendet.'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Obere Leuchteinheit',
                    'italic'  => true
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'DeactivateUpperLightUnitChangeColor',
                    'caption' => 'Farbe ändern'
                ],
                [
                    'type'    => 'Select',
                    'name'    => 'DeactivationUpperLightUnitColor',
                    'caption' => 'Farbe',
                    'options' => [
                        [
                            'caption' => '0 - Aus',
                            'value'   => 0
                        ],
                        [
                            'caption' => '1 - Blau',
                            'value'   => 1
                        ],
                        [
                            'caption' => '2 - Grün',
                            'value'   => 2
                        ],
                        [
                            'caption' => '3 - Türkis',
                            'value'   => 3
                        ],
                        [
                            'caption' => '4 - Rot',
                            'value'   => 4
                        ],
                        [
                            'caption' => '5 - Violett',
                            'value'   => 5
                        ],
                        [
                            'caption' => '6 - Gelb',
                            'value'   => 6
                        ],
                        [
                            'caption' => '7 - Weiß',
                            'value'   => 7
                        ]
                    ]
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'DeactivateUpperLightUnitChangeBrightness',
                    'caption' => 'Helligkeit ändern'
                ],
                [
                    'type'    => 'NumberSpinner',
                    'name'    => 'DeactivationUpperLightUnitBrightness',
                    'caption' => 'Helligkeit',
                    'suffix'  => '%',
                    'minimum' => 0,
                    'maximum' => 100
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Untere Leuchteinheit',
                    'italic'  => true
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'DeactivateLowerLightUnitChangeColor',
                    'caption' => 'Farbe ändern'
                ],
                [
                    'type'    => 'Select',
                    'name'    => 'DeactivationLowerLightUnitColor',
                    'caption' => 'Farbe',
                    'options' => [
                        [
                            'caption' => '0 - Aus',
                            'value'   => 0
                        ],
                        [
                            'caption' => '1 - Blau',
                            'value'   => 1
                        ],
                        [
                            'caption' => '2 - Grün',
                            'value'   => 2
                        ],
                        [
                            'caption' => '3 - Türkis',
                            'value'   => 3
                        ],
                        [
                            'caption' => '4 - Rot',
                            'value'   => 4
                        ],
                        [
                            'caption' => '5 - Violett',
                            'value'   => 5
                        ],
                        [
                            'caption' => '6 -Gelb',
                            'value'   => 6
                        ],
                        [
                            'caption' => '7 - Weiß',
                            'value'   => 7
                        ]
                    ]
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'DeactivateLowerLightUnitChangeBrightness',
                    'caption' => 'Helligkeit ändern'
                ],
                [
                    'type'    => 'NumberSpinner',
                    'name'    => 'DeactivationLowerLightUnitBrightness',
                    'caption' => 'Helligkeit',
                    'suffix'  => '%',
                    'minimum' => 0,
                    'maximum' => 100
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Reaktivierung',
                    'italic'  => true,
                    'bold'    => true
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Nachfolgende Funktionen werden nur verwendet, wenn keine Auslöser genutzt werden.'
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Ansonsten wird anhand der Auslöserliste geschaltet.'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Obere Leuchteinheit',
                    'italic'  => true
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'ReactivateUpperLightUnitLastColor',
                    'caption' => 'Letzte Farbe'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'ReactivateUpperLightUnitLastBrightness',
                    'caption' => 'Letzte Helligkeit'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Untere Leuchteinheit',
                    'italic'  => true
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'ReactivateLowerLightUnitLastColor',
                    'caption' => 'Letzte Farbe'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'ReactivateLowerLightUnitLastBrightness',
                    'caption' => 'Letzte Helligkeit'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Automatische Deaktivierung',
                    'italic'  => true,
                    'bold'    => true
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'UseAutomaticDeactivation',
                    'caption' => 'Automatische Deaktivierung'
                ],
                [
                    'type'    => 'SelectTime',
                    'name'    => 'AutomaticDeactivationStartTime',
                    'caption' => 'Startzeit'
                ],
                [
                    'type'    => 'SelectTime',
                    'name'    => 'AutomaticDeactivationEndTime',
                    'caption' => 'Endzeit'
                ]
            ]
        ];

        ########## Actions

        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Konfiguration',
            'items'   => [
                [
                    'type'    => 'Button',
                    'caption' => 'Neu laden',
                    'onClick' => self::MODULE_PREFIX . '_ReloadConfig($id);'
                ]
            ]
        ];

        //Test center
        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Schaltfunktionen',
            'items'   => [
                [
                    'type' => 'TestCenter',
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [

                    'type'    => 'Button',
                    'caption' => 'Status aktualisieren',
                    'onClick' => self::MODULE_PREFIX . '_UpdateLightUnits(' . $this->InstanceID . '); echo "Status wird aktualisiert!";'
                ]
            ]
        ];

        //Registered references
        $registeredReferences = [];
        $references = $this->GetReferenceList();
        foreach ($references as $reference) {
            $name = 'Objekt #' . $reference . ' existiert nicht';
            $rowColor = '#FFC0C0'; //red
            if (@IPS_ObjectExists($reference)) {
                $name = IPS_GetName($reference);
                $rowColor = '#C0FFC0'; //light green
            }
            $registeredReferences[] = [
                'ObjectID' => $reference,
                'Name'     => $name,
                'rowColor' => $rowColor];
        }

        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Registrierte Referenzen',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'RegisteredReferences',
                    'rowCount' => 10,
                    'sort'     => [
                        'column'    => 'ObjectID',
                        'direction' => 'ascending'
                    ],
                    'columns' => [
                        [
                            'caption' => 'ID',
                            'name'    => 'ObjectID',
                            'width'   => '150px',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "RegisteredReferencesConfigurationButton", "ID " . $RegisteredReferences["ObjectID"] . " aufrufen", $RegisteredReferences["ObjectID"]);'
                        ],
                        [
                            'caption' => 'Name',
                            'name'    => 'Name',
                            'width'   => '300px',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "RegisteredReferencesConfigurationButton", "ID " . $RegisteredReferences["ObjectID"] . " aufrufen", $RegisteredReferences["ObjectID"]);'
                        ]
                    ],
                    'values' => $registeredReferences
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'name'     => 'RegisteredReferencesConfigurationButton',
                    'caption'  => 'Aufrufen',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Registered messages
        $registeredMessages = [];
        $messages = $this->GetMessageList();
        foreach ($messages as $id => $messageID) {
            $name = 'Objekt #' . $id . ' existiert nicht';
            $rowColor = '#FFC0C0'; //red
            if (@IPS_ObjectExists($id)) {
                $name = IPS_GetName($id);
                $rowColor = '#C0FFC0'; //light green
            }
            switch ($messageID) {
                case [10001]:
                    $messageDescription = 'IPS_KERNELSTARTED';
                    break;

                case [10603]:
                    $messageDescription = 'VM_UPDATE';
                    break;

                default:
                    $messageDescription = 'keine Bezeichnung';
            }
            $registeredMessages[] = [
                'ObjectID'           => $id,
                'Name'               => $name,
                'MessageID'          => $messageID,
                'MessageDescription' => $messageDescription,
                'rowColor'           => $rowColor];
        }

        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Registrierte Nachrichten',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'RegisteredMessages',
                    'rowCount' => 10,
                    'sort'     => [
                        'column'    => 'ObjectID',
                        'direction' => 'ascending'
                    ],
                    'columns' => [
                        [
                            'caption' => 'ID',
                            'name'    => 'ObjectID',
                            'width'   => '150px',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "RegisteredMessagesConfigurationButton", "ID " . $RegisteredMessages["ObjectID"] . " aufrufen", $RegisteredMessages["ObjectID"]);'
                        ],
                        [
                            'caption' => 'Name',
                            'name'    => 'Name',
                            'width'   => '300px',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "RegisteredMessagesConfigurationButton", "ID " . $RegisteredMessages["ObjectID"] . " aufrufen", $RegisteredMessages["ObjectID"]);'
                        ],
                        [
                            'caption' => 'Nachrichten ID',
                            'name'    => 'MessageID',
                            'width'   => '150px'
                        ],
                        [
                            'caption' => 'Nachrichten Bezeichnung',
                            'name'    => 'MessageDescription',
                            'width'   => '250px'
                        ]
                    ],
                    'values' => $registeredMessages
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'name'     => 'RegisteredMessagesConfigurationButton',
                    'caption'  => 'Aufrufen',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        ########## Status

        $form['status'][] = [
            'code'    => 101,
            'icon'    => 'active',
            'caption' => self::MODULE_NAME . ' wird erstellt',
        ];
        $form['status'][] = [
            'code'    => 102,
            'icon'    => 'active',
            'caption' => self::MODULE_NAME . ' ist aktiv',
        ];
        $form['status'][] = [
            'code'    => 103,
            'icon'    => 'active',
            'caption' => self::MODULE_NAME . ' wird gelöscht',
        ];
        $form['status'][] = [
            'code'    => 104,
            'icon'    => 'inactive',
            'caption' => self::MODULE_NAME . ' ist inaktiv',
        ];
        $form['status'][] = [
            'code'    => 200,
            'icon'    => 'inactive',
            'caption' => 'Es ist Fehler aufgetreten, weitere Informationen unter Meldungen, im Log oder Debug!',
        ];

        return json_encode($form);
    }
}