<html lang="pt-BR" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
	<head>	
		<title>Ponto de Equilíbrio</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		

		<!-- STYLE -->
		<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />

		
		<script src="scripts/jquery/jquery-last.min.js"></script>
		<script src="scripts/livequery/jquery.livequery.js"></script>
		<script src="scripts/mask/jquery.price_format.1.5.js"></script>
		<script src="scripts/charts/highcharts.src.js"></script>
		<script src="scripts/blockUI/jquery.blockUI.js"></script>
		
	</head>

	<body>

		<div id="content">
			<div class="inner">
				
			<h1 id="header">Custo total</h1>
			
			<div class="one column_last widget">
					
				<h2>Informe os dados</h2>
				
				<form id="formulario">
				
					<table id="tabela-dados" class="display formEl_a tabela-sorter">

						<thead>
							<tr>
								<th width="200" class="header">Nome</th>
								<th width="200" class="header">Custo Fixo</th>
								<th width="200" class="header">Custo Variável</th>
								<th width="10" class="header"></th>
							</tr>
						</thead>

						<tfoot>
							<tr align="right">
								<th width="200" class="header"></th>
								<th width="200" class="header"></th>
								<th width="200" class="header"><a class="addLinha button" href="">+ Adicionar Linha</a></th>
								<th width="200" class="header"></th>
							</tr>
						</tfoot>
						
						<tbody>
							<tr align="center" class="cloneLinha" style="display:none">
								<td class="nome"><input name="nome[]" size="25" /></td>
								<td class="fixo"><input class="dinheiro" name="fixo[]" value="0.00" size="25" /></td>
								<td class="variavel"><input class="dinheiro" name="variavel[]" value="0.00" size="25" /></td>
								<td class="variavel"><a class="removeLinha" href="">Remover Linha</a></td>
							</tr>
							
							<tr align="center">
								<td class="nome"><input name="nome[]" value="A" size="25" /></td>
								<td class="fixo"><input class="dinheiro" name="fixo[]" value="500000.00" size="25" /></td>
								<td class="variavel"><input class="dinheiro" name="variavel[]" value="0.00" size="25" /></td>
								<td class="variavel"><a class="removeLinha" href="">Remover Linha</a></td>
							</tr>
							
							<tr align="center">
								<td class="nome"><input name="nome[]" value="B" size="25" /></td>
								<td class="fixo"><input class="dinheiro" name="fixo[]" value="200000.00" size="25" /></td>
								<td class="variavel"><input class="dinheiro" name="variavel[]" value="90.00" size="25" /></td>
								<td class="variavel"><a class="removeLinha" href="">Remover Linha</a></td>
							</tr>
							
							<tr align="center">
								<td class="nome"><input name="nome[]" value="C" size="25" /></td>
								<td class="fixo"><input class="dinheiro" name="fixo[]" value="350000.00" size="25" /></td>
								<td class="variavel"><input class="dinheiro" name="variavel[]" value="35.00" size="25" /></td>
								<td class="variavel"><a class="removeLinha" href="">Remover Linha</a></td>
							</tr>
							
						</tbody>

					</table>
					
				</form>
				
				<a href="#" id="geraGrafico" class="button button-primary ">Gerar Gráfico</a>
				
			</div>
			
				<div class="ajaxmsg"></div>
			
			
			</div>
			
		</div> <!--// End inner -->
	</div> <!--// End content -->


			<!-- JAVASCRIPTS  -->
			<script>

				jQuery.noConflict();

				// Use jQuery via jQuery(...)
				jQuery(document).ready(function($){
							
					$('.addLinha').livequery('click', function(e){
						
						e.preventDefault();
									
						$linha = $('#tabela-dados .cloneLinha').clone();

						$linha.find('.acao a.removeSolicitacao').attr('href', 1);

						$linha.insertAfter('#tabela-dados tbody tr:last').removeClass('cloneLinha').fadeIn('fast');
							
						$(".dinheiro").priceFormat({
							prefix: "",
							centsSeparator: ",",
							thousandsSeparator: ".",
							allowNegative: true
						});

					});
					
					$('.removeLinha').livequery('click', function(e){
						
						e.preventDefault();
									
						$linha = $(this).parents('tr').remove();
						
					});
					
					$('#geraGrafico').livequery('click', function(e){
						
						e.preventDefault();
						
						$.blockUI({
							message: jQuery('#blockUI'),
							fadeIn: 500
						});
										
						$.ajax({
							url         : 'acao.php',
							async       : true,
							cache       : false,
							type        : 'post',
							data        : $('#formulario').serialize(),
							dataType    : 'html',
							timeout     : 20000,
							beforeSend : function(){
								$('.ajaxmsg').fadeOut('fast').html('');
							},
							success : function(retorno){						
								$('.ajaxmsg').html(retorno).fadeIn('fast');
							},
							error : function(){
								$('.ajaxmsg').html('<p class="simple-error">Erro ao efetuar requisição</p>').fadeIn('fast');
							}

						});
						
					});
					
				});
						
				//**************************************************************************************************
				//  BLOCKUI

				// unblock when ajax activity stops
				jQuery(document).ajaxStop( jQuery.unblockUI );

				/**
				 * Grid theme for Highcharts JS
				 * @author Torstein Hønsi
				 */
				
				Highcharts.theme = {
				   colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
				   chart: {
					  backgroundColor: {
						 linearGradient: [0, 0, 500, 500],
						 stops: [
							[0, 'rgb(255, 255, 255)'],
							[1, 'rgb(240, 240, 255)']
						 ]
					  },
					  borderWidth: 2,
					  plotBackgroundColor: 'rgba(255, 255, 255, .9)',
					  plotShadow: true,
					  plotBorderWidth: 1
				   },
				   title: {
					  style: {
						 color: '#000',
						 font: 'bold 16px "Trebuchet MS", Verdana, sans-serif'
					  }
				   },
				   subtitle: {
					  style: {
						 color: '#666666',
						 font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
					  }
				   },
				   xAxis: {
					  gridLineWidth: 1,
					  lineColor: '#000',
					  tickColor: '#000',
					  labels: {
						 style: {
							color: '#000',
							font: '11px Trebuchet MS, Verdana, sans-serif'
						 }
					  },
					  title: {
						 style: {
							color: '#333',
							fontWeight: 'bold',
							fontSize: '12px',
							fontFamily: 'Trebuchet MS, Verdana, sans-serif'

						 }
					  }
				   },
				   yAxis: {
					  minorTickInterval: 'auto',
					  lineColor: '#000',
					  lineWidth: 1,
					  tickWidth: 1,
					  tickColor: '#000',
					  labels: {
						 style: {
							color: '#000',
							font: '11px Trebuchet MS, Verdana, sans-serif'
						 }
					  },
					  title: {
						 style: {
							color: '#333',
							fontWeight: 'bold',
							fontSize: '12px',
							fontFamily: 'Trebuchet MS, Verdana, sans-serif'
						 }
					  }
				   },
				   legend: {
					  itemStyle: {
						 font: '9pt Trebuchet MS, Verdana, sans-serif',
						 color: 'black'

					  },
					  itemHoverStyle: {
						 color: '#039'
					  },
					  itemHiddenStyle: {
						 color: 'gray'
					  }
				   },
				   labels: {
					  style: {
						 color: '#99b'
					  }
				   }
				};

				// Apply the theme
				var highchartsOptions = Highcharts.setOptions(Highcharts.theme);

			</script>
			<!-- FIM JAVASCRIPTS  -->

			<div id="blockUI" style="display:none; padding: 10px;">
				<h1 style="border: medium none; font-size: 20px;"><img src="images/loadder/busy.gif" /> Estamos efetuando sua requisição...</h1>
			</div>

	</body>
</html>