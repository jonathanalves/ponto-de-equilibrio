<?PHP

try{

	//	CANCELA TIMEOUT
		set_time_limit(0);

	//	RECEBE VALORES DENTRO DE UM VETOR

		/* inicia vetor */
		$dadosIniciais = array();

		/* conta tamanho do vetor */
		$tamanho = count($_POST['nome']);

		/* incremental */
		$i = 0;

	//	EFETUA TRATAMENTO DOS VALORES

		while($i < $tamanho):

			if($_POST['nome'][$i] != ""){

				$dadosIniciais[] = array(
					'nome' 		=> $_POST['nome'][$i],
					'fixo' 		=> (float) str_replace(",", ".", str_replace(".", "", $_POST['fixo'][$i] ) ),
					'variavel' 	=> (float) str_replace(",", ".", str_replace(".", "", $_POST['variavel'][$i] ) )
				);

			}

			++$i;

		endwhile;

		/* qtd de dados */
		$qtdDados = count($dadosIniciais);

		/* se nao houver dados, retorna erro */
		if( $qtdDados <= 1 )
			throw new Exception("Por favor, verifique seus dados");

	//	CALCULA EQUILIBRIOS ENTRE OS VALORES (SE VALOR FOR MENOR QUE ZERO MULTIPLICA POR -1)

		/* inicia vetor de equilibrios */
		$equilibrios = array();

		/* adiciona valor inicial zero */
		$equilibrios[] = number_format( 0, 2, '.', '' );

		/* dados com média ja calculada */
		$vistos = array();

		/* incremental */
		$lacoUm = 0;

		/* inicia laço */
		while( $lacoUm < $qtdDados ):

			/* armazena os indices que ja foram vistos */
			$vistos[] = $lacoUm;

			/* incremental */
			$lacoDois = 0;

			/* inicia laço */
			while( $lacoDois < $qtdDados ):

				/* efetua operação caso o indice ainda nao foi visto */
				if(!in_array($lacoDois, $vistos)):

					/* calcula equilibrio */
					
					$parte1 = ( (float)$dadosIniciais[$lacoUm]['fixo'] - (float)$dadosIniciais[$lacoDois]['fixo'] );
					$parte2 = ( (float)$dadosIniciais[$lacoDois]['variavel'] - (float)$dadosIniciais[$lacoUm]['variavel'] );
					
					if( $parte2 == 0 )
						$equilibrio = 0;
					else
						$equilibrio = ( $parte1 / $parte2 );
							
					/* formata numero */
					$equilibrio = number_format( (float)$equilibrio, 2, '.', '' );

					/* se resultado for negativo, multiplica por 1 */
					if($equilibrio < 0)
						$equilibrio = $equilibrio * -1;

					/* adiciona ao vetor de equilibrios */
					$equilibrios[] = $equilibrio;

				endif;

				/* incremental */
				$lacoDois++;

			endwhile;

			/* incremental */
			$lacoUm++;

		endwhile;

	//	TRATA VETOR DE EQUILIBRIOS

		/* remove valores duplicados */
		$equilibrios = array_unique($equilibrios);

		/* odena em ordem crescente */
		sort($equilibrios);

	//	OPERAÇOES COM PONTOS DE EQUILIBRIO

		/* calcula quantidade de pontos de equilibrio */
		$qtdEqui = count($equilibrios);

		/* media dos pontos de equilibrio */
		$mediaPE = array();

		/* calcula media dos pontos de equilibrio */
		$total = 0;

		/* equilibrio mais alto */
		$equilMax = $equilibrios[ count( $equilibrios) - 1 ];

		$total = $equilMax*2; // ignora laço abaixo
			
		while($total < $equilMax):
		
			$total = (float)$total + (float)($equilMax / ( $qtdEqui - 1 ) );
			$mediaPE[] = number_format( (float)$total, 2, '.', '' );

		endwhile;

	//	EQUILIBRIOS PARA CALCULO

		/* efetua merge */
		$equilibriosFinais = array_merge($mediaPE, $equilibrios);

		/* remove valores duplicados */
		$equilibriosFinais = array_unique($equilibriosFinais);

		/* odena em ordem crescente */
		sort($equilibriosFinais);

	//	CALCULA SALDOS POR EQUILIBRIO

		/* inicia array de series */
		$series = array();

		/* inicia laço com os nomes dos vetores */
		foreach($dadosIniciais as $d):

			$series[$d['nome']] = array();

			foreach($equilibriosFinais as $e):

				$custototal = (float) ( $d['fixo'] + ( $d['variavel'] * $e ) );
				$series[$d['nome']][] = array($e, number_format( (float)$custototal, 2, '.', '' ));

			endforeach;

		endforeach;

	//	MONTA STRINGS DAS SERIES DO GRAFICO (VALORES TOTAIS)

		/* inicia array */
		$seriesString = array();
		
		foreach($series as $key => $array):

			$cats = array();
			
			foreach($array as $v):
				
				$cats[] = "[ $v[0], $v[1] ]";
			
			endforeach;
			
			/* retira elementos do array */
			$cats = implode(", ", $cats);

			/* criar array de strings */
			$seriesString[] ="
					{
						name: '{$key}',
						data: [{$cats}]
					}";

		endforeach;
		
		/* concatena string */
		$seriesString = implode(", ", $seriesString);
		$seriesString .= "\n";

	//	MONTA STRING DA LEGENDA DO GRAFICO (PE'S) - CATEGORIAS

		/* retira elementos do array */
		$categorias = implode(", ", $equilibriosFinais);

	//	INICIA CRIAÇÃO DA TABELA

		/*	cabeçalho */

		$thead = '';

		foreach($equilibriosFinais as $eq):

			$num = number_format( (float)$eq, 2, ',', '.' );
			$thead .= "<th>{$num}</th>";

		endforeach;

		/*	valores */

		$tbody = '';
		foreach($series as $key => $array):
			
			$tbody .= "<tr class='linhaTabela'><td><strong>{$key}</strong></td>";

			foreach($array as $a):
				$num = number_format( (float)$a[1], 2, ',', '.' );
				$tbody .= "<td>{$num}</td>";
			endforeach;

			$tbody .= "</tr>";

		endforeach;

	//	IMPRIME NA TELA O SCRIPT DO HIGHCHARTS

	?>

	<div class="one column_last" style="over">
	
		<h2>Resultados</h2>
		
		<div style="overflow:auto;">
		
			<table border="1" cellpadding="1">
				<thead>
					<tr>
						<th><strong>Quantidade</strong></th>
						<?PHP echo $thead; ?>
					</tr>
				</thead>

				<tbody>
					<?PHP echo $tbody; ?>
				</tbody>

			</table>
				
		</div>
		
	</div>
	
	<div class="one column_last">

		<h2>Gráfico</h2>
		
		<br />
		<br />
		
		<div id="container" style="width: 100%; margin:0 auto" class="clear"></div>
		<div class="clear"></div>
		<br />
		
	</div>
	
	
	<script>
	
		var chart;

		jQuery.noConflict();

		jQuery(document).ready(function($){
			chart = new Highcharts.Chart({
				chart: {
					renderTo: 'container',
					type: 'line',
					marginRight: 30,
					marginBottom: 25,
					width: 900
				},
				title: {
					text: 'Custo Total',
					x: -20 //center
				},
				subtitle: {
					text: 'Com base na aula do dia 28/03',
					x: -20
				},
				xAxis: {
                    //categories: [],
					showFirstLabel: false,
                    labels: {
                        x: -10,
                        //formatter: function() {
                        //   return Highcharts.numberFormat(this.value, 2);
                        //}
                    }
                },
				yAxis: {
                    title: {
                        text: 'Custo Total'
                    },
                    labels: {
                        align: 'right',
                        x: -10,
                        y: 0,
                        //formatter: function() {
                        //   return Highcharts.numberFormat(this.value, 2);
                        //}
                    }
                },
				tooltip: {
					formatter: function() {
							return '<b>'+ this.series.name +'</b><br/>'+
							'Qtd: ' + Highcharts.numberFormat(this.x, 2) +' | R$ '+ Highcharts.numberFormat(this.y, 2);
					}
				},
				legend: {
					align: 'center',
					verticalAlign: 'top',
					x: 0,
					y: 50
				},
				series: [<?PHP echo $seriesString; ?>],
				credits: {
						enabled: false
				}
			});
		});
	</script>

	<?PHP

}catch(Exception $e){
	echo '<div class="one column_last widget"><h2>Um Erro Ocorreu!</h2><br /><p class="simple-error">'.$e->getMessage().'</p></div>';
}

exit();

?>