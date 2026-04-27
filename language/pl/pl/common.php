<?php
/**
 * Forum Portal language file [pl].
 * Polskie tłumaczenie: Tomasz Hetman - ToTemat YT.
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
    'FORUMPORTAL_DISABLED'                => 'Portal jest obecnie wyłączony.',
    'FORUMPORTAL_FORUM_UNAVAILABLE'       => 'Fora źródłowe portalu są niedostępne lub nie masz uprawnień do ich czytania.',
    'FORUMPORTAL_NAV'                     => 'Portal',
    'FORUMPORTAL_BACK_TO_FORUM'           => 'Przejdź do indeksu forum',
    'FORUMPORTAL_GO_TO_FORUM'             => 'Otwórz forum',
    'FORUMPORTAL_FORUM_INDEX'             => 'Forum',
    'FORUMPORTAL_READ_MORE'               => 'Czytaj więcej',
    'FORUMPORTAL_POST_OPTIONS'            => 'Ustawienia portalu',
    'FORUMPORTAL_ENABLE_LABEL'            => 'Pokaż ten temat w portalu',
    'FORUMPORTAL_ENABLE_EXPLAIN'          => 'Dostępne tylko dla pierwszego posta w temacie wewnątrz jednego z wybranych forów źródłowych.',
    'FORUMPORTAL_IMAGE_LABEL'             => 'Zewnętrzny adres URL obrazu',
    'FORUMPORTAL_IMAGE_EXPLAIN'           => 'Opcjonalne. Jeśli pozostanie puste, portal spróbuje użyć pierwszego załączonego obrazu, następnie pierwszego odpowiedniego obrazu znalezionego w treści posta, a na końcu domyślnego obrazu skonfigurowanego w ACP.',
    'FORUMPORTAL_NO_IMAGE_LABEL'          => 'Nie używaj obrazu',
    'FORUMPORTAL_NO_IMAGE_EXPLAIN'        => 'Jeśli zaznaczone, portal nie użyje obrazu dla tego tematu, nawet jeśli istnieje ręczny adres URL, załącznik, ikona lub obraz w treści.',
    'FORUMPORTAL_ORDER_LABEL'             => 'Kolejność w portalu',
    'FORUMPORTAL_ORDER_EXPLAIN'           => 'Opcjonalne. Użyj 0 dla automatycznej kolejności. Niższe wartości pojawiają się wcześniej w portalu.',
    'FORUMPORTAL_EXCERPT_LABEL'           => 'Własny wypis (zajawka)',
    'FORUMPORTAL_EXCERPT_EXPLAIN'         => 'Opcjonalne. Pozostaw puste, aby automatycznie wygenerować podsumowanie z pierwszego posta.',
    'FORUMPORTAL_FEATURED_LABEL'           => 'Wyróżnij w portalu',
    'FORUMPORTAL_FEATURED_EXPLAIN'         => 'Opcjonalne. Wyświetla ten temat przed innymi w portalu.',
    'FORUMPORTAL_FIXED_HEADLINE_LABEL'     => 'Użyj jako główny nagłówek',
    'FORUMPORTAL_FIXED_HEADLINE_EXPLAIN'   => 'Opcjonalne. Ustawia ten temat jako główny nagłówek portalu. Jeśli odznaczysz tę opcję, a temat jest obecnym stałym nagłówkiem, portal powróci do zachowania automatycznego.',
    'FORUMPORTAL_EMPTY'                   => 'W portalu nie ma jeszcze żadnych opublikowanych tematów.',
    'FORUMPORTAL_STATS_REPLIES'           => 'Odpowiedzi',
    'FORUMPORTAL_STATS_VIEWS'             => 'Odsłony',
    'FORUMPORTAL_STATS_COMMENTS'          => 'Komentarze',
    'FORUMPORTAL_FEATURED'                => 'Wyróżnione',
    'FORUMPORTAL_NO_IMAGE'                => 'Brak obrazu',
    'FORUMPORTAL_EDITORIAL_HIGHLIGHT'     => 'Wyróżnienie redakcyjne',
    'FORUMPORTAL_LATEST_STORIES'          => 'Najnowsze artykuły',
    'FORUMPORTAL_HEADLINES'               => 'Najnowsze nagłówki',
    'FORUMPORTAL_NOTICES'                 => 'Ogłoszenia i przyklejone',
    'FORUMPORTAL_NOTICE_LABEL'            => 'Ogłoszenie',
    'FORUMPORTAL_NOTICE_ANNOUNCEMENT'     => 'Ogłoszenie',
    'FORUMPORTAL_NOTICE_STICKY'           => 'Przyklejony',
    'FORUMPORTAL_NOTICE_GLOBAL'           => 'Globalny',
    'FORUMPORTAL_MOST_READ'               => 'Najczęściej czytane',
    'FORUMPORTAL_MOST_COMMENTED'          => 'Najczęściej komentowane',
    'FORUMPORTAL_TOP_CONTRIBUTORS'        => 'Top autorzy',
    'FORUMPORTAL_PERIOD_ALL_TIME'        => 'cały czas',
    'FORUMPORTAL_PERIOD_LAST_DAY'        => 'ostatni dzień',
    'FORUMPORTAL_PERIOD_LAST_DAYS'       => 'ostatnie %d dni',
    'FORUMPORTAL_STATS_CONTRIBUTIONS'     => 'Wkład',
    'FORUMPORTAL_FORUM_GATEWAY'           => 'Kontynuuj na forum',
    'FORUMPORTAL_FORUM_GATEWAY_EXPLAIN'   => 'Przeczytaj tutaj wyróżnienie i otwórz forum, aby zobaczyć pełny temat oraz dyskusję.',
    'FORUMPORTAL_CUSTOM_BLOCK'            => 'Własny blok',
    'FORUMPORTAL_CUSTOM_LINKS'           => 'Własne linki',

    'FORUMPORTAL_MONTH_SHORT_1'          => 'Sty',
    'FORUMPORTAL_MONTH_SHORT_2'          => 'Lut',
    'FORUMPORTAL_MONTH_SHORT_3'          => 'Mar',
    'FORUMPORTAL_MONTH_SHORT_4'          => 'Kwi',
    'FORUMPORTAL_MONTH_SHORT_5'          => 'Maj',
    'FORUMPORTAL_MONTH_SHORT_6'          => 'Cze',
    'FORUMPORTAL_MONTH_SHORT_7'          => 'Lip',
    'FORUMPORTAL_MONTH_SHORT_8'          => 'Sie',
    'FORUMPORTAL_MONTH_SHORT_9'          => 'Wrz',
    'FORUMPORTAL_MONTH_SHORT_10'         => 'Paź',
    'FORUMPORTAL_MONTH_SHORT_11'         => 'Lis',
    'FORUMPORTAL_MONTH_SHORT_12'         => 'Gru',

    'ACL_CAT_FORUMPORTAL'                => 'Portal Forum',
    'ACL_F_FORUMPORTAL_PUBLISH'          => 'Może publikować tematy w portalu i edytować dane portalu w opcjach pierwszego posta',
    'ACL_M_FORUMPORTAL_EDIT'             => 'Może edytować dane publikacji portalu w opcjach pierwszego posta',
    'ACL_M_FORUMPORTAL_FEATURE'          => 'Może wyróżniać lub usuwać wyróżnienia tematów w portalu',

    'FORUMPORTAL_ENABLE_EXPLAIN_AUTO' => 'Tematy z wybranych forów źródłowych mogą być dołączane automatycznie. Odznacz tę opcję, aby zatrzymać ten temat poza portalem.',
    'FORUMPORTAL_POLLS'                   => 'Ankiety',
    'FORUMPORTAL_POLL_VOTE'               => 'Głosuj',
    'FORUMPORTAL_POLL_VIEW_TOPIC'         => 'Zobacz ankietę w temacie',
    'FORUMPORTAL_POLL_TOTAL_VOTES'        => 'Liczba głosów',
    'FORUMPORTAL_POLL_SELECT_ONE'         => 'Wybierz jedną opcję.',
    'FORUMPORTAL_POLL_MAX_OPTIONS'        => 'Maksymalna liczba opcji',
    'FORUMPORTAL_POLL_ALREADY_VOTED'      => 'Już brałeś udział w tej ankiecie.',
    'FORUMPORTAL_POLL_GUESTS_DISABLED'   => 'Goście muszą głosować w temacie.',
    'FORUMPORTAL_POLL_CAN_CHANGE'         => 'Możesz zmienić swój głos.',
    'FORUMPORTAL_POLL_CLOSED'             => 'Ankieta zamknięta.',
    'FORUMPORTAL_POLL_VOTED'              => 'Twój głos został zapisany.',
    'FORUMPORTAL_POLL_CHANGED'            => 'Twój głos został zaktualizowany.',
    'FORUMPORTAL_POLL_ERROR_SELECT'       => 'Wybierz poprawną opcję, aby oddać głos.',
    'FORUMPORTAL_POLL_ERROR_TOO_MANY'     => 'Wybrałeś zbyt wiele opcji. Maksymalna dozwolona liczba to %1$d.',
    'FORUMPORTAL_POLL_ERROR_CLOSED'       => 'Ta ankieta została już zamknięta.',
    'FORUMPORTAL_POLL_ERROR_ALREADY_VOTED'=> 'Już głosowałeś w tej ankiecie, a zmiana głosów jest niedozwolona.',
    'FORUMPORTAL_POLL_ERROR_GUESTS_DISABLED'=> 'Głosowanie gości z poziomu portalu jest wyłączone.',
    'FORUMPORTAL_POLL_ERROR_NO_PERMISSION'=> 'Nie masz uprawnień do głosowania w tej ankiecie.',
    'FORUMPORTAL_POLL_ERROR_UNAVAILABLE'  => 'Ankieta jest obecnie niedostępna.',

    'FORUMPORTAL_QUICK_ACTIONS'           => 'Akcje portalu',
    'FORUMPORTAL_PUBLISH_TO_PORTAL'       => 'Opublikuj w portalu',
    'FORUMPORTAL_REMOVE_FROM_PORTAL'      => 'Usuń z portalu',
    'FORUMPORTAL_FEATURE_TOPIC'           => 'Wyróżnij w portalu',
    'FORUMPORTAL_UNFEATURE_TOPIC'         => 'Usuń wyróżnienie z portalu',
    'FORUMPORTAL_EDIT_PORTAL_DATA'        => 'Edytuj dane portalu',

    'FORUMPORTAL_NAV_FORUM'               => 'Forum',


));
