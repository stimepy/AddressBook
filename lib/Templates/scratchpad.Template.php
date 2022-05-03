<?php

class scratchpadTemplate
{
    function __constructor(){
    }

    function scratchpadMain($body, $lang, $fileURL){
        $witdth = 550;
        $rows = 30;
        $useUrl = true;  // for use when we want an url;

        $output = "<body>
            <form name=\"Scratchpad\" action=\"". $fileURL['FILE_SCRATCHPAD']."\" method=\"post\">
                <input type=\"hidden\" name=\"saveNotes\" value=\"YES\">
                <table class =\"width570\">
                    <tr>
                        <td CLASS=\"navMenu\">
                            ". ButtonUrl($lang['BTN_EDIT'], $useUrl,"#edit") ."
                            ". ButtonUrl($lang['BTN_LIST'], $useUrl,$fileURL['FILE_LIST']) ."
                        </td>
                    </tr>
                    <tr>
                        <td class=\"headTitle\">
                            ". $lang['TITLE_SCRATCH'] ."
                        </td>
                    </tr>
                    <tr>
                        <td class=\"infoBox\">
                            <table style='width:75%'>
                                ". $this->recentSave($body["recentSave"],$lang) ."
                                <tr style=\"vertical-align: top\">
                                    <td class=\"data\">
                                        ". $lang['SCRATCH_HELP'] ."
                                    </td>
                                </tr>
                                <tr style=\"vertical-align: top\">
                                    <td class=\"listDivide500\">&nbsp;</td>
                                </tr>
                                <tr style=\"vertical-align: top\">
                                    <td class=\"data550\">
                                        ".$body['notes']."
                                    </td>
                                </tr>
                                <tr style=\"vertical-align: top\">
                                    <td class=\"listDivide\">&nbsp;</td>
                                </tr>
                               <tr style=\"vertical-align: top\">
                                    <td class=\"listHeader550\">
                                        <a NAME=\"edit\"></a>". ucfirst($lang['BTN_EDIT']) ."
                                    </td>
                               </tr>
                               <tr style=\"vertical-align: top\">
                                    <td class=\"data550\">
                                        ". createTextArea($witdth, $rows, "notes", $body['notes']) ."
                                    </td>
                               </tr>
                               <tr style=\"vertical-align: top\">
                                    <td class\"listDivide550\">&nbsp;</td>
                               </tr>
                               <tr style=\"vertical-align: top\">
                                    <td class=\"navMenu550\">
                                        ". ButtonUrl($lang['BTN_SAVE']) ."
                                        ". ButtonUrl($lang['BTN_LIST'], $useUrl, $fileURL['FILE_LIST']) ."
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
               </table>
            </form>
            <table>
                ". printFooter() ."
            </table>     
            </body>
        </html>";

        return $output;
    }

    private function recentSave($didSave, $lang){
        if($didSave) {
            return "<tr style=\"vertical-align: top\">
                <td class=\"data550\">
                    <b>" . $lang['SCRATCH_SAVED'] . "</b>
                </td>
            </tr>
            <tr style=\"vertical-align: top\">
                <td class=\"listDivide500\">&nbsp;</td>
            </tr>";
        }
        return "";
    }


}