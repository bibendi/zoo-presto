function translit( str )
{
   var slash = "";
   
   var LettersFrom = "абвгдеёжзийклмнопрстуфхыюэ";
   var LettersTo   = "abvgdeejziyklmnoprstufhyue";

   var BiLetters = {  
     "ц" : "ts",  "ч" : "ch", 
     "ш" : "sh", "щ" : "shh", "я" : "ja"
                   };
   str = str.replace( /(ь|ъ)/g, "");

   var _str = "";
   for( var x=0; x<str.length; x++)
    if ((index = LettersFrom.indexOf(str.charAt(x))) > -1)
     _str+=LettersTo.charAt(index);
    else
     _str+=str.charAt(x);
   str = _str;

   var _str = "";
   for( var x=0; x<str.length; x++)
    if (BiLetters[str.charAt(x)])
     _str+=BiLetters[str.charAt(x)];
    else
     _str+=str.charAt(x);
   str = _str;

   return str;
}

function str2url(str,encoding,ucfirst)
{

    
	str = str.toUpperCase();
	str = str.toLowerCase();

	str = str.replace(/[\u0105\u0104\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5]/g,'a');
	str = str.replace(/[\u00E7\u010D\u0107\u0106]/g,'c');
	str = str.replace(/[\u010F]/g,'d');
	str = str.replace(/[\u00E8\u00E9\u00EA\u00EB\u011B\u0119\u0118]/g,'e');
	str = str.replace(/[\u00EC\u00ED\u00EE\u00EF]/g,'i');
	str = str.replace(/[\u0142\u0141]/g,'l');
	str = str.replace(/[\u00F1\u0148]/g,'n');
	str = str.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8\u00D3]/g,'o');
	str = str.replace(/[\u0159]/g,'r');
	str = str.replace(/[\u015B\u015A\u0161]/g,'s');
	str = str.replace(/[\u00DF]/g,'ss');
	str = str.replace(/[\u0165]/g,'t');
	str = str.replace(/[\u00F9\u00FA\u00FB\u00FC\u016F]/g,'u');
	str = str.replace(/[\u00FD\u00FF]/g,'y');
	str = str.replace(/[\u017C\u017A\u017B\u0179\u017E]/g,'z');
	str = str.replace(/[\u00E6]/g,'ae');
	str = str.replace(/[\u0153]/g,'oe');
	str = str.replace(/[\u013E\u013A]/g,'l');
	str = str.replace(/[\u0155]/g,'r');
	
  str = translit(str);
  //alert(translit(str));
	str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]/g,'');
	str = str.replace(/[\s\'\:\/\[\]-]+/g,' ');
	str = str.replace(/[ ]/g,'-');
	str = str.replace(/[\/]/g,'-');

	if (ucfirst == 1) {
		c = str.charAt(0);
		str = c.toUpperCase()+str.slice(1);
	}
	return str;
}