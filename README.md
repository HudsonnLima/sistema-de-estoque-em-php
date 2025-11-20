# 📦 Sistema de Gerenciamento de Estoque e Compras

![PHP](https://img.shields.io/badge/PHP-8.4%20%7C%20POO-777BB4?logo=php)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&logoColor=black)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)
![Status](https://img.shields.io/badge/Status-Em%20Desenvolvimento-yellow)
![License](https://img.shields.io/badge/License-MIT-green)

------------------------------------------------------------------------

## 📚 Índice

1.  [Login](#-login)\
2.  [Home](#-home)\
3.  [Fornecedores](#-fornecedores)\
4.  [Compras](#-compras)\
5.  [Produtos](#-produtos)\
6.  [Entrada de Produtos](#-entrada-de-produtos)\
7.  [Saída de Produtos](#-saída-de-produtos)\
8.  [Usuários / Funcionários](#-usuários--funcionários)\
9.  [Relatórios](#-relatórios)\
10. [Configurações](#-configurações)\
11. [Sair](#-sair)

------------------------------------------------------------------------

## 🔐 Login

-   Acesso através de **email e senha**
-   Botão para **salvar email**
-   Link **"Esqueci minha senha"**
-   Alerta se o email informado não existir
-   Validação de senha e email

## 🏠 Home

Dashboard com: - Últimas compras - Fornecedores mais comprados -
Entradas x saídas dos últimos 6 meses - Funcionários com mais retiradas

## 🧾 Fornecedores

### ✔ Cadastro

-   Validação de CNPJ e Inscrição Estadual (front e back-end)
-   Busca de endereço via API dos Correios
-   Campos opcionais: telefone, celular, email, CEP

### 🔍 Gerenciamento

-   Busca por CNPJ ou Razão Social
-   Editar fornecedor
-   Excluir (somente sem compras vinculadas)
-   Últimas 10 compras daquele fornecedor

## 🛒 Compras

### ✔ Cadastro

-   Fornecedor, condição de pagamento, previsão e data da compra
-   Previsão não pode ser anterior à data da compra

### 🧩 Produtos da compra

-   Autocomplete de produtos
-   Quantidade
-   Preço unitário ↔ total automático
-   Estoque atual exibido
-   Botão **+ Produto**

### ✉ Notificações

-   Email automático ao cadastrar compra
-   Email secundário opcional

### 🔄 Edição / Exclusão

-   Editar cabeçalho ou itens
-   Excluir compra inteira

### 📜 Histórico com alertas

-   Verde = dentro do prazo
-   Azul = entrega hoje
-   Vermelho = atrasada

## 📦 Produtos

### ✔ Cadastro

-   Nome popular, descrição, código interno
-   Unidade (cx, L, kg, etc.)
-   Prioridade (para alertas)
-   Estoque mínimo e máximo

### 🔍 Listagem

-   Quantidade
-   Estoque mínimo/máximo
-   Editar, excluir (ou inativar)
-   Últimas 10 compras do produto

## 📥 Entrada de Produtos

-   Exibe compras realizadas
-   Conferência de fornecedor, forma de pagamento, datas, NFe
-   Produtos com quantidade recebida, preço e estoque atual

## 📤 Saída de Produtos

-   Funcionário, data, autocomplete de produto
-   Quantidade e estoque atual
-   Alerta se (estoque - quantidade) \< mínimo
-   Alerta por email conforme prioridade
-   Botão para adicionar mais itens

## 👤 Usuários / Funcionários

-   Cadastro com permissões
-   Listagem com edição
-   "Meu Perfil" para editar dados pessoais

## 📑 Relatórios

Filtros: - Entrada/Saída - Produto - Funcionário - Intervalo de datas

## ⚙ Configurações

-   Nome e endereço do sistema
-   SMTP: email, senha, porta e servidor
-   Emails de alerta (principal + cópias)

## 🚪 Sair

-   Logout do sistema
