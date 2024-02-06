1) Leia o arquivo "COMANDOS NECESSARIOS.txt".

2) Sobre o comando "ini_set('memory_limit', '-1')"

    Pelo que entendi ao ler a documentação do PHP,
    o comando suspende a limitação de MEMORY_LIMIT.
    Contudo, memory limit é importante e não deve ser removido pois pode
    resultar em abertura para ataques DDos, que inundam o site com
    solicitações que preenchem arbitrariamente a memória RAM,
    até que o site fique inutilizado.
    MEMORY_LIMIT é um guardião contra scripts descontrolados.

3) Programa "index.php"

    Carrega uma página simples (até simplória) de html que faz a chamada dos demais programas.

4) Programa "carrega_arquivos.php"

    Lê o conteúdo de arquivos PDF e salva na pasta de /treinamento/, convertendo
    para o formato TXT.

5) Programa "valida_modelo.php"

    Carrega as amostras e rótulos em um conjunto de dados ($dataset)
    usando o método build(), randomiza a ordem, pega as 10.000 primeiras linhas
    e as coloca em um novo conjunto de dados.

    Obs: em WordCountVectorizer, eu mudei o número para PHP_INT_MAX,
    pois estava criando restrições na hora de treinar o modelo, mas neste programa
    achei que 10.000 linhas seriam suficientes.

    Após, carrega o histórico de salvamentos gravado em "tipo_documento.rbx"
    e com Predict(), pega um conjunto de dados de entrada e retorna
    uma matriz de previsões.

    Ao final, gera um relatório com o resultado do treinamento, que é salvo em "relatorio.json".

    Sobre os resultados salvos em ""relatorio.json":

    Accuracy - quanto o algoritmo acertou em relação a todas as classes
               (não é indicada quando o algoritmo está desbalanceado).

    Recall - das amostras positivas, quanto o algoritmo acertou.

    Precision - das amostras que o algoritmo disse que eram positivas,
                quantas realmente são.

    F1 score - representa uma média harmônica entre Precision e Recall
               (é indicada quando a classe positiva for mais importante que a negativa).

    Obs: Precision e Recall não consideram os "true negatives", eles só validam
         se o algoritmo é bom em acertar as amostras positivas. Em alguns problemas de classificação,
         a classe negativa tem muito mais importância que a classe positiva.
         Para esses casos, existem NPV e Specificity.

    Negative predictive value (NPV) - das amostras que o algoritmo disse que eram negativas,
                                      quantas realmente são.

    Specificity - das amostras negativas, quantas o algoritmo acertou.

    F-beta - é uma generalização da F1. Quando beta=1, temos a fórmula original do F1 score.
             Quando beta=2, Recall tem um peso maior que Precision.

    Matthews Correlation Coefficient (MCC) - métrica que leva em consideração
          todas as possibilidades de um problema de classificação binária (TP, TN, FP e FN).
          Quanto mais próximo de -1 sua MCC, pior está o classificador. Ou seja, ele está errando mais do que acertando.
          Quanto mais próximo de +1, melhor está o classificador. Ou seja, ele está acertando mais do que errando.
          Quando o coeficiente é próximo de 0, mostra que o classificador está “chutando” as classes mais frequentes.

    CONCLUSÃO: Se possível, sempre calcule outras métricas.

6) Programa "prever_documento.php"

    Usa PersistentModel para carregar a rede que foi treinada anteriormente,
    salva em "tipo_documento.rbx".

    Em seguida, obtém o conteúdo do arquivo PDF carregado.

    Unlabeled() usa um conjunto de dados não rotulados para treinar
    modelos não supervisionados, para alimentar amostras desconhecidas
    e fazer previsões.

    Predict() pega um conjunto de dados de entrada e retorna uma matriz de previsões.

    Ao final, o programa mostra o resultado da previsão.

7) Programa "treina_classificador.php"

    Carrega o conjunto de dados, define a arquitetura da rede neural e instancia o classificador
    Multilayer Perceptron.

    A rede usa 5 camadas ocultas que consistem em uma camada densa (Dense) de neurônios seguida
    por uma camada de ativação não linear (Activation) e uma camada
    batch norm (BatchNorm) opcional para normalizar as ativações.

    As primeiras 3 camadas ocultas usam uma função de ativação LeakyReLU(),
    enquanto as 2 últimas utilizam LeakyReLU() e PReLU().
    O benefício que oferece é que permite
    que os neurônios aprendam mesmo que não tenham sido ativados.

    WordCountVectorizer: converte uma coleção de documentos de texto em uma matriz
    onde as linhas representam os documentos e as colunas representam os tokens
    (palavras ou n-gramas). Conta as ocorrências de cada token em cada documento,
    criando uma "matriz de termos do documento" com valores inteiros representando
    a frequência de cada token. Obs: não considera a importância dos tokens,
    simplesmente conta as ocorrências. É útil para as tarefas onde a frequência dos tokens
    é essencial, como classificação de texto ou agrupamento com base na frequência das palavras.

    Foi escolhido um tamanho de lote de 256 amostras.
    AdaMax é baseado no algoritmo Adam, mas tende a lidar melhor
    com atualizações esparsas. Ao definir a taxa de aprendizado de um otimizador,
    é importante observar que uma taxa de aprendizado muito baixa fará com que a rede
    aprenda lentamente, enquanto uma taxa muito alta impedirá que a rede aprenda.

    Uma taxa de aprendizagem global de 0,0001 parece funcionar bem para este problema.

    Definir o parâmetro de histórico como verdadeiro diz ao persister para manter
    um histórico de salvamentos anteriores.

    Durante o treinamento, o modelo registrará a pontuação de validação e a perda
    em cada iteração. A pontuação de validação é calculada usando a métrica F-Beta padrão
    em uma parte de validação do conjunto de treinamento.

    Para gerar as pontuações e perdas, é chamado o método steps() e o resultado é salvo
    em um arquivo CSV chamado "progresso.csv".

8) Programa "treina_classificador_random.php"

    Utiliza o método de aprendizagem Random Forest para
    construir árvores de decisão no momento do treinamento.

    Foi só um teste, foi o primeiro que fiz, mas achei interessante deixar salvo dentro
    do projeto para consulta.

    Obs: pode ser modificado para outro método, como o ClassificationTree(),
    para verificar qual apresenta melhor acurácia.

9) Sobre as pastas "teste", "treinamento" e "treinamento_txt"

    Nestas pastas são guardados os aquivos PDF do tipo petição inicial e procuração,
    na seguinte ordem:

        /treinamento/inicial/ - arquivos para treinamento do modelo, do tipo petição inicial;
        /treinamento/procuracao/ - arquivos para treinamento do modelo, do tipo procuração;

        /teste/inicial/ - arquivos para teste do modelo, do tipo petição inicial;
         /teste/procuracao/ - arquivos para teste do modelo, do tipo procuração.

    A pasta "treinamento_txt" é usada para guardar o resultado da conversão dos arquivos
    de treinamento, do formato PDF para o formato TXT, e segue a mesma ordem descrita acima.

    Os arquivos de teste convertidos não são salvos em pasta, pois não é necessário,
    já que o programa apenas testa se o conteúdo do arquivo carregado
    representa o conteúdo de uma petição inicial ou de uma procuração.

    As pastas foram deixadas vazias no GIT porque não posso fornecer dados reais para o treinamento,
    em virtude da LGPD.

10) Sobre os parâmetros passados em "valida_modelo.php" e "treina_classificador.php"

    Ainda estou ajustado, quem utilizar o código poderá modificá-los.

11) Por que PHP e não Python?

    Porque o sistema processual do meu local de trabalho foi desenvolvido em PHP,
    então achei que seria um desafio interessante desenvolver algum estudo sobre
    machine learning utilizando PHP.

12) Pode melhorar?

    Sempre pode melhorar.

13) Estou certa sobre tudo que falei aqui?

    Provavelmente não, mas espero ter contribuído de alguma maneira.
