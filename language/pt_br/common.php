<?php
/**
 * Forum Portal language file [pt_br].
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
    'FORUMPORTAL_DEFAULT_PAGE_TITLE'      => 'Portal',
    'FORUMPORTAL_DEFAULT_NAV_TITLE'       => 'Portal',
    'FORUMPORTAL_DISABLED'                => 'O portal está desativado no momento.',
    'FORUMPORTAL_FORUM_UNAVAILABLE'       => 'Os fóruns de origem do portal estão indisponíveis ou você não tem permissão para lê-los.',
    'FORUMPORTAL_NAV'                     => 'Portal',
    'FORUMPORTAL_BACK_TO_FORUM'           => 'Ir para o índice do fórum',
    'FORUMPORTAL_GO_TO_FORUM'             => 'Abrir fórum',
    'FORUMPORTAL_FORUM_INDEX'             => 'Fórum',
    'FORUMPORTAL_READ_MORE'               => 'Leia mais',
    'FORUMPORTAL_POST_OPTIONS'            => 'Configurações do portal',
    'FORUMPORTAL_ENABLE_LABEL'            => 'Mostrar este tópico no portal',
    'FORUMPORTAL_ENABLE_EXPLAIN'          => 'Disponível apenas para a primeira mensagem do tópico dentro de um dos fóruns de origem selecionados.',
    'FORUMPORTAL_IMAGE_LABEL'             => 'URL externa da imagem',
    'FORUMPORTAL_IMAGE_EXPLAIN'           => 'Opcional. Se ficar em branco, o portal tentará usar a primeira imagem anexada ao tópico, depois a primeira imagem relevante da mensagem e, por fim, a imagem padrão definida no ACP.',
    'FORUMPORTAL_NO_IMAGE_LABEL'          => 'Não usar imagem',
    'FORUMPORTAL_NO_IMAGE_EXPLAIN'        => 'Se marcado, o portal não usará imagem para este tópico, mesmo que exista URL manual, anexo, ícone ou imagem no conteúdo.',
    'FORUMPORTAL_ORDER_LABEL'             => 'Ordem no portal',
    'FORUMPORTAL_ORDER_EXPLAIN'           => 'Opcional. Use 0 para ordem automática. Valores menores aparecem antes no portal.',
    'FORUMPORTAL_EXCERPT_LABEL'           => 'Resumo personalizado',
    'FORUMPORTAL_EXCERPT_EXPLAIN'         => 'Opcional. Deixe em branco para gerar o resumo automaticamente a partir da primeira mensagem.',
    'FORUMPORTAL_FEATURED_LABEL'        => 'Destacar no portal',
    'FORUMPORTAL_FEATURED_EXPLAIN'      => 'Opcional. Destaca este tópico no topo do portal antes dos demais.',
    'FORUMPORTAL_FIXED_HEADLINE_LABEL'  => 'Usar como manchete principal',
    'FORUMPORTAL_FIXED_HEADLINE_EXPLAIN' => 'Opcional. Define este tópico como a manchete principal do portal. Se você desmarcar este tópico e ele for a manchete fixa atual, o portal volta ao comportamento automático.',
    'FORUMPORTAL_EMPTY'                   => 'Ainda não há tópicos publicados no portal.',
    'FORUMPORTAL_STATS_REPLIES'           => 'Respostas',
    'FORUMPORTAL_STATS_VIEWS'             => 'Visualizações',
    'FORUMPORTAL_STATS_COMMENTS'          => 'Comentários',
    'FORUMPORTAL_FEATURED'                => 'Destaque',
    'FORUMPORTAL_NO_IMAGE'                => 'Sem imagem',
    'FORUMPORTAL_EDITORIAL_HIGHLIGHT'     => 'Destaque editorial',
    'FORUMPORTAL_LATEST_STORIES'          => 'Últimas publicações',
    'FORUMPORTAL_HEADLINES'               => 'Últimas manchetes',
    'FORUMPORTAL_NOTICES'                 => 'Comunicados e fixos',
    'FORUMPORTAL_NOTICE_LABEL'            => 'Aviso',
    'FORUMPORTAL_NOTICE_ANNOUNCEMENT'     => 'Comunicado',
    'FORUMPORTAL_NOTICE_STICKY'           => 'Fixo',
    'FORUMPORTAL_NOTICE_GLOBAL'           => 'Global',
    'FORUMPORTAL_MOST_READ'               => 'Mais lidas',
    'FORUMPORTAL_MOST_COMMENTED'          => 'Mais comentadas',
    'FORUMPORTAL_FORUM_GATEWAY'           => 'Continuar no fórum',
    'FORUMPORTAL_FORUM_GATEWAY_EXPLAIN'   => 'Leia o destaque aqui e siga para o fórum para ver o tópico completo e a discussão.',
    'FORUMPORTAL_CUSTOM_BLOCK'            => 'Bloco personalizado',

    'ACL_CAT_FORUMPORTAL'                => 'Forum Portal',
    'ACL_F_FORUMPORTAL_PUBLISH'          => 'Pode publicar tópicos no portal e editar os dados do portal nas Opções da primeira mensagem',
    'ACL_M_FORUMPORTAL_EDIT'             => 'Pode editar a publicação no portal nas Opções da primeira mensagem',
    'ACL_M_FORUMPORTAL_FEATURE'          => 'Pode destacar ou remover destaque de tópicos no portal',
));
