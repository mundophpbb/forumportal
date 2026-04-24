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
    'FORUMPORTAL_TOP_CONTRIBUTORS'        => 'Principais colaboradores',
    'FORUMPORTAL_PERIOD_ALL_TIME'        => 'histórico completo',
    'FORUMPORTAL_PERIOD_LAST_DAY'        => 'último dia',
    'FORUMPORTAL_PERIOD_LAST_DAYS'       => 'últimos %d dias',
    'FORUMPORTAL_STATS_CONTRIBUTIONS'     => 'Contribuições',
    'FORUMPORTAL_FORUM_GATEWAY'           => 'Continuar no fórum',
    'FORUMPORTAL_FORUM_GATEWAY_EXPLAIN'   => 'Leia o destaque aqui e siga para o fórum para ver o tópico completo e a discussão.',
    'FORUMPORTAL_CUSTOM_BLOCK'            => 'Bloco personalizado',

    'ACL_CAT_FORUMPORTAL'                => 'Forum Portal',
    'ACL_F_FORUMPORTAL_PUBLISH'          => 'Pode publicar tópicos no portal e editar os dados do portal nas Opções da primeira mensagem',
    'ACL_M_FORUMPORTAL_EDIT'             => 'Pode editar a publicação no portal nas Opções da primeira mensagem',
    'ACL_M_FORUMPORTAL_FEATURE'          => 'Pode destacar ou remover destaque de tópicos no portal',

    'FORUMPORTAL_ENABLE_EXPLAIN_AUTO' => 'Os tópicos dos fóruns de origem selecionados podem ser incluídos automaticamente. Desmarque esta opção para manter o tópico fora do portal.',
    'FORUMPORTAL_POLLS'                   => 'Enquetes',
    'FORUMPORTAL_POLL_VOTE'               => 'Votar',
    'FORUMPORTAL_POLL_VIEW_TOPIC'         => 'Ver enquete no tópico',
    'FORUMPORTAL_POLL_TOTAL_VOTES'        => 'Total de votos',
    'FORUMPORTAL_POLL_SELECT_ONE'         => 'Selecione uma opção.',
    'FORUMPORTAL_POLL_MAX_OPTIONS'        => 'Máximo de opções',
    'FORUMPORTAL_POLL_ALREADY_VOTED'      => 'Você já votou nesta enquete.',
    'FORUMPORTAL_POLL_GUESTS_DISABLED'   => 'Convidados devem votar no tópico.',
    'FORUMPORTAL_POLL_CAN_CHANGE'         => 'Você pode alterar seu voto.',
    'FORUMPORTAL_POLL_CLOSED'             => 'Enquete encerrada.',
    'FORUMPORTAL_POLL_VOTED'              => 'Seu voto foi registrado.',
    'FORUMPORTAL_POLL_CHANGED'            => 'Seu voto foi atualizado.',
    'FORUMPORTAL_POLL_ERROR_SELECT'       => 'Selecione uma opção válida para votar.',
    'FORUMPORTAL_POLL_ERROR_TOO_MANY'     => 'Você selecionou opções demais. O máximo permitido é %1$d.',
    'FORUMPORTAL_POLL_ERROR_CLOSED'       => 'Esta enquete já foi encerrada.',
    'FORUMPORTAL_POLL_ERROR_ALREADY_VOTED'=> 'Você já votou nesta enquete e ela não permite alteração de voto.',
    'FORUMPORTAL_POLL_ERROR_GUESTS_DISABLED'=> 'O voto de convidados pelo portal está desativado.',
    'FORUMPORTAL_POLL_ERROR_NO_PERMISSION'=> 'Você não tem permissão para votar nesta enquete.',
    'FORUMPORTAL_POLL_ERROR_UNAVAILABLE'  => 'A enquete não está disponível no momento.',

));
