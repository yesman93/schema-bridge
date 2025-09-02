<?php
/**
 * patterny pro postovni smerovaci cisla
 * 
 * @author TB 11.7.2022
 * 
 */



$config = [];


$config["GB"] = "GIR[ ]?0AA|((AB|AL|B|BA|BB|BD|BH|BL|BN|BR|BS|BT|CA|CB|CF|CH|CM|CO|CR|CT|CV|CW|DA|DD|DE|DG|DH|DL|DN|DT|DY|E|EC|EH|EN|EX|FK|FY|G|GL|GY|GU|HA|HD|HG|HP|HR|HS|HU|HX|IG|IM|IP|IV|JE|KA|KT|KW|KY|L|LA|LD|LE|LL|LN|LS|LU|M|ME|MK|ML|N|NE|NG|NN|NP|NR|NW|OL|OX|PA|PE|PH|PL|PO|PR|RG|RH|RM|S|SA|SE|SG|SK|SL|SM|SN|SO|SP|SR|SS|ST|SW|SY|TA|TD|TF|TN|TQ|TR|TS|TW|UB|W|WA|WC|WD|WF|WN|WR|WS|WV|YO|ZE)(\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}))|BFPO[ ]?\d{1,4}";
$config["JE"] = "JE\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}";
$config["GG"] = "GY\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}";
$config["IM"] = "IM\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}";
$config["US"] = "\d{5}([ \-]\d{4})?";
$config["CA"] = "[ABCEGHJKLMNPRSTVXY]\d[ABCEGHJ-NPRSTV-Z][ ]?\d[ABCEGHJ-NPRSTV-Z]\d";
$config["DE"] = "\d{5}";
$config["JP"] = "\d{3}-\d{4}";
$config["FR"] = "\d{2}[ ]?\d{3}";
$config["AU"] = "\d{4}";
$config["IT"] = "\d{5}";
$config["CH"] = "\d{4}";
$config["AT"] = "\d{4}";
$config["ES"] = "\d{5}";
$config["NL"] = "\d{4}[ ]?[A-Z]{2}";
$config["BE"] = "\d{4}";
$config["DK"] = "\d{4}";
$config["SE"] = "\d{3}[ ]?\d{2}";
$config["NO"] = "\d{4}";
$config["BR"] = "\d{5}[\-]?\d{3}";
$config["PT"] = "\d{4}([\-]\d{3})?";
$config["FI"] = "\d{5}";
$config["AX"] = "22\d{3}";
$config["KR"] = "\d{3}[\-]\d{3}";
$config["CN"] = "\d{6}";
$config["TW"] = "\d{3}(\d{2})?";
$config["SG"] = "\d{6}";
$config["DZ"] = "\d{5}";
$config["AD"] = "AD\d{3}";
$config["AR"] = "([A-HJ-NP-Z])?\d{4}([A-Z]{3})?";
$config["AM"] = "(37)?\d{4}";
$config["AZ"] = "\d{4}";
$config["BH"] = "((1[0-2]|[2-9])\d{2})?";
$config["BD"] = "\d{4}";
$config["BB"] = "(BB\d{5})?";
$config["BY"] = "\d{6}";
$config["BM"] = "[A-Z]{2}[ ]?[A-Z0-9]{2}";
$config["BA"] = "\d{5}";
$config["IO"] = "BBND 1ZZ";
$config["BN"] = "[A-Z]{2}[ ]?\d{4}";
$config["BG"] = "\d{4}";
$config["KH"] = "\d{5}";
$config["CV"] = "\d{4}";
$config["CL"] = "\d{7}";
$config["CR"] = "\d{4,5}|\d{3}-\d{4}";
$config["HR"] = "\d{5}";
$config["CY"] = "\d{4}";
$config["CZ"] = "\d{3}[ ]?\d{2}";
$config["DO"] = "\d{5}";
$config["EC"] = "([A-Z]\d{4}[A-Z]|(?:[A-Z]{2})?\d{6})?";
$config["EG"] = "\d{5}";
$config["EE"] = "\d{5}";
$config["FO"] = "\d{3}";
$config["GE"] = "\d{4}";
$config["GR"] = "\d{3}[ ]?\d{2}";
$config["GL"] = "39\d{2}";
$config["GT"] = "\d{5}";
$config["HT"] = "\d{4}";
$config["HN"] = "(?:\d{5})?";
$config["HU"] = "\d{4}";
$config["IS"] = "\d{3}";
$config["IN"] = "\d{6}";
$config["ID"] = "\d{5}";
$config["IL"] = "\d{5}";
$config["JO"] = "\d{5}";
$config["KZ"] = "\d{6}";
$config["KE"] = "\d{5}";
$config["KW"] = "\d{5}";
$config["LA"] = "\d{5}";
$config["LV"] = "\d{4}";
$config["LB"] = "(\d{4}([ ]?\d{4})?)?";
$config["LI"] = "(948[5-9])|(949[0-7])";
$config["LT"] = "\d{5}";
$config["LU"] = "\d{4}";
$config["MK"] = "\d{4}";
$config["MY"] = "\d{5}";
$config["MV"] = "\d{5}";
$config["MT"] = "[A-Z]{3}[ ]?\d{2,4}";
$config["MU"] = "(\d{3}[A-Z]{2}\d{3})?";
$config["MX"] = "\d{5}";
$config["MD"] = "\d{4}";
$config["MC"] = "980\d{2}";
$config["MA"] = "\d{5}";
$config["NP"] = "\d{5}";
$config["NZ"] = "\d{4}";
$config["NI"] = "((\d{4}-)?\d{3}-\d{3}(-\d{1})?)?";
$config["NG"] = "(\d{6})?";
$config["OM"] = "(PC )?\d{3}";
$config["PK"] = "\d{5}";
$config["PY"] = "\d{4}";
$config["PH"] = "\d{4}";
$config["PL"] = "\d{2}-\d{3}";
$config["PR"] = "00[679]\d{2}([ \-]\d{4})?";
$config["RO"] = "\d{6}";
$config["RU"] = "\d{6}";
$config["SM"] = "4789\d";
$config["SA"] = "\d{5}";
$config["SN"] = "\d{5}";
$config["SK"] = "\d{3}[ ]?\d{2}";
$config["SI"] = "\d{4}";
$config["ZA"] = "\d{4}";
$config["LK"] = "\d{5}";
$config["TJ"] = "\d{6}";
$config["TH"] = "\d{5}";
$config["TN"] = "\d{4}";
$config["TR"] = "\d{5}";
$config["TM"] = "\d{6}";
$config["UA"] = "\d{5}";
$config["UY"] = "\d{5}";
$config["UZ"] = "\d{6}";
$config["VA"] = "00120";
$config["VE"] = "\d{4}";
$config["ZM"] = "\d{5}";
$config["AS"] = "96799";
$config["CC"] = "6799";
$config["CK"] = "\d{4}";
$config["RS"] = "\d{6}";
$config["ME"] = "8\d{4}";
$config["CS"] = "\d{5}";
$config["YU"] = "\d{5}";
$config["CX"] = "6798";
$config["ET"] = "\d{4}";
$config["FK"] = "FIQQ 1ZZ";
$config["NF"] = "2899";
$config["FM"] = "(9694[1-4])([ \-]\d{4})?";
$config["GF"] = "9[78]3\d{2}";
$config["GN"] = "\d{3}";
$config["GP"] = "9[78][01]\d{2}";
$config["GS"] = "SIQQ 1ZZ";
$config["GU"] = "969[123]\d([ \-]\d{4})?";
$config["GW"] = "\d{4}";
$config["HM"] = "\d{4}";
$config["IQ"] = "\d{5}";
$config["KG"] = "\d{6}";
$config["LR"] = "\d{4}";
$config["LS"] = "\d{3}";
$config["MG"] = "\d{3}";
$config["MH"] = "969[67]\d([ \-]\d{4})?";
$config["MN"] = "\d{6}";
$config["MP"] = "9695[012]([ \-]\d{4})?";
$config["MQ"] = "9[78]2\d{2}";
$config["NC"] = "988\d{2}";
$config["NE"] = "\d{4}";
$config["VI"] = "008(([0-4]\d)|(5[01]))([ \-]\d{4})?";
$config["PF"] = "987\d{2}";
$config["PG"] = "\d{3}";
$config["PM"] = "9[78]5\d{2}";
$config["PN"] = "PCRN 1ZZ";
$config["PW"] = "96940";
$config["RE"] = "9[78]4\d{2}";
$config["SH"] = "(ASCN|STHL) 1ZZ";
$config["SJ"] = "\d{4}";
$config["SO"] = "\d{5}";
$config["SZ"] = "[HLMS]\d{3}";
$config["TC"] = "TKCA 1ZZ";
$config["WF"] = "986\d{2}";
$config["XK"] = "\d{5}";
$config["YT"] = "976\d{2}";
// --- POSTAL CODES END ---


return $config;



