# Statusanzeige  

[![Image](../imgs/logo-homematic.png)](https://homematic-ip.com/de)

Zur Verwendung dieses Moduls als Privatperson, Einrichter oder Integrator wenden Sie sich bitte zunächst an den Autor.  

Für dieses Modul besteht kein Anspruch auf Fehlerfreiheit, Weiterentwicklung, sonstige Unterstützung oder Support.  
Bevor das Modul installiert wird, sollte unbedingt ein Backup von IP-Symcon durchgeführt werden.  
Der Entwickler haftet nicht für eventuell auftretende Datenverluste oder sonstige Schäden.  
Der Nutzer stimmt den o.a. Bedingungen, sowie den Lizenzbedingungen ausdrücklich zu.  


### Inhaltsverzeichnis

1. [Modulbeschreibung](#1-modulbeschreibung)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Schaubild](#3-schaubild)
4. [Auslöser](#4-auslöser)
5. [Externe Aktion](#5-externe-aktion)
6. [PHP-Befehlsreferenz](#6-php-befehlsreferenz)
   1. [Signalisierung auslösen](#61-signalisierung-auslösen)
   2. [Alternative Ansteuerung](#62-alternative-ansteuerung)

### 1. Modulbeschreibung

Dieses Modul integriert eine [Homematic](https://www.eq-3.de/produkte/homematic.html) Statusanzeige in [IP-Symcon](https://www.symcon.de).  

### 2. Voraussetzungen

- IP-Symcon ab Version 6.1
- HM-LC-Sw4-WM

Sollten mehrere Homematic Geräte geschaltet werden, so sollte zusätzlich das Modul Ablaufsteuerung genutzt werden.

### 3. Schaubild

```
                       +--------------------------+
                       | Statusanzeige HM (Modul) |
                       |                          |
Auslöser <-------------+ Statusanzeige            |<------------- externe Aktion
                       +-----------+--+-----------+
                                   |  |
                                   |  |
                                   |  |    +---------------------------+
                                   |  +--->|  Ablaufsteuerung (Modul)  |
                                   |       +--------------+------------+
                                   |                      |
                                   |                      |
                                   v                      |
                       +----------------------+           |
                       |  Statusanzeige (HW)  |<----------+
                       +----------------------+
```

### 4. Auslöser

Das Modul Statusanzeige reagiert auf verschiedene Auslöser.  

### 5. Externe Aktion

Das Modul Statusanzeige kann über eine externe Aktion geschaltet werden.  
Nachfolgendes Beispiel schaltet die Statusanzeige an.

> SAHM_ToggleSignalling(12345, true, false, true);  

### 6. PHP-Befehlsreferenz

#### 6.1 Signalisierung auslösen

```
boolean SAHM_ToggleSignalling(integer INSTANCE_ID, bool STATE, bool OVERRIDE_MAINTENANCE, bool CHECK_DEVICE_STATE);
```

Konnte der Befehl erfolgreich ausgeführt werden, liefert er als Ergebnis **TRUE**, andernfalls **FALSE**.

| Parameter              | Wert       | Bezeichnung    | Beschreibung                     |
|------------------------|------------|----------------|----------------------------------|
| `INSTANCE_ID`          |            | ID der Instanz |                                  |
|                        |            |                |                                  |
| `STATE`                |            | Status         |                                  |
|                        | false      | Aus            | Statusanzeige Aus                |
|                        | true       | An             | Statusanzeige An                 |
|                        |            |                |                                  |
| `OVERRIDE_MAINTENANCE` |            |                |                                  |
|                        | false      | Aus            | Wartungsmodus wird geprüft       |
|                        | true       | An             | Wartungsmodus wird nicht geprüft |
|                        |            |                |                                  |
| `CHECK_DEVICE_STATE`   |            |                |                                  |
|                        | false      | Aus            | Prüft keinen Gerätestatus        |
|                        | true       | An             | Prüft den Gerätestatus           |

Beispiel:  

> SAHM_ToggleSignalling(12345, false, true, false);

---

#### 6.2 Alternative Ansteuerung

Die Ansteuerung kann alternativ auch direkt an das Gerät erfolgen.

```
boolean HM_WriteValueBoolean(integer INSTANCE_ID, string STATE, boolean VALUE); 
```
Konnte der jeweilige Befehl erfolgreich ausgeführt werden, liefert er als Ergebnis **TRUE**, andernfalls **FALSE**.

| Parameter        | Beschreibung          |
|------------------|-----------------------|
| `INSTANCE_ID`    | ID der Geräte-Instanz |
| `STATE`          | STATE                 |
| `VALUE`          | Wert                  |

Beispiel:
> HM_WriteValueBoolean(98765, 'STATE', false);
---