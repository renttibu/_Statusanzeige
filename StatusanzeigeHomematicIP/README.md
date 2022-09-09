# Statusanzeige  

[![Image](../imgs/logo-homematic-ip.png)](https://homematic-ip.com/de)

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

Dieses Modul integriert eine [Homematic IP](https://homematic-ip.com/de) Statusanzeige HmIP-BSL, HmIP-MP3P in [IP-Symcon](https://www.symcon.de).  

### 2. Voraussetzungen

- IP-Symcon ab Version 6.1
- HmIP-BSL
- HmIP-MP3P

In der Homematic CCU Zentrale sollte bei den Geräteeinstellungen des jeweiligen Kanals unter **Aktion bei Spannungszufuhr** der **Schaltzustand: Ein** ausgewählt sein.  
Sollten mehrere Homematic Geräte geschaltet werden, so sollte zusätzlich das Modul Ablaufsteuerung genutzt werden.

### 3. Schaubild

```
                       +-----------------------------+
                       | Statusanzeige HmIP (Modul)  |
                       |                             |
Auslöser <-------------+ Obere Leuchteinheit         |<------------- externe Aktion
                       |   Farbe                     |
                       |   Helligkeit                |
                       |                             |
Auslöser <-------------+ Untere Leuchteinheit        |<------------- externe Aktion
                       |   Farbe                     |
                       |   Helligkeit                |
                       +-----------+--+--------------+
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
Bei mehreren Auslösern kann eine Priorität zugeordnet werden.  
Die zutreffende Bedingung mit der höchsten Priorität wird dann ausgeführt.  

### 5. Externe Aktion

Das Modul Statusanzeige kann über eine externe Aktion geschaltet werden.  
Nachfolgendes Beispiel setzt die obere Leuchteinheit auf den Farbwert Rot bei einer Helligkeit von 100 %.

> SAHMIP_SetDeviceSignaling(12345, 0, 4, 100);  

### 6. PHP-Befehlsreferenz

#### 6.1 Signalisierung auslösen

```
boolean SAHMIP_SetDeviceSignaling(integer INSTANCE_ID, integer LIGHT_UNIT, integer COLOR, integer BRIGHTNESS);
```

Konnte der Befehl erfolgreich ausgeführt werden, liefert er als Ergebnis **TRUE**, andernfalls **FALSE**.

| Parameter     | Wert      | Bezeichnung      | Beschreibung         |
|---------------|-----------|------------------|----------------------|
| `INSTANCE_ID` |           | ID der Instanz   |                      |
|               |           |                  |                      |
| `LIGHT_UNIT`  |           | Leuchteinheit    |                      |
|               | 0         | Upper light unit | Obere Leuchteinheit  |
|               | 1         | Lower light unit | Untere Leuchteinheit |
|               |           |                  |                      |
| `COLOR`       |           | Farbe            |                      |
|               | 0         | BLACK            | Schwarz (Aus)        |
|               | 1         | BLUE             | Blau                 |
|               | 2         | GREEN            | Grün                 |
|               | 3         | TURQUOISE        | Türkis               |
|               | 4         | RED              | Rot                  |
|               | 5         | PURPLE           | Violett              |
|               | 6         | YELLOW           | Gelb                 |
|               | 7         | WHITE            | Weiß                 |
|               |           |                  |                      |
| `BRIGHTNESS`  |           | Helligkeit       |                      |
|               | 0 bis 100 | 0 bis 100%       |                      |

Beispiel:  

> SAHMIP_SetDeviceSignaling(12345, 1, 2, 50);

---

#### 6.2 Alternative Ansteuerung

Die Ansteuerung kann alternativ auch direkt an das Gerät erfolgen.

| Gerät     | Kanal | Bezeichnung          |
|-----------|-------|----------------------|
| HmIP-BSL  | 8     | Obere Leuchteinheit  |
| HmIP-BSL  | 12    | Untere Leuchteinheit |
| HmIP-MP3P | 6     | Leuchtring           |



```
boolean HM_WriteValueInteger(integer INSTANCE_ID, 'COLOR', integer COLOR);  
boolean HM_WriteValueFloat(integer INSTANCE_ID, 'LEVEL', float BRIGHTNESS);
```
Konnte der jeweilige Befehl erfolgreich ausgeführt werden, liefert er als Ergebnis **TRUE**, andernfalls **FALSE**.

| Parameter     | Beschreibung            |
|---------------|-------------------------|
| `INSTANCE_ID` | ID der Geräte-Instanz   |
| `COLOR`       | Farbe (siehe oben)      |
| `BRIGHTNESS`  | Helligkeit (siehe oben) |

Die Werte für **COLOR** und **BRIGHTNESS** entnehmen Sie bitte der entsprechenden Tabelle.


Beispiel:
> HM_WriteValueInteger(98765, 'COLOR', 4);  
> HM_WriteValueFloat(98765, 'LEVEL', 1);

---