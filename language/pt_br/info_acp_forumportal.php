<?php
/**
 * Forum Portal ACP language [pt_br].
 */

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

$lang = array_merge($lang, array(
    'ACP_FORUMPORTAL'                           => 'Forum Portal',
    'ACP_FORUMPORTAL_SETTINGS'                  => 'Configurações',
    'ACP_FORUMPORTAL_SETTINGS_EXPLAIN'          => 'Configure uma página inicial estilo portal alimentada por tópicos de um ou mais fóruns.',
    'ACP_FORUMPORTAL_GENERAL'                   => 'Configurações gerais',
    'ACP_FORUMPORTAL_DISPLAY'                   => 'Configurações de exibição',
    'ACP_FORUMPORTAL_DISPLAY_EXPLAIN'           => 'Ajuste títulos, quantidade de itens, imagem padrão e o formato geral da home.',
    'ACP_FORUMPORTAL_EDITORIAL'                 => 'Controles editoriais',
    'ACP_FORUMPORTAL_EDITORIAL_EXPLAIN'         => 'Escolha quais blocos e metadados aparecem no portal. Blocos vazios são ocultados automaticamente.',
    'ACP_FORUMPORTAL_CUSTOM_HTML'               => 'Bloco HTML personalizado',
    'ACP_FORUMPORTAL_CUSTOM_HTML_SECTION_EXPLAIN' => 'Use este espaço para avisos, texto institucional, banner simples ou conteúdo complementar.',
    'ACP_FORUMPORTAL_SAVED'                     => 'As configurações do Forum Portal foram salvas com sucesso.',
    'ACP_FORUMPORTAL_ENABLED'                   => 'Ativar portal',
    'ACP_FORUMPORTAL_ENABLED_EXPLAIN'           => 'Liga ou desliga a página do portal.',
    'ACP_FORUMPORTAL_HOME_ENABLED'              => 'Usar o portal como página inicial',
    'ACP_FORUMPORTAL_HOME_ENABLED_EXPLAIN'      => 'Quando ativado, acessos ao index.php são redirecionados para /portal.',
    'ACP_FORUMPORTAL_SOURCE_FORUM'              => 'Fóruns de origem',
    'ACP_FORUMPORTAL_SOURCE_FORUM_EXPLAIN'      => 'Somente tópicos dos fóruns selecionados podem ser publicados no portal. Segure Ctrl para selecionar vários itens.',
    'ACP_FORUMPORTAL_PAGE_TITLE'                => 'Título da página do portal',
    'ACP_FORUMPORTAL_NAV_TITLE'                 => 'Rótulo da navegação',
    'ACP_FORUMPORTAL_TOPICS_PER_PAGE'           => 'Quantidade de cards',
    'ACP_FORUMPORTAL_EXCERPT_LIMIT'             => 'Limite de caracteres do resumo',
    'ACP_FORUMPORTAL_DEFAULT_IMAGE'             => 'URL da imagem padrão',
    'ACP_FORUMPORTAL_FIXED_TOPIC_ID'           => 'ID da manchete fixa (manual)',
    'ACP_FORUMPORTAL_FIXED_TOPIC_ID_EXPLAIN'   => 'Opcional. Informe manualmente o ID de um tópico já publicado no portal para mantê-lo como manchete principal. Se preferir, você também pode marcar isso direto nas Opções da primeira mensagem do tópico.',
    'ACP_FORUMPORTAL_DEFAULT_IMAGE_EXPLAIN'     => 'Imagem de fallback opcional quando um tópico não tiver imagem personalizada no portal.',
    'ACP_FORUMPORTAL_HTML_POSITION'             => 'Posição do HTML personalizado',
    'ACP_FORUMPORTAL_HTML_TOP'                  => 'Topo do portal',
    'ACP_FORUMPORTAL_HTML_BOTTOM'               => 'Rodapé do portal',
    'ACP_FORUMPORTAL_CUSTOM_HTML_TITLE'         => 'Título do bloco HTML',
    'ACP_FORUMPORTAL_CUSTOM_HTML_TITLE_EXPLAIN' => 'Opcional. Exibe um título acima do bloco HTML personalizado.',
    'ACP_FORUMPORTAL_CUSTOM_HTML_EXPLAIN'       => 'Bloco HTML opcional exibido acima ou abaixo da lista de tópicos do portal.',
    'ACP_FORUMPORTAL_DATE_FORMAT'                    => 'Formato de data e hora no portal',
    'ACP_FORUMPORTAL_DATE_FORMAT_EXPLAIN'            => 'Use as mesmas opções de formato do phpBB. Em branco, o portal segue o formato padrão do phpBB/usuário.',
    'ACP_FORUMPORTAL_DATE_FORMAT_DEFAULT'            => 'Usar padrão do phpBB / do usuário',
    'ACP_FORUMPORTAL_DATE_FORMAT_CUSTOM'             => 'formato personalizado salvo anteriormente',
    'ACP_FORUMPORTAL_HEADLINES_LIMIT'                => 'Quantidade em Últimas manchetes',
    'ACP_FORUMPORTAL_HEADLINES_LIMIT_EXPLAIN'        => 'Define quantos itens aparecem no bloco Últimas manchetes da coluna da direita.',
    'ACP_FORUMPORTAL_MOST_READ_LIMIT'                => 'Quantidade em Mais lidas',
    'ACP_FORUMPORTAL_MOST_READ_LIMIT_EXPLAIN'        => 'Define quantos itens aparecem no bloco Mais lidas.',
    'ACP_FORUMPORTAL_MOST_COMMENTED_LIMIT'           => 'Quantidade em Mais comentadas',
    'ACP_FORUMPORTAL_MOST_COMMENTED_LIMIT_EXPLAIN'   => 'Define quantos itens aparecem no bloco Mais comentadas.',
    'ACP_FORUMPORTAL_NOTICES_LIMIT'                  => 'Quantidade em Comunicados e fixos',
    'ACP_FORUMPORTAL_NOTICES_LIMIT_EXPLAIN'          => 'Define quantos itens aparecem no bloco Comunicados e fixos.',

    'ACP_FORUMPORTAL_SHOW_AUTHOR'               => 'Mostrar autor',
    'ACP_FORUMPORTAL_SHOW_AUTHOR_EXPLAIN'       => 'Exibe ou oculta o nome do autor nos destaques e cards do portal.',
    'ACP_FORUMPORTAL_SHOW_DATE'                 => 'Mostrar data',
    'ACP_FORUMPORTAL_SHOW_DATE_EXPLAIN'         => 'Exibe ou oculta a data/hora nos blocos do portal.',
    'ACP_FORUMPORTAL_SHOW_VIEWS'                => 'Mostrar visualizações',
    'ACP_FORUMPORTAL_SHOW_VIEWS_EXPLAIN'        => 'Exibe ou oculta a contagem de visualizações na manchete e nos cards.',
    'ACP_FORUMPORTAL_SHOW_HEADLINES'            => 'Mostrar Últimas manchetes',
    'ACP_FORUMPORTAL_SHOW_HEADLINES_EXPLAIN'    => 'Exibe ou oculta o bloco Últimas manchetes na coluna da direita.',
    'ACP_FORUMPORTAL_SHOW_MOST_READ'            => 'Mostrar Mais lidas',
    'ACP_FORUMPORTAL_SHOW_MOST_READ_EXPLAIN'    => 'Exibe ou oculta o bloco Mais lidas.',
    'ACP_FORUMPORTAL_SHOW_MOST_COMMENTED'       => 'Mostrar Mais comentadas',
    'ACP_FORUMPORTAL_SHOW_MOST_COMMENTED_EXPLAIN'=> 'Exibe ou oculta o bloco Mais comentadas.',
    'ACP_FORUMPORTAL_SHOW_NOTICES'              => 'Mostrar Comunicados e fixos',
    'ACP_FORUMPORTAL_SHOW_NOTICES_EXPLAIN'      => 'Exibe ou oculta o bloco com tópicos fixos e comunicados dos fóruns de origem.',
    'ACP_FORUMPORTAL_SHOW_HERO_EXCERPT'         => 'Mostrar resumo na manchete principal',
    'ACP_FORUMPORTAL_SHOW_HERO_EXCERPT_EXPLAIN' => 'Quando desativado, a manchete principal mostra apenas título, meta e link.',

));
