/**
 * Xaman - Estrutura de Desenvolvimento
 * Copyright (C) 2008  Ivan Ogassavara  
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @license    http://www.gnu.org/licenses    GNU License
 * @copyright  Copyright (C) 2008  Ivan Ogassavara
 * @category   mascara
 * @package    xaman.mascara
 * @version    Release: @package_version@
 * @author     Ivan Ogassavara <ivan.ogassavara@gmail.com>
 */

/** CLASSE XAMAN */
function Xaman ()
{
    /* ATRIBUTOS */
    var autenticacao;
    
    /* M�TODOS P�BLICOS */
    var atribuiEvento;
    var atribuiAutenticacao;
    var requisitaListaDados;
    var requisitaDados;
    var requisitaMascara;
    var ativaEventoTeclado;
    var preparaDados;
    var processaDados;
    
    /* M�TODOS PRIVADOS */
    var _requisicaoMudancaProcesso;
    
    this.atribuiEvento              = xaman_atribuiEvento;
    this.atribuiAutenticacao        = xaman_atribuiAutenticacao;
    this.requisitaListaDados      = xaman_requisitaListaDados;
    this.requisitaDados           = xaman_requisitaDados;
    this.requisitaMascara           = xaman_requisitaMascara;
    this._requisicaoMudancaProcesso = _xaman_requisicaoMudancaProcesso;
    this.ativaEventoTeclado         = xaman_ativaEventoTeclado;
    this.preparaDados               = xaman_preparaDados;
    this.processaDados              = xaman_processaDados;
}

function xaman_atribuiAutenticacao(autenticacao)
{
    this.autenticacao = autenticacao;
}

function xaman_atribuiEvento(objeto, tipo, evento, cap)
{ 
    if(objeto.addEventListener) { 
        objeto.addEventListener(tipo,evento,cap);
         
    } else if(objeto.attachEvent) { 
        objeto.attachEvent("on" + tipo,evento); 
    } 
} 

function xaman_requisitaMascara(url, objeto, parametro)
{
    var tamanhoRequisicao = 0;
    
    if (!parametro > 0) {
        tamanhoRequisicao = 0;
    } else {
        tamanhoRequisicao = parametro.length;
    }
    
    try {
        var mascara;
        mascara = new XMLHttpRequest();
        
    } catch(tratamentoXaman) {
        try {
            // Para o IE da MS
            mascara = new ActiveXObject("Msxml2.XMLHTTP");
        } catch(tratamentoXaman1) {
            try {
                // Para o IE da MS
                mascara = new ActiveXObject("Microsoft.XMLHTTP");
            } catch(tratamentoXaman2) {
                mascara = false;
            }
        }
    }
    
    if(window.XMLHttpRequest) {
        mascara.open("POST", url, true);
        mascara.setRequestHeader(
                "Content-Type", "application/x-www-form-urlencoded");
        mascara.setRequestHeader("Content-length", tamanhoRequisicao);
        mascara.setRequestHeader("Connection", "close");
        mascara.send(parametro);
        
        mascara.onreadystatechange = 
            function() {
                xaman._requisicaoMudancaProcesso(mascara, objeto);
            }
    } else if((window.ActiveXObject)) {
        if(mascara) {
            mascara.open("POST",url, true);
            mascara.setRequestHeader(
                    "Content-Type", "application/x-www-form-urlencoded");
            mascara.setRequestHeader("Content-length", tamanhoRequisicao);
            mascara.setRequestHeader("Connection", "close");
            mascara.send(parametro);
            
            mascara.onreadystatechange = 
                function () {
                    xaman._requisicaoMudancaProcesso(mascara, objeto);
                }
        }
    }

} 

function _xaman_requisicaoMudancaProcesso(requisicao, objeto)
{
    switch(requisicao.readyState) { 
        case 0: { 
            objeto.innerHTML = "NAO_INICIADO";
            break; 
        } 
        case 1: { 
            objeto.innerHTML = "CARREGANDO";
            break; 
        } 
        case 2: { 
            objeto.innerHTML = "CARREGADO";
            break; 
        } 
        case 3: { 
            objeto.innerHTML = "<span class='body'>CARREGANDO ...</span>";
            break; 
        } 
        case 4: { 
            //completado 
            if(requisicao.status == 200 || requisicao.status == 0) { 
                    objeto.innerHTML = "INICIADO";
                    objeto.innerHTML = requisicao.responseText;
            } else { 
                    objeto.innerHTML = "Status: " 
                        + requisicao.status 
                        + "<br><br>PEDIDO_INVALIDO<br><br>" 
                        + requisicao.statusText;
            } 
        } 
    }
    
    return;
}

function xaman_requisitaListaDados(url, parametro)
{
    var tamanhoRequisicao = 0;
    
    if (!parametro > 0) {
        tamanhoRequisicao = 0;
    } else {
        tamanhoRequisicao = parametro.length;
    }
    
    try{
        var mascara = new XMLHttpRequest();
        
        if(mascara.overrideMimeType) {
            mascara.overrideMimeType('text/xml');
        }

    } catch(tratamentoXaman) {
    
        try {
            /* Para o IE da MS */
            mascara = new ActiveXObject("Msxml2.XMLHTTP");
            
        } catch(tratamentoXaman1) {
            try {
                /* Para o IE da MS */
                mascara = new ActiveXObject("Microsoft.XMLHTTP");
                
            } catch(tratamentoXaman2) {
                mascara = false;
            }
        }
    }
    
    var retorno = null;

    if(window.XMLHttpRequest) {
        mascara.open("POST", url, false);
        mascara.setRequestHeader(
                "Content-Type", "application/x-www-form-urlencoded");
        mascara.setRequestHeader("Content-length", tamanhoRequisicao);
        mascara.setRequestHeader("Connection", "close");

        mascara.send(parametro);
        
        return mascara.responseXML;
        
    } else if((window.ActiveXObject)) {
        if(mascara) {
            mascara.open("POST",url, false);
            mascara.setRequestHeader(
                    "Content-Type", "application/x-www-form-urlencoded");
            mascara.setRequestHeader("Content-length", tamanhoRequisicao);
            mascara.setRequestHeader("Connection", "close");
            mascara.send(parametro);

            return mascara.responseXML;
        }
    }
    
    return retorno;
}

function xaman_requisitaDados(url, info, parametro)
{
    var xml = this.requisitaListaDados(url, parametro);
    
    if (typeof(xml.getElementsByTagName('modeloXml')[0]) != 'undefined') {
        
        var objDados = null;
        
        for (var contador = 0; 
            contador < xml.getElementsByTagName('modeloXml').length; contador++) {
        
            objDados = xml.getElementsByTagName('modeloXml')[contador];
            
            return objDados.getElementsByTagName(info
                )[0].firstChild.nodeValue;
        }
        
    } else {
        return null;
    }
    
    return null;
}

function xaman_ativaEventoTeclado(evento, metodo, sinalAtivacao)
{
    var tecla;
    var texto;

    if (window.event){
        tecla = evento.keyCode;
    } else {
        tecla = evento.which;
    }

    if (tecla == sinalAtivacao){
        eval(metodo);
        return false;
    }
}

function xaman_preparaDados(obj)
{
    var resposta = '';
    var sel;
    
    for (i=0; i<obj.getElementsByTagName("input").length; i++) {
        if (obj.getElementsByTagName("input")[i].type == "text") {
           resposta += obj.getElementsByTagName("input")[i].name + "=" + 
                   obj.getElementsByTagName("input")[i].value + "&";
        }
        if (obj.getElementsByTagName("input")[i].type == "hidden") {
            resposta += obj.getElementsByTagName("input")[i].name + "=" + 
                    obj.getElementsByTagName("input")[i].value + "&";
        }
        if (obj.getElementsByTagName("input")[i].type == "checkbox") {
           if (obj.getElementsByTagName("input")[i].checked) {
              resposta += obj.getElementsByTagName("input")[i].name + "=" + 
                   obj.getElementsByTagName("input")[i].value + "&";
           } else {
              resposta += obj.getElementsByTagName("input")[i].name + "=&";
           }
        }
        if (obj.getElementsByTagName("input")[i].type == "radio") {
           if (obj.getElementsByTagName("input")[i].checked) {
              resposta += obj.getElementsByTagName("input")[i].name + "=" + 
                   obj.getElementsByTagName("input")[i].value + "&";
           }
        }  
        if (obj.getElementsByTagName("input")[i].tagName == "SELECT") {
            sel = obj.getElementsByTagName("input")[i];
            resposta += sel.name + "=" + sel.options[sel.selectedIndex].value + "&";
        }
    }
    
    for (i=0; i<obj.getElementsByTagName("textarea").length; i++) {
        if (obj.getElementsByTagName("textarea")[i].type == "textarea") {
           resposta += obj.getElementsByTagName("textarea")[i].name + "=" + 
                   obj.getElementsByTagName("textarea")[i].value + "&";
        }
    }
    
    for (i=0; i<obj.getElementsByTagName("select").length; i++) {
        if (obj.getElementsByTagName("select")[i].type == "select-one") {
           resposta += obj.getElementsByTagName("select")[i].name + "=" + 
                   obj.getElementsByTagName("select")[i].value + "&";
        }
    }
    
    return resposta;
}

function xaman_processaDados(caminho, pedido)
{
    return this.requisitaListaDados(caminho, pedido);
}

var xaman = new Xaman();

function var_dump(data,addwhitespace,safety,level) {
        var rtrn = '';
    var dt,it,spaces = '';
    if(!level) {level = 1;}
    for(var i=0; i<level; i++) {
       spaces += '   ';
    }//end for i<level
    if(typeof(data) != 'object') {
       dt = data;
       if(typeof(data) == 'string') {
          if(addwhitespace == 'html') {
             dt = dt.replace(/&/g,'&amp;');
             dt = dt.replace(/>/g,'&gt;');
             dt = dt.replace(/</g,'&lt;');
          }//end if addwhitespace == html
          dt = dt.replace(/\"/g,'\"');
          dt = '"' + dt + '"';
       }//end if typeof == string
       if(typeof(data) == 'function' && addwhitespace) {
          dt = new String(dt).replace(/\n/g,"\n"+spaces);
          if(addwhitespace == 'html') {
             dt = dt.replace(/&/g,'&amp;');
             dt = dt.replace(/>/g,'&gt;');
             dt = dt.replace(/</g,'&lt;');
          }//end if addwhitespace == html
       }//end if typeof == function
       if(typeof(data) == 'undefined') {
          dt = 'undefined';
       }//end if typeof == undefined
       if(addwhitespace == 'html') {
          if(typeof(dt) != 'string') {
             dt = new String(dt);
          }//end typeof != string
          dt = dt.replace(/ /g,"&nbsp;").replace(/\n/g,"<br>");
       }//end if addwhitespace == html
       return dt;
    }//end if typeof != object && != array
    for (var x in data) {
       if(safety && (level > safety)) {
          dt = '*RECURSION*';
       } else {
          try {
             dt = var_dump(data[x],addwhitespace,safety,level+1);
          } catch (e) {continue;}
       }//end if-else level > safety
       it = var_dump(x,addwhitespace,safety,level+1);
       rtrn += it + ':' + dt + ',';
       if(addwhitespace) {
          rtrn += '\n'+spaces;
       }//end if addwhitespace
    }//end for...in
    if(addwhitespace) {
       rtrn = '{\n' + spaces + rtrn.substr(0,rtrn.length-(2+(level*3))) + 
            '\n' + spaces.substr(0,spaces.length-3) + '}';
    } else {
       rtrn = '{' + rtrn.substr(0,rtrn.length-1) + '}';
    }//end if-else addwhitespace
    if(addwhitespace == 'html') {
       rtrn = rtrn.replace(/ /g,"&nbsp;").replace(/\n/g,"<br>");
    }//end if addwhitespace == html
    return rtrn;
 }//end function var_dump