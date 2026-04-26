# Forum Portal

**Forum Portal** é uma extensão para phpBB 3.3.x que transforma um fórum selecionado em uma página estilo portal/revista, com notícias em destaque, blocos laterais, enquetes, colaboradores, opções visuais e integração com o layout do fórum.

A proposta é permitir que uma comunidade phpBB tenha uma página inicial mais editorial, útil para destacar conteúdos importantes sem alterar o core do phpBB.

## Principais recursos

### Portal de notícias

- Rota pública `/portal`.
- Opção para ativar/desativar o portal pelo ACP.
- Opção para usar o portal como página inicial.
- Opção SEO para aplicar `noindex,follow` nas páginas paginadas do portal.
- Campo ACP para definir uma meta description personalizada da página do portal.
- Correção v1.2.4: o bloco de enquetes não é mais ocultado pela proteção contra tópicos duplicados.
- Seleção do fórum de origem do conteúdo.
- Publicações em formato editorial com título, autor, data, resumo, imagem e botão **Leia mais**.
- Opção no formulário de postagem para publicar ou não o tópico no portal.
- Campo para imagem personalizada da notícia.
- Campo para resumo personalizado.
- Integração por hook em `portal_body.html` para permitir blocos extras de outras extensões.

### Layout e visual

- Modo visual editorial próprio do portal.
- Modo visual integrado ao ProSilver.
- Opção de tipografia:
  - usar visual próprio do portal com `Georgia, "Times New Roman", serif`;
  - ou herdar a fonte do fórum/tema ativo.
- Cabeçalho personalizado opcional:
  - manter cabeçalho padrão do phpBB;
  - exibir banner próprio abaixo do cabeçalho;
  - usar banner próprio no lugar da área visual do cabeçalho padrão.
- Campos para imagem, título, subtítulo e altura do cabeçalho personalizado.
- Compatibilidade visual com tema escuro:
  - modo automático;
  - modo forçado para corrigir contraste em temas escuros.
- Layout das publicações configurável:
  - lista editorial;
  - grade com 2 cards.
- Ajustes responsivos para tablet e celular.

### Blocos laterais

- Comunicados e tópicos fixos.
- Últimas manchetes.
- Principais colaboradores.
- Enquetes.
- Mais lidas.
- Mais comentadas.
- HTML personalizado lateral.
- Ordem dos blocos configurável pelo ACP.
- Etiquetas de período nos blocos com contagem por dias ou histórico completo.

### Mais lidas, mais comentadas e colaboradores

- Bloco de tópicos mais lidos.
- Bloco de tópicos mais comentados.
- Filtro por período em dias.
- Valor `0` para histórico completo.
- Bloco de principais colaboradores com avatar circular.
- Limite configurável de usuários exibidos.

### Enquetes no portal

- Bloco de enquetes recentes.
- Opção para fixar uma enquete específica por ID do tópico.
- Opção para permitir voto diretamente no portal.
- Respeito à permissão nativa `f_vote` do phpBB.
- Proteção contra voto duplicado quando a enquete não permite alteração.
- Suporte a alteração de voto quando a enquete original permite.
- Opção para permitir ou bloquear voto de convidados pelo portal.
- Bloqueio de voto em tópicos movidos, tópicos bloqueados, fóruns bloqueados ou usuários sem permissão.
- Exibição de resultados quando o usuário já votou, quando a enquete está fechada ou quando o voto pelo portal está desativado.

### ACP

- Painel de administração organizado em abas internas:
  - Geral;
  - Conteúdo;
  - Visual;
  - Blocos laterais;
  - Enquetes;
  - HTML personalizado.
- Organização visual sem criar novos módulos no menu esquerdo do ACP.
- CSS próprio do ACP separado em `adm/style/acp_forumportal.css`.

### CSS e manutenção

- CSS dividido por recurso para facilitar manutenção.
- Arquivos separados para tipografia, cabeçalho, enquetes, colaboradores, responsividade, compatibilidade visual e modos escuros.
- Carregamento dos estilos via eventos do phpBB e `INCLUDECSS` da extensão.
- Correção do botão **Votar** para não puxar estilo escuro em tema claro.
- Padronização de imagens e avatares.

## Instalação

1. Copie a pasta da extensão para:

   ```text
   ext/mundophpbb/forumportal/
   ```

2. No ACP do phpBB, acesse:

   ```text
   Personalizar > Gerenciar extensões
   ```

3. Ative a extensão **Forum Portal**.

4. Limpe o cache do phpBB.

5. Configure o portal no ACP da extensão.

## Atualização

Ao atualizar a extensão:

1. Desative a extensão no ACP, se necessário.
2. Substitua os arquivos antigos pelos novos.
3. Reative ou atualize a extensão.
4. Limpe o cache do phpBB.
5. Faça um refresh forçado no navegador para evitar CSS antigo em cache.

## Compatibilidade

- phpBB 3.3.x.
- PHP 7.1 ou superior, conforme `composer.json` atual.
- Desenvolvida sem alteração no core do phpBB.

## Changelog

### Pacote atual

- Reorganizado o ACP em abas internas para facilitar a configuração.
- Adicionado modo visual integrado ao ProSilver.
- Mantido modo visual editorial próprio do portal.
- Restaurada a tipografia editorial com `Georgia, "Times New Roman", serif` quando o visual próprio do portal está ativo.
- Adicionada opção para herdar a fonte do fórum.
- Adicionado cabeçalho personalizado opcional com imagem, título, subtítulo e altura configurável.
- Adicionada compatibilidade com temas escuros, incluindo modo forçado.
- Adicionado bloco de principais colaboradores por período.
- Adicionado bloco de enquetes com suporte a voto no portal.
- Revisadas permissões das enquetes para respeitar usuários sem permissão, convidados, tópicos bloqueados, fóruns bloqueados e tópicos movidos.
- Adicionado controle de período em dias para mais lidas, mais comentadas, colaboradores e enquetes.
- Adicionadas etiquetas de período nos blocos.
- Adicionada opção de layout das publicações: lista editorial ou grade com 2 cards.
- Padronizadas imagens de notícias, cabeçalho e avatares.
- Corrigido espaçamento do link de retorno ao fórum no canto superior direito.
- Removido o efeito visual de cards “pulando” ao passar o mouse.
- Ajustado hover dos títulos e links para um comportamento mais estável.
- Corrigido botão **Votar** para usar visual correto em tema claro e escuro.
- CSS dividido por recurso para melhorar manutenção.
- Corrigido carregamento dos CSS separados usando `INCLUDECSS` da extensão.
- Removido carregamento duplicado de CSS em template do portal.
- Melhorados ajustes responsivos para tablet e celular.
- Adicionadas traduções em `pt_br` e `en` para as novas opções.

### Base inicial

- Criada rota pública `/portal`.
- Criada configuração inicial no ACP.
- Criado sistema para publicar tópicos no portal a partir do formulário de postagem.
- Criados campos de imagem e resumo personalizados.
- Criado bloco HTML personalizado.
- Adicionados idiomas `pt_br` e `en`.
- Adicionado hook `mundophpbb_forumportal_after_content` em `portal_body.html` para integração com blocos extras.

### Ajustes após novo feedback

- Adicionado modo de ícone das publicações: megafone padrão, ícone do tópico do phpBB ou nenhum ícone.
- Quando o modo “ícone do tópico” estiver ativo, tópicos sem ícone não exibem megafone.
- O link **Abrir fórum** agora tem aparência de botão.
- Adicionadas cores configuráveis para os botões de navegação **Portal** e **Fórum**.
- Adicionado botão **Fórum** na navegação quando o portal estiver configurado como página inicial.
- A barra superior do portal passa a usar a cor configurada do Portal.
- Barras de resultado das enquetes passam a usar o vermelho padrão do ProSilver.
- Melhorado o espaçamento de etiquetas, metadados e valores destacados na lateral.
- Removida a repetição de “Últimas notícias” dentro de cada card de publicação; o título fica apenas na seção.

## Observações

Esta extensão foi construída para evoluir sem alterar o core do phpBB. Algumas opções visuais são intencionalmente configuráveis para permitir dois caminhos: um portal com identidade editorial própria ou um portal mais integrado ao ProSilver.

- Navigation color correction: Portal and Board index now follow the active phpBB style; only the Open forum button has its own color setting.

### v1.2.5 - Layout / Portal CSS polish

- Alinha melhor a barra do Portal com a largura útil do conteúdo.
- Reduz espaçamentos laterais excessivos em estilos phpBB mais largos.
- Mantém o portal responsivo sem forçar uma largura fixa interna.

### v1.2.7 - Release cleanup / JS externo

- Removido JavaScript inline de `overall_header_head_append.html`.
- Adicionado `forumportal_home_breadcrumb.js` para manter o ajuste do breadcrumb quando o portal é usado como página inicial.
- Mantido o retorno seguro ao índice real do fórum com `forumportal_bypass=1`.
- Pacote preparado para nova rodada de feedback público.

## Portal URL and `app.php`

Forum Portal uses phpBB's routing system and does not force clean URLs at extension level. When phpBB URL rewriting is enabled in the ACP and the web server is configured correctly, the portal route may be displayed without `app.php`. Otherwise, `app.php/portal` is the safe and expected phpBB route.

### Version 1.2.9

- Month abbreviations used by the portal date badge now come from language files instead of being hardcoded in PHP.
- This allows translators to localize month names properly for additional languages.


### v1.2.10 - Final visual and SEO polish

- Metadados da sidebar (datas, comentários, visualizações e períodos) foram suavizados para não competir visualmente com os títulos.
- O botão/link do Portal na navegação foi ajustado para ficar mais coerente com o botão azul "Abrir fórum".
- Adicionado campo no ACP para meta description personalizada do portal.
- Quando preenchida, a página do portal exibe `<meta name="description">` com o texto configurado.
- A extensão continua respeitando o sistema de rotas do phpBB e não força URLs sem `app.php`.
