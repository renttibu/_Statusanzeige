<?php

/**
 * @project       _Statusanzeige/StatusanzeigeHomematic
 * @file          SAHM_Config.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

declare(strict_types=1);

trait SAHM_Config
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
                    'image' => 'data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAANwAAAAeCAYAAABHenA+AAAAmmVYSWZNTQAqAAAACAAGARIAAwAAAAEAAQAAARoABQAAAAEAAABWARsABQAAAAEAAABeASgAAwAAAAEAAgAAATEAAgAAABUAAABmh2kABAAAAAEAAAB8AAAAAAAAAEgAAAABAAAASAAAAAFQaXhlbG1hdG9yIFBybyAyLjQuMQAAAAKgAgAEAAAAAQAAANygAwAEAAAAAQAAAB4AAAAA54/IdAAAAAlwSFlzAAALEwAACxMBAJqcGAAAA21pVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IlhNUCBDb3JlIDYuMC4wIj4KICAgPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICAgICAgPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIKICAgICAgICAgICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgICAgICAgICAgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iPgogICAgICAgICA8ZXhpZjpQaXhlbFlEaW1lbnNpb24+MzA8L2V4aWY6UGl4ZWxZRGltZW5zaW9uPgogICAgICAgICA8ZXhpZjpQaXhlbFhEaW1lbnNpb24+MjIwPC9leGlmOlBpeGVsWERpbWVuc2lvbj4KICAgICAgICAgPHhtcDpDcmVhdG9yVG9vbD5QaXhlbG1hdG9yIFBybyAyLjQuMTwveG1wOkNyZWF0b3JUb29sPgogICAgICAgICA8eG1wOk1ldGFkYXRhRGF0ZT4yMDIyLTA5LTAyVDA4OjQ5OjA2KzAyOjAwPC94bXA6TWV0YWRhdGFEYXRlPgogICAgICAgICA8dGlmZjpYUmVzb2x1dGlvbj43MjAwMDAvMTAwMDA8L3RpZmY6WFJlc29sdXRpb24+CiAgICAgICAgIDx0aWZmOlJlc29sdXRpb25Vbml0PjI8L3RpZmY6UmVzb2x1dGlvblVuaXQ+CiAgICAgICAgIDx0aWZmOllSZXNvbHV0aW9uPjcyMDAwMC8xMDAwMDwvdGlmZjpZUmVzb2x1dGlvbj4KICAgICAgICAgPHRpZmY6T3JpZW50YXRpb24+MTwvdGlmZjpPcmllbnRhdGlvbj4KICAgICAgPC9yZGY6RGVzY3JpcHRpb24+CiAgIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+CiBruBwAABa/SURBVHic7Zx7fFXVte+/Y669k52E8BDxBcgrLwyisBMe2nqjnrZ6qvb2eKLWPr2neqoVK1aSQB/uWpGnh3NK7T09Xu3DtrZy9J6+rra3KioWIQlQlAIhJFTBB4ogeeznmuP+sXbCTrKT7A0h9HPr7/NZn89ec805xlh7zTHnHI85paSi1gIiyMaEONfvrV/6Olli5sy7C6I5zgsKs1HazynMG7N+fSiRLZ0PcGIoCdbehfBAP4/fF/SLuxtWPgFotrTLKmuvtsqv+q0gMqepfnl9tnSHAsWVtZeI8nwvgbZb+EJzw/KtqaVllbVXu8rPBfJTy61rzmjeuuydky2r6WaIPSFCqsmPKHJihD7AycIoi1w8c+bd+YNX7QNxVb44UAXXPcEOdAIQ16YZQCwipk+5m7YuGJOb9SB0PDCDV/kA/79A4CNRx4zKtl3ZvLpJgl5+MmT6W8MHCve3hVJrZFa2jTTO9UDBSZDnbw6+Uy3ABxhW+IzhRuC3mTYoLw/lxCVy40mUaVghYo4IUq9oXmp5JBZxh4P/Bwr3NwZVriqatXhcpg6CeH7nh1EZf7LlGi40Na7YAFSdKv5/PQpXXe1Mbyk+I6GJ0ep3BNyjue35B3fsCMWGhkHIlATbTrOWM8jJwUQi7xf6Og42Nv5HfPC2KuWVNWfGXBljDDaayH9737bQkaGRC4LBW/ydnH563EbH4POJtbSZMzrfbn5qbXSoeKRgpOOz/wj8z0FrVlc72iofFSgcKuaTq0IBf0eszJAot2rOEWy+QgLkXaPaRICG3S+tbOuqP3Pm3QXRQtPD9Nn90op2+JYEgwTaA52OGzF5ppfjVcEYjReUXlxTCLD7nIpO1l3nVlWFfO+8QyAxulP60pRBHSfB4C3+dhk91WJmGOxElEI1Iqi0ibLftfEdubHC5v767SlWOJWyOUuK1XU/qa1UubjjMCbXuFYEiSbywu8WV9S84Bhn3a7Nuc0QGtQTVhpccqkV9xoAVfvd5saC1qJgx4WGyD+JOLMcxxSqa4WcnMhR9R8oDdY9FY7xi9deWX64N62pwdpRPtFroO6/x9Wci5GAoprjD3eUBms3icqju7YEtmYiVzpMq7xrosF//VHlEsE923H8AVUVI8R4J/+9kmDdZqM8cSI8POhfQCZ13ymfr6oKPTRY6GZ687kTXEcrQXK6C4WDKCOBQLZSlFTUzKG9806QWYqcLmghKn4RLNCpyHsSpam0ovZ7vnDeUzt2hGIRv/MoUc5MpVM+/66r4535eW0k7iEq5SJ2JEgvbjJVVR4kSgdAUUv9gmbY8kZbeB55ei9RyU2tfe75i6967RX69IFUlM2uC7YZvoQyR9AzFBmlojmemmpMhaPG8R2M5YUbSipq/r2pYUV9byU+ZQpXVLQg14yqW2JdFiCM9kKBIMmRqktKgY9Ya2tLKsJrC/WW0GAzkjVuUFS/AiDKk6UVnbMU8zBooSLSTVlBkKCKXpWbw10llTW3NtWvfKaLbemcmqBavgtSATieLMfaqjBXRT9fUhlZaU9f8C/ZzEYT5i/My4/n1qrqIvE6rvHI9hpgxV5mRe4oqex8TBI19+zeuvKNTHmkQlX+jwi3dpMVSg60RYPApoHaJcScL0hZSlEUpR6oIAuFCwZv8beb0+pU9R68dz2mHd4vA4xEGKkwGeWieF542YT5C9cQYxYwOZVepMPxiT8WcMSZoeh86aNsIJCPcEHXX4A4IwFU7WjEzO0dhwvkBJz+5C8vD+XE88L3WPRuFH+q/Cm885LXmQIzQG4sqaxbc05B6BupA9vwK1z1405RS/0FBvMgwrxkqQLvAYcVCYNFkAAwGmQM3pJmSZsZ87Gi2TW3j5IjjQMrngiAOuYjqNYCOUAEeBd4X1EVJB8YBxSKUAzmRyUVtUsK9fBjR2V0lVrzEGjXrNAGHFQ0LCoGkULQM4HRqC417xScPzVYW9fSuOK1AV+9+nHnlb1bLrRxXQ56mXgdzQJHQA8rdAiiqgTEG4TGAgWofFEdrSiurP3KnsktL7FuXVYGvqC/R+UGhDEAqhSI6JWgm/tbRk2uCgWkLVwJnJVS3ILIq6hWZMq7bM7isW3Wfg3VL5EcuDIQeARwT0E8J6GiTt/Za7igUhSsmZqQ8AqUTyIZe/UFyEWpe6M9fP70uYsW7txUsBdCdpjDAiFT3NpwmcE82q1swgHE/BjVu4yV69WxV0o8foWKrRbROxH5AejrydD6bGPM99vNaZdXVYUGHSxEdQGoH2hGdQ0iNzlu4uMSj1+ByA2q+nXgBY+2jge9r43RNwry/aSyxYBnEK0TozeI33+F8cvHEXsTImuAZo+Tfson+v3SiprSgeT5U2tD0Br7b8BleMp2SFUfw1ID3KAOVzrqfMyBa8VyO+j3VNmDNx9fKJaflbZM+wdCoay+m/Xxloq8mFKUI+j8aRctGtdfm5xofCxGLqW7t6sCO7F2Z6Z8J8xfmGetLgD+CW/0zwY5Cl9PDrinBEVza8cbMfcpXJ2FsvXGx1zXrCwKJqbAMM9wpbNjMxT5JkIZ3qy2TdH74vG8Z9M4IQ4A26YGa3/jN+YJRe9BmQc6Q5V794ejrwF/HoTlaJAWRW8L58Q37N+4JtyTvjaUzVnytFpbp/AFkIkIK4Az8ZTtUWPMyl2blzX1ortvwvyFf8yP+V8GsxTR80AuV5U7Si+uqUs1+rswNVg7CtHVKBfhrZ9fBfs1cP/YtOWBQ/RMt3oTeKXoygW/dA7mPWmRhQJXIoy3qktLfhs53AR/GOz/7oJJOEfAPo/w9yS/uSolJmYuAP5v2jZxt9hFgylrvwjIBhGOZJqSkR/zfwzR24GR/VRpAbahHBJDAUqZKhemdO5+nTUmkRPRHHcHikEpRDgv9blCp8BeoBNBseZohmInETLGDd8KfAJvhdQTSjuwWYS/eLc6GWQefQcWH3CFQ7w5GAzdk6JwplA0Pre4snZKdoJBp5WAER0xUJ3y8ttGxIx7k8B8wCBywKKfba7P3zmQQ6ClccX7oL+bdtGiLU7M+S2e/RA0rq0FPp+BeLfvach/Jj0P0V2baSqbV3evTegU8dzFXQb6ZtfK8qaGZXvTEd2/cU2Y6upfF++bMkZUvgOMUOxHiDs/B17sXd+H3I/qh5LL3a2uutXNjStbBvKMeXahrp9W+dVmR3P+HeyVIhSp6oLyykWv7qhf9VYG74+rmvCJblXkNWCq9+pMFJVgVVXouXTOExU+KdrDzulQa58WIyWZ8PTid+EHgLF9n8rriH7b74s94XQ40fcKXHtmbJQcJuz3OxJU1ZUowYHoN/3J/+bMmdE7O/NGOD4Nz7eWp3vxaEH1NuvL2w4woYDO5kwET2J6Rew8FxbSR4E0DvJYwrX3+QvkrQ4bTwAUGL8v3mHP8vvMAwpX9yIXUOHW97XjsW6FE3SGIOuyT2sFGdybSrygcJpYPgc4Cp2gNzbXr9iRIQfd+0cOls2rq7YJfQav03ympKLmwaaGlZsHaNjQ1LDiqcGo73o58FpJMPJTEZ2jnjEdBf3D3i0rBv5G69a5WrTgZzI6/w7gQhGZhLrTQTekKlLp7CUzVdybPWWT1xG7qLlhVQusyuDfFt1bz+slwcVfRUw56CSBS+KY/wb6eCaubICYz/dnv7V/RnVqsshB9MNvvt/5Y6CHM2bC/IV5GtfqXiS27tmycmdpRW1GChfPj3wBZWqaR6+o6O176le8SMqsvv/Y82enXrjoU45j1orwd/Rr94Xs9u2eB7Jk9qJOekYOEKxV42tv3hQ6Ct1r/8xQXe24re5K+s5WHSrmX/KiiRXbt6/uSNOybcrcJTf7XXc1cF0v2QNGzFeGzYYTl88ApwEIPLynfsUL2dI4KxDYr8q/4jlADJh7QPu1qEVZnxnlkBVxdyp0OT0iGLM9k5bNzWujqHYpdY4gEydXfSvV5SzqJD4H6gONq+qT/s78TWSZsd/UuGyXwL1JW2o0Vi8vmntHxvGx1k33HwStx/vvukSb4wrn9q5bEM25Gjg7tUyRH2TKq6oq5BOlT3aKQKcI9yW/fb/v37Jt1R4j5tsc+x7DipKWomLgo30eCL+PRljTj7IB0Lrp/rfFuqsEXQV8r9e1P2VJqa2q8ojIwLGI9JAcQe9U+n48DyGjEr7WY8NhcfTH2fOA9etDiaKKug2C7gHOB720aG7t+OZNqQPkMViTvjwdVH2HMPaI1w3UJWHezbgt8mqX1qvV03LjnX6SHbt8fmhMPB6ei+d8eEeE3+3YEWrPlHYqRmjg520SXgSUITIX8kYCmdomKmqfUTVfRrpd+qeL4XLg5WPVQkad8D/3UAflYNyNDLpS6MLrHfGzHbTPTKjoC/Zw+JeZ0Jg5ZdbLf2ppeAqR2zLlO2QwehnaZ2ZVUfvAa6+sGlQ/dm9ZvR1IO2CnLCl5y7G+R3du9We9H6704s4Cjcmn0fQKV3Zh5FwLkwBEdLtPNWNF6A3NSRyQmLNb4Xwg17jOTEivWGrJPEvFaAwk7g28oirxjNuK2CPdeeAiudGOaPfHisYjk0zSjlFIIHpWaUXtJzKWKwXthAHZB1oGWmwSzoB2c2+4RyINZnR+K3BGt+yY64ClXffTK2LnuUplr6a/3LftXzPOrHFsYhKSxtEg5ofNzZnFK9etu84tnVP3hFodfoVTe0GaUETr2SMKNu0+QdLdCmeBhA97PBkNuW1324i//3CJ67PFgnjBXZXWsEo4fc3BEVW3zYdzyLtTUXX7mVVBMrRv0kDT7aXqH/3XFezpYPJBETgXlUdObONVd+s8K3oWsCvTls3Na6MlwdrHEeam0JtZNrsuuGvL8kYAF/caetourhp+kqWEoySN7ZWwujEbOn4xO2IMS05xT6iMS9OXXxmKTdXDYsMZL8gMgIpG/AE97jQlX+4otZ4dk4T4T0y6kwtB/aDJzqfqebmG5jKWrGNUfvhPoIcNYj3XPed/qG4McCk9Vz5/sn43Y6UGcEz6flUQkz7hkoGQUP3rOTVAh2aD7fDE4ay0dU0CgimIdvSfRjMojhwJiC9Q0LX0M6rvDZGUJwUGp9Ni4970LwcU/Y4gnUNC2ycZenmPYUfjiteKK2qflVTXtXB1eeWis6JRZgDTUuur8PuoulkpirrSgaj2niVi/sREyNxH4Gpi4vHHm48fKhxOs1hL53HNGsOicJrr7CaeULxeNzXPieUBx5Vt73NGjIX4hG7ahj1DJOZJQcKJHxTX6Uh+wHafo/+1c9PKUyqzqHkEsccUThmTQD6eTFdL3YpzSJSX929cE+lDZACoyBsYjff2Q6pxrqIfZ0J6OZ1Lj+P4lROGKHv65kIzffqsr03auXXpX06E9rAMH00blx4Q7U6DCtLL5ZwNjIlNBmYkb4/6w/nbTlS+k4lEXmGLIF17z8Zaa7LecX0MKlRXO1RXO5BdeldPxDaAHOs4glHMzajOp2dWxU7QXWTZ6ws5tBdN6z1dUD5/4WmZ0CiaGxoJ+tls+A4VrLHpbM0c68S/TGaJncnv9Hivq9oZtvk6aXgrEHDFXVheHurrxRoE5wRD+WrlWrykYxT+Y+j2y50c7FsfiqD8Hs8vNVZVriqfH8qo0/VAdbVTXFn31dLWKS+WtE57vqQifOvkqlDWW2QA2mnrFLRXZoYGEek+t0RRF2SbeyS8L1v6ycTyX6d5NC4ey1leNGtxvzmcAKUX1xQaN3IXkFGQfaiR21mwkTSxckU+W1pRe01R0YLcNM0AbxdMcbD2UyWtU18uaW3Y3fOa+uthUzhrzH8Crcnb6ngg8jlvpM4chUQ+LnBD8na/Fd93h1TIkwRx3F8AbwEG9Ip4IvKPweAt2Th7pGTftEtFuVuR+V5itW3dtz50XBtU32g8J6Iiz9HTeeIjxTspyBGEDZm68XvDKD8AescbHUSuN8YuLZtdF0yXtFAUXDSNmNSA/jOn6BwVbxA393qDTg+cqcr9zqj8/zHtorvP6N2uaG5opBlT8HkRvgFUgExLuSYj+pthS15O5OW25HR0/i9UvgkEEO4t3jfFHV8VejQTd2tpxeJrwK4GxgAJlDV7G7I/Q/NUYPfm1a2lFXXfUPQhYBxqv95uRncAPx28dcgUVXZ8GNWleDN7XOC3cTUvcdwGTsgaW/tna9g1QM7iQcearLOBurCrMW9HcWX4UdFj+/A86EiEz1nRi0oqFzeordliDIesyghBZ4DMU2U6p/jQokAs/mTEb65DuCqlWBDOU/i2E3NuKK2o2aRqmhCNiegkdcOXABeQXIHRo6FuIMF/DdsMt299KOJY3w+B3yXd42cb5IE32sLf8Nbr6XFO8Jb80oqaOxX7yLFMFn3CzXWzig2dauyesvdHiD7i3clEVXm4pKLmganB2gGPrSupCH/GqPMoaCVgQHa6jt7nJXUfPwrI24vSr/0ryPM7G+/PKDk6PULWEl8GpNvOkwuUo/pZEVmtKo8IfAeV2/CS00/5CWHbt6/uEIdvgaZLXh8LXKJwF6IPAg+pN5H8HWmUDXhDkRW7t654c1i35+xsvP/NsjmLa1xrRwl8SJUxCN80iXB1SbD2x8AGVT0E4PhNvnXtfFQ+bdHKZCA1IfCsK/LtvX9cfXA4ZT9hrFvnOsEl33QlUQjyCbwd0wt9wlWlwZqfiJFnXFcPAxijp6k680T0RoXZyRBIAmUHyA3Nm1Yed6ZOFxobQ52lFTUbEfkH1T7xPCvwC07QRbi3fuSB0orwYkX+Dey5XRuDU2BIddz1fGpBXwU5m/Sd+KRj9+b8LaUVnXUKy/DCJb3kF4fBN9W+CdxfqIf/AKLDHuTYtXlZk+OTmwQexrNrQJiOsExF14vIS2LkRevqRpC1CPPEe7H3EPmhqvlK5rsM/rqws/H+N43P1OElsr6d7IAlKnKvVZ4XIy+KkRcV8xyiqz1lA+BdRH5qsdc2NS7LKgg9EFSdl1R5O82j5sSRjqyyQtIjZN0jnU8rLEYl43AAEAWeA25GdUhilseHkN09pfV/W+EuhOeADA6c6oYF6kFr/eG8h7pOKBg6heum1H/2fhd2vbx8H7m6CDW3gj6Gd7wCgjjJYwDGAl1OhfeBJ0XlS7F4ZNFQdrhTgV0vL9+XUO4FbkLkR9Dd4X14733s3ZUo8EtgATZwd3PjqrR7844X5xTmNoNuTfPoR9k6S/z9uICam9dGx48IrMPIzcCDCIOtTParyEpBv9zUkN+QtkaGGaSqNuOzGayNpq+7bp3bXL/i14m4/RJKncAWYCCfgwWaVWSpWPeLTQ0rf5LqSfcZY8oAYq6NnDvC/+bxfNHt20eEp89qu1b9ObkSS+j69aFBE+C8XdGhXxXNPfQsicA4g3xIjJmN6nhVjAgHVWSLNbLBJuyBlsZAGywfNL0mkCsPx2PyK4BILJzxsrPQvvf6ETPu035j8xKu4yYKfQcybRuIuS/EAv5SAJ/I+00NowfM4E/aX0+VXlyzwY3oGUZMJTBHYaKAD/SgqGwTxzwTiYUP7is+0JbJOSa5efKDeEx+k1qm1pXRMjVtsHb9+lCiJBi6wzjRUGp934j8ft/dF857JhrouNjn+JzUNpG8vH4DwkmnWH0wGNrRTudaq/JRQT6K6Gy8/YftimwR0d+4xJ92C0a+s299yAu2m7rLjEi36aPWldnj5xxK7aeduYmGEW6gx/EWEjdR23a0jw2aE+1Yb0cUXKjq61Ywta40FzW/R7qhJ4mWbav2BIO3rI3IqJ/F1cwC+XtUP4wwAcGgvIPIRlSfdsX3klvgP/YOKfh/QL7cKx+GpkgAAAAASUVORK5CYII='
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
                    'type'    => 'CheckBox',
                    'name'    => 'EnableSignalling',
                    'caption' => 'Statusanzeige'
                ]
            ]
        ];

        ##### Signalling

        //Signalling device instance
        $signallingDeviceInstance = $this->ReadPropertyInteger('SignallingDeviceInstance');
        $enableSignallingDeviceInstanceButton = false;
        if ($signallingDeviceInstance > 1 && @IPS_ObjectExists($signallingDeviceInstance)) { //0 = main category, 1 = none
            $enableSignallingDeviceInstanceButton = true;
        }

        //Signalling state
        $signallingDeviceState = $this->ReadPropertyInteger('SignallingDeviceState');
        $enableSignallingDeviceStateButton = false;
        if ($signallingDeviceState > 1 && @IPS_ObjectExists($signallingDeviceState)) { //0 = main category, 1 = none
            $enableSignallingDeviceStateButton = true;
        }

        $form['elements'][] =
            [
                'type'    => 'ExpansionPanel',
                'caption' => 'Anzeige',
                'items'   => [
                    [
                        'type'    => 'Label',
                        'caption' => 'Gerät',
                        'italic'  => true,
                        'bold'    => true
                    ],
                    [
                        'type'    => 'Select',
                        'name'    => 'SignallingDeviceType',
                        'caption' => 'Typ',
                        'options' => [
                            [
                                'caption' => 'Kein Gerät',
                                'value'   => 0
                            ],
                            [
                                'caption' => 'HM-LC-Sw4-WM, Kanal n',
                                'value'   => 1
                            ]
                        ]
                    ],
                    [
                        'type'  => 'RowLayout',
                        'items' => [
                            [
                                'type'     => 'SelectInstance',
                                'name'     => 'SignallingDeviceInstance',
                                'caption'  => 'Instanz',
                                'width'    => '600px',
                                'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "SignallingDeviceInstanceConfigurationButton", "ID " . $SignallingDeviceInstance . " Instanzkonfiguration", $SignallingDeviceInstance);'
                            ],
                            [
                                'type'    => 'Label',
                                'caption' => ' '
                            ],
                            [
                                'type'     => 'OpenObjectButton',
                                'name'     => 'SignallingDeviceInstanceConfigurationButton',
                                'caption'  => 'ID ' . $signallingDeviceInstance . ' Instanzkonfiguration',
                                'visible'  => $enableSignallingDeviceInstanceButton,
                                'objectID' => $signallingDeviceInstance
                            ]
                        ]
                    ],
                    [
                        'type'  => 'RowLayout',
                        'items' => [
                            [
                                'type'     => 'SelectVariable',
                                'name'     => 'SignallingDeviceState',
                                'caption'  => 'Variable STATE',
                                'width'    => '600px',
                                'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "SignallingDeviceStateConfigurationButton", "ID " . $SignallingDeviceState . " bearbeiten", $SignallingDeviceState);'
                            ],
                            [
                                'type'    => 'Label',
                                'caption' => ' '
                            ],
                            [
                                'type'     => 'OpenObjectButton',
                                'name'     => 'SignallingDeviceStateConfigurationButton',
                                'caption'  => 'ID ' . $signallingDeviceState . ' bearbeiten',
                                'visible'  => $enableSignallingDeviceStateButton,
                                'objectID' => $signallingDeviceState
                            ]
                        ]
                    ],
                    [
                        'type'    => 'NumberSpinner',
                        'name'    => 'SignallingDelay',
                        'caption' => 'Schaltverzögerung',
                        'minimum' => 0,
                        'suffix'  => 'Millisekunden'
                    ]
                ]
            ];

        ##### Inverted signalling

        //Inverted signalling device instance
        $invertedSignallingDeviceInstance = $this->ReadPropertyInteger('SignallingDeviceInstance');
        $enableInvertedSignallingDeviceInstanceButton = false;
        if ($invertedSignallingDeviceInstance > 1 && @IPS_ObjectExists($invertedSignallingDeviceInstance)) { //0 = main category, 1 = none
            $enableInvertedSignallingDeviceInstanceButton = true;
        }

        //Inverted signalling state
        $invertedSignallingDeviceState = $this->ReadPropertyInteger('SignallingDeviceState');
        $enableInvertedSignallingDeviceStateButton = false;
        if ($invertedSignallingDeviceState > 1 && @IPS_ObjectExists($invertedSignallingDeviceState)) { //0 = main category, 1 = none
            $enableInvertedSignallingDeviceStateButton = true;
        }

        $form['elements'][] =
            [
                'type'    => 'ExpansionPanel',
                'caption' => 'Invertierte Anzeige',
                'items'   => [
                    [
                        'type'    => 'Label',
                        'caption' => 'Gerät',
                        'italic'  => true,
                        'bold'    => true
                    ],
                    [
                        'type'    => 'Select',
                        'name'    => 'InvertedSignallingDeviceType',
                        'caption' => 'Typ',
                        'options' => [
                            [
                                'caption' => 'Kein Gerät',
                                'value'   => 0
                            ],
                            [
                                'caption' => 'HM-LC-Sw4-WM, Kanal n',
                                'value'   => 1
                            ]
                        ]
                    ],
                    [
                        'type'  => 'RowLayout',
                        'items' => [
                            [
                                'type'     => 'SelectInstance',
                                'name'     => 'InvertedSignallingDeviceInstance',
                                'caption'  => 'Instanz',
                                'width'    => '600px',
                                'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "InvertedSignallingDeviceInstanceConfigurationButton", "ID " . $InvertedSignallingDeviceInstance . " Instanzkonfiguration", $InvertedSignallingDeviceInstance);'
                            ],
                            [
                                'type'    => 'Label',
                                'caption' => ' '
                            ],
                            [
                                'type'     => 'OpenObjectButton',
                                'name'     => 'InvertedSignallingDeviceInstanceConfigurationButton',
                                'caption'  => 'ID ' . $invertedSignallingDeviceInstance . ' Instanzkonfiguration',
                                'visible'  => $enableInvertedSignallingDeviceInstanceButton,
                                'objectID' => $invertedSignallingDeviceInstance
                            ]
                        ]
                    ],
                    [
                        'type'  => 'RowLayout',
                        'items' => [
                            [
                                'type'     => 'SelectVariable',
                                'name'     => 'InvertedSignallingDeviceState',
                                'caption'  => 'Variable STATE',
                                'width'    => '600px',
                                'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "InvertedSignallingDeviceStateConfigurationButton", "ID " . $InvertedSignallingDeviceState . " bearbeiten", $InvertedSignallingDeviceState);'
                            ],
                            [
                                'type'    => 'Label',
                                'caption' => ' '
                            ],
                            [
                                'type'     => 'OpenObjectButton',
                                'name'     => 'InvertedSignallingDeviceStateConfigurationButton',
                                'caption'  => 'ID ' . $invertedSignallingDeviceState . ' bearbeiten',
                                'visible'  => $enableInvertedSignallingDeviceStateButton,
                                'objectID' => $invertedSignallingDeviceState
                            ]
                        ]
                    ],
                    [
                        'type'    => 'NumberSpinner',
                        'name'    => 'InvertedSignallingDelay',
                        'caption' => 'Schaltverzögerung',
                        'minimum' => 0,
                        'suffix'  => 'Millisekunden'
                    ]
                ]
            ];

        //Trigger list
        $triggerListValues = [];
        $variables = json_decode($this->ReadPropertyString('TriggerList'), true);
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

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Auslöser',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'TriggerList',
                    'rowCount' => 15,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
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
                            'caption' => 'Bezeichnung',
                            'name'    => 'Designation',
                            'onClick' => self::MODULE_PREFIX . '_ModifyTriggerListButton($id, "TriggerListConfigurationButton", $TriggerList["PrimaryCondition"]);',
                            'width'   => '400px',
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
                            'caption' => 'Mehrfachauslösung',
                            'name'    => 'UseMultipleAlerts',
                            'width'   => '200px',
                            'add'     => false,
                            'edit'    => [
                                'type' => 'CheckBox'
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
                            'caption' => 'Statusanzeige',
                            'name'    => 'Signalling',
                            'width'   => '300px',
                            'add'     => 0,
                            'visible' => false,
                            'edit'    => [
                                'type'    => 'Select',
                                'options' => [
                                    [
                                        'caption' => '0 - Aus',
                                        'value'   => 0
                                    ],
                                    [
                                        'caption' => '1 - An',
                                        'value'   => 1
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'values' => $triggerListValues,
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'name'     => 'TriggerListConfigurationButton',
                    'caption'  => 'Bearbeiten',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Command control
        $id = $this->ReadPropertyInteger('CommandControl');
        $enableButton = false;
        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
            $enableButton = true;
        }
        $form['elements'][] = [
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
                    'onClick' => self::MODULE_PREFIX . '_UpdateState(' . $this->InstanceID . '); echo "Status wird aktualisiert!";'
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