function SosXmn()
{
    /* ATRIBUTOS */
    var temporizadorSessao;
    var usuario;
    
    /* M�TODOS*/
    var consulta;
    var iniciaTemporizadorSessao;
    var inicia;
    var mensageiro;
    var recarregaSessao;
    var validaRequisicao; 
    
    this.usuario                  = new Array();
    
    this.consulta                 = sos_consulta;
    this.recarregaSessao          = sos_recarregaSessao;
    this.iniciaTemporizadorSessao = sos_iniciaTemporizadorSessao;
    this.inicia                   = sos_inicia;
    this.mensageiro               = sos_mensageiro;
    this.validaRequisicao         = sos_validaRequisicao;
}

function sos_inicia()
{
    /* CONSTANTES ARTESAOXMN */
    artesaoXmn.criaFerramenta('LARGURA_MAX_JANELA_FLUTUANTE', 400);
    artesaoXmn.criaFerramenta('LARGURA_MIN_JANELA_FLUTUANTE', 300);

    /*
     * LISTA DE FERRAMENTAS
     */
    artesaoXmn.criaListaFerramenta(
        xaman.requisitaListaDados('sos.php','entidade=sos&pedido=listar_ferramenta')
    );

    /* TEMPORIZADOR PARA REATIVA��O DA SESS�O */
    sosXmn.iniciaTemporizadorSessao(1000000);
}

function sos_consulta(consulta, pedido)
{
    switch (consulta) {
    case 'aviso':
        switch (pedido) {
        case 'limpar':
            $('#area_aviso').html('');
            break;
        }
        break;
    }
}
function sos_mensageiro(mensagem, xml)
{
    var estado = false;
    
    $('#' + mensagem, xml).each(function () {
        $('#area_aviso').html('&gt;&gt;&gt;&nbsp;'
            + $(this).attr('titulo') + ' - '
            + $(this).attr('texto'));
        estado = true;
    });
    
    if (estado == false) {
    	$('#area_aviso').html(mensagem);
    }
}

function sos_iniciaTemporizadorSessao(tempo)
{
    this.temporizadorSessao = setInterval('sosXmn.recarregaSessao()', tempo);
}

function sos_recarregaSessao()
{
    var div = document.getElementById("divSessao");
    var url = "sos.php?entidade=sos&pedido=recarregarSessao";
    
    xaman.requisitaMascara(url, div);
}

function sos_validaRequisicao()
{
}

var sosXmn = new SosXmn();