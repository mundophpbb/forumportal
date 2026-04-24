# Forum Portal CSS structure

The portal CSS is split by feature and loaded from `overall_header_head_append.html` using `INCLUDECSS`.

`forumportal_nav.css` is loaded globally because the Portal/Fórum breadcrumb buttons are injected outside the portal page.

The remaining portal CSS is conditional:

- `S_FORUMPORTAL_PAGE` loads the CSS on the public portal.
- `S_FORUMPORTAL_FIELDS` loads the CSS on posting pages that show Forum Portal fields.

`portal_body.html` intentionally does **not** include CSS files directly. This avoids duplicate loading and keeps the cascade predictable.

Load order:

1. `forumportal_nav.css` — global Portal/Fórum navigation buttons.
2. `forumportal_typography.css` — font mode rules.
3. `forumportal_header.css` — optional custom portal header.
4. `forumportal.css` — base layout, cards, stories, pagination and shared rules.
5. `forumportal_feedback.css` — avatar and hover tuning added from review feedback.
6. `forumportal_images_layout.css` — standardized images/avatars and optional 2-card post layout.
7. `forumportal_dark_auto.css` — automatic dark-theme compatibility selectors.
8. `forumportal_contributors.css` — top contributors block.
9. `forumportal_polls.css` — poll portal block.
10. `forumportal_theme_compat.css` — extra compatibility guards for dark/header behavior.
11. `forumportal_visual_modes.css` — ProSilver-integrated visual mode.
12. `forumportal_reviewer_polish.css` — newer reviewer feedback: navigation colors, topic icons, spacing and poll bar color.
13. `forumportal_dark_force.css` — ACP-forced dark compatibility mode.
14. `forumportal_responsive.css` — mobile and long-text polish.

Dark-mode note:

Avoid selectors like `.forumportal-page[class*="dark"]`, because the normal automatic mode class `forumportal-page--dark-auto` contains the word `dark` even when the current forum style is light. Use explicit ancestors such as `html.dark`, `body.dark`, `body.phpbb-dark`, or the forced class `.forumportal-page--dark-force`.

Poll visual note:

The result bar intentionally follows the ProSilver-style red (`#bc2a4d`) through `--forumportal-poll-color`.


## v1017 quick polish

- The poll vote button now follows the ProSilver-style poll red (`#bc2a4d`) instead of the older blue button skin.
- The topbar `Abrir fórum` button was widened and isolated from the topbar link rules so custom colors do not squeeze the button.
- Final polish keeps the breadcrumb Portal/Forum links native to the active phpBB style and only styles the portal Open forum button.

## v1018 — largura do tema

O ajuste final em `forumportal_responsive.css` remove limites internos de largura do portal, permitindo que `.forumportal-shell` acompanhe a largura real do `#wrap`/tema ativo. Isso evita que o portal fique preso à largura editorial antiga em estilos mais largos que o ProSilver.

## v1019 — Soft corners

Small radius rules were added to `forumportal_theme_compat.css` using `--forumportal-soft-radius: 4px`.
This keeps cards and sidebar blocks slightly rounded without changing the portal's layout or PHP logic.
