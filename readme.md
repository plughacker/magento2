# Plugin Magento

Através da instalação e ativação do módulo Malga na sua loja Magento, você tem disponível uma solução pronta para realizar cobranças por cartão de crédito, pix e boleto, com total segurança para os seus clientes, sem precisar desenvolver a integração, basta implementar o projeto na sua loja, realizar as configurações através do painel da loja e começar a processar pagamentos.

Além disso, com a infraestrutura da Malga, você pode realizar alterações no [Fluxo de pagamentos](https://docs.malga.io/docs/flow-guide/flow-dash) com autonomia pelo [Dashboard](https://dashboard.malga.io/sign-up), ajustando as regras de processamento e os [provedores de pagamento](https://docs.malga.io/api#section/Provedores-e-meios-de-pagamentos-suportados) da sua loja, e também fazer o acompanhamento dos dados das cobranças através das nossas soluções de [Painel de Dados](https://docs.malga.io/docs/dashboard/insights), [Analytics API](https://docs.malga.io/docs/intro-analytics) e [Exportação de cobranças](https://docs.malga.io/docs/dashboard/export-data).

Utilizando o módulo Malga para Magento, sua loja ganha novos recursos para melhorar a taxa de aprovação de pagamentos e flexibilidade para utilizar os provedores, de forma a reduzir custos e tornar o fluxo mais eficiente, aumentando seus resultados.

### Recursos do Conector Malga para Módulo Magento

| Recurso  |  Suporte do conector |
| ------------ | ------------ |
|  Suporte a pagamento por Pix, cartão de crédito e boleto | ✅ |
|  Suporte aos provedores de pagamento pela Malga | ✅ |
|  Suporta antifraude | ✅ |
|  Suporta adição de condicionais no fluxo inteligente de pagamentos, exceto envio de campo personalizado na cobrança | ✅ |
|  Estorno e captura de cobranças pelo painel VTEX | ✅ |
|  Acompanhamento das cobranças e disputas pelo Dashboard Malga | ✅ |
|  Acesso aos Dados através da Exportação em .csv, Analytics API e Painel | ✅ |
|  Gestão do fluxo de pagamentos pelo Dashboard Malga | ✅ |


## Get started

### Crie uma conta Malga
A criação da sua conta de cliente na Malga é uma etapa fundamental para utilização dos serviços. Com a criação da sua conta você terá acesso às credenciais client-id e api-key necessárias para utilização dos serviços.

Durante o setup é feita também a configuração dos provedores de pagamento que estarão disponíveis para autorização na sua conta.


Entre em contato conosco pelo e-mail suporte@malga.io solicitando a criação da conta e as credenciais, indicando os provedores e métodos de pagamento que deseja utilizar. Para conhecer os provedores e métodos disponíveis na Malga, consulte a [nossa documentação](https://docs.malga.io/api#section/Provedores-e-meios-de-pagamentos-suportados);

## Instale o Módulo​ via composer
```
PHP >= 8.1
Magento >= 2.4
```

### Adicione o código a seguir em seu arquivo: composer.json:
```bash
composer require plughacker/magento2
```

### Depois utilizar os comandos a seguir em seu Magento root:

```bash
composer update
./bin/magento setup:upgrade
./bin/magento setup:di:compile
```

**E pronto! 🥳** 
**O módulo Malga para Magento 2 está instalado na sua loja.**

O composer é responsável por fazer esta identificação e instalar o módulo correto. Assim que o último comando é executado você já pode acessar o módulo na área admin e partir para a configuração.


### Configure seu módulo para realizar cobranças
A configuração é a última etapa necessária para começar a transacionar com a Malga.

Para iniciar, temos que alterar algumas configurações padrão do Magento 2 para aceitar parâmetros de comprador que serão necessários para criar seus pedidos. 

Para isso é preciso que você acesse o seu painel admin do Magento 2 e selecione a seção Stores.

Dentro da aba Stores, deve-se selecionar a Configuration que está na subcategoria Settings.

![Configurações](https://docs.malga.io/img/magento/config1.png)

O próximo passo  é definir duas configurações de Customer, Em 
Customer > Customer Configuration > Create New Account Options, 

Você deve definir o campo Show VAT Number on Storefront como Yes para que possamos utilizar o CPF/CNPJ do comprador em registro online de boletos.

![Configurações](https://docs.malga.io/img/magento/config2.png)

Além disso, na opção Name and Address Options deve ser utilizado o valor 4 no campo Number of Lines in a Street Address para se adequar a lógica de endereços brasileira.

![Configurações](https://docs.malga.io/img/magento/config3.png)

Para finalizar, você já pode fechar a aba Customer e abrir 
Sales > Payment Methods. 

Ao ir mais para baixo nessa página você irá encontrar a aba
 Other Payment Methods >Malga, onde poderá começar a configuração de seu módulo.

## Configurações

Essas são as configurações responsáveis por identificar sua conta e ativar seus pagamentos.

![Configurações](https://docs.malga.io/img/magento/config4.png)

Explicando os campos da imagem acima:

| Campo  |  Descrição |
| ------------ | ------------ |
| Sandbox Mode Active  | Habilita o uso do Sandbox  |
| ClientId  |  Deve ser informado o ClientId recebido durante a criação da conta na Malga |
|  Api Key |  Deve ser informado o Api Key recebido durante a criação da conta na Malga |
|  Merchant Key | Deve ser informado o MerchantId recebido durante a criação da conta na Malga  |


### Configurações de cliente

Nas Configurações de Cliente é possível personalizar os campos de endereço no checkout.

![Configurações](https://docs.malga.io/img/magento/config5.png)

Nesses campos você deve relacionar os parâmetros de Rua (Street parameter), Número (Number parameter), Bairro (Neighborhood parameter) e Complemento (Complement parameter) com a ordem das linhas de endereço presentes na página de checkout.

### Configurações de pagamento

Em Payment methods devem ser configurados os meios de pagamento Cartão de crédito, Boleto e PIX, assim como algumas opções quanto ao uso de Multimeios e Multicompradores.

### Configurações de Cartão de Crédito

Essas são as configurações que definem como seus pedidos de cartão de crédito serão gerados pelo módulo.

![Configurações](https://docs.malga.io/img/magento/config6.png)

Explicando os campos da imagem acima:

| Campo  |  Descrição |
| ------------ | ------------ |
| Enabled | Ativa ou desativa a opção de oferecer cartão de crédito como método de pagamento. |
| Title  |  Esse campo define o nome que será mostrado na opção de cartão de crédito em sua página de finalização de pedido. |
|  Soft descriptor |  Descrição que aparecerá na fatura do cartão depois do nome de sua empresa. Máximo de 13 caracteres. |
|  Payment action | Define se as cobranças criadas devem ser somente autorizadas ou autorizadas e capturadas.  |
| Sort Order | 	
Define a ordem em que os métodos de pagamento serão mostrados em sua página de finalização de pedido. |

### Configurações de bandeiras

Nessa aba é possível configurar as bandeiras que serão aceitas na página de checkout. Lembrando que essas bandeiras devem estar configuradas em sua conta Malga para que possam ser utilizadas pelo módulo.

![Configurações](https://docs.malga.io/img/magento/config7.png)

### Configurações de parcelas
Configurações que definem as regras de parcelamento utilizadas em seus pedidos de cartão de crédito.

![Configurações](https://docs.malga.io/img/magento/config8.png)

Explicando os campos da imagem acima:

| Campo  |  Descrição |
| ------------ | ------------ |
| Active | Ativa ou desativa o oferecimento de parcelamento nos pedidos por cartão de crédito. |
| Default configuration for all brands | Define se as regras de parcelamento subsequentes serão aplicadas a todos os pedidos (Yes) ou se serão definidas regras para cada bandeira (No). |
| Max number of installments | Este campo define em até quantas vezes será possível parcelar um pedido. |
| Min installment amount | Define o valor mínimo que uma parcela pode assumir. Caso a opção de parcelamento do pedido resulte em um valor de parcela menor do que o Min installment amountt essa opção não irá aparecer para o comprador. |
| Enable Interest | Ativa ou desativa a configuração de juros para esse tipo de parcelamento (definido em Default configuration for all brands). |
| Initial interest rate (%) | O valor de juros passado neste campo define quanto, em porcentagem, iremos acrescentar ao valor do pedido caso seja escolhido a mínima quantidade de parcelas com juros. É possível deixar o campo como 0. |
| Incremental interest rate (%) | O valor de juros passado neste campo define quanto, em porcentagem, iremos acrescentar em cada parcela após a primeira em que há cobrança de juros. É possível deixar o campo como 0. |
| Number of installments without interest | Opção que define em até quantas parcelas daquele pedido não será cobrado juros. Exemplo: Digamos que o campo esteja configurado com 3, ou seja, caso o cliente opte por comprar em 1x, 2x ou 3x, seu pedido não será cobrado juros, mas caso escolha 4x, todas as parcelas terão juros aplicados. |

### Configurações de PIX

Essas são as configurações que definem como seus pedidos por PIX serão gerados pelo módulo.

![Configurações](https://docs.malga.io/img/magento/config8.png)

Explicando os campos da imagem acima:

| Campo  |  Descrição |
| ------------ | ------------ |
| Enabled | Ativa ou desativa a opção de oferecer PIX como método de pagamento. |
| Title | Esse campo define o nome que será mostrado na opção de PIX em sua página de finalização de pedido. |
| Sort Order | Define a ordem em que os métodos de pagamento serão mostrados em sua página de finalização de pedido. |

### Configurações de Boleto

Essas são as configurações que definem como seus boletos serão gerados pelo módulo.

![Configurações](https://docs.malga.io/img/magento/config9.png)

Explicando os campos da imagem acima:

| Campo  |  Descrição |
| ------------ | ------------ |
| Enabled | Ativa ou desativa a opção de oferecer Boleto como método de pagamento. |
| Title | Esse campo define o nome que será mostrado na opção de Boleto em sua página de finalização de pedido. |
| Sort Order | Define a ordem em que os métodos de pagamento serão mostrados em sua página de finalização de pedido. |



Caso ainda não tenha as credenciais de acesso, entre em contato conosco em [suporte@malga.io](mailto:suporte@malga.io) para solicitar.