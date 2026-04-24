# Forum Portal CSS structure

The portal CSS is split by feature and loaded once from `overall_header_head_append.html` using `INCLUDECSS`.

The load is conditional:

- `S_FORUMPORTAL_PAGE` loads the CSS on the public portal.
- `S_FORUMPORTAL_FIELDS` loads the CSS on posting pages that show Forum Portal fields.

`portal_body.html` intentionally does **not** include CSS files directly. This avoids duplicate loading and keeps the cascade predictable.

Load order:

1. `forumportal_typography.css` — font mode rules.
2. `forumportal_header.css` — optional custom portal header.
3. `forumportal.css` — base layout, cards, stories, pagination and shared rules.
4. `forumportal_feedback.css` — avatar and hover tuning added from review feedback.
5. `forumportal_dark_auto.css` — automatic dark-theme compatibility selectors.
6. `forumportal_contributors.css` — top contributors block.
7. `forumportal_polls.css` — poll portal block.
8. `forumportal_theme_compat.css` — extra compatibility guards for dark/header behavior.
9. `forumportal_visual_modes.css` — ProSilver-integrated visual mode.
10. `forumportal_dark_force.css` — ACP-forced dark compatibility mode.
11. `forumportal_responsive.css` — mobile and long-text polish.

Dark-mode note:

Avoid selectors like `.forumportal-page[class*="dark"]`, because the normal automatic mode class `forumportal-page--dark-auto` contains the word `dark` even when the current forum style is light. Use explicit ancestors such as `html.dark`, `body.dark`, `body.phpbb-dark`, or the forced class `.forumportal-page--dark-force`.

- `forumportal_images_layout.css` — padronização de imagens/avatares e layout opcional de publicações em 2 cards.
- `forumportal_polls.css`: bloco de enquetes, opções de voto, resultados, barra de porcentagem e botão votar.
  - v1016: polimento visual leve do bloco de enquetes, sem alteração na lógica de voto.
